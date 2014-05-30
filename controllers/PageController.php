<?php
/**
 * This HTML controller is what generates HTML pages (as opposed to JSON responses
 * generated by the other controllers).  The reason is testability: most of the 
 * manager app can be tested by $scriptProperties in, JSON out.  The HTML pages
 * generated by this controller end up being static HTML pages (well... ideally, 
 * anyway). 
 *
 * See http://stackoverflow.com/questions/10941249/separate-rest-json-api-server-and-client
 *
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart
 */
namespace Moxycart;
class PageController extends BaseController {

    public $loadHeader = false;
    public $loadFooter = false;
    // GFD... this can't be set at runtime. See improvised addStandardLayout() function
    public $loadBaseJavascript = false; 
    // Stuff needed for interfacing with Moxycart API (mapi)
    public $client_config = array();
    
    function __construct(\modX &$modx,$config = array()) {
        parent::__construct($modx,$config);
        static::$x =& $modx;
        // Set up any config data needed by the HTML client
        $this->client_config = array(
            'controller_url' => $this->config['controller_url']
        );
        $this->modx->regClientCSS($this->config['assets_url'].'css/moxycart.css');
        $this->modx->regClientCSS('//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-2.0.3.min.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/app.js');
    }

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
        require_once $tinyPath.'/tinymce.class.php';
        $tiny = new \TinyMCE( $this->modx, $tinyproperties);

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

    
    //------------------------------------------------------------------------------
    //! Assets
    //------------------------------------------------------------------------------
    /**
     * Asset management main page
     *
     * @param array $scriptProperties
     */
    public function getAssets(array $scriptProperties = array()) {
        $Obj = new Asset($this->modx);
        $results = $Obj->all($scriptProperties);
//        return $results; exit;
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/assets.php');
    }
 
     public function getAssetCreate(array $scriptProperties = array()) {
        $Obj = new Asset($this->modx);
        $results = $Obj->all($scriptProperties);
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('asset/create.php');
    }    

    public function getAssetEdit(array $scriptProperties = array()) {
        $asset_id = (int) $this->modx->getOption('asset_id',$scriptProperties);
        $Obj = new Asset($this->modx);    
        if (!$result = $Obj->find($asset_id)) {
            return $this->sendError('Page not found.');
        }
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('asset/edit.php');
    }

    //------------------------------------------------------------------------------
    //! Products
    //------------------------------------------------------------------------------
    /**
     *
     * @param array $scriptProperties
     */
    public function getProducts(array $scriptProperties = array()) {
        $Obj = new Product($this->modx);
        $results = $Obj->all($scriptProperties);
        $count = $Obj->count($scriptProperties);
        $offset = (int) $this->modx->getOption($scriptProperties,'offset',0);
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholder('results', $results);
        $this->setPlaceholder('count', $count);
        $this->setPlaceholder('offset', $offset);
        
        return $this->fetchTemplate('main/products.php');
    }
 
     public function getProductCreate(array $scriptProperties = array()) {
        $Obj = new Product($this->modx);
        $results = $Obj->all($scriptProperties);
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('product/create.php');
    }    

    public function getProductEdit(array $scriptProperties = array()) {

        $product_id = (int) $this->modx->getOption('product_id',$scriptProperties);
        $Obj = new Product($this->modx);    
        if (!$result = $Obj->find($product_id)) {
            return $this->sendError('Page not found.');
        }

        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);


        $this->modx->regClientCSS($this->config['assets_url'] . 'css/mgr.css');
        $this->modx->regClientCSS($this->config['assets_url'] . 'css/dropzone.css');
        $this->modx->regClientCSS($this->config['assets_url'].'css/datepicker.css');
        $this->modx->regClientCSS('//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-2.0.3.min.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-ui.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.tabify.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/dropzone.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/bootstrap.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/multisortable.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/script.js');
    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
    		var product = '.$result->toJson().';            
            var use_editor = "'.$this->modx->getOption('use_editor').'";
            var assets_url = "'.$this->config['assets_url'].'"; 
    
            jQuery(document).ready(function() {
                    var myDropzone = new Dropzone("div#image_upload", {url: moxycart.controller_url+"&class=asset&method=create&product_id='.$product_id.'"});
                    
                    // Refresh the list on success (append new tile to end)
                    myDropzone.on("success", function(file,response) {
    
                        console.log(response);
                        response = jQuery.parseJSON(response);
                        console.log(response);
                        if (response.success) {
                           
                            var url = moxycart.controller_url + "&class=asset&method=view&asset_id=" + response.asset_id;
                            jQuery.post( url, function(data){
                                jQuery("#product_images").append(data);
                                jQuery(".dz-preview").remove();
                            });
                       } 
                       // TODO: better formatting
                       else {                           
                            $(".dz-success-mark").hide();
                            $(".dz-error-mark").show();
                            $(".moxy-msg").show();
                            $("#moxy-result").html("Failed");
                            $("#moxy-result-msg").html(response.msg);
                            $(".moxy-msg").delay(3200).fadeOut(400);
                       }
                    });
            });
    		</script>');
        if ($this->modx->getOption('use_editor')) {
            $this->_load_tinyMCE();
        }
        
