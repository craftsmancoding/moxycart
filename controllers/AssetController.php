<?php
/**
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart
 */
namespace Moxycart;
class AssetController extends APIController {

    public $model = 'Asset';

    /**
     * $_FILES
     *
        Array
        (
            [file] => Array
                (
                    [name] => ext_js_firebug.jpg
                    [type] => image/jpeg
                    [tmp_name] => /Applications/MAMP/tmp/php/phpNpESmV
                    [error] => 0
                    [size] => 81367
                )
        
        )     
     */
    public function postCreate(array $scriptProperties = array()) {
        $this->modx->setLogLevel(4);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API $_FILES: '.print_r($_FILES,true),'',__CLASS__,__FUNCTION__,__LINE__);
        $fieldname = $this->modx->getOption('fieldname', $scriptProperties,'file');
        $page_id = $this->modx->getOption('page_id', $scriptProperties); // Optionally associate it with a product

        // Error checking
        if (empty($_FILES)) {
            return $this->sendFail(array('errors'=> 'No FILE data detected.'));
        }
        if (!isset($_FILES[$fieldname])){
            return $this->sendFail(array('errors'=> 'FILE data empty for field: '.$fieldname));
        }
        if (!empty($_FILES[$fieldname]['error'])) {
            return $this->sendFail(array('errors'=> 'Error uploading file: '.$_FILES[$filename]['error']));
        }        
        
        try {
            $Model = new \Assman\Asset($this->modx);    
            $Asset = $Model->fromFile($_FILES[$fieldname]);
        }
        catch (\Exception $e) {
            return $this->sendFail(array('msg'=> $e->getMessage()));    
        }  
        
        if (!$Asset->save()) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Error saving Asset '.print_r($_FILES[$fieldname],true).' '.print_r($Model->errors,true),'',__CLASS__,__FUNCTION__,__LINE__);
            return $this->sendFail(array('errors'=> $Model->errors));
        }            
        return $this->sendSuccess(array(
            'msg' => sprintf('%s created successfully.',$this->model),
            'class' => $this->model,
            'fields' => $Asset->toArray()
        ));
    }

    /**
     *
     */
    public function postEdit(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG,'API: '.print_r($scriptProperties,true),'',__CLASS__,__FUNCTION__,__LINE__);
        // This doesn't work unless you add the namespace.
        // Oddly, if you write it out (w/o a var), it works. wtf?
        $classname = '\\Assman\\Asset';
        $Model = new $classname($this->modx);    

        $id = (int) $this->modx->getOption($Model->getPK(),$scriptProperties);

        if (!$Obj = $Model->find($id)) {
            return $this->sendFail(array('msg'=>sprintf('%s not found', $this->model)));
        }
        $Obj->fromArray($scriptProperties);
        if (!$Obj->save()) {
            return $this->fail(array('errors'=> $Obj->errors));
        }
        return $this->sendSuccess(array(
            'msg' => sprintf('%s updated successfully.',$this->model)
        ));
    }

}
/*EOF*/