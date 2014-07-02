<?php
/**
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart 
 */
namespace Moxycart;
class ProductController extends APIController {

    public $model = 'Product';     

    /**
     * FULL-on data for this object, including all relations. Gotta structure this
     * very carefully so it plays nice when converted to JSON and has to be manipulated by JS.
     *
     */
    public function postView(array $scriptProperties = array()) {
        $product_id = (int) $this->modx->getOption('product_id',$scriptProperties);

        $P = new Product($this->modx);
        $product = $P->complete($product_id);
        if (empty($product)) {
            return $this->sendFail('Product not found');
        }

        return $this->sendSuccess($product);

        //$Obj = new Product($this->modx);
        $this->modx->setOption('assman.thumbnail_width', $this->modx->getOption('moxycart.thumbnail_width'));
        $this->modx->setOption('assman.thumbnail_height', $this->modx->getOption('moxycart.thumbnail_height'));
/*
        if (!$P = $this->modx->getObjectGraph('Product','{"Image":{},"Assets":{"Asset":{}},"Options":{"Option":{}},"Relations":{"Relation":{}}}',$product_id)) {
            return $this->sendFail('Product not found');
        }
*/
        // Only do 1:1 relations here
        if (!$P = $this->modx->getObjectGraph('Product','{"Image":{}}',$product_id)) {
            return $this->sendFail('Product not found');
        }

        
        //$Coll = $this->modx->getCollection('ProductAsset', array('product_id'=> $product_id));
        //print '<pre>'; print_r($Coll->toArray()); print '</pre>'; exit;
        // Reindexing doesn't work in all cases (e.g. Relations reuse the keys)
        // so we push related records onto the 'RelData' index, keyed off their primary key, e.g.
        // $P['RelData']['Asset'][123]  stores record data for asset_id 123
        $out = $P->toArray('',false,false,true);
        $out['Assets'] = array();
        $out['Options'] = array();
        $out['Relations'] = array();
//        print '<pre>'; print_r($P1); print '</pre>'; exit;  
        $c = $this->modx->newQuery('ProductAsset');
        $c->where(array('ProductAsset.product_id'=>$product_id));
        $c->sortby('ProductAsset.seq','ASC');
        if ($Assets = $this->modx->getCollectionGraph('ProductAsset','{"Asset":{}}', $c)) {
            foreach ($Assets as $A) {
                $out['Assets'][] = $A->toArray('',false,false,true);
            }
        }

        print '<pre>'; print json_encode($out, JSON_PRETTY_PRINT); print '</pre>'; exit;
        if (isset($P1['Assets']) && is_array($P1['Assets'])) {
            foreach ($P1['Assets'] as $k => $v) {
                if (isset($v['Asset']['asset_id']) && $v['Asset']['asset_id']) {
                    $tmp_asset = $v['Asset'];
                    $P1['RelData']['Asset'][ $v['Asset']['asset_id'] ] = $v['Asset'];
                    $P1['RelData']['Order'][] = $v['Asset']['asset_id'];
                    $P1['RelData']['Groups'][] = $v['group'];
                }
            }
        }
        print '<pre>'; print_r($P1); print '</pre>'; exit;
        if (isset($P1['Options']) && is_array($P1['Options'])) {        
            foreach ($P1['Options'] as $k => $v) {
                $P1['RelData']['Option'][ $v['Option']['option_id'] ] = $v['Option'];
            }
        }
        if (isset($P1['Relations']) && is_array($P1['Relations'])) {
            foreach ($P1['Relations'] as $k => $v) {
                $P1['RelData']['Relation'][ $v['Relation']['product_id'] ] = $v['Relation'];
            }
        }
        if ($raw) return $P1;
        return $this->sendSuccess($P1);
    }
    /**
     *
When submitted via a form, the format is something like this:
Array
(
    [name] => Another Sweater
    [product_id] => 3
    [title] => Another Sweatersss
    [alias] => another-sweater
    [description] => 100% Wool for hairless homeboys
    [sku] => ANOTHER-SWEATER
    [price] => 49
    [price_strike_thru] => 99
    [asset_id] => 0
    [in_menu] => 1
    [category] => Default
    [is_active] => 1
    [template_id] => 38
    [content] => <p>Just imagine this awesome sweater.</p>
    [qty_inventory] => 78
    [qty_alert] => 4
    [track_inventory] => 0
    [type] => regular
    [weight] => 0
    [Options] => Array
        (
            [option_id] => Array
                (
                    [1] => 1
                    [2] => 0
                )

            [meta] => Array
                (
                    [1] => omit_terms
                    [2] => omit_terms
                )

            [Terms] => Array
                (
                    [oterm_id] => Array()
                    [mod_price] => Array()
                    [mod_weight] => Array()
                    [mod_code] => Array()
                    [mod_category] => Array()
                    [asset_id] => Array(...future...)
                )

        )

    [sku_vendor] =>
    [price_sale] => 0
    [sale_start] => -001-11-30 00:00:00
    [sale_end] => -001-11-30 00:00:00
    [qty_min] => 1
    [qty_max] => 0
    [back_order_cap] =>
    [store_id] => 89
    [Fields] => Array
        (
            [field_id] => Array
                (
                    [0] => 12
                    [1] => 11
                )

            [value] => Array
                (
                    [0] => zzz
                    [1] => sxxx
                )

        )

    [Relations] => Array
        (
            [related_id] => Array
                (
                    [0] => 4
                    [1] => 5
                    [2] => 6
                )

            [type] => Array
                (
                    [0] => related
                    [1] => related
                    [2] => related
                )

        )

    [Assets] => Array
        (
            [asset_id] => Array
                (
                    [0] => 27
                )

        )

)

     */
    public function postEdit(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        
        // This doesn't work unless you add the namespace.
        // Oddly, if you write it out (w/o a var), it works. wtf?
        $classname = '\\Moxycart\\'.$this->model;
        $Model = new $classname($this->modx);    
        $id = (int) $this->modx->getOption($Model->getPK(),$scriptProperties);

        if (!$Product = $Model->find($id)) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Failed to update product. Id not found: '.$id,'',__CLASS__,__FUNCTION__,__LINE__);
            return $this->sendFail(array('msg'=>sprintf('%s not found', $this->model)));
        }

