<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController.
 */
namespace Moxycart\Controller;
//use Moxycart\Model;

require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class Product extends Base {
    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false; // GFD... this can't be set at runtime.
    
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function getIndex(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $Obj = new \Moxycart\Model\Product($this->modx);
        $results = $Obj::all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj::count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('product','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('product/index.php');
    }

    /**
     * Remember we have to set up the manager container
     *
     */
    public function getEdit(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->addStandardLayout();
        $product_id = (int) $this->modx->getOption('product_id',$scriptProperties);
        $Obj = new \Moxycart\Model\Product($this->modx);    
        if (!$result = $Obj::find($product_id)) {
            return $this->sendError('Page not found.');
        }
        
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('product/edit.php');
//        return $this->fetchTemplate('product/test.php');
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