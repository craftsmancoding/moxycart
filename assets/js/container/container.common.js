Moxycart.panel.ContainerAdvancedSettings = function(config) {
    config = config || {};
    var oc = {
        'change':{fn:MODx.fireResourceFormChange}
        ,'select':{fn:MODx.fireResourceFormChange}
    };
    var twitterAuthedLexiconKey = config.record && !Ext.isEmpty(config.record.setting_notifyTwitterAccessToken) ? 'moxycart.setting.notifyTwitter_desc' : 'moxycart.setting.notifyTwitter_notyet_desc';
    Ext.applyIf(config,{
        id: 'moxycart-panel-container-advanced-settings'
        ,border: false
        ,plain: true
        ,deferredRender: false
        ,anchor: '97%'
        ,items: [{
            title: _('moxycart.settings_general')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'combo-boolean'
                ,name: 'setting_updateServicesEnabled'
                ,hiddenName: 'setting_updateServicesEnabled'
                ,id: 'moxycart-setting-updateServicesEnabled'
                ,fieldLabel: _('moxycart.setting.updateServicesEnabled')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.updateServicesEnabled_desc')
                ,anchor: '40%'
                ,value: true
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-updateServicesEnabled'
                ,html: _('moxycart.setting.updateServicesEnabled_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'menuindex'
                ,id: 'moxycart-menuindex'
                ,fieldLabel: _('resource_menuindex')
                ,description: MODx.expandHelp ? '' : _('resource_menuindex_help')
                ,allowNegative: false
                ,allowDecimals: false
                ,width: 120
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-menuindex'
                ,html: _('resource_menuindex_help')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'longtitle'
                ,id: 'moxycart-longtitle'
                ,fieldLabel: _('resource_longtitle')
                ,description: MODx.expandHelp ? '' : _('resource_longtitle_help')
                ,anchor: '100%'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-longtitle'
                ,html: _('resource_longtitle_help')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_moxycartRichtext'
                ,hiddenName: 'setting_moxycartRichtext'
                ,id: 'moxycart-richtext'
                ,fieldLabel: _('resource_richtext')
                ,description: MODx.expandHelp ? '' : _('resource_richtext_help')
                ,width: 120
                ,listeners: oc
                ,value: 1
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-richtext'
                ,html: _('resource_richtext_help')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_sortBy'
                ,id: 'moxycart-setting-sortBy'
                ,fieldLabel: _('moxycart.setting.sortBy')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.sortBy_desc')
                ,anchor: '100%'
                ,value: 'publishedon'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-sortBy'
                ,html: _('moxycart.setting.sortBy_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_sortDir'
                ,id: 'moxycart-setting-sortDir'
                ,fieldLabel: _('moxycart.setting.sortDir')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.sortDir_desc')
                ,anchor: '100%'
                ,value: 'DESC'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-sortDir'
                ,html: _('moxycart.setting.sortDir_desc')
                ,cls: 'desc-under'
                ,listeners: oc

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_archivesIncludeTVs'
                ,hiddenName: 'setting_archivesIncludeTVs'
                ,id: 'moxycart-setting-archivesIncludeTVs'
                ,fieldLabel: _('moxycart.setting.archivesIncludeTVs')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.archivesIncludeTVs_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-archivesIncludeTVs'
                ,html: _('moxycart.setting.archivesIncludeTVs_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_includeTVsList'
                ,id: 'moxycart-setting-includeTVsList'
                ,fieldLabel: _('moxycart.setting.includeTVsList')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.includeTVsList_desc')
                ,anchor: '100%'
                ,value: ''
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-includeTVsList'
                ,html: _('moxycart.setting.includeTVsList_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_archivesProcessTVs'
                ,hiddenName: 'setting_archivesProcessTVs'
                ,id: 'moxycart-setting-archivesProcessTVs'
                ,fieldLabel: _('moxycart.setting.archivesProcessTVs')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.archivesProcessTVs_desc')
                ,anchor: '30%'
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-archivesProcessTVs'
                ,html: _('moxycart.setting.archivesProcessTVs_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_processTVsList'
                ,id: 'moxycart-setting-processTVsList'
                ,fieldLabel: _('moxycart.setting.processTVsList')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.processTVsList_desc')
                ,anchor: '100%'
                ,value: ''
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-includeTVsList'
                ,html: _('moxycart.setting.includeTVsList_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_otherGetArchives'
                ,id: 'moxycart-setting-otherGetArchives'
                ,fieldLabel: _('moxycart.setting.otherGetArchives')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.otherGetArchives_desc')
                ,anchor: '100%'
                ,value: ''
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-otherGetArchives'
                ,html: _('moxycart.setting.otherGetArchives_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_articleUriTemplate'
                ,id: 'moxycart-setting-articleUriTemplate'
                ,fieldLabel: _('moxycart.setting.articleUriTemplate')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.articleUriTemplate_desc')
                ,anchor: '100%'
                ,value: '%Y/%m/%d/%alias/'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-articleUriTemplate'
                ,html: _('moxycart.setting.articleUriTemplate_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: _('moxycart.settings_pagination')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'numberfield'
                ,name: 'setting_moxycartPerPage'
                ,id: 'moxycart-setting-moxycartPerPage'
                ,fieldLabel: _('moxycart.setting.moxycartPerPage')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.moxycartPerPage_desc')
                ,allowNegative: false
                ,allowDecimals: false
                ,width: 120
                ,value: 10
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-moxycartPerPage'
                ,html: _('moxycart.setting.moxycartPerPage_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_pageLimit'
                ,id: 'moxycart-setting-pageLimit'
                ,fieldLabel: _('moxycart.setting.pageLimit')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pageLimit_desc')
                ,allowNegative: false
                ,allowDecimals: false
                ,width: 120
                ,value: 5
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pageLimit'
                ,html: _('moxycart.setting.pageLimit_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_pageNavTpl'
                ,id: 'moxycart-setting-pageNavTpl'
                ,fieldLabel: _('moxycart.setting.pageNavTpl')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pageNavTpl_desc')
                ,anchor: '100%'
                ,value: '<li[[+classes]]><a[[+classes]][[+title]] href="[[+href]]">[[+pageNo]]</a></li>'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pageNavTpl'
                ,html: _('moxycart.setting.pageNavTpl_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_pageActiveTpl'
                ,id: 'moxycart-setting-pageActiveTpl'
                ,fieldLabel: _('moxycart.setting.pageActiveTpl')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pageActiveTpl_desc')
                ,anchor: '100%'
                ,value: '<li[[+activeClasses]]><a[[+activeClasses:default=` class="active"`]][[+title]] href="[[+href]]">[[+pageNo]]</a></li>'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pageActiveTpl'
                ,html: _('moxycart.setting.pageActiveTpl_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_pageFirstTpl'
                ,id: 'moxycart-setting-pageFirstTpl'
                ,fieldLabel: _('moxycart.setting.pageFirstTpl')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pageFirstTpl_desc')
                ,anchor: '100%'
                ,value: '<li class="control"><a[[+classes]][[+title]] href="[[+href]]">First</a></li>'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pageFirstTpl'
                ,html: _('moxycart.setting.pageFirstTpl_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_pageLastTpl'
                ,id: 'moxycart-setting-pageLastTpl'
                ,fieldLabel: _('moxycart.setting.pageLastTpl')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pageLastTpl_desc')
                ,anchor: '100%'
                ,value: '<li class="control"><a[[+classes]][[+title]] href="[[+href]]">Last</a></li>'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pageLastTpl'
                ,html: _('moxycart.setting.pageLastTpl_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_pagePrevTpl'
                ,id: 'moxycart-setting-pagePrevTpl'
                ,fieldLabel: _('moxycart.setting.pagePrevTpl')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pagePrevTpl_desc')
                ,anchor: '100%'
                ,value: '<li class="control"><a[[+classes]][[+title]] href="[[+href]]">&lt;&lt;</a></li>'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pagePrevTpl'
                ,html: _('moxycart.setting.pagePrevTpl_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_pageNextTpl'
                ,id: 'moxycart-setting-pageNextTpl'
                ,fieldLabel: _('moxycart.setting.pageNextTpl')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pageNextTpl_desc')
                ,anchor: '100%'
                ,value: '<li class="control"><a[[+classes]][[+title]] href="[[+href]]">&gt;&gt;</a></li>'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pageNextTpl'
                ,html: _('moxycart.setting.pageNextTpl_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_pageOffset'
                ,id: 'moxycart-setting-pageOffset'
                ,fieldLabel: _('moxycart.setting.pageOffset')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pageOffset_desc')
                ,anchor: '30%'
                ,minWidth: 100
                ,allowNegative: false
                ,allowDecimals: false
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pageOffset'
                ,html: _('moxycart.setting.pageOffset_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_pageVarKey'
                ,id: 'moxycart-setting-pageVarKey'
                ,fieldLabel: _('moxycart.setting.pageVarKey')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pageVarKey_desc')
                ,anchor: '100%'
                ,value: 'page'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pageVarKey'
                ,html: _('moxycart.setting.pageVarKey_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_pageTotalVar'
                ,id: 'moxycart-setting-pageTotalVar'
                ,fieldLabel: _('moxycart.setting.pageTotalVar')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pageTotalVar_desc')
                ,anchor: '100%'
                ,value: 'total'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pageTotalVar'
                ,html: _('moxycart.setting.pageTotalVar_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_pageNavVar'
                ,id: 'moxycart-setting-pageNavVar'
                ,fieldLabel: _('moxycart.setting.pageNavVar')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.pageNavVar_desc')
                ,anchor: '100%'
                ,value: 'page.nav'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-pageNavVar'
                ,html: _('moxycart.setting.pageNavVar_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: _('moxycart.settings_archiving')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'textfield'
                ,name: 'setting_tplArchiveMonth'
                ,id: 'moxycart-setting-tplArchiveMonth'
                ,fieldLabel: _('moxycart.setting.tplArchiveMonth')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.tplArchiveMonth_desc')
                ,anchor: '100%'
                ,value: 'row'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-tplArchiveMonth'
                ,html: _('moxycart.setting.tplArchiveMonth_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_archiveListingsLimit'
                ,id: 'moxycart-setting-archiveListingsLimit'
                ,fieldLabel: _('moxycart.setting.archiveListingsLimit')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.archiveListingsLimit_desc')
                ,allowNegative: false
                ,allowDecimals: false
                ,width: 120
                ,value: 10
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-archiveListingsLimit'
                ,html: _('moxycart.setting.archiveListingsLimit_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_archiveByMonth'
                ,hiddenName: 'setting_archiveByMonth'
                ,id: 'moxycart-setting-archiveByMonth'
                ,fieldLabel: _('moxycart.setting.archiveByMonth')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.archiveByMonth_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-archiveByMonth'
                ,html: _('moxycart.setting.archiveByMonth_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_archiveCls'
                ,id: 'moxycart-setting-archiveCls'
                ,fieldLabel: _('moxycart.setting.archiveCls')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.archiveCls_desc')
                ,anchor: '100%'
                ,valie: 'arc-row'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-archiveCls'
                ,html: _('moxycart.setting.archiveCls_desc')
                ,cls: 'desc-under'
            },{
                xtype: 'textfield'
                ,name: 'setting_archiveAltCls'
                ,id: 'moxycart-setting-archiveAltCls'
                ,fieldLabel: _('moxycart.setting.archiveAltCls')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.archiveAltCls_desc')
                ,anchor: '100%'
                ,value: 'arc-row-alt'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-archiveAltCls'
                ,html: _('moxycart.setting.archiveAltCls_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_archiveGroupByYear'
                ,hiddenName: 'setting_archiveGroupByYear'
                ,id: 'moxycart-setting-archiveGroupByYear'
                ,fieldLabel: _('moxycart.setting.archiveGroupByYear')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.archiveGroupByYear_desc')
                ,anchor: '30%'
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-archiveGroupByYear'
                ,html: _('moxycart.setting.archiveGroupByYear_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_archiveGroupByYearTpl'
                ,id: 'moxycart-setting-archiveGroupByYearTpl'
                ,fieldLabel: _('moxycart.setting.archiveGroupByYearTpl')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.archiveGroupByYearTpl_desc')
                ,anchor: '100%'
                ,value: 'sample.ArchiveGroupByYear'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-archiveGroupByYearTpl'
                ,html: _('moxycart.setting.archiveGroupByYearTpl_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: _('moxycart.settings_tagging')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'textfield'
                ,name: 'setting_tplTagRow'
                ,id: 'moxycart-setting-tplTagRow'
                ,fieldLabel: _('moxycart.setting.tplTagRow')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.tplTagRow_desc')
                ,anchor: '100%'
                ,value: 'tag'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-tplTagRow'
                ,html: _('moxycart.setting.tplTagRow_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_tagsLimit'
                ,id: 'moxycart-setting-tagsLimit'
                ,fieldLabel: _('moxycart.setting.tagsLimit')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.tagsLimit_desc')
                ,allowNegative: false
                ,allowDecimals: false
                ,width: 120
                ,value: 10
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-tagsLimit'
                ,html: _('moxycart.setting.tagsLimit_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_tagCls'
                ,id: 'moxycart-setting-tagCls'
                ,fieldLabel: _('moxycart.setting.tagCls')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.tagCls_desc')
                ,anchor: '100%'
                ,value: 'tl-tag'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-tagCls'
                ,html: _('moxycart.setting.tagCls_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_tagAltCls'
                ,id: 'moxycart-setting-tagAltCls'
                ,fieldLabel: _('moxycart.setting.tagAltCls')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.tagAltCls_desc')
                ,anchor: '100%'
                ,value: 'tl-tag-alt'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-tagAltCls'
                ,html: _('moxycart.setting.tagAltCls_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: _('moxycart.settings_rss')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'textfield'
                ,name: 'setting_rssAlias'
                ,id: 'moxycart-setting-rssAlias'
                ,fieldLabel: _('moxycart.setting.rssAlias')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.rssAlias_desc')
                ,anchor: '100%'
                ,value: 'feed.rss,rss'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-rssAlias'
                ,html: _('moxycart.setting.rssAlias_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_rssItems'
                ,id: 'moxycart-setting-rssItems'
                ,fieldLabel: _('moxycart.setting.rssItems')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.rssItems_desc')
                ,width: 120
                ,value: 10
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-rssItems'
                ,html: _('moxycart.setting.rssItems_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_tplRssFeed'
                ,id: 'moxycart-setting-tplRssFeed'
                ,fieldLabel: _('moxycart.setting.tplRssFeed')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.tplRssFeed_desc')
                ,anchor: '100%'
                ,value: 'sample.MoxycartRss'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-tplRssFeed'
                ,html: _('moxycart.setting.tplRssFeed_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_tplRssItem'
                ,id: 'moxycart-setting-tplRssItem'
                ,fieldLabel: _('moxycart.setting.tplRssItem')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.tplRssItem_desc')
                ,anchor: '100%'
                ,value: 'sample.MoxycartRssItem'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-tplRssItem'
                ,html: _('moxycart.setting.tplRssItem_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: _('moxycart.settings_latest_posts')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'textfield'
                ,name: 'setting_latestPostsTpl'
                ,id: 'moxycart-setting-latestPostsTpl'
                ,fieldLabel: _('moxycart.setting.latestPostsTpl')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.latestPostsTpl_desc')
                ,anchor: '100%'
                ,value: 'sample.MoxycartLatestPostTpl'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-latestPostsTpl'
                ,html: _('moxycart.setting.latestPostsTpl_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_latestPostsLimit'
                ,id: 'moxycart-setting-latestPostsLimit'
                ,fieldLabel: _('moxycart.setting.latestPostsLimit')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.latestPostsLimit_desc')
                ,width: 120
                ,allowNegative: false
                ,allowDecimals: false
                ,value: 5
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-latestPostsLimit'
                ,html: _('moxycart.setting.latestPostsLimit_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_latestPostsOffset'
                ,id: 'moxycart-setting-latestPostsOffset'
                ,fieldLabel: _('moxycart.setting.latestPostsOffset')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.latestPostsOffset_desc')
                ,width: 120
                ,allowNegative: false
                ,allowDecimals: false
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-latestPostsOffset'
                ,html: _('moxycart.setting.latestPostsOffset_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_otherLatestPosts'
                ,id: 'moxycart-setting-otherLatestPosts'
                ,fieldLabel: _('moxycart.setting.otherLatestPosts')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.otherLatestPosts_desc')
                ,anchor: '100%'
                ,value: ''
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-latestPostsOffset'
                ,html: _('moxycart.setting.latestPostsOffset_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: _('moxycart.settings_notifications')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'combo-boolean'
                ,name: 'setting_notifyTwitter'
                ,hiddenName: 'setting_notifyTwitter'
                ,id: 'moxycart-setting-notifyTwitter'
                ,fieldLabel: _('moxycart.setting.notifyTwitter')
                ,description: MODx.expandHelp ? '' : _(twitterAuthedLexiconKey,{
                    authUrl: Moxycart.assets_url+'twitter.auth.php?container='+MODx.request.id
                })
                ,anchor: '30%'
                ,value: false
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-notifyTwitter'
                ,html: _(twitterAuthedLexiconKey,{
                    authUrl: Moxycart.assets_url+'twitter.auth.php?container='+MODx.request.id
                })
                ,cls: 'desc-under'

            },{
                xtype: 'text-password'
                ,name: 'setting_notifyTwitterConsumerKey'
                ,id: 'moxycart-setting-notifyTwitterConsumerKey'
                ,fieldLabel: _('moxycart.setting.notifyTwitterConsumerKey')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.notifyTwitterConsumerKey_desc')
                ,anchor: '100%'
                ,value: ''
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-notifyTwitterConsumerKey'
                ,html: _('moxycart.setting.notifyTwitterConsumerKey_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'text-password'
                ,name: 'setting_notifyTwitterConsumerKeySecret'
                ,id: 'moxycart-setting-notifyTwitterConsumerKeySecret'
                ,fieldLabel: _('moxycart.setting.notifyTwitterConsumerKeySecret')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.notifyTwitterConsumerKeySecret_desc')
                ,anchor: '100%'
                ,value: ''
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-notifyTwitterConsumerKeySecret'
                ,html: _('moxycart.setting.notifyTwitterConsumerKeySecret_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_notifyTwitterTpl'
                ,id: 'moxycart-setting-notifyTwitterTpl'
                ,fieldLabel: _('moxycart.setting.notifyTwitterTpl')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.notifyTwitterTpl_desc')
                ,anchor: '100%'
                ,value: ''
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-notifyTwitterTpl'
                ,html: _('moxycart.setting.notifyTwitterTpl_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_notifyTwitterTagLimit'
                ,id: 'moxycart-setting-notifyTwitterTagLimit'
                ,fieldLabel: _('moxycart.setting.notifyTwitterTagLimit')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.notifyTwitterTagLimit_desc')
                ,anchor: '30%'
                ,value: 3
                ,allowNegative: false
                ,allowDecimals: false
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-notifyTwitterTagLimit'
                ,html: _('moxycart.setting.notifyTwitterTagLimit_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'moxycart-combo-shorteners'
                ,name: 'setting_shorteningService'
                ,hiddenName: 'setting_shorteningService'
                ,id: 'moxycart-setting-shorteningService'
                ,fieldLabel: _('moxycart.setting.shorteningService')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.shorteningService_desc')
                ,anchor: '30%'
                ,value: 'tinyurl'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-shorteningService'
                ,html: _('moxycart.setting.shorteningService_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: _('moxycart.settings_comments')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsEnabled'
                ,hiddenName: 'setting_commentsEnabled'
                ,id: 'moxycart-setting-commentsEnabled'
                ,fieldLabel: _('moxycart.setting.commentsEnabled')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsEnabled_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsEnabled'
                ,html: _('moxycart.setting.commentsEnabled_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsThreaded'
                ,hiddenName: 'setting_commentsThreaded'
                ,id: 'moxycart-setting-commentsThreaded'
                ,fieldLabel: _('moxycart.setting.commentsThreaded')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsThreaded_desc')
                ,anchor: '30%'
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsThreaded'
                ,html: _('moxycart.setting.commentsThreaded_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsReplyResourceId'
                ,id: 'moxycart-setting-commentsReplyResourceId'
                ,fieldLabel: _('moxycart.setting.commentsReplyResourceId')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsReplyResourceId_desc')
                ,anchor: '30%'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsReplyResourceId'
                ,html: _('moxycart.setting.commentsReplyResourceId_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_commentsMaxDepth'
                ,id: 'moxycart-setting-commentsMaxDepth'
                ,fieldLabel: _('moxycart.setting.commentsMaxDepth')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsMaxDepth_desc')
                ,anchor: '30%'
                ,allowDecimals: false
                ,allowNegative: false
                ,value: 5
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsMaxDepth'
                ,html: _('moxycart.setting.commentsMaxDepth_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsRequirePreview'
                ,hiddenName: 'setting_commentsRequirePreview'
                ,id: 'moxycart-setting-commentsRequirePreview'
                ,fieldLabel: _('moxycart.setting.commentsRequirePreview')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsRequirePreview_desc')
                ,anchor: '30%'
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsRequirePreview'
                ,html: _('moxycart.setting.commentsRequirePreview_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_commentsCloseAfter'
                ,id: 'moxycart-setting-commentsCloseAfter'
                ,fieldLabel: _('moxycart.setting.commentsCloseAfter')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsCloseAfter_desc')
                ,anchor: '30%'
                ,allowDecimals: false
                ,allowNegative: false
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsCloseAfter'
                ,html: _('moxycart.setting.commentsCloseAfter_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsDateFormat'
                ,id: 'moxycart-setting-commentsDateFormat'
                ,fieldLabel: _('moxycart.setting.commentsDateFormat')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsDateFormat_desc')
                ,anchor: '100%'
                ,value: '%b %d, %Y at %I:%M %p'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsDateFormat'
                ,html: _('moxycart.setting.commentsDateFormat_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsAutoConvertLinks'
                ,hiddenName: 'setting_commentsAutoConvertLinks'
                ,id: 'moxycart-setting-commentsAutoConvertLinks'
                ,fieldLabel: _('moxycart.setting.commentsAutoConvertLinks')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsAutoConvertLinks_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsAutoConvertLinks'
                ,html: _('moxycart.setting.commentsAutoConvertLinks_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_commentsLimit'
                ,id: 'moxycart-setting-commentsLimit'
                ,fieldLabel: _('moxycart.setting.commentsLimit')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsLimit_desc')
                ,anchor: '30%'
                ,allowDecimals: false
                ,allowNegative: false
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsLimit'
                ,html: _('moxycart.setting.commentsLimit_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: ' &#8212; '+_('moxycart.settings_comments_display')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'textfield'
                ,name: 'setting_commentsTplComment'
                ,id: 'moxycart-setting-commentsTplComment'
                ,fieldLabel: _('moxycart.setting.commentsTplComment')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsTplComment_desc')
                ,anchor: '100%'
                ,value: 'quipComment'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsTplComment'
                ,html: _('moxycart.setting.commentsTplComment_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsTplCommentOptions'
                ,id: 'moxycart-setting-commentsTplCommentOptions'
                ,fieldLabel: _('moxycart.setting.commentsTplCommentOptions')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsTplCommentOptions_desc')
                ,anchor: '100%'
                ,value: 'quipCommentOptions'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsTplCommentOptions'
                ,html: _('moxycart.setting.commentsTplCommentOptions_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsTplComments'
                ,id: 'moxycart-setting-commentsTplComments'
                ,fieldLabel: _('moxycart.setting.commentsTplComments')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsTplComments_desc')
                ,anchor: '100%'
                ,value: 'quipComments'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsTplComments'
                ,html: _('moxycart.setting.commentsTplComments_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsTplAddComment'
                ,id: 'moxycart-setting-commentsAddComment'
                ,fieldLabel: _('moxycart.setting.commentsTplAddComment')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsTplAddComment_desc')
                ,anchor: '100%'
                ,value: 'quipAddComment'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsTplAddComment'
                ,html: _('moxycart.setting.commentsTplAddComment_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsTplLoginToComment'
                ,id: 'moxycart-setting-commentsTplLoginToComment'
                ,fieldLabel: _('moxycart.setting.commentsTplLoginToComment')
                ,description: MODx.expandHelp ? '' : _('moxycart.commentsTplLoginToComment_desc')
                ,anchor: '100%'
                ,value: 'quipLoginToComment'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsTplLoginToComment'
                ,html: _('moxycart.setting.commentsTplLoginToComment_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsTplPreview'
                ,id: 'moxycart-setting-commentsTplPreview'
                ,fieldLabel: _('moxycart.setting.commentsTplPreview')
                ,description: MODx.expandHelp ? '' : _('moxycart.commentsTplPreview_desc')
                ,anchor: '100%'
                ,value: 'quipPreviewComment'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsTplPreview'
                ,html: _('moxycart.setting.commentsTplPreview_desc')
                ,cls: 'desc-under'

            },{
                html: '<hr />'
                ,border: false
            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsUseCss'
                ,hiddenName: 'setting_commentsUseCss'
                ,id: 'moxycart-setting-commentsUseCss'
                ,fieldLabel: _('moxycart.setting.commentsUseCss')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsUseCss_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsUseCss'
                ,html: _('moxycart.setting.commentsUseCss_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsAltRowCss'
                ,id: 'moxycart-setting-commentsAltRowCss'
                ,fieldLabel: _('moxycart.setting.commentsAltRowCss')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsAltRowCss_desc')
                ,anchor: '100%'
                ,value: 'quip-comment-alt'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsAltRowCss'
                ,html: _('moxycart.setting.commentsAltRowCss_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: ' &#8212; '+_('moxycart.settings_comments_moderation')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsRequireAuth'
                ,hiddenName: 'setting_commentsRequireAuth'
                ,id: 'moxycart-setting-commentsRequireAuth'
                ,fieldLabel: _('moxycart.setting.commentsRequireAuth')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsRequireAuth_desc')
                ,anchor: '30%'
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsRequireAuth'
                ,html: _('moxycart.setting.commentsRequireAuth_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsModerate'
                ,hiddenName: 'setting_commentsModerate'
                ,id: 'moxycart-setting-commentsModerate'
                ,fieldLabel: _('moxycart.setting.commentsModerate')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsModerate_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsModerate'
                ,html: _('moxycart.setting.commentsModerate_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsModerators'
                ,id: 'moxycart-setting-commentsModerators'
                ,fieldLabel: _('moxycart.setting.commentsModerators')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsModerators_desc')
                ,anchor: '100%'
                ,value: ''
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsModerators'
                ,html: _('moxycart.setting.commentsModerators_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsModeratorGroup'
                ,id: 'moxycart-setting-commentsModeratorGroup'
                ,fieldLabel: _('moxycart.setting.commentsModeratorGroup')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsModeratorGroup_desc')
                ,anchor: '100%'
                ,value: 'Administrator'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsModeratorGroup'
                ,html: _('moxycart.setting.commentsModeratorGroup_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsModerateAnonymousOnly'
                ,hiddenName: 'setting_commentsModerateAnonymousOnly'
                ,id: 'moxycart-setting-commentsModerateAnonymousOnly'
                ,fieldLabel: _('moxycart.setting.commentsModerateAnonymousOnly')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsModerateAnonymousOnly_desc')
                ,anchor: '30%'
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsModerateAnonymousOnly'
                ,html: _('moxycart.setting.commentsModerateAnonymousOnly_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsModerateFirstPostOnly'
                ,hiddenName: 'setting_commentsModerateFirstPostOnly'
                ,id: 'moxycart-setting-commentsModerateFirstPostOnly'
                ,fieldLabel: _('moxycart.setting.commentsModerateFirstPostOnly')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsModerateFirstPostOnly_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsModerateFirstPostOnly'
                ,html: _('moxycart.setting.commentsModerateFirstPostOnly_desc')
                ,cls: 'desc-under'

            },{
                html: '<hr />'
                ,border: false
            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsReCaptcha'
                ,hiddenName: 'setting_commentsReCaptcha'
                ,id: 'moxycart-setting-commentsReCaptcha'
                ,fieldLabel: _('moxycart.setting.commentsReCaptcha')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsReCaptcha_desc')
                ,anchor: '30%'
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsReCaptcha'
                ,html: _('moxycart.setting.commentsReCaptcha_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsDisableReCaptchaWhenLoggedIn'
                ,hiddenName: 'setting_commentsDisableReCaptchaWhenLoggedIn'
                ,id: 'moxycart-setting-commentsDisableReCaptchaWhenLoggedIn'
                ,fieldLabel: _('moxycart.setting.commentsDisableReCaptchaWhenLoggedIn')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsDisableReCaptchaWhenLoggedIn_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsDisableReCaptchaWhenLoggedIn'
                ,html: _('moxycart.setting.commentsDisableReCaptchaWhenLoggedIn_desc')
                ,cls: 'desc-under'

            },{
                html: '<hr />'
                ,border: false
            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsAllowRemove'
                ,hiddenName: 'setting_commentsAllowRemove'
                ,id: 'moxycart-setting-commentsAllowRemove'
                ,fieldLabel: _('moxycart.setting.commentsAllowRemove')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsAllowRemove_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsAllowRemove'
                ,html: _('moxycart.setting.commentsAllowRemove_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_commentsRemoveThreshold'
                ,id: 'moxycart-setting-commentsRemoveThreshold'
                ,fieldLabel: _('moxycart.setting.commentsRemoveThreshold')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsRemoveThreshold_desc')
                ,anchor: '30%'
                ,allowDecimals: false
                ,allowNegative: false
                ,value: 3
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsRemoveThreshold'
                ,html: _('moxycart.setting.commentsRemoveThreshold_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsAllowReportAsSpam'
                ,hiddenName: 'setting_commentsAllowReportAsSpam'
                ,id: 'moxycart-setting-commentsAllowReportAsSpam'
                ,fieldLabel: _('moxycart.setting.commentsAllowReportAsSpam')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsAllowReportAsSpam_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsAllowReportAsSpam'
                ,html: _('moxycart.setting.commentsAllowReportAsSpam_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: ' &#8212; '+_('moxycart.settings_comments_latest')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'textfield'
                ,name: 'setting_latestCommentsTpl'
                ,id: 'moxycart-setting-latestCommentsTpl'
                ,fieldLabel: _('moxycart.setting.latestCommentsTpl')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.latestCommentsTpl_desc')
                ,anchor: '100%'
                ,value: 'quipLatestComment'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-latestCommentsTpl'
                ,html: _('moxycart.setting.latestCommentsTpl_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_latestCommentsLimit'
                ,id: 'moxycart-setting-latestCommentsLimit'
                ,fieldLabel: _('moxycart.setting.latestCommentsLimit')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.latestCommentsLimit_desc')
                ,width: 120
                ,allowNegative: false
                ,allowDecimals: false
                ,value: 10
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-latestCommentsLimit'
                ,html: _('moxycart.setting.latestCommentsLimit_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_latestCommentsBodyLimit'
                ,id: 'moxycart-setting-latestCommentsBodyLimit'
                ,fieldLabel: _('moxycart.setting.latestCommentsBodyLimit')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.latestCommentsBodyLimit_desc')
                ,width: 150
                ,allowNegative: false
                ,allowDecimals: false
                ,value: 300
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-latestCommentsBodyLimit'
                ,html: _('moxycart.setting.latestCommentsBodyLimit_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_latestCommentsRowCss'
                ,id: 'moxycart-setting-latestCommentsRowCss'
                ,fieldLabel: _('moxycart.setting.latestCommentsRowCss')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.latestCommentsRowCss_desc')
                ,anchor: '100%'
                ,value: 'quip-latest-comment'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-latestCommentsRowCss'
                ,html: _('moxycart.setting.latestCommentsRowCss_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_latestCommentsAltRowCss'
                ,id: 'moxycart-setting-latestCommentsAltRowCss'
                ,fieldLabel: _('moxycart.setting.latestCommentsAltRowCss')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.latestCommentsAltRowCss_desc')
                ,anchor: '100%'
                ,value: 'quip-latest-comment-alt'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-latestCommentsAltRowCss'
                ,html: _('moxycart.setting.latestCommentsAltRowCss_desc')
                ,cls: 'desc-under'

            }]
        },{
            title: ' &#8212; '+_('moxycart.settings_comments_other')
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsGravatar'
                ,hiddenName: 'setting_commentsGravatar'
                ,id: 'moxycart-setting-commentsGravatar'
                ,fieldLabel: _('moxycart.setting.commentsGravatar')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsGravatar_desc')
                ,anchor: '30%'
                ,value: 1
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsGravatar'
                ,html: _('moxycart.setting.commentsGravatar_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsGravatarIcon'
                ,id: 'moxycart-setting-commentsGravatarIcon'
                ,fieldLabel: _('moxycart.setting.commentsGravatarIcon')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsGravatarIcon_desc')
                ,anchor: '100%'
                ,value: 'identicon'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsGravatarIcon'
                ,html: _('moxycart.setting.commentsGravatarIcon_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'numberfield'
                ,name: 'setting_commentsGravatarSize'
                ,id: 'moxycart-setting-commentsGravatarSize'
                ,fieldLabel: _('moxycart.setting.commentsGravatarSize')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsGravatarSize_desc')
                ,anchor: '100%'
                ,allowNegative: false
                ,value: 50
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsGravatarSize'
                ,html: _('moxycart.setting.commentsGravatarSize_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsNameField'
                ,id: 'moxycart-setting-commentsNameField'
                ,fieldLabel: _('moxycart.setting.commentsNameField')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsNameField_desc')
                ,anchor: '100%'
                ,value: 'name'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsNameField'
                ,html: _('moxycart.setting.commentsNameField_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'combo-boolean'
                ,name: 'setting_commentsShowAnonymousName'
                ,hiddenName: 'setting_commentsShowAnonymousName'
                ,id: 'moxycart-setting-commentsShowAnonymousName'
                ,fieldLabel: _('moxycart.setting.commentsShowAnonymousName')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsShowAnonymousName')
                ,anchor: '30%'
                ,value: 0
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsShowAnonymousName'
                ,html: _('moxycart.setting.commentsShowAnonymousName_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_commentsAnonymousName'
                ,id: 'moxycart-setting-commentsAnonymousName'
                ,fieldLabel: _('moxycart.setting.commentsAnonymousName')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.commentsAnonymousName_desc')
                ,anchor: '100%'
                ,value: 'Anonymous'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-commentsAnonymousName'
                ,html: _('moxycart.setting.commentsAnonymousName_desc')
                ,cls: 'desc-under'

            }]
        }]
    });
    Moxycart.panel.ContainerAdvancedSettings.superclass.constructor.call(this,config);
};
Ext.extend(Moxycart.panel.ContainerAdvancedSettings,MODx.VerticalTabs,{

});
Ext.reg('moxycart-tab-advanced-settings',Moxycart.panel.ContainerAdvancedSettings);

Moxycart.panel.ContainerTemplateSettings = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'moxycart-panel-container-template-settings'
        ,layout: 'column'
        ,border: false
        ,anchor: '100%'
        ,defaults: {
            layout: 'form'
            ,labelAlign: 'top'
            ,anchor: '100%'
            ,border: false
            ,labelSeparator: ''
        }
        ,items: this.getItems(config)
    });
    Moxycart.panel.ContainerTemplateSettings.superclass.constructor.call(this,config);
};
Ext.extend(Moxycart.panel.ContainerTemplateSettings,MODx.Panel,{
    getItems: function(config) {
        var oc = {
            'change':{fn:MODx.fireResourceFormChange}
            ,'select':{fn:MODx.fireResourceFormChange}
        };
        var flds = [];
        flds.push({
            xtype: 'modx-combo-template'
            ,fieldLabel: _('resource_template')
            ,description: MODx.expandHelp ? '' : '<b>[[*template]]</b><br />'+_('resource_template_help')
            ,name: 'template'
            ,id: 'modx-resource-template'
            ,anchor: '100%'
            ,editable: false
            ,baseParams: {
                action: 'getList'
                ,combo: '1'
            }
            ,value: config.record.template || MODx.config['moxycart.default_container_template']
            ,listeners: oc
        },{
            xtype: MODx.expandHelp ? 'label' : 'hidden'
            ,forId: 'modx-resource-template'
            ,id: 'modx-resource-template-label'
            ,html: _('moxycart.template_desc')
            ,cls: 'desc-under'
        });
        var ct = this.getContentField(config);
        for (var f in ct) {
            flds.push(ct[f]);
        }
        return [{
            columnWidth: .5
            ,items: flds
        },{
            columnWidth: .5
            ,items: [{
                xtype: 'modx-combo-template'
                ,name: 'setting_articleTemplate'
                ,hiddenName: 'setting_articleTemplate'
                ,id: 'moxycart-setting-articleTemplate'
                ,fieldLabel: _('moxycart.setting.articleTemplate')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.articleTemplate_desc')
                ,anchor: '100%'
                ,value: config.record.setting_articleTemplate || MODx.config['moxycart.default_article_template']
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-articleTemplate'
                ,html: _('moxycart.setting.articleTemplate_desc')
                ,cls: 'desc-under'

            },{
                xtype: 'textfield'
                ,name: 'setting_tplArticleRow'
                ,id: 'moxycart-setting-tplArticleRow'
                ,fieldLabel: _('moxycart.setting.tplArticleRow')
                ,description: MODx.expandHelp ? '' : _('moxycart.setting.tplArticleRow_desc')
                ,anchor: '100%'
                ,value: config.record.setting_tplArticleRow || 'sample.ArticleRowTpl'
                ,listeners: oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'moxycart-setting-tplArticleRow'
                ,html: _('moxycart.setting.tplArticleRow_desc')
                ,cls: 'desc-under'

            }]
        }];
    }

    ,getContentField: function(config) {
        return [{
            id: 'modx-content-above'
            ,border: false
        },{
            xtype: 'textarea'
            ,name: 'ta'
            ,id: 'ta'
            ,fieldLabel: _('moxycart.content')
            ,anchor: '100%'
            ,height: 250
            ,grow: false
            ,border: false
            ,value: config.record && config.record.content ? config.record.content : "[[+moxycart]]\n\n[[+paging]]"
        },{
            id: 'modx-content-below'
            ,border: false
        }];
    }
});
Ext.reg('moxycart-tab-template-settings',Moxycart.panel.ContainerTemplateSettings);

Moxycart.combo.Shorteners = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [
                [_('none'),'']
                ,['Tinyurl','tinyurl']
                ,['Digg','digg']
                ,['Isgd','isgd']
            /*    ,['Bit.ly','bitly'] */
            ]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
    });
    Moxycart.combo.Shorteners.superclass.constructor.call(this,config);
};
Ext.extend(Moxycart.combo.Shorteners,MODx.combo.ComboBox);
Ext.reg('moxycart-combo-shorteners',Moxycart.combo.Shorteners);