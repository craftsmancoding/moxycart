<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/create.class.php';

class StoreCreateManagerController extends ResourceCreateManagerController {

    public $resource;

    public function loadCustomCssJs() {

        parent::loadCustomCssJs();

        // Req'd for dev overrides
        $assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');
        
		//Add below for customization
        $this->addJavascript($assets_url . 'js/productcontainer.js');
    	$moxycart_connector_url = $assets_url.'connector.php?f=';
    	$this->addHtml('
			<script type="text/javascript">
                var connector_url = "'.$moxycart_connector_url.'";
                var site_url = "'.MODX_SITE_URL.'";                
				isProductContainerCreate = true;
				Ext.onReady(function(){
					MODx.activePage.config.record = MODx.activePage.config.record || {};
					MODx.activePage.config.record.properties = MODx.activePage.config.record.properties || {};
					MODx.activePage.config.record.properties.moxycart = '.json_encode($this->resource->getProperties('moxycart')).'
					renderProductContainer(isProductContainerCreate, MODx.config);

				});
			</script>');
			
        $this->addCss($assets_url.'css/mgr.css');
    }    
    
    public function getLanguageTopics() {
        return array('resource','moxycart:default');
    }
    /**
     * Return the pagetitle in the <title>
     *
     * @return string
     */
    public function getPageTitle() {
        return $this->modx->lexicon('container_new');
    }
    /**
     * Used to set values on the resource record sent to the template for derivative classes
     *
     * @return void
     */
    public function prepareResource() {
        $settings = $this->resource->get('properties');
        //$this->modx->log(1,print_r($settings,true));
        if (empty($settings)) $settings = array();
        foreach ($settings as $k => $v) {
            $this->resourceArray[$k] = $v;
        }
    }    

}