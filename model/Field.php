<?php
/**
 * Field
 * Defines custom field types for products
 */
namespace Moxycart;
class Field extends BaseModel {

    public $xclass = 'Field';
    public $default_sort_col = 'slug';

    public $search_columns = array('slug','label','type','group'); 

    public static function getTypes() {
        return array(
            'text'=>'Text',
            'textarea'=>'Textarea',
            'checkbox'=>'Checkbox',
            'dropdown'=>'Dropdown',
            'asset' => 'Asset (Image)'
            //'multicheck'=>'Multi-Check'
        );    
    }
    
    /**
     * Generate an HTML form for the field requested
     * @param integer $field_id
     * @param string $value the default 
     * @param string $name for the element (= key in post)
     * @return mixed HTML on success, boolen false on fail
     */
    public function generate($field_id,$value='',$name=null) {
        if (!$F = $this->modx->getObject('Field', $field_id)) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Field id not found: '.$field_id,'',__CLASS__,__FUNCTION__,__LINE__);
            return false;
        }
        $type = $F->get('type');
        $attr = $F->toArray();
        
        if (!$name) {
            $name = $attr['slug'];
        }
        $args = json_decode($attr['config'], true);
        switch ($type) {
            case 'dropdown':
                $out = \Formbuilder\Form::dropdown($name,$args,$value, array('label'=>$attr['label'],'description'=>$attr['description']));
                break;
            
            case 'checkbox':
                $args['label'] = $attr['label'];
                $args['description'] = $attr['description'];
                $out = \Formbuilder\Form::checkbox($name,$value, $args);
                $out->__toString(); // <-- force to string!
                break;
            case 'text':
            case 'textarea':
                $args['label'] = $attr['label'];
                $args['description'] = $attr['description'];
                $out = \Formbuilder\Form::$type($name,$value,$args);
                $out->__toString(); // <-- force to string!
                break;
            case 'asset':
                return 'ASDFA';
                $args['label'] = $attr['label'];
                $args['description'] = $attr['description'];
                $out = \Formbuilder\Form::$type($name,$value,$args);
                break;
            default:
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'Unsupported field type: '.$type,'',__CLASS__,__FUNCTION__,__LINE__);  
                return false;
        }
        
        return $out;
    
    }
    
    /**
     * Must test for reserved Words!
     *
     */
    public function save() {
        $result = true; 
        if (!$this->get('slug')) {
            $this->errors['slug'] = 'The slug is required.';
            return false;
        }

        if (in_array($this->get('slug'), $this->reserved_words)) {
            $this->errors['slug'] = 'The slug cannot be a reserved word.';
            $result = false;
        }
        if (!$result) {
            return false;
        }
    
        return parent::save();
    }    

}
/*EOF*/