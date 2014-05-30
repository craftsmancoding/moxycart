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
     * Load a product AND its fields from a given $url
     *
     * @param string $uri relative to MODX_BASE_URL e.g. "mystore/myproduct"
     * @param boolean $force_fresh bypass cache if true. (default: false)
     * @param string $cache_dir sub-dir inside of core/cache/ (default: moxycart)
     * @return mixed array of product attributes or false on not found
     */
    public function request($uri,$force_fresh=false,$cache_dir='moxycart') {
    
        $cache_opts = array(\xPDO::OPT_CACHE_KEY => $cache_dir); 
        $fingerprint = 'product/'.$uri;

        $product_attributes = $this->modx->cacheManager->get($fingerprint, $cache_opts);

        // Cache our custom browser-specific version of the page.
        if ($force_fresh || empty($product_attributes)) {
            $this->modx->log(\modX::LOG_LEVEL_DEBUG,'Refresh requested or no cached data detected.','',__CLASS__,__FUNCTION__,__LINE__);
               
            $Product = $this->modx->getObjectGraph('Product','{"Fields":{"Field":{}}}',array('uri'=>$uri));

            if (!$Product) {
                $this->modx->log(\modX::LOG_LEVEL_DEBUG,'No Product found for uri '.$uri,'',__CLASS__,__FUNCTION__,__LINE__);
                return false;  // it's a real 404
            } 

            $product_attributes = $Product->toArray();

            foreach ($Product->Fields as $F) {
                $product_attributes[$F->Field->get('slug')] = $F->get('value');
            }
            
            $this->modx->cacheManager->set($fingerprint, $product_attributes, $Product->lifetime, $cache_opts);
            
        }
        
        return $product_attributes; 
    }
    
    //------------------------------------------------------------------------------
    public function addReview() {
        // addOne not good enough?
    }


    public function addAssetFromFile($path) {
    
    }

    //------------------------------------------------------------------------------
    //! Assets
    //------------------------------------------------------------------------------
    /**
     * Add assets to the current product.
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array of asset_id's
     */
    public function addAssets(array $array) {
        $this_product_id = $this->_verifyExisting();
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'asset_id'=> $id
            );
            if (!$PA = $this->modx->getObject('ProductAsset', $props)) {
                if (!$A = $this->modx->getObject('Asset', $id)) {
                    throw new \Exception('Invalid asset ID '.$id);    
                }
                $PA = $this->modx->newObject('ProductAsset', $props);
                $PA->save();
            }
        }
        return true;
    }

    /**
     * Remove assets to the current product.
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array of asset_id's
     */
    public function removeAssets(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $asset_id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'asset_id'=> $asset_id,
            );
            if ($PR = $this->modx->getObject('ProductAsset', $props)) {
                $PR->remove();
            }
        }
        return true;
    }
    
    /**
     * Dictate assets to the current product.
     * This will remove all assets not in the given $array, add any new assets from the $array,
     * it will order the assets based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $dictate'd asset_id's
     */
    public function dictateAssets(array $dictate) {
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id, 
            'type' => $type
        );
        
        // Array of asset_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductAsset', $props)) {
            $existing[] = $ExistingColl->get('asset_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $this->removeAssets($to_remove,$type);
        $this->addAssets($to_add,$type);
        $this->orderAssets($dictate);
        
        return true;
    }
    /**
     * Adjust the seq into ascending order for the given asset_ids
     *
     * @param array $dictate'd asset_id's
     * @param string $type name of the type of relation, used for grouping.          
     */    
    public function orderAssets(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        $seq = 0;
        foreach ($array as $asset_id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'asset_id'=> $asset_id,
            );
            if ($PR = $this->modx->getObject('ProductAsset', $props)) {
                $PR->set('seq',$seq);
                $PR->save();
                $seq++;
            }
        }
        
        return true;
    }


    //------------------------------------------------------------------------------
    //! Relations
    //------------------------------------------------------------------------------
    /**
     * Add relations to the current product.
     * Exeptions are thrown if the product ids do not exist.
     * 
     * array(
     *   array(
     *       'related_id' => 53,
     *       'type' => 'related',
     *       'seq' => 1
     *   )
     * )
     * @param array $data
     */
    public function addRelations(array $array) {
        $this_product_id = $this->_verifyExisting();

        foreach ($array as $r) {
            if (!isset($r['related_id'])) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'Missing related_id','',__CLASS__,__FUNCTION__,__LINE__); 
                continue;
            }

            if (!$PR = $this->modx->getObject('ProductRelation', $r)) {
                if (!$P = $this->modx->getObject('Product', $r['related_id'])) {
                    throw new \Exception('Invalid related ID '.$id);    
                }
                $PR = $this->modx->newObject('ProductRelation', $props);
            }
            if (isset($r['type'])) $PR->set('type', $r['type']);
            $PR->save();
        }
        
        return true;    
    }

    /**
     * Remove relations to the current product.
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array of related_id's
     * @param string $type name of the type of relation, used for grouping.     
     */
    public function removeRelations(array $array, $type='related') {

        $this_product_id = $this->_verifyExisting();
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Removing relations: '.implode(',',$array). ' from product_id '.$this_product_id,'',__CLASS__,__FILE__,__LINE__);        
        foreach ($array as $related_id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'related_id'=> $related_id,
                'type' => $type
            );
            if ($PR = $this->modx->getObject('ProductRelation', $props)) {
                if (!$PR->remove()) {
                    $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Error removing ProductRelation '.$PR->get('id'),'',__CLASS__,__FILE__,__LINE__);
                }
            }
            else {
                $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Could not find ProductRelation '.print_r($props,true),'',__CLASS__,__FILE__,__LINE__);
            }
        }
        return true;
    }
    
    /**
     * Dictate relations to the current product.
     * This will remove all relations not in the given $array, add any new relations from the $array,
     * it will order the relations based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $data
     * @param string $type name of the type of relation, used for grouping.          
     */
    public function dictateRelations(array $dictate) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Dictating relations: '.implode(',',$dictate),'',__CLASS__,__FILE__,__LINE__);
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
/*
        
        
        
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Existing relations for product_id '.$this_product_id.': '.implode(',',$existing),'',__CLASS__,__FILE__,__LINE__);        
        $to_remove = array_diff($existing,$dictate);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Product relations to be removed from product_id '.$this_product_id.': '.implode(',',$to_remove),'',__CLASS__,__FILE__,__LINE__);
        $to_add = array_diff($dictate,$existing);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Product relations to be added to product_id '.$this_product_id.': '.implode(',',$to_add),'',__CLASS__,__FILE__,__LINE__);
        $this->removeRelations($to_remove,$type);
        $this->addRelations($to_add,$type);
        $this->save();
        $this->orderRelations($dictate);
*/
        
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
     * Add terms to a product
     * @param array $array of taxonomy page ids
     */
    public function addTerms(array $array) {
        $this_product_id = $this->_verifyExisting();

        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'term_id'=> $id
            );
            if (!$PT = $this->modx->getObject('ProductTerm', $props)) {
                if (!$T = $this->modx->getObject('Term', $id)) {
                    throw new \Exception('Invalid term ID '.$id);    
                }
                $PT = $this->modx->newObject('ProductTerm', $props);
                $PT->save();
            }
        }

        return true;    
    }

    /** 
     * Remove terms from a product. We don't care here if the referenced taxonomy ids are valid or not.
     * @param array $array of taxonomy page ids
     */
    public function removeTerms(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'term_id'=> $id
            );
            if ($PT = $this->modx->getObject('ProductTerm', $props)) {
                $PT->remove();
            }
        }
        
        return true;
    }

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
        
        $props = array(
            'product_id'=> $this_product_id,
        );
        
        // Array of related_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductTerm', $props)) {
            $existing[] = $ExistingColl->get('term_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $this->removeTerms($to_remove);
        $this->addTerms($to_add);
        
        return true;
    
    }
        
    //------------------------------------------------------------------------------
    //! Fields
    //------------------------------------------------------------------------------
    /** 
     * Add field data to a product.  We don't do this via addMany... shrugs.
     *
     * array(
     *      array(
     *          'field_id' => 123,          (required)
     *          'value'=>'something'        (optional)
     *          'seq' => 1                  (optional)
     *      ),
     * )
     *
     * @param array $data of ProductField data -- they omit the product_id since that is inherited
     */
    public function addFields(array $data) {
        $this_product_id = $this->_verifyExisting();

        foreach ($data as $r) {
            if (!isset($r['field_id'])) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'Missing field_id','',__CLASS__,__FUNCTION__,__LINE__); 
                continue;
            }
            if (!$F = $this->modx->getObject('Field', $r['field_id'])) {
                throw new \Exception('Invalid field ID '.$r['field_id']);    
            }            

            $props = array(
                'product_id' => $this_product_id,
                'field_id' => $r['field_id'] 
            );

            if (!$PF = $this->modx->getObject('ProductField', $props)) {                
                $PF = $this->modx->newObject('ProductField', $props);
            }
            if (isset($r['value'])) $PF->set('value', $r['value']);
            if (isset($r['seq'])) $PF->set('seq', $r['seq']);
            $PF->save();
        }

        return true;    
    }

    /** 
     * Remove fields from a product. We don't care here if the referenced taxonomy ids are valid or not.
     * @param array $array of taxonomy page ids
     */
    public function removeFields(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'field_id'=> $id
            );
            if ($PT = $this->modx->getObject('ProductField', $props)) {
                $PT->remove();
            }
        }
        return true;
    
    }

    /**
     * Dictate fields for the current product. See $data structure above @addFields()
     *
     * This will remove all fields not in the given $array, add any new relations from the $array,
     * it will order the relations based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $data of records
     */
    public function dictateFields(array $data) {
        $this_product_id = $this->_verifyExisting();
        
        $dictate = array();
        foreach($data as $r) {
            $dictate[] = $r['field_id'];
        }
        $props = array(
            'product_id'=> $this_product_id,
        );
        
        // Array of related_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductField', $props)) {
            $existing[] = $ExistingColl->get('field_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $newdata = array();
        foreach ($data as $r) {
            if (in_array($r['field_id'],$to_add)) {
                $newdata[] = $r;
            }
        }

        $this->removeFields($to_remove);
        $this->addFields($newdata);
        
        return true;
    
    }

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
     'OptionTypes' => array(1,2,3),
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
     */
    public function saveRelated($data) {
        // Extra stuff is ignored
        $this->fromArray($data);
        if (!$this->save()) {
            return false;
        }
        $product_id = $this->get('product_id');
        if (isset($data['Assets'])) $this->dictateAssets($data['Assets']);
        if (isset($data['Fields'])) $this->dictateFields($data['Fields']);
        if (isset($data['OptionTypes'])) $this->dictateAssets($data['OptionTypes']);
        if (isset($data['Relations'])) $this->dictateAssets($data['Relations']);
        if (isset($data['Taxonomies'])) $this->dictateAssets($data['Taxonomies']);
        if (isset($data['Terms'])) $this->dictateAssets($data['Terms']);
        
    }
    /** 
     * get all Fields and Values for this product
     * @param array $array of taxonomy page ids
     */
/*
    public function getFields() {
        $this_product_id = $this->_verifyExisting();

        $pages = $this->modx->getCollectionGraph('ProductField','{"Field":{}}',$criteria);
        return true;    
    }
*/

    //------------------------------------------------------------------------------
    //! OptionTypes
    //------------------------------------------------------------------------------
    /** 
     * Add variation-types to a product. 
     *
     * @param array $array of otype_ids
     */
    public function addOptionTypes(array $array) {
        $this_product_id = $this->_verifyExisting();

        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'otype_id'=> $id
            );
            if (!$PVT = $this->modx->getObject('ProductOptionType', $props)) {
                if (!$VT = $this->modx->getObject('OptionType', $id)) {
                    throw new \Exception('Invalid Option Type ID '.$id);    
                }
                $PVT = $this->modx->newObject('ProductOptionType', $props);
            }
            $PVT->save();
        }

        return true;    
    }

    /** 
     * Remove variation-types from a product. We don't care here if the referenced ids are valid or not.
     * @param array $array of otype_ids
     */
    public function removeOptionTypes(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'otype_id'=> $id
            );
            if ($PVT = $this->modx->getObject('ProductOptionType', $props)) {
                $PVT->remove();
            }
        }
        return true;
    }

    /**
     * Dictate variation-type ids for the current product.
     * This will remove all fields not in the given $array, add any new otype_id's from the $array.

     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $dictate'd otype_id's
     */
    public function dictateOptionTypes(array $dictate) {
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id,
        );
        
        // Array of related_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductOptionType', $props)) {
            $existing[] = $ExistingColl->get('otype_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $this->removeOptionTypes($to_remove,$type);
        $this->addOptionTypes($to_add,$type);
        
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
     * @param integer $store_id
     */
    public function inheritFromStore($store_id) {

        // Set defaults from the parent Store
        if ($Store = $this->modx->getObject('Store', $store_id)) {
            if ($properties = $Store->get('properties')) {
                $this->template_id = (isset($properties['moxycart']['product_template'])) ? $properties['moxycart']['product_template'] : $this->modx->getOption('default_template');
                $this->type = (isset($properties['moxycart']['product_type'])) ? $properties['moxycart']['product_type'] : 'regular';
                // This is not persisted...wtf
                $this->sort_order = (isset($properties['moxycart']['sort_order'])) ? $properties['moxycart']['sort_order'] : 'name';
                $this->qty_alert = (isset($properties['moxycart']['qty_alert'])) ? $properties['moxycart']['qty_alert'] : 0;
                $this->track_inventory = (isset($properties['moxycart']['track_inventory'])) ? $properties['moxycart']['track_inventory'] : 0;
                // addOne
                //$this->fields = (isset($properties['moxycart']['specs'])) ? $properties['moxycart']['specs'] : array();
                //$this->taxonomies = (isset($properties['moxycart']['taxonomies'])) ? $properties['moxycart']['taxonomies'] : array();
            }
        }
        else {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, 'store_id does not exist',__CLASS__);
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
        
        $matrix = array( $otype_id => $oterm_id [, $otype_id2 => $oterm_id2 ... ] )
     */
/*
    public function addVariant($matrix) {
        $this_product_id = $this->_verifyExisting();
        $V = new Variant($this->modx);
        //$V->alias = $V->
        
    }
*/
    
    
    /** 
     *
     */
    
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