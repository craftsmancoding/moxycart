<?php
/**
 * OptionType
 *
 * E.g. Color, Size, Material -- any container for VariationTerms
 */
namespace Moxycart;
class Option extends BaseModel {

    public $xclass = 'Option';
    public $default_sort_col = 'name';     
    public $otype_id;
    public $search_columns = array('slug','name','description'); 

    /**
     * Verify that the existing product is in fact an existing, valid, persisted product
     * and not a new, unsaved product.
     * @return integer product_id
     */
    private function _verifyExisting() {
    
        if (!$this->modelObj->isNew() && !empty($this->otype_id)) {
            return $this->otype_id;
        }

        if(!$this->otype_id = $this->get('otype_id')) {
            throw new \Exception('Option Type ID not defined');
        }
        // Make sure we're not getting jerked around by an un-persisted product_id
        if (!$O = $this->modx->getObject('OptionType', $this->otype_id)) {
            throw new \Exception('Option Type does not exist '.$this->otype_id);    
        }
        
        return $this->otype_id; 
    }

    /**
     * Add and Remove anything not set in the 
     */
    public function dictateTerms(array $records) {
        $otype_id = $this->_verifyExisting();
        $Terms = $this->modx->getCollection('OptionTerm', array('otype_id'=>$otype_id));
 
        $ids = array();
        foreach ($records as $r) {
            $ids[] = $r['oterm_id'];
        }
        foreach ($Terms as $t) {
            if (!in_array($t->get('oterm_id'), $ids)) {
                $t->remove();
            }
        }
        foreach ($records as $r) {
            if ($r['oterm_id']) {
                if (!$OT = $this->modx->getObject('OptionTerm', $r['oterm_id'])) {
                    $OT = $this->modx->newObject('OptionTerm');
                }
            }
            else {
                $OT = $this->modx->newObject('OptionTerm');
            }
            $OT->fromArray($r);
            $OT->set('otype_id',$otype_id);
            $OT->save();
        }
    }
    
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