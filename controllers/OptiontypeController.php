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

        $seq = 0;
        foreach ($scriptProperties['seq'] as $oterm_id) {
            $OTerm = $this->modx->getObject('OptionTerm', array('oterm_id' => $oterm_id));
            $OTerm->set('seq', $seq);
            $OTerm->save();
            $seq++;
        }

        unset($scriptProperties['seq']);


        $OT = new OptionType($this->modx);
        $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $OT = $OT->find($otype_id);
    
        if (!$OT = $OT->find($otype_id)) {
            return $this->sendFail(array('msg'=>'Parent Option Type not found'));
        }
        
        $records = $OT->indexedToRecordset($scriptProperties);
        $OT->dictateTerms($records);


        return $this->sendSuccess(array(
            'msg' => 'Terms updated successfully'
        ));
        
    }
        
}
/*EOF*/