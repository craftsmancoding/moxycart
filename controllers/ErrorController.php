<?php
/**
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart 
 */
namespace Moxycart;
class ErrorController extends BaseController {
    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false; // GFD... this can't be set at runtime.

    function __construct(\modX &$modx,$config = array()) {
        parent::__construct($modx,$config);
        static::$x =& $modx;
        $this->modx->regClientCSS($this->config['assets_url'].'css/moxycart.css');        
    }

    /**
     * Any specific processing we want to do here. Return a string of html.
     *
     * @param array $scriptProperties
     *
     * @return rendered
     */
    public function get404(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));
        $this->scriptProperties['_nolayout'] = true;
        $this->setPlaceholders($scriptProperties);    
        return $this->fetchTemplate('error/404.php');
    }

    /**
     * Check important settings of moxycart moxycart.api_key and moxycart.domain
     * they must have value/valid value
     *
     * @param array $scriptProperties
     *
     * @return rendered
     */
    public function getInstall(array $scriptProperties = array()) {
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Controller: ' .__CLASS__.'::'.__FUNCTION__.' data: '.print_r($scriptProperties,true));  
        $this->scriptProperties['_nolayout'] = true;
        $this->setPlaceholders($scriptProperties); 
        $this->setPlaceholder('errors', $this->config['errors']);
        return $this->fetchTemplate('error/install.php');
    }
}
/*EOF*/