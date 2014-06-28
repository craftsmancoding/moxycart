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
        $name = (int) $this->modx->getOption('name',$scriptProperties);        
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
        /*
$name = $this->modx->getOption('name',$scriptProperties);
        $Obj = new Field($this->modx);    
        if (!$F = $Obj->find($field_id)) {
            return $this->sendError('Page not found.');
        }
        
        $type = $F->get('type');
        $value = ''; // todo: read default value from config?
        $label = $F->get('label');
        $description = $F->get('description');
        $config = $F->get('config');
        $args = json_decode($config,true);

        if ($type=='dropdown') {
            $out = \Formbuilder\Form::dropdown($name,$args,$value, array('label'=>$label,'description'=>$description));
        } 
        elseif ($type=='checkbox') {
            $args['label'] = $label;
            $args['description'] = $description;
            $out = \Formbuilder\Form::checkbox($name,$value, $args);
        }
        else {
            $args['label'] = $label;
            $args['description'] = $description;
            $out = \Formbuilder\Form::$type($name,$value,$args);
        }
*/
        

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