<?php
/**
 * MODX 2.3.x
 * Groan... this redundant file "catches" requests that do not get handled by the main index.class.php...
 * still figuring out the routing for CMPS in 2.3...
 *
 * This is what MODX calls our "base controller". Per MODx parlance, this file must reside in the 
 * directory defined as the namespace's core path.  After that tip-of-the-hat politeness to the MODX
 * "rules", we head off the map on our own custom path: all manager requests to our own classes (see 
 * the BaseController.php).
 *
 * ARCHITECTURE: 
 *
 * The Page controller generates HTML pages whereas all other controllers generate JSON responses,
 * whereas all others (which extend the APIController) are meant for API interactions only (e.g. post
 * back data etc)
 *
 * This is not a textbook example, but the goal is to have a mostly REST based interface for easier
 * testing. (as opposed to JSON responses generated by the other controllers).  The reason is 
 * testability: most of the manager app can be tested by $scriptProperties in, JSON out.  The HTML 
 * pages generated by this controller end up being static HTML pages (well... ideally, anyway). 
 *
 * See http://stackoverflow.com/questions/10941249/separate-rest-json-api-server-and-client 
 *
 * FUTURE:
 * Static pages. A manager page gets a JSON variable generated for it (from an API request)
 * containing all the data it needs: e.g. a record (e.g. a Field record) or for a product, 
 * it would include all related data in whatever detail the request requires. 
 * 
 * JSON data should be formatted in *exactly* the format that it should be 
 * submitted in (garbage in, garbage out: no sleight of hand or restructuring of the JSON or twerking 
 * by the indexedToRecordset() function after submission).
 *
 * The HTML page should include the appropriate Javascript to populate the form and format any records
 * (e.g. using Handlebars). Dynamic editing of any parts of the data should not be concerned about 
 * the _names_ of the HTML field elements: instead all manipulations should change the source JSON 
 * directly, e.g. 
 *      moxycart.product.asset[asset_id].title = "new title" 
 *      instead of:
 *      jQuery('#arbitrary_label_'+asset_id).val("new title")
 *
 * The trickiest part about this is handling the custom fields and the fact that they trigger a form element to 
 * be generated.  It's like a snake eating its own tail...
 *
 * JSON REST API
 * 
 * Responses follow jSend guidelines: http://labs.omniti.com/labs/jsend
 *
 * ROUTING:
 *
 * I'm overriding a fair number of the modExtraManagerController functions there to support
 * custom routing.  By overriding the getInstance() method I am abandoning 
 * the somewhat limited MODX convention of mapping the &action URL parameter to a controller class
 * and instead I'm organizing requests as follows:
 *
 *  &class = classname of the controller class. 
 *  &method = base name of the method being called, default is "index"
 *
 * If POST data is detected, "post" is prepended to the method name; otherwise "get" is prepended.
 * Thus the mapping behaves like this:
 *
 * URL: /index.php?a=xxx&class=product&method=find   
 *      maps to :                       ProductController->getFind() 
 *      OR if $_POST data is present :  ProductController->postFind()  
 *
 * This allows for cleaner classnames and the ability to support dynamic routing and 404s.
 *
 * @package moxycart
 */

// Gotta do this here because we don't have a reliable event for this. 
require_once dirname(dirname(dirname(__FILE__))) .'/vendor/autoload.php';
// MoxycartIndexManagerController is used when the namespace and action are read correctly...
// MODX looks for IndexManagerController when the search hits a snag somehow
class IndexManagerController extends \Moxycart\BaseController {

    public static $errors = array();

    /**
     * This acts as a class loader.  Beware the difficulties with testing with the "new" keyword!!!
     * See composer.json's autoload section: Controller classes should be found in the controllers/ directory
     * We ignore the incoming $className here and instead fallback to our own mapping which follows the
     * pattern : \Moxycart\{$Controller_Class_Slug}Controller
     * We can't override the Base controller constructor because this loops back onto it.
     *
     * @param modX   $modx
     * @param string $className (ignored, instead we look to $_REQUEST['class'])
     * @param        array      array config
     *
     * @throws Exception
     * @internal param \object \modX $instance
     * @return instance of a controller object
     */
    public static function getInstanceDeprecated(\modX &$modx, $className, array $config = array()) {
//        $modx->setLogLevel(3);
        $config['method'] = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : 'index';

        $class = (isset($_REQUEST['class'])) ? $_REQUEST['class'] : 'Page'; // Default Controller
        
        if (!is_scalar($class)) {
            throw new \Exception('Invalid data type for class');
        }

        $config['controller_url'] = self::url();
        $config['core_path'] = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
        $config['assets_url'] = $modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');

        $config['assman_core_path'] = $modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
        $config['assman_assets_url'] = $modx->getOption('assman.assets_url', null, MODX_ASSETS_URL.'components/assman/');

        // Load up related libs
//        print $config['assman_core_path'] .'vendor/autoload.php'; exit;
        require_once $config['assman_core_path'] .'vendor/autoload.php';

        // If you don't do this, the $_POST array will seem to be populated even during normal GET requests.
        unset($_POST['HTTP_MODAUTH']);
        // Function names are not case sensitive
        if ($_FILES || !empty($_POST)) {
            unset($_POST['_moxycart']);
            $config['method'] = 'post'.ucfirst($config['method']);
        }
        else {
            $config['method'] = 'get'.ucfirst($config['method']);
        }
        // Classnames are not case-sensitive, but since it triggers the autoloader,
        // we need to manipulate it because some environments are case-sensitive.
        $class = '\\Moxycart\\'.ucfirst(strtolower($class)).'Controller';

        // Override on error
        if (!class_exists($class)) {
            $modx->log(\modX::LOG_LEVEL_ERROR,'[moxycart] class not found: '.$class,'',__FUNCTION__,__FILE__,__LINE__);            
            $class = '\\Moxycart\\ErrorController';
            $config['method'] = 'get404';
        }

        $modx->log(\modX::LOG_LEVEL_INFO,'[moxycart] Instantiating '.$class.' with config '.print_r($config,true),'',__FUNCTION__,__FILE__,__LINE__);
        
        // See Base::render() for how requests get handled.          
        if (!self::_checkSettings($modx)) {
            $class = '\\Moxycart\\ErrorController';
            $config['method'] = 'getInstall';
            $config['errors'] = self::$errors;
            return new $class($modx,$config);
        }
        
        return new $class($modx,$config);

    }
    
    /**
     * Ensure that our local settings are valid.
     *      - moxycart.domain
     *      - moxycart.api_key
     */
    private static function _checkSettings(\modX &$modx) {
        $errors = array(); // collect 'em all!
        $valid_domain = filter_var($modx->getOption('moxycart.domain'), FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
        if(!$valid_domain) {
            self::$errors[] = 'moxycart.domain must contain a valid URL';
        }
        $api_key = $modx->getOption('moxycart.api_key');
        if(empty($api_key)) {
            self::$errors[] = 'moxycart.api_key cannot be empty';
        }
        elseif(strlen($api_key) < 60) {
            self::$errors[] = 'moxycart.api_key is too short to be a Foxycart API key.';        
        }
        elseif (substr($api_key, 0, 6) != 'm42Ccf') {
            self::$errors[] = 'moxycart.api_key must begin with "m42Ccf"';
        }
        
        if(!$modx->getOption('friendly_urls') ) { // || !$modx->getOption('use_alias_path')) {
            self::$errors[] = 'friendly_urls must be enabled for Moxycart to work.';
        }
        
        if (!empty(self::$errors)) {
            return false;
        }
        else {
            return true;
        }
    }

    
}
/*EOF*/
