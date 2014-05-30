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
        $product_id = $this->modx->getOption('product_id', $scriptProperties); // Optionally associate it with a product

        if (empty($_FILES)) {
            return $this->sendFail(array('errors'=> 'No FILE data detected.'));
        }
        if (!isset($_FILES[$fieldname])){
            return $this->sendFail(array('errors'=> 'FILE data empty for field: '.$fieldname));
        }
        if (!empty($_FILES[$fieldname]['error'])) {
            return $this->sendFail(array('errors'=> 'Error uploading file: '.$_FILES[$filename]['error']));
        }        
        $storage_basedir = MODX_ASSETS_PATH . $this->modx->getOption('moxycart.upload_dir');
        
//        try {
            $Model = new Asset($this->modx);    
            $Asset = $Model->fromFile($_FILES[$fieldname],$storage_basedir);
    
            if (!$Asset->save()) {
                return $this->sendFail(array('errors'=> $Model->errors));
            }
            if ($product_id) {
                $PA = $this->modx->newObject('ProductAsset',array('product_id'=>$product_id,'asset_id'=>$Asset->getPrimaryKey()));
                $PA->save();
            }
            
            return $this->sendSuccess(array(
                'msg' => sprintf('%s created successfully.',$this->model),
                'class' => $this->model,
                'fields' => $Asset->toArray()
            ));
//        }
         // oddly, trapping exceptions here winds us up on the MODX error pages
//        catch (\Exception $e) {
//            $this->sendError($e->getMessage(),'Exception');
//        }
    }

}
/*EOF*/