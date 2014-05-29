<?php
/**
 * The "almost abstract" Manager Controller.
 * As expected, in this class, we define behaviors we want on all of our controllers.
 *
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart 
 */
namespace Moxycart; 
require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class BaseController extends \modExtraManagerController {
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
        static::$x =& $modx; // kinda dumb...
    }
    
    /**
     * Catch all for bad function requests -- our 404
     */
    public function __call($name,$args) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'[moxycart] Invalid function name '.$name);
        $this->addStandardLayout($args); // For some reason we have to do this here (?)
        $class = '\\Moxycart\\ErrorController';
        $Error = new $class($this->modx,$config);
        $args['msg'] = 'Invalid routing function name: '. $name;
        // We need to send headers like this, otherwise Ajax requests etc. get confused.
        header('HTTP/1.0 404 Not Found');
        return $Error->get404($args);
    }

    
    private function _send401() {
        header('HTTP/1.0 401 Unauthorized');
        print 'Unauthorized';
        exit;
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
     * Add the standard MODX manager layout to a response.
     * We have to manually re-run this after setting loadBaseJavascript to true.
     * That's the only way to get the resource tree going if your controller has declared 
     * loadBaseJavascript = false -- overriding that at runtime takes this sleight of hand.
     *
     * @param array $scriptProperties
     */
    public function addStandardLayout($scriptProperties) {
        if (!isset($scriptProperties['_nolayout'])) {
            $this->loadHeader = true;
            $this->loadFooter = true;
            $this->loadBaseJavascript = true;
            $this->registerBaseScripts(); // <-- *facepalm*
        }
    }
    
    /**
     * We can use this to check if the user has permission to see this controller
     * @return bool
     */
    public function checkPermissions() {
        return true; // TODO
    }

    /**
     * Override parent function. 
     * Override Smarty. I don't wants it. But BEWARE: the loadHeader and loadFooter bits require 
     * the functionality of the original fetchTemplate function.  ARRRGH.  You try to escape but you can't.
     *
     * @param string $file (relative to the views directory)
     * @return rendered string (e.g. HTML)
     */
    public function fetchTemplate($file) {
        // Conditional override! Gross! 
        // If we don't give Smarty a free pass, we end up with "View file does not exist" errors because
        // MODX relies on the parent fetchTemplate function to load up its header.tpl and footer.tpl files. Ick.
        if (substr($file,-4) == '.tpl') {
            return parent::fetchTemplate($file);
        }
        
//        $this->modx->regClientCSS($this->config['assets_url'] . 'css/mgr.css');
        $this->modx->regClientCSS($this->config['assets_url'] . 'css/dropzone.css');
        $this->modx->regClientCSS($this->config['assets_url'].'css/datepicker.css');
        $this->modx->regClientCSS('//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-2.0.3.min.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-ui.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.tabify.js');
//        $this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.form.min.js');
/*
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/dropzone.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/bootstrap.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/multisortable.js');
        $this->modx->regClientStartupScript($this->config['assets_url'].'js/script.js');        
*/
        
        // Late register here so controllers can add relevant bits of config data
        if ($this->client_config) {
            $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var moxycart = '.json_encode($this->client_config).';
            </script>');
        }
        
        $path = $this->modx->getOption('moxycart.core_path','', MODX_CORE_PATH.'components/moxycart/').'views/';

        $data =& $this->getPlaceholders();
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'View: ' .$file.' data: '.print_r($data,true), __FUNCTION__,__LINE__);
		if (!is_file($path.$file)) {
    		$this->modx->log(\modX::LOG_LEVEL_ERROR, 'View file does not exist: ' .$path.$file, __FUNCTION__,__LINE__);
    		return $this->modx->lexicon('view_not_found', array('file'=> 'views/'.$file));		
		}
		// Load up our page
        $content = '';
        if (!isset($this->scriptProperties['_nolayout'])) {
			ob_start();
			include $path.'header.php';
			$content .= ob_get_clean();        
        }

		ob_start();
		include $path.$file;
		$content .= ob_get_clean();

        if (!isset($this->scriptProperties['_nolayout'])) {
			ob_start();
			include $path.'footer.php';
			$content .= ob_get_clean();        
        }
        return $content;		
    }
        
    /**
     * Defines the lexicon topics to load in our controller.
     * @return array
     */
    public function getLanguageTopics() {
        return array('moxycart:default');
    }

    /**
     * Return a flash message
     *
     */
    public function getMsg() {
        $msg = (isset($_SESSION['msg'])) ? $_SESSION['msg'] : '';
        unset($_SESSION['msg']);
        return $msg;
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
        // so we do this:       
        $method = $this->config['method'];
        $filters = $this->scriptProperties;
        $this->addStandardLayout($filters);
        unset($filters['a']);
        unset($filters['class']);
        unset($filters['method']);
        unset($filters['_nolayout']);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Calling Controller: ' .get_class($this).'::'.$method.' data: '.print_r($filters,true));
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

    /**
     *
     */
    public function sendError($msg='Error') {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'[moxycart] Invalid function name '.$name);
        $this->addStandardLayout(); // For some reason we have to do this here (?)
        $class = '\\Moxycart\\ErrorController';
        $Error = new $class($this->modx,$config);
        $this->setPlaceholder('msg',$msg);
        $args=array();
        $args['msg'] = $this->fetchTemplate('error.php');   
        return $Error->get404($args);
    }

    /**
     * Set a flash message
     *
     */
    public function setMsg($msg,$type='success') {

        $path = $this->modx->getOption('moxycart.core_path','', MODX_CORE_PATH.'components/moxycart/').'views/msgs/';
        $file = $path.$type.'.php';
		if (is_file($file)) {
			ob_start();
			include $file;
			$_SESSION['msg'] = ob_get_clean();
			return true; 
		}
		$this->modx->log(\modX::LOG_LEVEL_ERROR, 'View file does not exist: ' .$file, __FUNCTION__,__LINE__);
		return $this->modx->lexicon('view_not_found', array('file'=> 'views/msgs/'.$type.'.php'));

    }

    /**
     * Used to toggle sort parameters in column headers
     * 
     * @param string $column name
     * @param string $base_url 
     * @return string
     */
    public static function toggle($column,$base_url='?') {
        if (isset($_GET['sort']) && $_GET['sort'] == $column) {
            if (isset($_GET['dir']) && $_GET['dir'] == 'ASC') {
                return $base_url . '&sort='.$column.'&dir=DESC';
            }
        }
        return $base_url . '&sort='.$column.'&dir=ASC';
    }
    
    /**
     * Gotta look up the URL of our CMP and its actions

     * @param string $class of one of our controllers
     * @param string $method default: index
     * @param array any optional arguments, e.g. array('action'=>'children','parent'=>123)
     * @return string
     */
    public static function url($class='',$method='index',$args=array()) {
        // future: pass as args:
        $namespace='moxycart';
        $controller='index';
        $url = MODX_MANAGER_URL;
        if ($Action = static::$x->getObject('modAction', array('namespace'=>$namespace,'controller'=>$controller))) {
            $url .= '?a='.$Action->get('id');
            if ($class && $method) {
                $url .= '&class='.$class.'&method='.$method;
                if ($args) {
                    foreach ($args as $k=>$v) {
                        $url.='&'.$k.'='.$v;
                    }
                }
            }
        }
        return $url;
    }    

    /**
     * Gotta look up the URL of a regular page (a function in the PageController)

     * @param string $page name of a function inside PageController (without get/post prefix)
     * @param array any optional arguments, e.g. array('action'=>'children','parent'=>123)
     * @return string
     */
    public static function page($page,$args=array()) {
        return self::url('page',$page,$args);
    }
        
}