<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController (see index.class.php)
 */
namespace Moxycart;

require_once MODX_CORE_PATH.'model/modx/modmanagercontroller.class.php'; 
class FieldController extends BaseController {
    public $loadHeader = false;
    public $loadFooter = false;
    public $loadBaseJavascript = false; // GFD... this can't be set at runtime.
    
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function getIndex(array $scriptProperties = array()) {
        return 'Index.';
    }
        
}
/*EOF*/