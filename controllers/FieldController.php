<?php
/**
 *
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart 
 */
namespace Moxycart;
class FieldController extends APIController {
    public $model = 'Field';        

    /**
     * This is a nutty thing; the calculus of page generation, an order 
     * of complexity beyond the norm b/c unlike the other API methods,
     * this one must return *HTML*, not JSON.
     * 
     */
    public function postGenerate(array $scriptProperties = array()) {
    
        $field_id = (int) $this->modx->getOption('field_id',$scriptProperties);
        $product_id = (int) $this->modx->getOption('product_id',$scriptProperties);
        $name = $this->modx->getOption('name',$scriptProperties);        
        $Field = new Field($this->modx);
        $value = '';
        if ($product_id) {
            if($ProductField = $this->modx->getObject('ProductField', array('field_id'=>$field_id,'product_id'=>$product_id))) {
                $value = $ProductField->get('value');
            }
        }
        
        $out = $Field->generate($field_id,$value,$name);
        if ($out === false) {
            return $this->sendError('Field not found.');
        }

        // Double-quoting here is REQUIRED to trigger the __toString on the Formbuilder object.
        // Otherwise an empty object reference is sent.
        return $this->sendSuccess("$out");
    }
    
    /**
     * Generate multiple fields based on field_ids
     */
    public function postGeneratemulti(array $scriptProperties = array()) {
    
        $field_ids = $this->modx->getOption('field_ids',$scriptProperties,array());
        $product_id = (int) $this->modx->getOption('product_id',$scriptProperties);
        $Field = new Field($this->modx);
        $value = '';

        if (empty($field_ids) || !is_array($field_ids)) {
            return $this->sendError('Invalid Field IDs.');
        }
        $out = '';
        foreach ($field_ids as $field_id) {
            if ($product_id) {
                if($ProductField = $this->modx->getObject('ProductField', array('field_id'=>$field_id,'product_id'=>$product_id))) {
                    $value = $ProductField->get('value');
                }
            }
            $out .= $Field->generate($field_id,$value);
        }
        if ($out === false) {
            return $this->sendError('Field not found.');
        }

        // Double-quoting here is REQUIRED to trigger the __toString on the Formbuilder object.
        // Otherwise an empty object reference is sent.
        return $this->sendSuccess("$out");
    }    
    
    
    // For testing.
    public function getGenerate(array $scriptProperties = array()) {    
        return $this->postGenerate($scriptProperties);
    }
}
/*EOF*/