        $P = new Product($this->modx);
        
        // thumbnail
        
        
        // assets
        
        // product_fields
        $c = $this->modx->newQuery('ProductField');
        $c->where(array('ProductField.product_id' => $product_id));
        $PF = $this->modx->getCollectionGraph('ProductField','{"Field":{}}',array('product_id'=> $product_id));
        $this->setPlaceholder('product_fields',$PF);

        
        // fields (dropdown)
        $Fs = $this->modx->getCollection('Field'); 
        $fields = array();
        foreach ($Fs as $f) {
            $fields[ $f->get('field_id') ] = sprintf('%s (%s)',$f->get('label'),$f->get('slug'));
        }
        $this->setPlaceholder('fields',$fields);
        
        // stores (dropdown)
        $Ss = $this->modx->getCollection('Store',array('class_key'=>'Store'));
        $stores = array();
        foreach ($Ss as $s) {
            $stores[ $s->get('id') ] = sprintf('%s (%s)',$s->get('pagetitle'),$s->get('id'));
        }
        $this->setPlaceholder('stores',$stores);
        
        // related_products (multicheck)
        $c = $this->modx->newQuery('ProductRelation');
        $c->where(array('ProductRelation.product_id' => $product_id));
        $PR = $this->modx->getCollectionGraph('ProductRelation','{"Relation":{}}',array('product_id'=> $product_id));
        $this->setPlaceholder('related_products',$PR);
        $PR = new ProductRelation($this->modx);
        $this->setPlaceholder('relation_types',$PR->getTypes());
        
        // categories (foxycart)
        $this->setPlaceholder('categories',json_decode($this->modx->getOption('moxycart.categories'),true));
        
        
        // templates
        $Ts = $this->modx->getCollection('modTemplate');
        $templates = array();
        foreach ($Ts as $t) {
            $templates[$t->get('id')] = sprintf('%s (%s)',$t->get('templatename'),$t->get('id'));
        }
        $this->setPlaceholder('templates',$templates);
        
        // option_types
        //otype_ids
        $OTs = $this->modx->getCollection('OptionType');
        $OptionTypes = array();
        foreach ($OTs as $o) {
            $OptionTypes[$o->get('id')] = sprintf('%s (%s)',$o->get('name'),$o->get('slug'));
        }
        $this->setPlaceholder('OptionTypes',$OptionTypes);
        
        // types (dropdown -- product types)
        $this->setPlaceholder('types',$P->getTypes());
        
