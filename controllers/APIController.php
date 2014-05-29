<?php
/**
 * Handles JSON API within MODX manager. Responses follow Jsend suggested format:
 *
 * http://labs.omniti.com/labs/jsend
 *
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart 
 */
namespace Moxycart;
require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class APIController extends \modExtraManagerController {

    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false;
    public $templatesPaths = array();      
    public $model;


    /**
     * Catch all for bad function requests -- our 404
     */
    public function __call($name,$args) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'[moxycart] Invalid function name '.$name);
        header('HTTP/1.0 404 Not Found');
        return $this->error('Invalid API method: '.$name);
    }
        
    /** 
     * Send JSON Success response
     * @param mixed $data you want to return, e.g. a record or recordset
     */
    public function success($data) {
        $out = array(
            'status' => 'success',
            'data' => $data,
        );
        return json_encode($out);
    }
    
    /** 
     * Send JSON fail response
     * @param mixed $data you want to return, e.g. a record or recordset
     */    
    public function fail($data) {
        $out = array(
            'status' => 'fail',
            'data' => $data,
        );
        return json_encode($out);    
    }
    
    /**
     * Send JSON error response. More serious than a fail, e.g. 
     * When an exception is throw.
     */
    public function error($message,$code=null, $data=null) {
        $out = array(
            'status' => 'error',
            'message' => $message,
        );
        if ($code) $out['code'] = $code;
        if ($data) $out['data'] = $data;
        return json_encode($out);    
    }

    /**
     * 
     */
    public function postCreate(array $scriptProperties = array()) {
        $classname = '\\Moxycart\\'.$this->model;
        $Model = new $classname($this->modx);    
        $Model->fromArray($scriptProperties);
        if (!$Model->save()) {
            return $this->fail(array('errors'=> $Model->errors));
        }
        return $this->success(array(
            'msg' => sprintf('%s created successfully.',$this->model)
        ));
    }

    /**
     * 
     */
    public function postDelete(array $scriptProperties = array()) {
        $classname = '\\Moxycart\\'.$this->model;
        $Model = new $classname($this->modx);    
        $id = (int) $this->modx->getOption($Model->getPK(),$scriptProperties);

        if (!$Obj = $Model->find($id)) {
            return $this->fail(array('msg'=>sprintf('%s not found', $this->model)));
        }

        if (!$Obj->remove()) {
            return $this->fail(array('errors'=> $Model->errors));
        }
        return $this->success(array(
            'msg' => sprintf('%s deleted successfully.',$this->model)
        ));
    }

    /**
     *
     */
    public function postEdit(array $scriptProperties = array()) {
        // This doesn't work unless you add the namespace.
        // Oddly, if you write it out (w/o a var), it works. wtf?
        $classname = '\\Moxycart\\'.$this->model;
        $Model = new $classname($this->modx);    

        $id = (int) $this->modx->getOption($Model->getPK(),$scriptProperties);

        if (!$Obj = $Model->find($id)) {
            return $this->fail(array('msg'=>sprintf('%s not found', $this->model)));
        }
        $Obj->fromArray($scriptProperties);
        if (!$Obj->save()) {
            return $this->fail(array('errors'=> $Obj->errors));
        }
        return $this->success(array(
            'msg' => sprintf('%s updated successfully.',$this->model)
        ));
    }





    /**
     * This is what ultimately responds to a manager request and send a JSON response
     *
     * We override this so we can route to functions other than the simple "process"
     * 
     * There are 2 class vars important here:
     *
     *      $this->scriptProperties : contains all request data
     *      $this->config : set in our constructor. Contains "method"
     *
     * @return string
     */
    public function render() {
        if (!$this->checkPermissions()) {
            return $this->modx->error->failure($this->modx->lexicon('access_denied'));
        }

        // This routing comes from the index.class.php
        $method = $this->config['method'];
        $props = $this->scriptProperties;
        unset($props['a']);
        unset($props['class']);
        unset($props['method']);
        return $this->$method($props);
    }


}
/*EOF*/