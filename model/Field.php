<?php
/**
 * Field
 * Defines custom field types for products
 */
namespace Moxycart;
class Field extends BaseModel {

    public $xclass = 'Field';
    public $default_sort_col = 'slug';

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