        return $this->fetchTemplate('product/edit.php');
    }

     public function getProductInventory(array $scriptProperties = array()) {
        $Obj = new Product($this->modx);
        $scriptProperties['limit'] = 0;
        $results = $Obj->all($scriptProperties);
        $count = $Obj->count($scriptProperties);
        $offset = (int) $this->modx->getOption($scriptProperties,'offset',0);
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholder('results', $results);
        $this->setPlaceholder('count', $count);
        $this->setPlaceholder('offset', $offset);

        return $this->fetchTemplate('product/inventory.php');
    }    

    /**
     * Basically take a product ID (product_id) and forward 
     *
     */
    public function getProductPreview(array $scriptProperties = array()) {
        $product_id = (int) $this->modx->getOption('product_id', $scriptProperties);
        $Obj = new Product($this->modx);    
        if (!$result = $Obj->find($product_id)) {
            return $this->sendError('Page not found.');
        }
        header('Location: '.MODX_SITE_URL . $result->get('uri'));
        exit;        
    }

    
    //------------------------------------------------------------------------------
    //! Fields
    //------------------------------------------------------------------------------
    /**
     * Field Management main page
     * @param array $scriptProperties
     */
    public function getFields(array $scriptProperties = array()) {
        $Obj = new Field($this->modx);
        $results = $Obj->all($scriptProperties);
        //$debug = $Obj->all($scriptProperties,true);
        //print $debug; exit;
        $this->setPlaceholder('debug', $debug);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('field','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/fields.php');
    }
    
    public function getFieldCreate(array $scriptProperties = array()) {
        $Obj = new Field($this->modx);
        $results = $Obj->all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('field','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('field/create.php');
    }    

    /**
     * Remember we have to set up the manager container
     *
     */
    public function getFieldEdit(array $scriptProperties = array()) {
        $field_id = (int) $this->modx->getOption('field_id',$scriptProperties);
        $Obj = new Field($this->modx);    
        if (!$result = $Obj->find($field_id)) {
            return $this->sendError('Page not found.');
        }
        $scriptProperties['baseurl'] = self::url('field','edit',array('field_id'=>$field_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('field/edit.php');
    }
    
    
    /**
     * 
     * @param array $scriptProperties
     */
    public function getIndex(array $scriptProperties = array()) {
        return $this->fetchTemplate('main/index.php');
    }

    //------------------------------------------------------------------------------
    //! Options
    //------------------------------------------------------------------------------
    /**
     * Options Management
     * @param array $scriptProperties
     */
    public function getOptions(array $scriptProperties = array()) {
        $Obj = new OptionType($this->modx);
        $results = $Obj->all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('optiontype','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/options.php');
    }

    public function getOptionCreate(array $scriptProperties = array()) {
        $Obj = new OptionType($this->modx);    

        $scriptProperties['baseurl'] = self::url('optiontype','create');
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($Obj->toArray());
        $this->setPlaceholder('result',$Obj);
        return $this->fetchTemplate('optiontype/create.php');
    }    

    /**
     * 
     */
    public function getOptionEdit(array $scriptProperties = array()) {    
        $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $Obj = new OptionType($this->modx);    
        if (!$result = $Obj->find($otype_id)) {
            return $this->sendError('Page not found.');
        }
        $scriptProperties['baseurl'] = self::url('optiontype','edit',array('otype_id'=>$otype_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('optiontype/edit.php');
    }
    
    /**
     * 
     */
    public function getOptionTerms(array $scriptProperties = array()) {
        $otype_id = (int) $this->modx->getOption('otype_id',$scriptProperties);
        $Obj = new OptionType($this->modx);    
        if (!$result = $Obj->find($otype_id)) {
            return $this->sendError('Invalid option type');
        }
        $Terms = new OptionTerm($this->modx);
        $Terms = $Terms->all(array('otype_id'=>$otype_id,'sort'=>'seq'));
        $scriptProperties['baseurl'] = self::url('optiontype','terms',array('otype_id'=>$otype_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        $this->setPlaceholder('terms', $Terms);
        return $this->fetchTemplate('optiontype/terms.php');
    }


    //------------------------------------------------------------------------------
    //! Reports
    //------------------------------------------------------------------------------
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function getReports(array $scriptProperties = array()) {
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/reports.php');
    }
    
    //------------------------------------------------------------------------------
    //! Reviews
    //------------------------------------------------------------------------------
    /**
     * Review Management
     * @param array $scriptProperties
     */
    public function getReviews(array $scriptProperties = array()) {

        $Obj = new Review($this->modx);
        $results = $Obj->all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::page('reviews');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/reviews.php');
    }

    /**
     *
     */
    public function getReviewEdit(array $scriptProperties = array()) {
        $review_id = (int) $this->modx->getOption('review_id',$scriptProperties);
        $Obj = new Review($this->modx);    
        if (!$result = $Obj->find($review_id)) {
            return $this->sendError('Page not found.');
        }
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('review/edit.php');
    }


    /**
     * 
     */
    public function getReviewCreate(array $scriptProperties = array()) {
        $Obj = new Review($this->modx);    
        //$scriptProperties['baseurl'] = self::url('review','create');
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($Obj->toArray());
        $this->setPlaceholder('result',$Obj);
        return $this->fetchTemplate('review/create.php');
    }

    
    //------------------------------------------------------------------------------
    //! Settings
    //------------------------------------------------------------------------------
    /**
     * @param array $scriptProperties
     */
    public function getSettings(array $scriptProperties = array()) {

        return $this->fetchTemplate('main/settings.php');
     
    }
    
    //------------------------------------------------------------------------------
    //! Store
    //------------------------------------------------------------------------------
    /**
     * Called from the Store CRC 
     *
     * @param array $scriptProperties
     */
    public function getStoreProducts(array $scriptProperties = array()) {
        $this->scriptProperties['_nolayout'] = true;
        $Obj = new Product($this->modx);
        $results = $Obj->all($scriptProperties);
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/storeproducts.php');
    }
    
    
    
    public function getTest(array $scriptProperties = array()) {
        return $this->fetchTemplate('main/test.php');
    }
    
        
}
/*EOF*/