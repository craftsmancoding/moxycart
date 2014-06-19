<?php
/**
 * OptionTerm
 *
 * Terms for a given OptionTerm, e.g. Red, Blue, Green
 */
namespace Moxycart;
class OptionTerm extends BaseModel {

    public $xclass = 'OptionTerm';
    public $default_sort_col = 'name';
    
    /**
     * Validate the option_id
     *
     */
    public function save() {
        $result = true;
        if (!$Otype = $this->modx->getObject('Option', $this->get('option_id'))) {
            $this->errors['option_id'] = 'Invalid Option Type';
            $result = false;
        }
        
        if (!$result) {
            return false;
        }
        
        return parent::save();
    }

    public static function types() {
        return array(':'=>':','+'=>'+','-'=>'-');
    }
}
/*EOF*/