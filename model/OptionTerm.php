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
     * Validate the otype_id
     *
     */
    public function save() {
        $result = true;
        if (!$Otype = $this->modx->getObject('OptionType', $this->get('otype_id'))) {
            $this->errors['otype_id'] = 'Invalid Option Type';
            $result = false;
        }
        
        if (!$result) {
            return false;
        }
        
        return parent::save();
    }

}
/*EOF*/