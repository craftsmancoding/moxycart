<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/update.class.php';

class StoreUpdateManagerController extends ResourceUpdateManagerController {

    public $resource;

    public function loadCustomCssJs() {

        parent::loadCustomCssJs();

        $mgr_url = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');
        
		//Add below for customization
//        $this->addJavascript($assets_url . 'js/productcontainer.js');
        $this->addJavascript($assets_url . 'js/store.js');
        $store_id = (int) (isset($_GET['id'])) ? $_GET['id'] : 0;
    	$moxycart_connector_url = $assets_url.'connector.php?f=';
    	//getControllerUrl()
    	$moxycart_connector_url = '/manager/index.php?a=89&action=products';
    	$this->addHtml('
			<script type="text/javascript">
                var connector_url = "'.$moxycart_connector_url.'";
                var site_url = "'.MODX_SITE_URL.'";
				isProductContainerCreate = false;
				Ext.onReady(function(){
					renderProductContainer(isProductContainerCreate, MODx.config);					
					Ext.Ajax.request({
                        url: connector_url+"&action=products&store_id='.$store_id.'",
                        params: {},
                        async:false,
                        success: function(response){
                            Ext.fly("store_products").update(response.responseText);
                        }
                    });
				});
			</script>');
			
        $this->addCss($assets_url.'css/mgr.css');

    }
        
    public function getLanguageTopics() {
        return array('resource','moxycart:default');
    }
    /**
     * Return the pagetitle
     *
     * @return string
     */
/*
    public function getPageTitle() {
        return $this->modx->lexicon('container_update');
    }
*/
    /**
     * Used to set values on the resource record sent to the template for derivative classes (wtf?)
     * We're doing this in the store model instead...
     * @return void
     */
    public function prepareResource() {

    }    
}