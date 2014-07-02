<?php
/**
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart 
 */
namespace Moxycart;
class OptionController extends APIController {

    public $model = 'Option'; 
        
 
    public function postTerms(array $scriptProperties = array()) {    
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, print_r($scriptProperties,true),'','Moxycart OptionController:'.__FUNCTION__);
        $OT = new Option($this->modx);
/*
        $data = $OT->indexedToRecordset($scriptProperties);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, print_r($data,true),'','Moxycart OptionController:'.__FUNCTION__);
        $seq = 0;
        foreach ($scriptProperties['seq'] as $oterm_id) {
            $OTerm = $this->modx->getObject('OptionTerm', array('oterm_id' => $oterm_id));
            $OTerm->set('seq', $seq);
            $OTerm->save();
            $seq++;
        }

        unset($scriptProperties['seq']);
*/


        
        $option_id = (int) $this->modx->getOption('option_id',$scriptProperties);
        $OT = $OT->find($option_id);
    
        if (!$OT = $OT->find($option_id)) {
            return $this->sendFail(array('msg'=>'Parent Option not found'));
        }
        
        $records = $OT->indexedToRecordset($scriptProperties);
        $OT->dictateTerms($records);


        return $this->sendSuccess(array(
            'msg' => 'Terms updated successfully'
        ));
        
    }
        
}
/*EOF*/