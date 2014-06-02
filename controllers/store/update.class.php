<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/update.class.php';

class StoreUpdateManagerController extends ResourceUpdateManagerController {

    public $resource;

    public function loadCustomCssJs() {

        // return parent::loadCustomCssJs(); // uncomment to turn off all customizations 
        parent::loadCustomCssJs(); // load up the parent resource stuff we're trying to augment
        
        $mgr_url = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');
        //print_r($this->config); exit;
		//Add below for customization
        //$this->addJavascript($assets_url . 'js/productcontainer.js');
        $this->addJavascript($assets_url . 'js/store.js');
        $store_id = (int) (isset($_GET['id'])) ? $_GET['id'] : 0;
    	$B = new \Moxycart\BaseController($this->modx); 
    	$moxycart_connector_url = $B->url();

    	$this->addHtml('
			<script type="text/javascript">
                console.log("[Moxycart] Loading update.class.php");
                /*
                @param integer offset
                @param string sort column name
                @param string dir ASC|DESC 
                */
                function get_data(offset,sort,dir) {
                    sort = typeof sort !== "undefined" ? sort : "name";
                    dir = typeof dir !== "undefined" ? dir : "ASC";
                    var url = connector_url+"&class=page&method=storeproducts&offset="+offset+"&sort="+sort+"&dir="+dir+"&store_id='.$store_id.'";
                    console.log("[Moxycart get_data()] requesting URL",url);
					Ext.Ajax.request({
                        url: url,
                        params: {},
                        async:false,
                        success: function(response){
                            console.log("Success: Data received from "+url);
                            Ext.fly("store_products").update(response.responseText);
                        },
                        failure: function(response){
                            console.error("The request to "+url+" failed.", response);
                        }
                    });                
                }
                var connector_url = "'.$moxycart_connector_url.'&_nolayout=1";
                var site_url = "'.MODX_SITE_URL.'";
				isProductContainerCreate = false;
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