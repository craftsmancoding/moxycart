<?php
/**
 * OptionType
 *
 * E.g. Color, Size, Material -- any container for VariationTerms
 */
namespace Moxycart;
class OptionType extends BaseModel {

    public $xclass = 'OptionType';
    public $default_sort_col = 'name';


    /**
     * Must test for reserved Words!
     *
     */
    public function save() {
        $result = true; 

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