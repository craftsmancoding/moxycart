<?php
// Required: or require this on the OnInitCulture event
require_once dirname(__FILE__) .'/vendor/autoload.php';
/**
 * This is the "base controller".
 *
 * Per MODx parlance, this file must reside in the directory defined as the namespace's core path.
 * I'm overriding a fair number of the modExtraManagerController functions here to support
 * my own custom routing within the manager.  By overriding the getInstance() method I am abandoning 
 * the somewhat limited MODX convention of mapping the &action URL parameter to a controller class
 * and instead I'm organizing requests as follows:
 *
 *  &class = classname of the controller class. 
 *  &method = base name of the method being called, default is "index"
 *
 * If POST data is detected, "post" is prepended to the method name; otherwise "get" is prepended.
 * Thus the mapping behaves like this:
 *
 * /index.php?a=xxx&class=product&method=find
 *
 * This allows for cleaner classnames and the ability to support dynamic routing and 404s.
 *
 */
class IndexManagerController extends \Moxycart\Controller\Base {

    /**
     * This acts as a class loader.  Beware the "new" keyword!!!
     * See composer.json's autoload section: Moxycart\Controller class should be found in the controllers/ directory
     * We essentially ignore the incoming $className here and instead fallback to our own mapping.
     * We can't override the Base controller constructor because this loops back onto it.
     *
     * @param modX
     * @param string base class name (no namespacing!)
     * @param array config
     * @return instance
     */
    public static function getInstance(\modX &$modx, $className, array $config = array()) {
        //print_r($config); exit;
        $config['method'] = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : 'index';
        $class = (isset($_REQUEST['class'])) ? $_REQUEST['class'] : 'Main';
        
        if (!$config['class']) {
            // ?? 404   
        }
        $class = '\\Moxycart\\Controller\\'.$class;
//print $class .'<br/>'. __FILE__.':'.__LINE__;exit;
/*
        if (!class_exists($class)) {
            $modx->log(\modX::LOG_LEVEL_ERROR,'[moxycart] Invalid class name '.$class,'',__FUNCTION__,__FILE__,__LINE__);        
            // 404
            print '404!'; 
            exit;
        }
*/
        
        if (!empty($_POST)) {
            $config['method'] = 'post'.ucfirst($config['method']);
        }
        else {
            $config['method'] = 'get'.ucfirst($config['method']);
        }
        
        $config['core_path'] = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
        $config['assets_url'] = $modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');

        $modx->log(\modX::LOG_LEVEL_DEBUG,'[moxycart] Instantiating '.$class.' with config '.print_r($config,true),'',__FUNCTION__,__FILE__,__LINE__);        
        
        // See Base::render() for how requests get handled.        
        return new $class($modx,$config);

    }

    /**
     * Return the class name of a controller given the action
     * @static
     * @param string $action The action name, eg: "home" or "create"
     * @param string $namespace The namespace of the Exra
     * @param string $postFix The string to postfix to the class name
     * @return string A full class name of the controller class
     */
/*
    public static function getControllerClassName($action,$namespace = '',$postFix = 'ManagerController') {
        $className = explode('/',$action);
        $o = array();
        foreach ($className as $k) {
            $o[] = ucfirst(str_replace(array('.','_','-'),'',$k));
        }
        return ucfirst($namespace).implode('',$o).$postFix;
    }
*/

    /**
     * Defines the name or path to the default controller to load.
     * @return string
     */
/*
    public static function getDefaultController() {
        return 'home';
    }
*/
    public function process(array $scriptProperties = array()) {
        return 'Testing...';
    }
}
/*EOF*/