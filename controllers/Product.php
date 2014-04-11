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
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $Obj = new \Moxycart\Model\Product($this->modx);
        $results = $Obj::all($scriptProperties);
        $scriptProperties['count'] = $Obj::count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('product','index');        
        
        // TODO: system setting for this or parent setting
        $this->setPlaceholder('results', $results);
        $this->setPlaceholder('pagination_links', $this->paginationLinks($scriptProperties));
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('product/index.php');
    }
    
    /**
     *
     *
     */
    public function getEdit(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->setPlaceholders($scriptProperties);
        $Obj = new \Moxycart\Model\Product($this->modx);    
        $results = $Obj::find();
        return $this->fetchTemplate('product/edit.php');
    }

    /**
     *
     *
     */
    public function getPreview(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        return 'Preview!!';
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