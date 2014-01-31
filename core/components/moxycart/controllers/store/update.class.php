<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/update.class.php';

class StoreUpdateManagerController extends ResourceUpdateManagerController {

    public $resource;

    public function loadCustomCssJs() {

        parent::loadCustomCssJs();
        
        $mgr_url = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        
		//Add below for customization
        $this->addJavascript($assets_url . 'components/moxycart/js/productcontainer.js');
    	$moxycart_connector_url = $assets_url.'components/moxycart/connector.php?f=';
    	$this->addHtml('
			<script type="text/javascript">
                var connector_url = "'.$moxycart_connector_url.'";
                var site_url = "'.MODX_SITE_URL.'";
				isProductContainerCreate = false;
				Ext.onReady(function(){
					renderProductContainer(isProductContainerCreate, MODx.config);
				});
			</script>');
			
        $this->addCss($assets_url.'components/moxycart/css/mgr.css');

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