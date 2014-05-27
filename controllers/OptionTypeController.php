<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController (see index.class.php)
 */
namespace Moxycart;

require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class OptionTypeController extends BaseController {
    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false; // GFD... this can't be set at runtime.
    
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function getIndex(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->addStandardLayout();
        $Obj = new OptionType($this->modx);
        $results = $Obj->all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('optiontype','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('optiontype/index.php');
    }

    /**
     * Remember we have to set up the manager container
     *
     */
    public function getEdit(array $scriptProperties = array()) {    
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->addStandardLayout();
        $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $Obj = new OptionType($this->modx);    
        if (!$result = $Obj->find($otype_id)) {
            return $this->sendError('Page not found.');
        }
        $scriptProperties['baseurl'] = self::url('optiontype','edit',array('otype_id'=>$otype_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('optiontype/edit.php');
    }

    /**
     * 
     *
     *
     */
    public function postEdit(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $Obj = new OptionType($this->modx);    
        if (!$result = $Obj::find($otype_id)) {
            return $this->sendError('Page not found.');
        }
        $result->fromArray($scriptProperties);
        if (!$result->save()) {
            return $this->sendError('There was a problem saving.');
        }
        $this->setMsg('OptionType saved.','success');
        return $this->getIndex(array());
    }

    /**
     * Manage OptionTerms for this OptionType
     *
     */
    public function getTerms(array $scriptProperties = array()) {    
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->addStandardLayout();
        $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $Obj = new OptionType($this->modx);    
        if (!$result = $Obj->find($otype_id)) {
            return $this->sendError('Invalid option type');
        }
        $Terms = new OptionTerm($this->modx);
        $Terms = $Terms->all(array('otype_id'=>$otype_id,'sort'=>'seq'));
        $scriptProperties['baseurl'] = self::url('optiontype','terms',array('otype_id'=>$otype_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        $this->setPlaceholder('terms', $Terms);
        return $this->fetchTemplate('optiontype/terms.php');
    }

    public function postTerms(array $scriptProperties = array()) {    
//        return '<pre>' .print_r($scriptProperties,true).'</pre>';
        $OT = new OptionType($this->modx);
        $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $OT = $OT->find($otype_id);
        $records = $OT->indexedToRecordset($scriptProperties);
        $OT->dictateTerms($records);
/*
        $cnt = count($scriptProperties['otype_id']);
        for ( $i= 0; $i <= $cnt; $i++ ) {
//            return 'avast:'.$scriptProperties['slug'][$i];
            $T = new OptionTerm($this->modx);
            if ($oterm_id = $scriptProperties['oterm_id'][$i]) {
//                return 'loading existing:'.$oterm_id;
                $T = $T->find($oterm_id);
            }
//            return 'New.';
            $T->otype_id = $scriptProperties['otype_id'][$i];
            $T->slug = $scriptProperties['slug'][$i];
            $T->name = $scriptProperties['name'][$i];
            $T->mod_price = $scriptProperties['mod_price'][$i];
            $T->mod_weight = $scriptProperties['mod_weight'][$i];
            $T->mod_code = $scriptProperties['mod_code'][$i];
            $T->mod_category = $scriptProperties['mod_category'][$i];
            $T->seq = $i;
//return '<pre>'.print_r($T->toArray(),true);            
            if ($T->slug) {
                if (!$T->save()) {
                    return '<pre>'.print_r($T->errors,true).'</pre>';
                }
            }
        }
*/
        $this->setMsg('Terms updated.','success');
        return $this->getTerms(array('otype_id'=>$scriptProperties['otype_id']));
        
    }
    /**
     * 
     *
     *
     */
    public function getCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->addStandardLayout();
        $Obj = new OptionType($this->modx);    

        $scriptProperties['baseurl'] = self::url('optiontype','create');
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($Obj->toArray());
        $this->setPlaceholder('result',$Obj);
        return $this->fetchTemplate('optiontype/create.php');
    }

    /**
     * 
     *
     *
     */
    public function postCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));

        $Obj = new OptionType($this->modx);    
        $Obj->fromArray($scriptProperties);
        if (!$Obj->save()) {
            return $this->sendError('Error Saving.');        
        }
        $this->setMsg('Option Type Created.','success');
        return $this->getIndex(array());
    }
        
}
/*EOF*/