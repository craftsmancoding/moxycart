<?php
/**
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart 
 */
namespace Moxycart;
class ProductassetController extends APIController {

    public $model = 'ProductAsset'; 
        
 
    /** 
     * We need a product ID here and an asset_id
     */
    public function postDelete(array $scriptProperties = array()) {    
//        $this->modx->setLogLevel(4);
//        $assman_core_path = $this->modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
//        require_once $assman_core_path.'vendor/autoload.php';
        
        $product_id = (int) $this->modx->getOption('product_id', $scriptProperties);
        $asset_id = (int) $this->modx->getOption('asset_id', $scriptProperties);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, print_r($scriptProperties,true),'','Moxycart OptionController:'.__FUNCTION__);
        
        // We must delete these separately because the relationship is not defined 
        if (!$PA = $this->modx->getObject('ProductAsset', array('product_id'=> $product_id, 'asset_id'=>$asset_id))) {
            return $this->sendFail(array('msg'=>'Record not found for product_id '.$product_id .' and asset_id '.$asset_id));
        }
        $PA->remove();

        if (!$Asset = $this->modx->getObject('Asset', $asset_id)) {
            return $this->sendFail(array('msg'=>'Record not found for asset_id '.$asset_id));
        }
        $Asset->remove();

        return $this->sendSuccess(array(
            'msg' => 'Asset Deleted successfully'
        ));
        
    }
        
}
/*EOF*/