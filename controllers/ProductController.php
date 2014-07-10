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
    }
    
    /**
     *
        When submitted via a form, the data format is something like this:
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


    /**
     * Handle Bulk Editing
     *
     *
     */
    public function postBulk(array $scriptProperties = array()) {

        $Model = new \Moxycart\Product($this->modx);    
        $data = $Model->indexedToRecordset($scriptProperties);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,print_r($data,true),'',__CLASS__,__FUNCTION__,__LINE__);
        $seq = 0;
        foreach ($data as $p) {
            if (!isset($p['product_id'])) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'Bulk editing requires product_id!','',__CLASS__,__FUNCTION__,__LINE__);
                continue;
            }
            if(!$Product = $this->modx->getObject('Product', $p['product_id'])) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'product_id not found: '.$p['product_id'],'',__CLASS__,__FUNCTION__,__LINE__);
                continue;
            }
            $p['seq'] = $seq;
            $Product->fromArray($p);
            if (!$Product->save()) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'Failed to save product: '.$p['product_id'],'',__CLASS__,__FUNCTION__,__LINE__);
                continue;
            }
            $seq++;
        }
        return $this->sendSuccess(array('msg' => $seq.' products updated.'));
    }
        
}
/*EOF*/