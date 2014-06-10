<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/create.class.php';

class StoreCreateManagerController extends ResourceCreateManagerController {

    public $resource;

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
				isProductContainerCreate = true;
				Ext.onReady(function(){
					renderProductContainer(isProductContainerCreate, MODx.config);
					get_data(0);
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