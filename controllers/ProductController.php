<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController.
 */
namespace Moxycart;
//use Moxycart\Model;

require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class ProductController extends BaseController {
    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false; // GFD... this can't be set at runtime.


    /**
    * Load TinyMCE
    * Add modx-richtext class on textarea
    * @param
    * @return
    **/
    private function _load_tinyMCE() 
    {
        $_REQUEST['a'] = '';  /* fixes E_NOTICE bug in TinyMCE */

        $plugin= $this->modx->getObject('modPlugin',array('name'=>'TinyMCE'));

        // Plugin not present.
        if (!$plugin) {
            return '';
        }

        $tinyPath =  $this->modx->getOption('core_path').'components/tinymce/';
        $tinyUrl =  $this->modx->getOption('assets_url').'components/tinymce/';
        
        $tinyproperties = $plugin->getProperties();
        require_once $tinyPath.'tinymce.class.php';
        $tiny = new TinyMCE( $this->modx, $tinyproperties);

        //$tinyproperties['language'] =  $modx->getOption('fe_editor_lang',array(),$language);
        $tinyproperties['frontend'] = true;
        $tinyproperties['cleanup'] = true; /* prevents "bogus" bug */
        $tinyproperties['width'] = empty ( $props['tinywidth'] )? '95%' :  $props['tinywidth'];
        $tinyproperties['height'] = empty ( $props['tinyheight'])? '400px' :  $props['tinyheight'];
       //$tinyproperties['resource'] =  $resource;
        $tiny->setProperties($tinyproperties);
        $tiny->initialize();

         $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            delete Tiny.config.setup; // remove manager specific initialization code (depending on ModExt)
            Ext.onReady(function() {
                MODx.loadRTE();
            });
        </script>');
    }
    
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function getIndex(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        if(!$store_id = $this->modx->getOption('store_id',$scriptProperties)) {
            unset($scriptProperties['store_id']);
        }
        if(!$store_id = $this->modx->getOption('parent_id',$scriptProperties)) {
            unset($scriptProperties['parent_id']);
        }
        $Obj = new Product($this->modx);
        $results = $Obj->all($scriptProperties);
        
        //return '<pre>'.print_r($scriptProperties,true).'</pre>';
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('product','index');
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholder('results', $results);
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
        $Obj = new Product($this->modx);    
        if (!$result = $Obj->find($product_id)) {
            return $this->sendError('Page not found.');
        }
        
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        $this->setPlaceholder('pagetitle','Edit Product');
        $this->setPlaceholder('product_form_action','product_update');
        return $this->fetchTemplate('product/edit.php');
    }

/**
     * Post data here to save it.  Data should be in the following format:
     *
     * keys in $_POST should match *exactly* column names in products table, e.g.
     *  name
     *  qty_inventory
     *  sale_end 
     *  ... etc..
     *
     * Related data should be stored in the following arrays:
     *
     *  relations   = key/value where key is product_id, value is type
     *  taxonomies  = array(1,2,3)  a simple array of taxonomy_id's
     *  terms       = array(4,5,6)  a simple array of term_id's
     *  fields       = array(            An array of key/value pairs: keys=field_ids, values=values for that field
     *                  array(7 => "Value1"), 
     *                  array(8 => "Value2")
     *                )
     *  assets      = array(2,4,8)  a simple array of asset_ids
     *
     * Finally, an "action" parameter should be passed to indicate whether this function should
     * create, update, or delete a product record.
     *
     */
    public function postEdit(array $scriptProperties = array()) {
        $product_id = (int) $this->modx->getOption('product_id',$scriptProperties);
        $out = array(
            'success' => true,
            'msg' => 'Product Created Successfully',
            'product_id' => $product_id
        );
        return json_encode($out);
        return '<pre>'.print_r($scriptProperties,true).'</pre>';
    }
    
    /**
     * 
     */
    public function getCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->addStandardLayout();
        $Obj = new Product($this->modx);    
        $Obj->store_id = (int) $this->modx->getOption('store_id',$scriptProperties);
        $Obj->inheritFromStore($Obj->store_id);
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($Obj->toArray());
        $this->setPlaceholder('pagetitle','Create New Product');
        $this->setPlaceholder('product_form_action','product_create');        
        $this->setPlaceholder('result',$Obj);
        return $this->fetchTemplate('product/edit.php');
    }

    public function postCreate(array $scriptProperties = array()) {
        $product_id = '';
        $out = array(
            'success' => true,
            'msg' => 'Product Created Successfully',
            'product_id' => $product_id
        );
        return json_encode($out);
        //return '<pre>'.print_r($scriptProperties,true).'</pre>';
    }

    
    /**
     * Basically take a product ID (product_id) and forward 
     *
     */
    public function getPreview(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $product_id = (int) $this->modx->getOption('product_id', $scriptProperties);
        $Obj = new Product($this->modx);    
        if (!$result = $Obj->find($product_id)) {
            return $this->sendError('Page not found.');
        }
        header('Location: '.MODX_SITE_URL . $result->get('uri'));
        exit;        
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