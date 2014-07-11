<?php
/**
 * Model
 * This is a bit tricky because we already have an ORM layer: that Product class already
 * handles newObject, save, etc.  The getCollection 
 *
 * This model class should define a unified interface that define the graphs that represent
 * an object data in its entirety.
 */
namespace Moxycart;
class Product extends BaseModel {

    public $xclass = 'Product';
    public $default_sort_col = 'name'; 
    // Searches in the manager using "searchterm" will trigger a LIKE search matching any of these columns
    public $search_columns = array('name','title','meta_keywords','description','content','sku'); 
    private $product_id;
    
    /**
     * Verify that the existing product is in fact an existing, valid, persisted product
     * and not a new, unsaved product.
     * @return integer product_id
     */
    private function _verifyExisting() {
    
        if (!$this->modelObj->isNew() && !empty($this->product_id)) {
            return $this->product_id;
        }

        if(!$this->product_id = $this->get('product_id')) {
            throw new \Exception('Product ID not defined');
        }
        // Make sure we're not getting jerked around by an un-persisted product_id
        if (!$P = $this->modx->getObject('Product', $this->product_id)) {
            throw new \Exception('Product does not exist '.$this->product_id);    
        }
        
        return $this->product_id; 
    }

    /**
     * Retrive "all" records matching the filter $args.
     *
     * We use getIterator, but we have to work around the "feature" (bug?) that 
     * it will not return an empty array if it has no results. See
     * https://github.com/modxcms/revolution/issues/11373
     *
     * @param array $arguments (including filters)
     * @param boolean $debug
     * @return mixed xPDO iterator (i.e. a collection, but memory efficient) or SQL query string
     */
    public function all($args,$debug=false) {

        // If you get this error: "Call to a member function getOption() on a non-object", it could mean:
        // 1) you tried to call this method statically, e.g. Product::all()
        // 2) you forgot to initialize the class and pass a modx instance to the contructor (dependency injection!)
        $limit = (int) $this->modx->getOption('limit',$args,$this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page')));
        $offset = (int) $this->modx->getOption('offset',$args,0);
        $sort = $this->quoteSort($this->modx->getOption('sort',$args,$this->default_sort_col));
        $dir = $this->modx->getOption('dir',$args,$this->default_sort_dir);
        $debug = $this->modx->getOption('debug',$args);
        $select_cols = $this->modx->getOption('select',$args);
        
        // Clear out non-filter criteria
        $args = self::getFilters($args); 
//        return '<pre>'.print_r($args,true).'</pre>';
        $criteria = $this->modx->newQuery($this->xclass);

        if ($args) {
            if (isset($args['searchterm'])) {
                $searchterm = $args['searchterm'];
                unset($args['searchterm']);
                $search_c = array();
                $first = array_shift($this->search_columns);
                $search_c[$first.':LIKE'] = '%'.$searchterm.'%';
                foreach ($this->search_columns as $c) {
                    $search_c['OR:'.$c.':LIKE'] = '%'.$searchterm.'%'; 
                }
                $criteria->where($search_c);
            }
            else {
                $criteria->where($args);
            }
        }
        
        if ($limit) {
            $criteria->limit($limit, $offset); 
        }
        if ($sort) {
            $criteria->sortby($sort,$dir);
        }    
        if ($debug) {
            $criteria->bindGraph('{"Image":{}}');
            $criteria->prepare();
            print $criteria->toSQL();
            exit;
        }

        // Both array and string input seem to work
        if (!empty($select_cols)) {
            $criteria->select($select_cols);
        }

        $out = array();
        if ($Products = $this->modx->getCollectionGraph('Product','{"Image":{}}',$criteria)) {

            foreach ($Products as $P) {
                $att = $P->toArray('',false,false,true);
                if ($P->Image) {
                    $att['img'] = $P->Image->get('url');
                    $att['thumb'] = $P->Image->get('thumbnail_url');
                    $att['thumb_tag'] = sprintf('<img src="%s" alt="thumbnail for %s" />', $att['thumb'], $att['name']);
                }
                else {
                    $w = $this->modx->getOption('moxycart.thumbnail_width');
                    $h = $this->modx->getOption('moxycart.thumbnail_height');
                    $att['img'] = '';
                    $att['thumb'] = '';
                    $att['thumb_tag'] = sprintf('<img src="%s" alt="thumbnail for %s" />', 
                        sprintf('http://placehold.it/%sx%s&text=%s',$w,$h,$att['name']), 
                        $att['name']);
                }
                $out[] = $this->flattenArray($att);   
            }
        }
        //print_r($out); exit;
        return $out;
    }

    /**
     * For use in the manager: get a product and ALL of its related data for a complete record.
     * We have to format the result very carefully so it plays nice when converted to JSON
     * @param integer $product_id
     * @return array (empty if product not found)
     */
    public function complete($product_id) {
        // Ensure we generate the proper thumbnail dimensions by overloading config
        $this->modx->setOption('assman.thumbnail_width', $this->modx->getOption('moxycart.thumbnail_width'));
        $this->modx->setOption('assman.thumbnail_height', $this->modx->getOption('moxycart.thumbnail_height'));
        
        if (!$P = $this->modx->getObjectGraph('Product','{"Image":{}}',$product_id)) {
            return array(
                'Image' => array(),
                'Assets' => array(),
                'Options' => array(),
                'Fields' => array(),
                'Relations' => array(),
            );
        }

        $out = $P->toArray('',false,false,true);
        $out['Assets'] = array();
        $out['Options'] = array();
        $out['Fields'] = array();
        $out['Relations'] = array();

        $c = $this->modx->newQuery('ProductAsset');
        $c->where(array('ProductAsset.product_id'=>$product_id));
        $c->sortby('ProductAsset.seq','ASC');
        if ($Assets = $this->modx->getCollectionGraph('ProductAsset','{"Asset":{}}', $c)) {
            foreach ($Assets as $A) {
                if (isset($A->Asset) && !empty($A->Asset)) {
                    $out['Assets'][] = $A->toArray('',false,false,true);
                }
            }
        }

        $c = $this->modx->newQuery('ProductOption');
        $c->where(array('ProductOption.product_id'=>$product_id));
        $c->sortby('Option.seq','ASC');
        if ($Options = $this->modx->getCollectionGraph('ProductOption','{"Option":{}}', $c)) {
            foreach ($Options as $O) {
                $out['Options'][] = $O->toArray('',false,false,true);
            }
        }

        $c = $this->modx->newQuery('ProductField');
        $c->where(array('ProductField.product_id'=>$product_id));
        $c->sortby('Field.seq','ASC');
        if ($Fields = $this->modx->getCollectionGraph('ProductField','{"Field":{}}', $c)) {
            foreach ($Fields as $F) {
                $out['Fields'][] = $F->toArray('',false,false,true);
            }
        }

        $c = $this->modx->newQuery('ProductRelation');
        $c->where(array('ProductRelation.product_id'=>$product_id));
        $c->sortby('ProductRelation.seq','ASC');
        if ($Relations = $this->modx->getCollectionGraph('ProductRelation','{"Relation":{}}', $c)) {
            foreach ($Relations as $R) {
                $out['Relations'][] = $R->toArray('',false,false,true);
            }
        }

        //print '<pre>'; print json_encode($out, JSON_PRETTY_PRINT); print '</pre>'; exit;
        return $out;
    }
    
    /**
     * Load a product AND its fields from a given $url
     *
     * @param string $uri relative to MODX_BASE_URL e.g. "mystore/myproduct"
     * @param boolean $force_fresh bypass cache if true. (default: false)
     * @param string $cache_dir sub-dir inside of core/cache/ (default: moxycart)
     * @return mixed array of product attributes or false on not found
     */
    public function request($uri,$force_fresh=false,$cache_dir='moxycart') {
    
        $cache_opts = array(\xPDO::OPT_CACHE_KEY => $cache_dir); 
        // $this->modx->request->parameters['GET']  ???
        $fingerprint = 'product/'.$uri;

        $product_attributes = $this->modx->cacheManager->get($fingerprint, $cache_opts);

        // Cache our custom browser-specific version of the page.
        if ($force_fresh || empty($product_attributes)) {
            $this->modx->log(\modX::LOG_LEVEL_DEBUG,'Refresh requested or no cached data detected.','',__CLASS__,__FUNCTION__,__LINE__);
               
            $Product = $this->modx->getObjectGraph('Product','{"Image":{},"Fields":{"Field":{}}}',array('uri'=>$uri));

            if (!$Product) {
                $this->modx->log(\modX::LOG_LEVEL_DEBUG,'No Product found for uri '.$uri,'',__CLASS__,__FUNCTION__,__LINE__);
                return false;  // it's a real 404
            } 

            $product_attributes = $Product->toArray();

            foreach ($Product->Fields as $F) {
                $product_attributes[$F->Field->get('slug')] = $F->get('value');
            }

            if (isset($Product->Image) && !empty($Product->Image)) {
                $product_attributes['img'] = $Product->Image->get('url');
                $product_attributes['thumb'] = $Product->Image->get('thumbnail_url');
            }
            
            $this->modx->cacheManager->set($fingerprint, $product_attributes, $Product->lifetime, $cache_opts);
            
        }
        
        return $product_attributes; 
    }
    
    //------------------------------------------------------------------------------
    public function addReview() {
        // addOne not good enough?
    }


    
    //------------------------------------------------------------------------------
    //! Relations
    //------------------------------------------------------------------------------
    /**
     * Dictate relations to the current product.
     * This will remove all relations not in the given $array, add any new relations from the $array,
     * it will order the relations based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $data
     * @param string $type name of the type of relation, used for grouping.          
     */
    public function dictateRelations(array $data) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Dictating relations: '.print_r($data,true),'',__CLASS__,__FILE__,__LINE__);
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id, 
        );
        
