var Moxycart = function(config) {
    config = config || {};
    Moxycart.superclass.constructor.call(this,config);
};
Ext.extend(Moxycart,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}
    ,connector_url: ''
});
Ext.reg('Moxycart',Moxycart);

Moxycart = new Moxycart();


Moxycart.combo.PublishStatus = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: [[1,_('published')],[0,_('unpublished')]]
        ,name: 'published'
        ,hiddenName: 'published'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Moxycart.combo.PublishStatus.superclass.constructor.call(this,config);
};
Ext.extend(Moxycart.combo.PublishStatus,MODx.combo.ComboBox);
Ext.reg('moxycart-combo-publish-status',Moxycart.combo.PublishStatus);

Moxycart.combo.FilterStatus = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: [['',_('moxycart.all')],['published',_('published')],['unpublished',_('unpublished')],['deleted',_('deleted')]]
        ,name: 'filter'
        ,hiddenName: 'filter'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
        ,emptyText: _('moxycart.filter_ellipsis')
    });
    Moxycart.combo.FilterStatus.superclass.constructor.call(this,config);
};
Ext.extend(Moxycart.combo.FilterStatus,MODx.combo.ComboBox);
Ext.reg('moxycart-combo-filter-status',Moxycart.combo.FilterStatus);

Moxycart.PanelSpacer = { html: '<br />' ,border: false, cls: 'moxycart-panel-spacer' };