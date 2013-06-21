
Moxycart.window.MoxycartImport = function(config) {
    config = config || {};
    this.ident = config.ident || 'arimp'+Ext.id();
    Ext.applyIf(config,{
        title: _('moxycart.moxycart_import')
        ,id: this.ident
        ,height: 150
        ,width: '75%'
        ,minWidth: 650
        ,url: Moxycart.connector_url
        ,action: 'container/import'
        ,fileUpload: true
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,value: MODx.request.id
        },{
            xtype: 'combo'
            ,store: [['MODX','MODX'],['WordPress','WordPress'],['Blogger','Blogger']]
            ,name: 'service'
            ,hiddenName: 'service'
            ,fieldLabel: _('moxycart.import_service')
            ,forceSelection: true
            ,editable: false
            ,triggerAction: 'all'
            ,id: this.ident+'-service'
            ,value: 'MODX'
            ,anchor: '100%'
            ,listeners: {
                'select':{fn:this.changeService,scope:this}
            }
        },{
            xtype: MODx.expandHelp ? 'label' : 'hidden'
            ,forId: this.ident+'-service'
            ,html: _('moxycart.import_service_desc')
            ,cls: 'desc-under'

        },{
            xtype: 'moxycart-panel-import-MODX'
        },{
            xtype: 'moxycart-panel-import-WordPress'
            ,hidden: true
        },{
            xtype: 'moxycart-panel-import-Blogger'
            ,hidden: true
        }]
    });
    Moxycart.window.MoxycartImport.superclass.constructor.call(this,config);
    this.on('activate',function() {
        Ext.getCmp(this.activeOptions).hide();
        Ext.getCmp('moxycart-options-MODX').show();
        this.activeOptions = 'moxycart-options-MODX';
    },this);
};
Ext.extend(Moxycart.window.MoxycartImport,MODx.Window,{
    activeOptions: 'moxycart-options-MODX'
    ,changeService: function(cb,s) {
        var nv = cb.getValue();
        var op = Ext.getCmp(this.activeOptions);

        var nop = 'moxycart-options-'+nv;
        var p = Ext.getCmp(nop);
        if (p) {
            op.hide();
            p.show();
            this.activeOptions = nop;
        }
        return true;
    }
});
Ext.reg('moxycart-window-import',Moxycart.window.MoxycartImport);

Moxycart.panel.ImportOptionsWordPress = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'moxycart-options-WordPress'
        ,xtype: 'fieldset'
        ,title: _('moxycart.import_options')
        ,defaults: {
            msgTarget: 'under'
        }
        ,items: [{
            xtype: 'textfield'
            ,inputType: 'file'
            ,name: 'wp-file'
            ,fieldLabel: _('moxycart.import_wp_file')
            ,id: this.ident+'-wp-file'
            ,anchor: '98%'
        },{
            xtype: MODx.expandHelp ? 'label' : 'hidden'
            ,forId: this.ident+'-wp-file'
            ,html: _('moxycart.import_wp_file_desc')
            ,cls: 'desc-under'
        },{
            xtype: 'textfield'
            ,name: 'wp-file-server'
            ,fieldLabel: _('moxycart.import_wp_file_server')
            ,description: MODx.expandHelp ? '' : _('moxycart.import_wp_file_server')
            ,id: this.ident+'-wp-file-server'
            ,anchor: '98%'
            ,value: '{core_path}import/'
        },{
            xtype: MODx.expandHelp ? 'label' : 'hidden'
            ,forId: this.ident+'-wp-file-server'
            ,html: _('moxycart.import_wp_file_server_desc')
            ,cls: 'desc-under'
        }]
    });
    Moxycart.panel.ImportOptionsWordPress.superclass.constructor.call(this,config);
};
Ext.extend(Moxycart.panel.ImportOptionsWordPress,Ext.form.FieldSet);
Ext.reg('moxycart-panel-import-WordPress',Moxycart.panel.ImportOptionsWordPress);


