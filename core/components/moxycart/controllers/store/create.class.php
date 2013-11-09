<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/create.class.php';

class StoreCreateManagerController extends ResourceCreateManagerController {

    public $resource;

    public function loadCustomCssJs() {

        parent::loadCustomCssJs();
        
        $mgr_url = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        
		//Add below for customization
        $this->addJavascript($assets_url . 'components/moxycart/js/productcontainer.js');
        $this->addHtml('
			<script type="text/javascript">
				isProductContainerCreate = true;
				
				Ext.onReady(function(){
					renderProductContainer(isProductContainerCreate, MODx.config);
				    MODx.load({
                        xtype: "articles-page-articles-container-create"
                        ,record: '.json_encode($this->resource->getProperties('moxycart')).'
                    });

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
        $this->modx->log(1,print_r($settings,true));
        if (empty($settings)) $settings = array();
        foreach ($settings as $k => $v) {
            $this->resourceArray[$k] = $v;
        }
    }    

}