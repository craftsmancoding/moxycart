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
        $name = $this->modx->getOption('name',$scriptProperties);
        $Obj = new Field($this->modx);    
        if (!$F = $Obj->find($field_id)) {
            return $this->sendError('Page not found.');
        }
        
        $type = $F->get('type');
        $value = ''; // todo: read default value from config?
        $label = $F->get('label');
        $description = $F->get('description');

        $out = \Formbuilder\Form::$type($name,$value, array('label'=>$label,'description'=>$description));

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