        // Add Related w meta-data: one to many relationships including data about the relation 
        // (i.e. it's not just simple relations w only an array of ids and an implied seq)
        // Simple relations are: 'Options', 'Terms','Taxonomies'
        // Meta Data:
        //  Assets: has is_active
        //  Fields: has a value
        //  Relations: has a type
        $related_indices = array('Assets','Fields','Relations','Options','Meta');
        foreach($related_indices as $k) {
            if (isset($scriptProperties[$k])) $scriptProperties[$k] = $Product->indexedToRecordset($scriptProperties[$k]);
        }

        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'Restructured API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
//        print_r($scriptProperties); exit;
        $product_id = $Product->saveRelated($scriptProperties);
        
        return $this->sendSuccess(array(
            'msg' => sprintf('%s updated successfully.',$this->model),
            'id' => $product_id
        ));
    }

    /**
     * Pretty much identical to postEdit, but we don't validate for the product_id
     */
    public function postCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        // Prep the string
        $classname = '\\Moxycart\\'.$this->model;
        $Product = new $classname($this->modx);    

        $related_indices = array('Assets','Fields','Relations','Options','Meta');
        foreach($related_indices as $k) {
            if (isset($scriptProperties[$k])) $scriptProperties[$k] = $Product->indexedToRecordset($scriptProperties[$k]);
        }
        $product_id = $Product->saveRelated($scriptProperties);
        if (!$product_id) {
            return $this->sendFail(array('msg'=>'Error saving product.', 'errors'=>$Product->errors));
        
        }
//        $this->modx->log(\modX::LOG_LEVEL_ERROR,'FOFFFF>>: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        return $this->sendSuccess(array(
            'msg' => sprintf('%s updated successfully.',$this->model),
            'id' => $product_id            
        ));
    }

    /**
     * Overriding for special compatibility for product data
     * Used by autocomplete. Default limit is 25 terms
     * http://www.pontikis.net/blog/jquery-ui-autocomplete-step-by-step
     *
     * results should be an array with id, value, label keys
     *
     * data will look like this:
     *     "results":[{"id":"1","value":"2","label":"My Product"},...]
     */
    public function postSearch(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        // This doesn't work unless you add the namespace.
        // Oddly, if you write it out (w/o a var), it works. wtf?
        $classname = '\\Moxycart\\'.$this->model;
        $Model = new $classname($this->modx);    

        $scriptProperties['limit'] = $this->modx->getOption('limit',$scriptProperties,25);
        //$results = $Model->all(array('name:like'=>'shirt','limit'=>25));
        if (!$results = $Model->all($scriptProperties)) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'No results found: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
            return $this->sendFail(array(
                'msg'=>sprintf('%s not found', $this->model),
                'params' => print_r($scriptProperties,true)
            ));
        }
        
        $data = array();
        foreach ($results as $r) {
            // The autocomplete needs these 3 (and ONLY these 3) items
            $data[] = array(
                'id' => $r['product_id'],
                'value' => $r['name'],
                'label' => strip_tags(sprintf('%s (%s)',$r['name'],$r['sku']))
            );
        }
        $this->modx->log(\modX::LOG_LEVEL_INFO,'Success! Search results found: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        return $this->sendSuccess(array('results' => $data));
    }

        
}
/*EOF*/