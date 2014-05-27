<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController (see index.class.php)
 */
namespace Moxycart;

require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class FieldController extends BaseController {
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
        $Obj = new Field($this->modx);
        $results = $Obj::all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj::count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('currency','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('currency/index.php');
    }

    /**
     * Remember we have to set up the manager container
     *
     */
    public function getEdit(array $scriptProperties = array()) {    
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->addStandardLayout();
        $currency_id = (int) $this->modx->getOption('currency_id',$scriptProperties);
        $Obj = new \Moxycart\Model\Currency($this->modx);    
        if (!$result = $Obj::find($currency_id)) {
            return $this->sendError('Page not found.');
        }
        $scriptProperties['baseurl'] = self::url('currency','edit',array('currency_id'=>$currency_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('currency/edit.php');
    }

    /**
     * 
     *
     *
     */
    public function postEdit(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $currency_id = (int) $this->modx->getOption('currency_id',$scriptProperties);
        $Obj = new \Moxycart\Model\Currency($this->modx);    
        if (!$result = $Obj::find($currency_id)) {
            return $this->sendError('Page not found.');
        }
        $result->fromArray($scriptProperties);
        if (!$result->save()) {
            return $this->sendError('There was a problem saving.');
        }
        $this->setMsg('Currency saved.','success');
        return $this->getIndex(array());
    }

    /**
     * 
     *
     *
     */
    public function getCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->addStandardLayout();
        $Obj = new \Moxycart\Model\Currency($this->modx);    
        if (!$result = $Obj::create()) {
            return $this->sendError('Page not found.');
        }
        $scriptProperties['baseurl'] = self::url('currency','create');
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('currency/create.php');
    }

    /**
     * 
     *
     *
     */
    public function postCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));

        $Obj = new \Moxycart\Model\Currency($this->modx);    
        $result = $Obj::create($scriptProperties);
        if (!$result->save()) {
            return $this->sendError('Error Saving.');        
        }
        $this->setMsg('Currency Created.','success');
        return $this->getIndex(array());
    }
        
}
/*EOF*/