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

        $this->modx->addPackage('foxycart',$this->config['core_path'].'model/orm/','foxy_');
                
        $this->modx->regClientCSS($this->config['assets_url'].'css/moxycart.css');
        $this->modx->regClientCSS($this->config['assets_url'] . 'css/mgr.css');
        $this->modx->regClientCSS($this->config['assets_url'] . 'css/dropzone.css');
        $this->modx->regClientCSS($this->config['assets_url'].'css/datepicker.css');
        $this->modx->regClientCSS($this->config['assets_url'].'css/colorbox.css');
        
        $this->modx->regClientCSS('//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.min.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-ui.js'); 
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.tabify.js');
        $this->modx->regClientStartupScript($this->config['assman_assets_url'].'js/dropzone.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/bootstrap.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/form2js.js');        
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.colorbox.js');      
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/handlebars.js');
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

    /**
     *
     *
     */
    private function _setProductColumns() {
        if ($cols = $this->modx->getOption('moxycart.product_columns')) {
            $cols = json_decode($cols,true);
        }
        if (empty($cols) || !is_array($cols)) {
            $cols = array('name'=>'Name','sku'=>'SKU','category'=>'Foxycart Category');
        }
        $this->setPlaceholder('columns', $cols);    
    }
    
    /**
     * This is the data that is needed just to make the forms work
     *
     */
    private function _setUIdata() {
        // UI DATA ------------------------------------------
        // relation_types
        $PR = new ProductRelation($this->modx);
        $this->setPlaceholder('relation_types',$PR->getTypes());
        // stores (dropdown)
        $Ss = $this->modx->getCollection('Store',array('class_key'=>'Store'));
        $stores = array();
        foreach ($Ss as $s) {
            $stores[ $s->get('id') ] = sprintf('%s (%s)',$s->get('pagetitle'),$s->get('id'));
        }
        $this->setPlaceholder('stores',$stores);
        
        // fields (dropdown)
        $c = $this->modx->newQuery('Field');
        $c->sortby('seq','ASC');
        $Fs = $this->modx->getCollection('Field',$c); 
        $fields = array();
        foreach ($Fs as $f) {
            $fields[ $f->get('field_id') ] = sprintf('%s (%s)',$f->get('label'),$f->get('slug'));
        }
        $this->setPlaceholder('fields',$fields);

        // templates
        $Ts = $this->modx->getCollection('modTemplate');
        $templates = array();
        foreach ($Ts as $t) {
            $templates[$t->get('id')] = sprintf('%s (%s)',$t->get('templatename'),$t->get('id'));
        }
        $this->setPlaceholder('templates',$templates);

        // Taxonomies
        $taxonomies = array();
        $c = $this->modx->newQuery('Taxonomy');
        $c->where(array('published' => true, 'class_key'=>'Taxonomy'));
        $c->sortby('menuindex','ASC');
        $c->select(array('id','pagetitle'));
        if ($Taxes = $this->modx->getCollection('Taxonomy', $c)) {
            foreach ($Taxes as $t) {
                $taxonomies[ $t->get('id') ] = $t->get('pagetitle');
            }
        }
        $this->setPlaceholder('taxonomies',$taxonomies);
        
        
        //Options (All of them and all Terms)
        // [
        //   {
        //      slug:"",
        //      name:"", 
        //      Terms:[{slug:"","name":""}]
        //   }
        // ]
        $c = $this->modx->newQuery('Option');
        $c->sortby('Option.seq','ASC');
        $c->sortby('Terms.seq','ASC');

        $Os = $this->modx->getCollectionGraph('Option','{"Terms":{}}',$c);
        $Options = array();
        foreach ($Os as $o) {
            $Options[] = $o->toArray('',false,false,true);
        }
        //print_r($Options); exit;
        $this->setPlaceholder('Options',$Options);

        // categories (foxycart)
        $this->setPlaceholder('categories',json_decode($this->modx->getOption('moxycart.categories'),true));

        // types (dropdown -- product types)
        $P = new Product($this->modx);
        $this->setPlaceholder('types',$P->getTypes());        
    
    }

    //------------------------------------------------------------------------------
    //! Products
    //------------------------------------------------------------------------------
    /**
     *
     * @param array $scriptProperties
     */
    public function getProducts(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $Obj = new Product($this->modx);
        $results = $Obj->all($scriptProperties);
//        print $results; exit;
        $count = $Obj->count($scriptProperties);
        $offset = (int) $this->modx->getOption('offset',$scriptProperties,0);
        $this->_setProductColumns();
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholder('searchterm','');        
        $this->setPlaceholder('results', $results);
        $this->setPlaceholder('count', $count);
        $this->setPlaceholder('offset', $offset);
        $this->setPlaceholder('baseurl', $this->page('products'));
        
        return $this->fetchTemplate('main/products.php');
    }
    
    public function postProducts(array $scriptProperties = array()) {
        return $this->getProducts($scriptProperties);
    }
 
    /**
     * Evil copy of the product edit controller.
     *
     * UI Data:
     *      templates
     *      categories (foxycart)
     *      Fields
     *      Options
     *      Types (product types)
     */
    public function getProductCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);


        $this->setPlaceholder('product_form_action', 'product_create');
        $this->setPlaceholder('pagetitle', 'Create New Product');
        $store_id = (int) $this->modx->getOption('store_id',$scriptProperties);

        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $product_id = (int) $this->modx->getOption('product_id',$scriptProperties);

        $Obj = new Product($this->modx);
        $this->setPlaceholder('pagetitle', 'Create Product');
        $Obj->getDefaultValues($store_id);
        $full_product_data = $Obj->complete($product_id); 
        
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($Obj->toArray());
        $this->setPlaceholder('result',$Obj);
        
        $this->_setUIdata();
        // Register this in the moxycart variable
        $this->client_config['product'] = $full_product_data;
        $this->client_config['assets_url'] = self::url('asset','create',array(),'assman');
        $this->client_config['assets_delete_url'] = self::url('asset','delete',array(),'assman');
        $this->client_config['settings'] = array(
            'thumbnail_width' => $this->modx->getOption('moxycart.thumbnail_width'),
            'thumbnail_height' => $this->modx->getOption('moxycart.thumbnail_height')
        );
        $this->client_config['product_save_method'] = 'create';

    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
    	   console.log("[moxycart] '.__FUNCTION__.'");
            var use_editor = "'.$this->modx->getOption('use_editor').'";
            // Document read stuff has to be in here
            jQuery(document).ready(function() {
                product_init();
            });
    		</script>');
        if ($this->modx->getOption('use_editor')) {
            $this->_load_tinyMCE();
        }

            
        // thumbnail: Todo - write this via js
        $this->setPlaceholder('thumbnail_url','');
                    
        // product_fields
        $this->setPlaceholder('product_fields',array());

        // related_products
        $this->setPlaceholder('related_products',array());
        $PR = new ProductRelation($this->modx);
        $this->setPlaceholder('relation_types',$PR->getTypes());
                
        // product_options
        $this->setPlaceholder('product_options',array());

        // product_option_meta
        $this->setPlaceholder('product_option_meta',array());
                
        // ProductTaxonomy
        $this->setPlaceholder('product_taxonomies',array());        
        
        // Terms
        $T = new Taxonomy($this->modx);
        $terms = $T->getTaxonomiesAndTerms();
        $this->setPlaceholder('terms',$terms);
        
        // ProductTerm
        $this->setPlaceholder('product_terms',array());        
        
        // ProductOrder
        $this->setPlaceholder('product_orders',array());        
        
        return $this->fetchTemplate('product/edit.php');

    }    

    /**
     * HUGE.  Thin controller FAIL.  (hangs head in shame).
     */
    public function getProductEdit(array $scriptProperties = array()) {

        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $product_id = (int) $this->modx->getOption('product_id',$scriptProperties);
        $this->setPlaceholder('product_form_action', 'product_update');

        $Obj = new Product($this->modx);
        if (!$result = $Obj->find($product_id)) {
            return $this->sendError('Page not found.');
        }
        $this->setPlaceholder('pagetitle', 'Edit Product: '.$result->get('name'));
        $full_product_data = $Obj->complete($product_id); 
        
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        
        $this->_setUIdata();
        // Register this in the moxycart variable
        $this->client_config['product'] = $full_product_data;
        $this->client_config['assets_url'] = self::url('asset','create',array(),'assman');
        $this->client_config['assets_delete_url'] = self::url('asset','delete',array(),'assman');
        $this->client_config['settings'] = array(
            'thumbnail_width' => $this->modx->getOption('moxycart.thumbnail_width'),
            'thumbnail_height' => $this->modx->getOption('moxycart.thumbnail_height')
        );
        $this->client_config['product_save_method'] = 'edit';

    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
    	   console.log("[moxycart] '.__FUNCTION__.'");
            var use_editor = "'.$this->modx->getOption('use_editor').'";
            // Document read stuff has to be in here
            jQuery(document).ready(function() {
                product_init();
            });
    		</script>');
        if ($this->modx->getOption('use_editor')) {
            $this->_load_tinyMCE();
        }

            
        // thumbnail: Todo - write this via js
        $thumbnail_url = '';
        $this->modx->setOption('assman.thumbnail_width', $this->modx->getOption('moxycart.thumbnail_width'));
        $this->modx->setOption('assman.thumbnail_height', $this->modx->getOption('moxycart.thumbnail_height'));        
        if($A = $this->modx->getObject('Asset', $result->asset_id)) {
            $thumbnail_url = $A->get('thumbnail_url');
        }
        $this->setPlaceholder('thumbnail_url',$thumbnail_url);
                    
        // product_fields
        $product_fields = array();
        $c = $this->modx->newQuery('ProductField');
        $c->where(array('ProductField.product_id' => $product_id));
        $c->sortby('Field.seq','ASC');
        $PF = $this->modx->getCollectionGraph('ProductField','{"Field":{}}',$c);
        $Field = new Field($this->modx);
        foreach ($PF as $f) {
            if($tmp = $Field->generate($f->get('field_id'), $f->get('value'), 'Fields[value][]')) {
                $product_fields[$f->get('field_id')] = $tmp;
            }
        }
        $this->setPlaceholder('product_fields',$product_fields);

        // related_products
        $c = $this->modx->newQuery('ProductRelation');
        $c->where(array('ProductRelation.product_id' => $product_id));
        $c->sortby('ProductRelation.seq','ASC');
        $PR = $this->modx->getCollectionGraph('ProductRelation','{"Relation":{}}',array('product_id'=> $product_id));
        $this->setPlaceholder('related_products',$PR);
        $PR = new ProductRelation($this->modx);
        $this->setPlaceholder('relation_types',$PR->getTypes());
                
        // product_options
        $product_options = array();
        $Opts = $this->modx->getCollection('Option');
        foreach($Opts as $o) {
            $product_options[ $o->get('option_id') ]['option_id'] = $o->get('option_id');
            $product_options[ $o->get('option_id') ]['checked'] = false;
            $product_options[ $o->get('option_id') ]['meta'] = 'all_terms';        
        }
        
        $c = $this->modx->newQuery('ProductOption');
        $c->where(array('product_id' => $product_id));
        $c->sortby('seq','ASC');        
        $POTs = $this->modx->getCollection('ProductOption', $c);
        $meta = array();
        foreach ($POTs as $p) {
            $meta[ $p->get('option_id') ]['Terms'] = array(); // init
            $product_options[ $p->get('option_id') ]['option_id'] = $p->get('option_id');
            $product_options[ $p->get('option_id') ]['checked'] = true;
            $product_options[ $p->get('option_id') ]['meta'] = $p->get('meta');
        }
        $this->setPlaceholder('product_options',$product_options);

        // product_option_meta
        $c = $this->modx->newQuery('OptionTerm');
        $c->where(array('product_id' => $product_id));
        $c->sortby('seq','ASC');        
        $Terms = $this->modx->getCollectionGraph('OptionTerm', $c);

        foreach ($Terms as $t) {
            // Global values
            $meta[ $t->get('option_id') ]['Terms'][ $t->get('oterm_id') ] = $t->toArray();
            $meta[ $t->get('option_id') ]['Terms'][ $t->get('oterm_id') ]['checked'] = false;
            $meta[ $t->get('option_id') ]['Terms'][ $t->get('oterm_id') ]['is_override'] = false;
            $meta[ $t->get('option_id') ]['Terms'][ $t->get('oterm_id') ]['mod_price'] = '';
            $meta[ $t->get('option_id') ]['Terms'][ $t->get('oterm_id') ]['mod_weight'] = '';
            $meta[ $t->get('option_id') ]['Terms'][ $t->get('oterm_id') ]['mod_code'] = '';
            $meta[ $t->get('option_id') ]['Terms'][ $t->get('oterm_id') ]['mod_category'] = '';
            $meta[ $t->get('option_id') ]['Terms'][ $t->get('oterm_id') ]['asset_id'] = ''; // future

            // Overrides for this product and this option
            $Metas = $this->modx->getCollection('ProductOptionMeta',array('product_id'=>$product_id, 'option_id'=>$t->get('option_id')));
            foreach ($Metas as $m) {
                $meta[ $t->get('option_id') ]['Terms'][ $m->get('oterm_id') ]['checked'] = true; // if it exists, it is used i.e. checked
                if ($m->get('is_override')) {
                    $meta[ $t->get('option_id') ]['Terms'][ $m->get('oterm_id') ]['is_override'] = $m->get('is_override');
                    $meta[ $t->get('option_id') ]['Terms'][ $m->get('oterm_id') ]['mod_price'] = $m->get('mod_price');
                    $meta[ $t->get('option_id') ]['Terms'][ $m->get('oterm_id') ]['mod_weight'] = $m->get('mod_weight');
                    $meta[ $t->get('option_id') ]['Terms'][ $m->get('oterm_id') ]['mod_code'] = $m->get('mod_code');
                    $meta[ $t->get('option_id') ]['Terms'][ $m->get('oterm_id') ]['mod_category'] = $m->get('mod_category');
                    $meta[ $t->get('option_id') ]['Terms'][ $m->get('oterm_id') ]['asset_id'] = $m->get('asset_id');                
                }
            }
        }
        //print '<pre>'; print_r($meta); print '</pre>'; exit;
        $this->setPlaceholder('product_option_meta',$meta);
                
        // ProductTaxonomy
        $product_taxonomies = array();
        if ($PTs = $this->modx->getCollection('ProductTaxonomy', array('product_id'=>$product_id))) {
            foreach($PTs as $pt) {
                $product_taxonomies[] = $pt->get('taxonomy_id');
            }
        }
        $this->setPlaceholder('product_taxonomies',$product_taxonomies);        
        
        // Terms
        $T = new Taxonomy($this->modx);
        $terms = $T->getTaxonomiesAndTerms();
        $this->setPlaceholder('terms',$terms);
        
        // ProductTerm
        $product_terms = array();
        if ($PTs = $this->modx->getCollection('ProductTerm', array('product_id'=>$product_id))) {
            foreach($PTs as $pt) {
                $product_terms[] = $pt->get('term_id');
            }
        }
        $this->setPlaceholder('product_terms',$product_terms);        
        
        // ProductOrder
        $product_orders = array();
        $c = $this->modx->newQuery('TransactionDetailOption');
        $c->where(array(
            'product_option_name'=>'product_id',
            'product_option_value' => $product_id
        ));
        $c->sortby('TransactionDetail.transaction_id','DESC');
        if ($TDO = $this->modx->getCollectionGraph('TransactionDetailOption','{"TransactionDetail":{"Transaction":{}}}',$c)) {
            foreach ($TDO as $t) {
                $product_orders[] = $t->toArray('',false,false,true);
            }
        }
        $this->setPlaceholder('product_orders',$product_orders);        
        
        return $this->fetchTemplate('product/edit.php');
    }

     public function getProductInventory(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $Obj = new Product($this->modx);
        $scriptProperties['limit'] = 0;
        $results = $Obj->all($scriptProperties);
        $count = $Obj->count($scriptProperties);
        $offset = (int) $this->modx->getOption('offset',$scriptProperties,0);
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
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
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
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $Obj = new Field($this->modx);
        $scriptProperties['sort'] = 'seq';
        $scriptProperties['dir'] = 'ASC';
        $results = $Obj->all($scriptProperties);
        //$debug = $Obj->all($scriptProperties,true);
        //print $debug; exit;
        //$this->setPlaceholder('debug', $debug);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('field','index');
        $this->setPlaceholder('searchterm','');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/fields.php');
    }

    public function postFields(array $scriptProperties = array()) {
        $seq = 0;
        foreach ($scriptProperties['seq'] as $field_id) {
            $Field = $this->modx->getObject('Field', array('field_id' => $field_id));
            $Field->set('seq', $seq);
            $Field->save();
            $seq++;
        }
        unset($scriptProperties['seq']);
        return $this->getFields($scriptProperties);
    }
    
    public function getFieldCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $Obj = new Field($this->modx);
        $results = $Obj->all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('field','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholder('slug','');
        $this->setPlaceholder('label','');
        $this->setPlaceholder('type','');
        $this->setPlaceholder('description','');
        $this->setPlaceholder('config','');
        $this->setPlaceholder('group','');
        
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('field/create.php');
    }    

    /**
     * Remember we have to set up the manager container
     *
     */
    public function getFieldEdit(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
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
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__,__LINE__);
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
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $Obj = new Option($this->modx);
        $scriptProperties['sort'] = 'seq';
        $scriptProperties['dir'] = 'ASC';
        $results = $Obj->all($scriptProperties);
        // We need these for pagination
        $scriptProperties['count'] = $Obj->count($scriptProperties);        
        $scriptProperties['baseurl'] = self::url('option','index');
        $this->setPlaceholder('results', $results);
        $this->setPlaceholder('searchterm','');
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('main/options.php');
    }


    public function postOptions(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $seq = 0;
        foreach ($scriptProperties['seq'] as $option_id) {
            $OType = $this->modx->getObject('Option', array('option_id' => $option_id));
            $OType->set('seq', $seq);
            $OType->save();
            $seq++;
        }
        unset($scriptProperties['seq']);
        return $this->getOptions($scriptProperties);
    }

    public function getOptionCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $Obj = new Option($this->modx);    

        $scriptProperties['baseurl'] = self::url('option','create');
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($Obj->toArray());
        $this->setPlaceholder('result',$Obj);
        return $this->fetchTemplate('option/create.php');
    }    

    /**
     * 
     */
    public function getOptionEdit(array $scriptProperties = array()) {   
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__); 
        $option_id = (int) $this->modx->getOption('option_id',$scriptProperties);
        $Obj = new Option($this->modx);    
        if (!$result = $Obj->find($option_id)) {
            return $this->sendError('Page not found.');
        }
        $scriptProperties['baseurl'] = self::url('option','edit',array('option_id'=>$option_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        return $this->fetchTemplate('option/edit.php');
    }
    
    /**
     * 
     */
    public function getOptionTerms(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $option_id = (int) $this->modx->getOption('option_id',$scriptProperties);
        $Obj = new Option($this->modx);    
        if (!$result = $Obj->find($option_id)) {
            return $this->sendError('Invalid option type');
        }

        $Terms = new OptionTerm($this->modx);
        $Terms = $Terms->all(array('option_id'=>$option_id,'sort'=>'seq'));
        $scriptProperties['baseurl'] = self::url('option','terms',array('option_id'=>$option_id));
        $this->setPlaceholders($scriptProperties);
        $this->setPlaceholders($result->toArray());
        $this->setPlaceholder('result',$result);
        $this->setPlaceholder('terms', $Terms);
        return $this->fetchTemplate('option/terms.php');
    }



    //------------------------------------------------------------------------------
    //! Reports
    //------------------------------------------------------------------------------
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function getReports(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
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
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
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
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
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
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
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
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        return $this->fetchTemplate('main/settings.php');
     
    }
    
    //------------------------------------------------------------------------------
    //! Store
    //------------------------------------------------------------------------------
    /**
     * Called from the Store CRC: controllers/store/update.class.php and create.class.php 
     *
     * @param array $scriptProperties
     */
    public function getStoreProducts(array $scriptProperties = array()) {
//        return '<div>This is a  test...</div>';

        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $store_id = (int) $this->modx->getOption('store_id', $scriptProperties);
        $this->client_config['store_id'] = $store_id;
        $this->setPlaceholder('store_id', $store_id);
        $this->scriptProperties['_nolayout'] = true;
        $Obj = new Product($this->modx);
        $results = $Obj->all($scriptProperties);
        $count = $Obj->count($scriptProperties);
        $offset = (int) $this->modx->getOption('offset',$scriptProperties,0);
        $results_per_page = (int) $this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page'));
        $this->_setProductColumns();
        $this->setPlaceholder('baseurl', $this->page('storeproducts'));
        $this->setPlaceholder('results', $results);
        $this->setPlaceholder('count', $count);
        $this->setPlaceholder('offset', $offset);
        $this->setPlaceholder('results_per_page', $results_per_page);        
        $this->setPlaceholders($scriptProperties);
//        return '<div>This is a  test...'.__LINE__.'</div>';
//        return '<pre>'.print_r($results,true).'</pre>';
        return $this->fetchTemplate('main/storeproducts.php');

    }

    public function getStoreCreate(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $this->scriptProperties['_nolayout'] = true;
        return $this->fetchTemplate('main/storecreate.php');
    }
    
    
    
    public function getTest(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        return $this->fetchTemplate('main/test.php');
    }
    
    //------------------------------------------------------------------------------
    //! Tools
    //------------------------------------------------------------------------------
    public function getTools(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);    
        return $this->fetchTemplate('main/tools.php');
    }

    public function getXcart(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);
        $this->_setUIdata();
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('tools/xcart.php');
    }

    public function postXcart(array $scriptProperties = array()) {
        $assman_core_path = $this->modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
        $taxonomies_core_path = $this->modx->getOption('taxonomies.core_path', null, MODX_CORE_PATH.'components/taxonomies/');

        require_once $assman_core_path.'vendor/autoload.php';
        require_once $taxonomies_core_path.'vendor/autoload.php';

        $this->modx->log(\modX::LOG_LEVEL_INFO, print_r($scriptProperties,true),'','Moxycart PageController:'.__FUNCTION__);    
        $this->_setUIdata();
        $this->setPlaceholders($scriptProperties);
        return $this->fetchTemplate('tools/xcart.php');
    }

}
/*EOF*/