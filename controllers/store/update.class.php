<?php
// MODX doesn't seem to load this correctly via plugin all the time...
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/update.class.php';

class StoreUpdateManagerController extends ResourceUpdateManagerController {

    public $resource;
    public $client_config = array();
    
    /**
     * Due to Ext JS, we must add our custom HTML via Javascript.
     *
     */
    public function loadCustomCssJs() {

        parent::loadCustomCssJs(); // load up the parent resource stuff we're trying to augment
        
        $assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');

		//Add below for customization
        $this->addJavascript($assets_url . 'js/store.js');
        $this->addJavascript($assets_url.'js/jquery.min.js');
        $this->addJavascript($assets_url.'js/jquery-ui.js');
        $this->addJavascript($assets_url . 'js/jquery.colorbox.js');
        $this->addJavascript($assets_url . 'js/app.js');
        $this->addCss($assets_url.'css/colorbox.css');

        $config = array();
    	$Page = new \Moxycart\BaseController($this->modx, $config); 
        $this->client_config['controller_url'] = $Page->url();
        $this->client_config['store_id'] = (int) (isset($_GET['id'])) ? $_GET['id'] : 0;
        
    	$this->addHtml('
			<script type="text/javascript">
                console.log("[Moxycart] Loading update.class.php");
                var moxycart = '.json_encode($this->client_config).';
				Ext.onReady(function(){
                    Ext.getCmp("modx-resource-tabs").insert(0, {
                        title: "Products",
                        id: "products-tab",
                        width: "95%",
                        html: "<div id=\"store_products\" style=\"padding:20px;\"></div>"
                    });
                    Ext.getCmp("modx-resource-tabs").insert(1, {
                        title: "Defaults",
                        id: "store-settings-tab",
                        width: "95%",
                        html: "<div id=\"store_settings\"></div>"
                    });
                    show_all_products();
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