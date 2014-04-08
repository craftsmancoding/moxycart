<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController.
 */
class MoxycartProductsManagerController extends MoxycartManagerController {
    /** @var bool Set to false to prevent loading of the header HTML. */
    public $loadHeader = false;
    /** @var bool Set to false to prevent loading of the footer HTML. */
    public $loadFooter = false;
    /** @var bool Set to false to prevent loading of the base MODExt JS classes. */
    public $loadBaseJavascript = false;
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function process(array $scriptProperties = array()) {

        $limit = (int) $this->modx->getOption('limit',$scriptProperties,$this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page')));
        $start = (int) $this->modx->getOption('start',$scriptProperties,0);
        $sort = $this->modx->getOption('sort',$scriptProperties,'name');
        $dir = $this->modx->getOption('dir',$scriptProperties,'ASC');


        $scriptProperties = $this->reduce($scriptProperties);        
        $this->setPlaceholders($scriptProperties);

//    return $Product->all($scriptProperties);
/*
        $this->setPlaceholder('count', $Product->all($scriptProperties,true));
        $this->setPlaceholder('results', $Product->all($scriptProperties));

        return $this->fetchTemplate('product/list.php');
*/
        
        
        $criteria = $this->modx->newQuery('Product');
        if ($store_id) {
            $criteria->where($scriptProperties);
        }
        
        $this->setPlaceholder('count', $this->modx->getCount('Product',$criteria));
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        // Both array and string input seem to work
        // TODO: config to let user define which columns to select
        $criteria->select(array('product_id','name','description','type','sku'));
        $this->setPlaceholder('results', $this->modx->getCollection('Product',$criteria));
        
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