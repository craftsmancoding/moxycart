<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/update.class.php';

class StoreUpdateManagerController extends ResourceUpdateManagerController {

    public $resource;
    public $client_config = array();
    
    public function loadCustomCssJs() {

        // return parent::loadCustomCssJs(); // uncomment to turn off all customizations 
        parent::loadCustomCssJs(); // load up the parent resource stuff we're trying to augment
        
        $assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');

		//Add below for customization
        $this->addJavascript($assets_url . 'js/store.js');
        $this->addJavascript($assets_url.'js/jquery.min.js');
        $this->addJavascript($assets_url . 'js/app.js');
        
    	$B = new \Moxycart\BaseController($this->modx); 
        $this->client_config['connector_url'] = $B->url();
        $this->client_config['store_id'] = (int) (isset($_GET['id'])) ? $_GET['id'] : 0;
        
    	$this->addHtml('
			<script type="text/javascript">
                console.log("[Moxycart] Loading update.class.php");
                var moxycart = '.json_encode($this->client_config).';
				isProductContainerCreate = false;
				Ext.onReady(function(){
					renderProductContainer(isProductContainerCreate, MODx.config);
					get_products(0);
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

    public function getPageTitle() {
        return $this->modx->lexicon('container_update');
    }
    
    /**
     * Used to set values on the resource record sent to the template for derivative classes (wtf?)
     * We're doing this in the store model instead...
     * @return void
     */
    public function prepareResource() {

    }    
}