Moxycart.panel.ImportOptionsMODX = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'moxycart-options-MODX'
        ,title: _('moxycart.import_options')
        ,defaults: {
            msgTarget: 'under'
        }
        ,items: [{
            html: '<p>'+_('moxycart.import_modx_intro')+'</p>'
            ,bodyCssClass: 'moxycart-import-intro'
            ,border: false
        },{
            layout: 'column'
            ,border: false
            ,defaults: {
                layout: 'form'
                ,labelAlign: 'top'
                ,anchor: '100%'
                ,border: false
                ,labelSeparator: ''
            }
            ,items: [{
                columnWidth: .5
                ,items: [{
                    xtype: 'textfield'
                    ,fieldLabel: _('moxycart.import_modx_parents')
                    ,description: MODx.expandHelp ? '' : _('moxycart.import_modx_parents_desc')
                    ,name: 'modx-parents'
                    ,id: this.ident+'-modx-parents'
                    ,value: ''
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-modx-parents'
                    ,html: _('moxycart.import_modx_parents_desc')
                    ,cls: 'desc-under'
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('moxycart.import_modx_resources')
                    ,description: MODx.expandHelp ? '' : _('moxycart.import_modx_resources_desc')
                    ,name: 'modx-resources'
                    ,id: this.ident+'-modx-resources'
                    ,value: ''
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-modx-resources'
                    ,html: _('moxycart.import_modx_resources_desc')
                    ,cls: 'desc-under'
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('moxycart.import_modx_tagsField')
                    ,description: MODx.expandHelp ? '' : _('moxycart.import_modx_tagsField_desc')
                    ,name: 'modx-tagsField'
                    ,id: this.ident+'-modx-tagsField'
                    ,value: 'tv.tags'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-modx-tagsField'
                    ,html: _('moxycart.import_modx_tagsField_desc')
                    ,cls: 'desc-under'
                }]
            },{
                columnWidth: .5
                ,items: [{
                    xtype: 'modx-combo-template'
                    ,fieldLabel: _('moxycart.import_modx_template')
                    ,description: MODx.expandHelp ? '' : _('moxycart.import_modx_template_desc')
                    ,name: 'modx-template'
                    ,hiddenName: 'modx-template'
                    ,id: this.ident+'-modx-template'
                    ,value: ''
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-modx-template'
                    ,html: _('moxycart.import_modx_template_desc')
                    ,cls: 'desc-under'
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('moxycart.import_modx_commentsThreadNameFormat')
                    ,description: MODx.expandHelp ? '' : _('moxycart.import_modx_commentsThreadNameFormat_desc')
                    ,name: 'modx-commentsThreadNameFormat'
                    ,id: this.ident+'-modx-commentsThreadNameFormat'
                    ,value: 'blog-post-[[*id]]'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-modx-commentsThreadNameFormat'
                    ,html: _('moxycart.import_modx_commentsThreadNameFormat_desc')
                    ,cls: 'desc-under'
                },{
                    xtype: 'checkbox'
                    ,boxLabel: _('moxycart.import_modx_hidemenu')
                    ,description: MODx.expandHelp ? '' : _('moxycart.import_modx_hidemenu_desc')
                    ,name: 'modx-hidemenu'
                    ,id: this.ident+'-modx-hidemenu'
                    ,inputValue: 1
                    ,checked: false
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-modx-hidemenu'
                    ,html: _('moxycart.import_modx_hidemenu_desc')
                    ,cls: 'desc-under'
                },{
                    xtype: 'checkbox'
                    ,boxLabel: _('moxycart.import_modx_unpublished')
                    ,description: MODx.expandHelp ? '' : _('moxycart.import_modx_unpublished_desc')
                    ,name: 'modx-unpublished'
                    ,id: this.ident+'-modx-unpublished'
                    ,inputValue: 1
                    ,checked: true
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-modx-unpublished'
                    ,html: _('moxycart.import_modx_unpublished_desc')
                    ,cls: 'desc-under'
                },{
                    xtype: 'checkbox'
                    ,boxLabel: _('moxycart.import_modx_change_template')
                    ,description: MODx.expandHelp ? '' : _('moxycart.import_modx_change_template_desc')
                    ,name: 'modx-change-template'
                    ,id: this.ident+'-modx-change-template'
                    ,inputValue: 1
                    ,checked: true
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-modx-change-template'
                    ,html: _('moxycart.import_modx_change_template_desc')
                    ,cls: 'desc-under'
                }]
            }]
        }]
    });
    Moxycart.panel.ImportOptionsMODX.superclass.constructor.call(this,config);
};
Ext.extend(Moxycart.panel.ImportOptionsMODX,Ext.form.FieldSet);
Ext.reg('moxycart-panel-import-MODX',Moxycart.panel.ImportOptionsMODX);

Moxycart.panel.ImportOptionsBlogger = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'moxycart-options-Blogger'
        ,xtype: 'fieldset'
        ,title: _('moxycart.import_options')
        ,defaults: {
            msgTarget: 'under'
        }
        ,items: [{
            xtype: 'textfield'
            ,inputType: 'file'
            ,name: 'blogger-file'
            ,fieldLabel: _('moxycart.import_blogger_file')
            ,id: this.ident+'-blogger-file'
            ,anchor: '98%'
        },{
            xtype: MODx.expandHelp ? 'label' : 'hidden'
            ,forId: this.ident+'-blogger-file'
            ,html: _('moxycart.import_blogger_file_desc')
            ,cls: 'desc-under'
        },{
            xtype: 'textfield'
            ,name: 'blogger-file-server'
            ,fieldLabel: _('moxycart.import_blogger_file_server')
            ,description: MODx.expandHelp ? '' : _('moxycart.import_blogger_file_server')
            ,id: this.ident+'-blogger-file-server'
            ,anchor: '98%'
            ,value: '{core_path}import/'
        },{
            xtype: MODx.expandHelp ? 'label' : 'hidden'
            ,forId: this.ident+'-blogger-file-server'
            ,html: _('moxycart.import_blogger_file_server_desc')
            ,cls: 'desc-under'
        }]
    });
    Moxycart.panel.ImportOptionsBlogger.superclass.constructor.call(this,config);
};
Ext.extend(Moxycart.panel.ImportOptionsBlogger,Ext.form.FieldSet);
Ext.reg('moxycart-panel-import-Blogger',Moxycart.panel.ImportOptionsBlogger);