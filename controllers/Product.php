<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController.
 */
namespace Moxycart\Controller;
require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class Product extends Base {
    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false;
    
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function getIndex(array $scriptProperties = array()) {

        $this->setPlaceholders($scriptProperties);
        $P = new \Moxycart\Model\Product($this->modx);
        $results = $P::all($scriptProperties);
        $count = $P::count($scriptProperties);
        // Both array and string input seem to work
        // TODO: config to let user define which columns to select
        //$criteria->select(array('product_id','name','description','type','sku'));
        $this->setPlaceholder('results', $results);
        $this->setPlaceholder('count', $count);
        return $this->fetchTemplate('product/list.php');
    }
    /**
     * The pagetitle to put in the <title> attribute.
     * @return null|string
     */
    public function getPageTitle() {
        return 'Products';
    }
    /**
     * Register needed assets. Using this method, it will automagically
     * combine and compress them if that is enabled in system settings.
     */
    public function loadCustomCssJs() {
/*
        $this->addCss('url/to/some/css_file.css');
        $this->addJavascript('url/to/some/javascript.js');
        $this->addLastJavascript('url/to/some/javascript_load_last.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            // We could run some javascript here
        });
        </script>');
*/
    }
    
    /**
     * Controls what is sent to the fetchTemplate function
     */
/*
    public function getTemplateFile() {
        return 'product/list.php';
    }
*/
    

        
}
/*EOF*/