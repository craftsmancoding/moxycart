<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/update.class.php';

class StoreUpdateManagerController extends ResourceUpdateManagerController {
    public $resource;

    // Copied from parent
    public function loadCustomCssJs() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        $this->addJavascript($mgrUrl.'assets/modext/util/datetime.js');
        $this->addJavascript($mgrUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
        $this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
        $this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
        $this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
        //$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
        $this->addJavascript($mgrUrl.'assets/modext/sections/resource/create.js');
        $this->addJavascript($assetsUrl.'components/moxycart/js/moxycart.js');        
        // 
        $this->addJavascript($assetsUrl.'components/moxycart/js/modx.panel.update_resource.js');
//        $this->addJavascript($mgrUrl.'assets/modext/sections/resource/create.js');
        $this->addHtml("
        <script type='text/javascript'>
/**
 * Loads the create resource page copied from manager/assets/modext/sections/resource/create.js
 * 
 * @class MODx.page.CreateResource
 * @extends MODx.Component
 * @param {Object} config An object of config properties
 * @xtype modx-page-resource-create
 */
MODx.page.CreateResource = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        url: MODx.config.connectors_url+'resource/index.php'
        ,formpanel: 'modx-panel-resource'
        ,id: 'modx-page-update-resource'
        ,which_editor: 'none'
        ,action: 'create'
    	,actions: {
            'new': MODx.action['resource/create']
            ,edit: MODx.action['resource/update']
            ,cancel: MODx.action['welcome']
        }
    	,buttons: this.getButtons(config)
    	,loadStay: true
        ,components: [{
            xtype: config.panelXType || 'modx-panel-resource'
            ,renderTo: config.panelRenderTo || 'modx-panel-resource-div'
            ,resource: 0
            ,record: config.record
            ,access_permissions: config.access_permissions
            ,publish_document: config.publish_document
            ,show_tvs: config.show_tvs
            ,mode: config.mode
            ,url: config.url
        }]
    });
    MODx.page.CreateResource.superclass.constructor.call(this,config);
};
Ext.extend(MODx.page.CreateResource,MODx.Component,{
    getButtons: function(cfg) {
        var btns = [];
        if (cfg.canSave == 1) {
            btns.push({
                process: 'create'
                ,id: 'modx-abtn-save'
                ,text: _('save')
                ,method: 'remote'
                ,checkDirty: true
                ,keys: [{
                    key: MODx.config.keymap_save || 's'
                    ,ctrl: true
                }]
            });
            btns.push('-');
        }
        btns.push({
            process: 'cancel'
            ,text: _('cancel')
            ,id: 'modx-abtn-cancel'
            ,params: { a: MODx.action['welcome'] }
        });
        btns.push('-');
        btns.push({
            text: _('help_ex')
            , handler:  function(b) {
                var url = 'http://craftsmancoding.com/'; //MODx.config.help_url;
                if (!url) { return false; }
                MODx.helpWindow = new Ext.Window({
            title: _('help')
            ,width: 850
            ,height: 500
            ,resizable: true
            ,maximizable: true
            ,modal: false
            ,layout: 'fit'
            ,html: '<iframe src=\"' + url + '\" width=\"100%\" height=\"100%\" frameborder=\"0\"></iframe>'
        });
        MODx.helpWindow.show(b);
        return true;
    }
    ,id: 'modx-abtn-help'
    });
    return btns;
    }
});
Ext.reg('modx-page-resource-create',MODx.page.CreateResource);        
        </script>");
        //        Moxycart.assets_url = "'.$assetsUrl.'";
         
        $this->addHtml('
        <script type="text/javascript">
        // <![CDATA[
        Moxycart.assets_url = "'.$assetsUrl.'";
        MODx.config.publish_document = "'.$this->canPublish.'";
        MODx.onDocFormRender = "'.$this->onDocFormRender.'";
        MODx.ctx = "'.$this->ctx.'";
        Ext.onReady(function() {
            MODx.load({
                xtype: "modx-page-resource-create"
                ,record: '.$this->modx->toJSON($this->resourceArray).'
                ,publish_document: "'.$this->canPublish.'"
                ,canSave: "'.($this->modx->hasPermission('save_document') ? 1 : 0).'"
                ,show_tvs: '.(!empty($this->tvCounts) ? 1 : 0).'
                ,mode: "create"
            });
        });
        // ]]>
        </script>');
        /* load RTE */
        $this->loadRichTextEditor();

        $this->addCss($assetsUrl.'components/moxycart/css/mgr.css');

    }

    
    
    public function getLanguageTopics() {
        return array('resource','moxycart:default');
    }
    /**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle() {
        return $this->modx->lexicon('container_new');
    }
    /**
     * Used to set values on the resource record sent to the template for derivative classes
     *
     * @return void
     */
    public function prepareResource() {
/*
        $settings = $this->resource->getProperties('moxycart');
        if (empty($settings)) $settings = array();
        
        $defaultContainerTemplate = $this->modx->getOption('moxycart.default_container_template',$settings,false);
        if (empty($defaultContainerTemplate)) {
            // @var modTemplate $template 
            $template = $this->modx->getObject('modTemplate',array('templatename' => 'sample.ArticlesContainerTemplate'));
            if ($template) {
                $defaultContainerTemplate = $template->get('id');
            }
        }
        $this->resourceArray['template'] = $defaultContainerTemplate;

        $defaultArticleTemplate = $this->modx->getOption('moxycart.default_moxycart_template',$settings,false);
        if (empty($defaultArticleTemplate)) {
            // @var modTemplate $template
            $template = $this->modx->getObject('modTemplate',array('templatename' => 'sample.ArticleTemplate'));
            if ($template) {
                $defaultArticleTemplate = $template->get('id');
            }
        }
        $this->resourceArray['setting_moxycartTemplate'] = $defaultArticleTemplate;

        foreach ($settings as $k => $v) {
            $this->resourceArray['setting_'.$k] = $v;
        }
*/
    }    
}