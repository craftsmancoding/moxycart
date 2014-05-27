<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController (see index.class.php)
 */
namespace Moxycart;

require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class ReviewController extends BaseController {
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
        $results = $Obj->all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('review','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('review/index.php');
    }

    /**
     * Remember we have to set up the manager container
     *
     */
    public function getEdit(array $scriptProperties = array()) {    
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->addStandardLayout();
        $review_id = (int) $this->modx->getOption('review_id',$scriptProperties);
        $Obj = new Field($this->modx);    
        if (!$result = $Obj->find($review_id)) {
            return $this->sendError('Page not found.');
        }
        $scriptProperties['baseurl'] = self::url('review','edit',array('review_id'=>$review_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('review/edit.php');
    }

    /**
     * 
     *
     *
     */
    public function postEdit(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $review_id = (int) $this->modx->getOption('review_id',$scriptProperties);
        $Obj = new Field($this->modx);    
        if (!$result = $Obj::find($review_id)) {
            return $this->sendError('Page not found.');
        }
        $result->fromArray($scriptProperties);
        if (!$result->save()) {
            return $this->sendError('There was a problem saving.');
        }
        $this->setMsg('Field saved.','success');
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
        $Obj = new Field($this->modx);    

        $scriptProperties['baseurl'] = self::url('review','create');
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($Obj->toArray());
        $this->setPlaceholder('result',$Obj);
        return $this->fetchTemplate('review/create.php');
    }

    /**
     * 
     *
     *
     */
    public function postCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));

        $Obj = new Field($this->modx);    
        $Obj->fromArray($scriptProperties);
        if (!$Obj->save()) {
            return $this->sendError('Error Saving.');        
        }
        $this->setMsg('Field Created.','success');
        return $this->getIndex(array());
    }
        
}
/*EOF*/