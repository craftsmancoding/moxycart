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
        $this->addJavascript($assets_url . 'js/jquery.colorbox.js');
        $this->addJavascript($assets_url . 'js/app.js');
        $this->addCss($assets_url.'css/colorbox.css');

        $config = array();
    	$Page = new \Moxycart\BaseController($this->modx, $config); 
        $this->client_config['controller_url'] = $Page->url();
        $this->client_config['store_id'] = (int) (isset($_GET['id'])) ? $_GET['id'] : 0;
        $path = $this->modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
        $html = file_get_contents($path.'views/main/storewelcome.tpl');
    	$this->addHtml('
			<script type="text/javascript">
                console.log("[Moxycart] Loading update.class.php");
                var moxycart = '.json_encode($this->client_config).';
				Ext.onReady(function(){
                    Ext.getCmp("modx-resource-tabs").insert(0, {
                        title: "Products",
                        id: "products-tab",
                        width: "95%",
                         html: '.json_encode(utf8_encode("$html")).'
                    });
                    Ext.getCmp("modx-resource-tabs").insert(1, {
                        title: "Defaults",
                        id: "store-settings-tab",
                        width: "95%",
                        html: "<div id=\"store_settings\"></div>"
                    });
                    get_store_settings();
                    Ext.getCmp("modx-resource-tabs").setActiveTab("products-tab");
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