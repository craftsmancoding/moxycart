<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController.
 */
class MoxycartGetImageManagerController extends MoxycartManagerController {
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
        unset($scriptProperties['a']);
        unset($scriptProperties['action']);
        if ($this->modx->getOption('image_navigate',$scriptProperties) == 1) {
            $args['limit'] = 1;
            $Image = $this->Moxycart->json_images($args,true);
      
            if ($Image['total'] == 0) {
                return 'Error loading image. '.print_r($scriptProperties,true);
            }
            $data = $Image['results'][0];
            return $this->_load_view('form_image_update.php',$data);
        }
        $id = (int) $this->modx->getOption('image_id', $scriptProperties);
        $Image = $this->modx->getObject('Image',$id);
        if (!$Image) {
            return 'Error loading image. '.print_r($scriptProperties,true);
        }
        $data = $Image->toArray();
        $data['action'] = $this->action;
        return $this->_load_view('product_image.php',$data);
    }
    /**
     * The pagetitle to put in the <title> attribute.
     * @return null|string
     */
    public function getPageTitle() {
        return 'Get Image';
    }
    /**
     * Register needed assets. Using this method, it will automagically
     * combine and compress them if that is enabled in system settings.
     */
    public function loadCustomCssJs() {
        $this->addCss($this->assets_url.'css/moxycart.css');
        $this->addJavascript($this->assets_url.'js/currencies.js');
        $this->addJavascript($this->assets_url.'js/RowEditor.js');
//        $this->addLastJavascript($this->assets_url.'url/to/some/javascript_load_last.js');
        $this->addHtml('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
    		Ext.onReady(function() {   		
    			renderManageCurrencies();
    		});
    		</script>');
    }
}
/*EOF*/