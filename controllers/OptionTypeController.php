<?php
/**
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart 
 */
namespace Moxycart;
class OptionTypeController extends APIController {

    public $model = 'OptionType'; 
        
 
    public function postTerms(array $scriptProperties = array()) {    

        $OT = new OptionType($this->modx);
        $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $OT = $OT->find($otype_id);
    
        if (!$OT = $OT->find($otype_id)) {
            return $this->fail(array('msg'=>'Parent Option Type not found'));
        }
        
        $records = $OT->indexedToRecordset($scriptProperties);
        $OT->dictateTerms($records);

        return $this->success(array(
            'msg' => 'Terms updated successfully'
        ));
        
    }
        
}
/*EOF*/