        // Get the existing relations
        $existing = array();
        if($Col = $this->modx->getIterator('ProductRelation', $props)) {
            foreach ($Col as $c) {
                 $k = $c->get('related_id').':'.$c->get('type');   
                 $existing[$k] = $c->get('id');
             }
        }
        
        $i = 0;
        foreach ($data as $r) {
            if (!isset($r['related_id']) || !isset($r['type'])) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'related_id and type are required','',__CLASS__,__FUNCTION__,__LINE__); 
                continue;
            }
            $r['product_id'] = $this_product_id;
            $k = $r['related_id'] .':'. $r['type'];
            if (!isset($existing[$k])) {
                // Create it
                $PR = $this->modx->newObject('ProductRelation', $r);
                $PR->set('seq', $i);
                $PR->save();
            }
            else {
                if ($PR = $this->modx->getObject('ProductRelation', $r)) {
                    $PR->set('seq', $i);
                    $PR->save();
                }
                unset($existing[$k]);
            }
            $i++;
        }
        
        foreach ($existing as $k => $id) {
            $PR = $this->modx->getObject('ProductRelation', $id);
            $PR->remove();
        }
        return true;
    }
    /**
     * Adjust the seq into ascending order for the given related_ids
     *
     * @param array $dictate'd related_id's
     * @param string $type name of the type of relation, used for grouping.          
     */    
    public function orderRelations(array $array, $type='related') {
        $this_product_id = $this->_verifyExisting();
        
        $seq = 0;
        foreach ($array as $related_id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'related_id'=> $related_id,
                'type' => $type
            );
            if ($PR = $this->modx->getObject('ProductRelation', $props)) {
                $PR->set('seq',$seq);
                $PR->save();
                $seq++;
            }
        }
        
        return true;
    }

    //------------------------------------------------------------------------------
    //! Taxonomies
    //------------------------------------------------------------------------------
    /** 
     * Add taxonomies to a product
     * @param array $array of taxonomy page ids
     */
    public function addTaxonomies(array $array) {
        $this_product_id = $this->_verifyExisting();

        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'taxonomy_id'=> $id
            );
            if (!$PT = $this->modx->getObject('ProductTaxonomy', $props)) {
                if (!$T = $this->modx->getObject('Taxonomy', $id)) {
                    throw new \Exception('Invalid taxonomy ID '.$id);    
                }
                $PT = $this->modx->newObject('ProductTaxonomy', $props);
                $PT->save();
            }
        }

        return true;    
    }

    /** 
     * Remove taxonomies from a product. We don't care here if the referenced taxonomy ids are valid or not.
     * @param array $array of taxonomy page ids
     */
    public function removeTaxonomies(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'taxonomy_id'=> $id
            );
            if ($PT = $this->modx->getObject('ProductTaxonomy', $props)) {
                $PT->remove();
            }
        }
        return true;
    
    }

    /**
     * Dictate taxonomies to the current product.
     * This will remove all taxonomies not in the given $array, add any new relations from the $array,
     * it will order the relations based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $dictate'd related_id's
     */
    public function dictateTaxonomies(array $dictate) {
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id,
        );
        
        // Array of related_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductTaxonomy', $props)) {
            $existing[] = $ExistingColl->get('taxonomy_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $this->removeTaxonomies($to_remove);
        $this->addTaxonomies($to_add);
        $this->orderTaxonomies($dictate);
        
        return true;
    
    }

    /**
     * Adjust the seq into ascending order for the given taxonomy ids
     *
     * @param array $dictate'd taxonomy id's
     */    
    public function orderTaxonomies(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        $seq = 0;
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'taxonomy_id'=> $id
            );
            if ($PT = $this->modx->getObject('ProductTaxonomy', $props)) {
                $PT->set('seq',$seq);
                $PT->save();
                $seq++;
            }
        }
        
        return true;
    }

    
    //------------------------------------------------------------------------------
    //! Terms
    //------------------------------------------------------------------------------
    /**
     * Dictate terms for the current product.
     * This will remove all terms not in the given $array, add any new relations from the $array,
     * it will order the relations based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $dictate'd related_id's
     */
    public function dictateTerms(array $dictate) {
        $this_product_id = $this->_verifyExisting();
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'Terms:'.print_r($dictate,true),'',__CLASS__,__FUNCTION__,__LINE__);  
        $props = array(
            'product_id'=> $this_product_id,
        );

        foreach ($dictate as $term_id) {
            $props = array('product_id'=>$this_product_id,'term_id'=>$term_id);
            if(!$PT = $this->modx->getObject('ProductTerm', $props)) {
                $PT = $this->modx->newObject('ProductTerm', $props);
            }
            $PT->save();
        }

        // Remove
        $c = $this->modx->newQuery('ProductTerm');
        if (empty($dictate)) {
            $c->where(array('product_id' => $this_product_id));                    
        }
        else {
            $c->where(array('product_id' => $this_product_id, 'term_id:NOT IN' => $dictate));
        }
        if($PT = $this->modx->getCollection('ProductTerm', $c)) {
            foreach ($PT as $p) {
                $p->remove();
            }
        }
        
        return true;
    
    }

    //------------------------------------------------------------------------------
    //! Fields
    //------------------------------------------------------------------------------
    /**
     * Dictate fields for the current product. See $data structure:
     *
     * array(
     *      array(
     *          'field_id' => 123,          (required)
     *          'value'=>'something'        (optional)
     *          'seq' => 1                  (optional)
     *      ),
     * )     
     *
     * This will remove all fields not in the given $array, add any new relations from the $array,
     * it will order the relations based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $data of records
     */
    public function dictateFields(array $data) {
        $this_product_id = $this->_verifyExisting();
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'Fields:'.print_r($data,true),'',__CLASS__,__FUNCTION__,__LINE__);  
        $dictate = array();
        foreach($data as $r) {
            $dictate[] = $r['field_id'];
        }
        $props = array(
            'product_id'=> $this_product_id,
        );

        $dictate = array();
        foreach ($data as $d) {
            $props = array('product_id'=>$this_product_id,'field_id'=>$d['field_id']);
            if(!$PF = $this->modx->getObject('ProductField', $props)) {
                $PF = $this->modx->newObject('ProductField', $props);
            }
            if (isset($d['value'])) $PF->set('value', $d['value']);
            $PF->save();
            $dictate[] = $PF->get('field_id');    
        }

        // Remove
        if($ExistingColl = $this->modx->getCollection('ProductField', array('product_id' => $this_product_id))) {
            foreach ($ExistingColl as $PF) {
                if (!in_array($PF->get('field_id'), $dictate)) {
                    $PF->remove();
                }
            }
        }
        
        return true;
    
    }

    //------------------------------------------------------------------------------
    //! Options
    //------------------------------------------------------------------------------
    /**
     * Dictate variation-type ids for the current product.
     * This will remove all fields not in the given $array, add any new option_id's from the $array.

     * Exeptions are thrown if the product ids do not exist.
     * Data structure must be like so:
     *  Array(
     *      {$option_id} => Array('meta'=>'all_terms'),
     *  )
     *
     * @param array $data related data
     */
    public function dictateOptions(array $data) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Dictating options: '.print_r($data,true),'',__CLASS__,__FILE__,__LINE__);
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id, 
        );
        
        $i = 0;
        $dictate = array();
        foreach ($data as $option_id => $r) {
            if (isset($r['checked']) && $r['checked'] == 0) {
                if ($PO = $this->modx->getObject('ProductOption', array('product_id'=>$this_product_id, 'option_id'=>$option_id))) {
                    if (!$PO->remove()) {
                        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Unable to delete ProductOption for product_id '.$this_product_id.' option_id: '.$option_id,'',__CLASS__,__FUNCTION__,__LINE__); 
                    }
                }
                continue;
            }
//            print_r($r); exit;
            if (!$PO = $this->modx->getObject('ProductOption', array('product_id'=>$this_product_id, 'option_id'=>$option_id))) {
                $PO = $this->modx->newObject('ProductOption');
            }
            $PO->set('option_id', $option_id);
            $PO->set('meta', $r['meta']);
            $PO->set('product_id', $this_product_id);
            if(!$PO->save()) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Error dictating option: '.print_r($r,true),'',__CLASS__,__FILE__,__LINE__);
            }
            $dictate[] = $PO->get('id');
        }
        

        $criteria = $this->modx->newQuery('ProductOption');
        $filter = array('product_id' => $this_product_id);
        if (!empty($dictate)) {
            $filter['id:NOT IN'] = $dictate; // this can't be empty
        }
        
        $criteria->where($filter);        
        if ($Remove = $this->modx->getCollection('ProductOption', $criteria)) {
            foreach ($Remove as $r) {
                $r->remove();
            }
        }
        
        return true;
    }


    //------------------------------------------------------------------------------
    //! Meta
    //------------------------------------------------------------------------------
    /**
     * Dictate product option meta data
     *
     * @param array $data related data
     */
    public function dictateMeta(array $data) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Dictating meta: '.print_r($data,true),'',__CLASS__,__FILE__,__LINE__);
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id, 
        );
        
        foreach ($data as $d) {
            if (!$M = $this->modx->getObject('ProductOptionMeta', 
                array('product_id'=>$this_product_id,
                    'option_id'=>$d['option_id'],
                    'oterm_id'=>$d['oterm_id'])
                )) {
                
                $M = $this->modx->newObject('ProductOptionMeta', 
                    array('product_id'=>$this_product_id,
                        'option_id'=>$d['option_id'],
                        'oterm_id'=>$d['oterm_id'])
                    );
            }
            
            if (!isset($d['checked']) || !$d['checked']) {
                $M->remove();
                continue;
            }
            
            $M->fromArray($d);
            $M->set('product_id',$this_product_id);
            $M->save();

        }

        return true;
    }

    
    //------------------------------------------------------------------------------
    /**
     * Determine whether 2 associative arrays contain the same elements, regardless of
     * order.
     *
     * @param array $h1
     * @param array $h2
     * @return array of differences
     */
    public function hashDelta($h1,$h2) {
        $x = array_diff_assoc($h1,$h2);
        $y = array_diff_assoc($h2,$h1);
        return array_merge($x,$y);
        
    }
    
    /**
     * Inherit values from the parent store and set them onto the $this->modelObj object
     * @param integer $store_id (optional)
     */
    public function getDefaultValues($store_id=null) {
        $this->template_id = $this->modx->getOption('default_template');

        // Inherit defaults from the parent Store
        if ($store_id) {
            $this->store_id = $store_id;
            if ($Store = $this->modx->getObject('Store', $store_id)) {
                if ($properties = $Store->get('properties')) {
                    $this->template_id = (isset($properties['moxycart']['product_template'])) ? $properties['moxycart']['product_template'] : $this->modx->getOption('default_template');
                    $this->type = (isset($properties['moxycart']['product_type'])) ? $properties['moxycart']['product_type'] : 'regular';
                    // This is not persisted...wtf
                    $this->sort_order = (isset($properties['moxycart']['sort_order'])) ? $properties['moxycart']['sort_order'] : 'name';
                    $this->qty_alert = (isset($properties['moxycart']['qty_alert'])) ? $properties['moxycart']['qty_alert'] : 0;
                    $this->track_inventory = (isset($properties['moxycart']['track_inventory'])) ? $properties['moxycart']['track_inventory'] : 0;
                }
            }
            else {
                $this->modx->log(\modX::LOG_LEVEL_ERROR, 'store_id does not exist',__CLASS__);
            } 
        }
    }
    
    /**
     * What types of products do we currently support?
     */
    function getTypes() {
        return array(
            'regular'=>'Regular'
            // FUTURE:
            //'subscription' => 'Subscription',
            //'download' => 'Download',
        );
    }
    
    /**
     * Add a child variant to the current product. 
     *
     * PRODUCT OPTION MODIFIERS
     * https://wiki.foxycart.com/v/1.1/cheat_sheet
     * https://wiki.foxycart.com/v/0.6.0/getting_started/adding_links_and_forms
         p for price
         w for weight
         c for product code
         y for category (v070+ only)
         
            <select name="size">
            	<option value="S{p+1.50|w-1|c:01a|y:teeny_category}">Small</option>
            	<option value="XL{p+2.50|w+1|c:01d}">X-Large</option>
            	<option value="Custom{p:5|w+1|c:01s}">Custom</option>
            </select>
        
        $matrix = array( $option_id => $oterm_id [, $option_id2 => $oterm_id2 ... ] )
     */
