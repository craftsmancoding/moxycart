<?php
/**
 * The abstract Manager Controller.
 * In this class, we define stuff we want on all of our controllers.
 *
 * WARNING: due to routing present in the "render" function, any functions whose names
 * begin with "get" or "post" may be inadvertently called when the &method argument 
 * passed is prepended with get or post (depending on whether or not post data is present).
 *
 */
namespace Moxycart\Controller; 
abstract class Base extends \modExtraManagerController {
    /** @var bool Set to false to prevent loading of the header HTML. */
    public $loadHeader = true;
    /** @var bool Set to false to prevent loading of the footer HTML. */
    public $loadFooter = true;
    /** @var bool Set to false to prevent loading of the base MODExt JS classes. */
    public $loadBaseJavascript = true;
    /** @var array An array of possible paths to this controller's templates directory. */
    public $templatesPaths = array();
    /** @var array An array of possible paths to this controller's directory. */
    //public $controllersPaths;
    /** @var modContext The current working context. */
    //public $workingContext;
    /** @var modMediaSource The default media source for the user */
    //public $defaultSource;
    /** @var string The current output content */
    //public $content = '';
    /** @var array An array of request parameters sent to the controller */
   // public $scriptProperties = array();
    /** @var array An array of css/js/html to load into the HEAD of the page */
    //public $head = array('css' => array(),'js' => array(),'html' => array(),'lastjs' => array());
    /** @var array An array of placeholders that are being set to the page */
    //public $placeholders = array();
    
    public $data = array(); // passed to views.
    
    public static $x; // for static refs
    
    private $core_path;
    private $assets_url;
    private $mgr_url;
    private $connector_url; 
    private $mgr_connector_url; 
    private $jquery_url;
    public $max_image_width = 250;
    public $thumb_width = 100;

    private $cache; // for iterative ops
    private $depth = 0; //
    
    
    /**
     * Map a function name to a MODX permission, e.g. 
     * 'edit_product' => 'edit_document'
     */
    private $perms = array(
        'edit_product' => 'edit_document',
    );
    
    /**
     * This is the permission tested against if nothing is explicitly defined
     * in the $perms array.
     */
    private $default_perm = 'view_document';

    function __construct(\modX &$modx,$config = array()) {
        parent::__construct($modx,$config);
        static::$x =& $modx;
/*
        $this->modx =& $modx;
        $this->config = !empty($config) && is_array($config) ? $config : array();

        $this->core_path = $this->modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
        $this->assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');
        $this->setProperty('core_path', $this->core_path);
        $this->setProperty('assets_url', $this->assets_url);
*/

    }
    
