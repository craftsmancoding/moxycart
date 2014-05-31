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
        $this->modx->setLogLevel(4);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        // This doesn't work unless you add the namespace.
        // Oddly, if you write it out (w/o a var), it works. wtf?
        $classname = '\\Moxycart\\'.$this->model;
        $Model = new $classname($this->modx);    
        $id = (int) $this->modx->getOption($Model->getPK(),$scriptProperties);

        if (!$Obj = $Model->find($id)) {
            return $this->sendFail(array('msg'=>sprintf('%s not found', $this->model)));
        }
/*
        $Obj->fromArray($scriptProperties);
        if (!$Obj->save()) {
            return $this->fail(array('errors'=> $Obj->errors));
        }
*/
        
        // Add Related Data: stuff that has one to many relationships 
        $related_indices = array('Assets','Fields','OptionTypes','Relations','Taxonomies','Terms');
        foreach($related_indices as $k) {
            if (isset($scriptProperties[$k])) $scriptProperties[$k] = $Obj->indexedToRecordset($scriptProperties[$k]);
        }
        $Obj->saveRelated($scriptProperties);
        
        return $this->sendSuccess(array(
            'msg' => sprintf('%s updated successfully.',$this->model)
        ));
    }
        
}
/*EOF*/