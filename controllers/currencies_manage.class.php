<?php
/**
 * The name of the controller is based on the action (home) and the
 * namespace. This home controller is loaded by default because of
 * our IndexManagerController.
 */
class MoxycartCurrenciesManageManagerController extends MoxycartManagerController {
    /** @var bool Set to false to prevent loading of the header HTML. */
    public $loadHeader = true;
    /** @var bool Set to false to prevent loading of the footer HTML. */
    public $loadFooter = true;
    /** @var bool Set to false to prevent loading of the base MODExt JS classes. */
    public $loadBaseJavascript = true;
    /**
     * Any specific processing we want to do here. Return a string of html.
     * @param array $scriptProperties
     */
    public function process(array $scriptProperties = array()) {
        //return '<pre>'.print_r($this->config,true).'</pre>';
        return '<h2 class="modx-page-header">Manage Currencies</h2><div id="moxycart_canvas"></div>
        <br/><a href="'.$this->getUrl('home').'">Back</a>';
    }
    /**
     * The pagetitle to put in the <title> attribute.
     * @return null|string
     */
    public function getPageTitle() {
        return 'Manage Currencies';
    }
    /**
     * Register needed assets. Using this method, it will automagically
     * combine and compress them if that is enabled in system settings.
     */
    public function loadCustomCssJs() {
        $this->addCss($this->assets_url.'components/moxycart/css/moxycart.css');
        $this->addJavascript($this->assets_url.'components/moxycart/js/currencies.js');
        $this->addJavascript($this->assets_url.'components/moxycart/js/RowEditor.js');
        $this->addLastJavascript($this->assets_url.'url/to/some/javascript_load_last.js');
        $this->addHtml('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
    		Ext.onReady(function() {   		
    			renderManageCurrencies();
    		});
    		</script>');
    }
}
/*EOF*/