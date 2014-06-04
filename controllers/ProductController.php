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
     * FULL-on data for this object, including all relations.
     * FUTURE: build this so it returns the same format needed to submit a new field.
     */
    public function postView(array $scriptProperties = array(),$raw=false) {
        $product_id = (int) $this->modx->getOption('product_id',$scriptProperties);
        //$Obj = new Product($this->modx);
        if (!$P = $this->modx->getObjectGraph('Product','{"Assets":{"Asset":{}},"OptionTypes":{"Type":{}},"Relations":{"Relation":{}}}',$product_id)) {
            return $this->sendFail('Product not found');
        }

        // Reindexing doesn't work in all cases (e.g. Relations reuse the keys)
        // so we push related records onto the 'RelData' index, keyed off their primary key, e.g.
        // $P['RelData']['Asset'][123]  stores record data for asset_id 123
        $P1 = $P->toArray('',false,false,true);
        if (isset($P1['Assets']) && is_array($P1['Assets'])) {
            foreach ($P1['Assets'] as $k => $v) {
                $P1['RelData']['Asset'][ $v['Asset']['asset_id'] ] = $v['Asset'];
            }
        }
        if (isset($P1['OptionTypes']) && is_array($P1['Assets'])) {        
            foreach ($P1['OptionTypes'] as $k => $v) {
                $P1['RelData']['OptionType'][ $v['Type']['otype_id'] ] = $v['Type'];
            }
        }
        if (isset($P1['Relations']) && is_array($P1['Assets'])) {
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
    [name] => Another Sweatersss
    [product_id] => 3
    [title] => Another Sweatersss
    [alias] => another-sweater
    [description] => 100% Wool for hairless homeboys
    [sku] => ANOTHER-SWEATER
    [price] => 49
    [price_strike_thru] => 99
    [in_menu] => 1
    [category] => Default
    [is_active] => 1
    [template_id] => 145
    [content] => <p>Just imagine this awesome sweater.</p>
    [qty_inventory] => 78
    [qty_alert] => 4
    [track_inventory] => 0
    [type] => regular
    [weight] => 0
    [OptionTypes] => Array
        (
            [0] => 1
            [1] => 3
        )

    [sku_vendor] =>
    [price_sale] => 0
    [sale_start] => -001-11-30 00:00:00
    [sale_end] => -001-11-30 00:00:00
    [qty_min] => 1
    [qty_max] => 0
    [back_order_cap] =>
    [store_id] => 218
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
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
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
        // Simple relations are: 'OptionTypes', 'Terms','Taxonomies'
        // Meta Data:
        //  Assets: has is_active
        //  Fields: has a value
        //  Relations: has a type
        $related_indices = array('Assets','Fields','Relations');
        foreach($related_indices as $k) {
            if (isset($scriptProperties[$k])) $scriptProperties[$k] = $Obj->indexedToRecordset($scriptProperties[$k]);
        }
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
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        // This doesn't work unless you add the namespace.
        // Oddly, if you write it out (w/o a var), it works. wtf?
        $classname = '\\Moxycart\\'.$this->model;
        $Product = new $classname($this->modx);    

        $related_indices = array('Assets','Fields','Relations');
        foreach($related_indices as $k) {
            if (isset($scriptProperties[$k])) $scriptProperties[$k] = $Product->indexedToRecordset($scriptProperties[$k]);
        }
        $product_id = $Product->saveRelated($scriptProperties);
        
        return $this->sendSuccess(array(
            'msg' => sprintf('%s updated successfully.',$this->model),
            'id' => $product_id            
        ));
    }

        
}
/*EOF*/