/*
    public function addVariant($matrix) {
        $this_product_id = $this->_verifyExisting();
        $V = new Variant($this->modx);
        //$V->alias = $V->
        
    }
*/
    
    
    /**
     * Data format should be ??? saveComplete
     *
     
     'title' => 'myproduct',
     'price' => 14.99
     // ...etc...
     
     // Related Data: some can pass simple arrays, others need to pass more data
     'Assets' => array(1,2,3),
     // Important: field associations infer a value!
     'Fields' => array(
        array(
            'field_id' => 4,
            'value' => 'Something'
        )
     ),
     'Options' => array(1,2,3),
     'Relations' => array(
        array(
            'related_id' => 53,
            'type' => 'related',
            'seq' => 1
        )
     ),
     'Taxonomies' => array(1,2,3),
     'Terms' => array(1,2,3),
     
     @param array $data (e.g. from $_POST)
     @return 
     */
    public function saveRelated($data) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'Save related data: '.print_r($data,true),'',__CLASS__,__FUNCTION__,__LINE__);
        // Extra stuff is ignored... it doesn't matter here whether we're creating or updating an object
        $this->fromArray($data);
        if (!$this->save()) {
            return false;
        }

        $product_id = $this->getPrimaryKey(); // $this->get('product_id');
        if (isset($data['Assets'])) {
            $Asset = $this->modx->newObject('Asset');
            $Asset->dictateRelations($data['Assets'], $product_id, 'product_id', 'ProductAsset');
        }
        if (isset($data['Fields'])) $this->dictateFields($data['Fields']);
        if (isset($data['Options'])) $this->dictateOptions($data['Options']);
        if (isset($data['Meta'])) $this->dictateMeta($data['Meta']);
        if (isset($data['Relations'])) $this->dictateRelations($data['Relations']);
        if (isset($data['Taxonomies'])) $this->dictateTaxonomies($data['Taxonomies']);
        if (isset($data['Terms'])) {
            $this->dictateTerms($data['Terms']);
        }
        else {
            $this->dictateTerms(array());
        }
        
        return $product_id;
    }
    
    /**
     * Override here for special stuff
     */
    public function save() {
        // /^[a-z0-9\-_\/]+$/i

        $result = true; 
        if (!preg_match('/^[a-z0-9\-_\/]+$/i', $this->get('alias'))) {
            $this->errors['alias'] = 'Invalid alias characters';
            $result = false;        
        }
/*
        if (!$Store = $this->modx->getObject('Store', $this->get('store_id'))) {
            $this->errors['store_id'] = 'Invalid Store ID';
            $result = false;
        }
        else {
            $this->set('uri', $Store->get('uri').$this->get('alias'));
        }
*/
        if (!$result) {
            return false;
        }
        return parent::save();
    }
    
}
/*EOF*/
