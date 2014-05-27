<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController (see index.class.php)
 */
namespace Moxycart;

require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class OptionTermController extends BaseController {
    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false; // GFD... this can't be set at runtime.
    
    /**
     * @param array $scriptProperties
     */
    public function getCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        // $this->addStandardLayout(); // Ajax!
        // $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $Obj = new OptionTerm($this->modx);

        $scriptProperties = $Obj->toArray();
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        //$scriptProperties['baseurl'] = self::url('optiontype','index');
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('optionterm/single.php');
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
        
}
/*EOF*/