    /**
     * Catch all for bad function requests -- our 404
     */
    public function __call($name,$args) {
//        print 'NOT FOUND...'; exit;
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'[moxycart] Invalid function name '.__FUNCTION__);
        print 'Invalid function name: '.$name; exit;
        //return $this->help($args);
    }

    
    private function _send401() {
        header('HTTP/1.0 401 Unauthorized');
        print 'Unauthorized';
        exit;
    }

    public function sendError($msg='Error') {
        $this->setPlaceholder('msg',$msg);
        return $this->fetchTemplate('error.php');        
    }
  
    /**
     * Generate pagination links
     *
		require_once('Pagination.php');
		$p = new Pagination();
		$offset = $p->page_to_offset($_GET['page'], $_GET['rpp']);
		$p->set_offset($offset); //
		$p->set_results_per_page($_GET['rpp']);  // You can optionally expose this to the user.
		$p->extra = 'target="_self"'; // optional
		print $p->paginate(100); // 100 is the count of records
     */
    public function paginationLinks(array $scriptProperties = array()) {
//        print print_r($scriptProperties,true); exit;
        $limit = (int) $this->modx->getOption('limit',$scriptProperties,$this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page')));
                
        $offset = (int) $this->modx->getOption('offset',$scriptProperties);        
        $baseurl = $this->modx->getOption('baseurl',$scriptProperties);
        $count = (int) $this->modx->getOption('count',$scriptProperties);

        if (!$limit) return;
        
        $P = new \Pagination();
        $P->set_offset($offset);
        $P->set_base_url($baseurl);
        $P->set_results_per_page($limit);
        return $P->paginate($count);
    }
    /** 
     * For iterative parsing of the Taxonomy/Terms properties
     * 
     {
     "children":{
        "44":{"alias":"popular","pagetitle":"Popular","published":true,"menuindex":0,"children":{
            "48":{"alias":"geek","pagetitle":"geek","published":true,"menuindex":0,"children":[]}}},
        "45":{"alias":"special","pagetitle":"Special","published":true,"menuindex":1,"children":[]}},
        "children_ids":{"44":true,"45":true}
     }
        
     convert this to a flat structure
     
     */
    private function _get_subterms($props) {
        $data = array();
        $data['terms'] = '';
        unset($props['children_ids']);
        if (!empty($props['children'])) {
            foreach($props['children'] as $term_id => $tdata) {
                $tdata['class'] = 'taxonomy_term_item';
                $tdata['terms'] = '';
                $tdata['depth'] = str_repeat('&nbsp;', $this->depth * 2);
                if (!empty($tdata['children'])) {
                    $this->depth++;
                    $tdata['terms'] = $this->_get_subterms($tdata);
                    $tdata['class'] = 'taxonomy_parent_item';                    
                }
                $tdata['term_id'] = $term_id;
                $tdata['is_checked'] = '';
                if (isset($this->cache[$term_id])) {
                    $tdata['is_checked'] = ' checked="checked"';
                }
                $data['terms'] .= $this->_load_view('product_term_item.php', $tdata);
            }
        }
        
        return $this->_load_view('product_term_list.php',$data);
    }
    

    /**
     * Initializes the main manager controller. You may want to load certain classes,
     * assets that are shared across all controllers or configuration. 
     *
     * All your other controllers in this namespace should extend this one.
     *
     */
    public function initialize() {
        $this->assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/assets/');
        $this->mgr_url = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $this->modx->addPackage('moxycart',$this->core_path.'model/','moxy_');
    }
    /**
     * Defines the lexicon topics to load in our controller.
     * @return array
     */
    public function getLanguageTopics() {
        return array('moxycart:default');
    }
    /**
     * We can use this to check if the user has permission to see this controller
     * @return bool
     */
    public function checkPermissions() {
        return true; // TODO
    }


    /**
     * Gotta look up the URL of our CMP and its actions

     * @param string $class of one of our controllers
     * @
     * @param array any optional arguments, e.g. array('action'=>'children','parent'=>123)

     * @return string
     */
    public static function url($class,$method='index',$args=array()) {
        // future: pass as args:
        $namespace='moxycart';
        $controller='index';
        $url = MODX_MANAGER_URL;
        if ($Action = static::$x->getObject('modAction', array('namespace'=>$namespace,'controller'=>$controller))) {
            $url .= '?a='.$Action->get('id');
            $url .= '&class='.$class.'&method='.$method;
            if ($args) {
                foreach ($args as $k=>$v) {
                    $url.='&'.$k.'='.$v;
                }
            }
        }
        return $url;
    }

    /**
     * Override Smarty. Don't want it.
     *
     * @param string $file (relative to the views directory)
     * @return rendered string (e.g. HTML)
     */
    public function fetchTemplate($file) {
        $path = $this->modx->getOption('moxycart.core_path','', MODX_CORE_PATH.'components/moxycart/').'/views/';

        $data =& $this->getPlaceholders();
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'View: ' .$file.' data: '.print_r($data,true), __FUNCTION__,__LINE__);
		if (is_file($path.$file)) {
			ob_start();
			include $path.$file;
			return ob_get_clean();
		}
		$this->modx->log(\modX::LOG_LEVEL_ERROR, 'View file does not exist: ' .$path.$file, __FUNCTION__,__LINE__);
		return $this->modx->lexicon('view_not_found', array('file'=> 'views/'.$file));
    }

    /**
     * Clean out the "scaffolding" gunk from $scriptProperties array.
     */
    public function reduce($scriptProperties = array()) {
        unset($scriptProperties['a']);
        unset($scriptProperties['HTTP_MODAUTH']);
        unset($scriptProperties['action']);
        return $scriptProperties;
    }


    /**
     * This is what ultimately responds to a manager request, e.g. generates a CMP.
     *
     * We override this so we can route to functions other than the simple "process"
     * 
     * There are 2 class vars important here:
     *
     *      $this->scriptProperties : contains all request data
     *      $this->config : set in our constructor. Contains "method"
     *
     * @return string
     */
    public function render() {
        if (!$this->checkPermissions()) {
            return $this->modx->error->failure($this->modx->lexicon('access_denied'));
        }

        $this->modx->invokeEvent('OnBeforeManagerPageInit',array(
            'action' => $this->config,
        ));

        $this->theme = $this->modx->getOption('manager_theme',null,'default');

        $this->prepareLanguage();
        $this->setPlaceholder('_ctx',$this->modx->context->get('key'));
        $this->loadControllersPath();
        $this->loadTemplatesPath();
        $content = '';

        $this->registerBaseScripts();

        $this->checkFormCustomizationRules();

        $this->setPlaceholder('_config',$this->modx->config);

        $this->modx->invokeEvent('OnManagerPageBeforeRender',array('controller' => &$this));

        // This was too simplistic:
        // $placeholders = $this->process($this->scriptProperties);
//        print_r($this->scriptProperties); exit;
//        print_r($this->config); exit;
        // $method = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : 'index';
        $method = $this->config['method'];
        $filters = $this->scriptProperties;
        unset($filters['a']);
        unset($filters['class']);
        unset($filters['method']);
        $placeholders = $this->$method($filters);
        
        if (!$this->isFailure && !empty($placeholders) && is_array($placeholders)) {
            $this->setPlaceholders($placeholders);
        } elseif (!empty($placeholders)) {
            $content = $placeholders;
        }
        if (!$this->isFailure) {
            $this->loadCustomCssJs();
        }
        $this->firePreRenderEvents();

        /* handle FC rules */
        if (!empty($this->ruleOutput)) {
            $this->addHtml(implode("\n",$this->ruleOutput));
        }

        /* register CSS/JS */
        $this->registerCssJs();

        $this->setPlaceholder('_pagetitle',$this->getPageTitle());

        $this->content = '';
        if ($this->loadHeader) {
            $this->content .= $this->getHeader();
        }

        $tpl = $this->getTemplateFile();
        if ($this->isFailure) {
            $this->setPlaceholder('_e', $this->modx->error->failure($this->failureMessage));
            $content = $this->fetchTemplate('error.tpl');
        } else if (!empty($tpl)) {
            $content = $this->fetchTemplate($tpl);
        }

        $this->content .= $content;

        if ($this->loadFooter) {
            $this->content .= $this->getFooter();
        }

        $this->firePostRenderEvents();
        $this->modx->invokeEvent('OnManagerPageAfterRender',array('controller' => &$this));

        return $this->content;
    }

}