function getQueryParams(qs) {
	qs = qs.split("+").join(" ");
	var params = {}, tokens, re = /[?&]?([^=]+)=([^&]*)/g;
	while (tokens = re.exec(qs)) {
		params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
	}
	return params;
}

var query = getQueryParams(document.location.search),
	pid = query.id;


function renderProduct(){
	var tabPanel = Ext.getCmp("modx-resource-tabs");

	if(tabPanel!=null){
		//Taxonomies tab configuration
		var taxonomiesTab = {
			title: 'Taxonomies',
			id: 'modx-resource-product-taxonomies-tab',
			cls: 'modx-resource-tab',
			layout: 'form',
			labelAlign: 'top',
			labelSeparator: '',
			bodyCssClass: 'tab-panel-wrapper main-wrapper',
			autoHeight: true,
			defaults: {
				border: false,
				msgTarget: 'under',
				width: 400
			},
			items: getTaxonomiesFields()
		};

		tabPanel.insert(0, taxonomiesTab);

		//Images tab configuration
		var imagesTab = {
			title: 'Images',
			id: 'modx-resource-product-images-tab',
			cls: 'modx-resource-tab',
			layout: 'form',
			labelAlign: 'top',
			labelSeparator: '',
			bodyCssClass: 'tab-panel-wrapper main-wrapper',
			autoHeight: true,
			defaults: {
				border: false,
				msgTarget: 'under',
				width: 400
			},
			items: getImagesFields()
		};

		tabPanel.insert(0, imagesTab);

		//Specs tab configuration
		var specsTab = {
			title: 'Specs',
			id: 'modx-resource-product-specs-tab',
			cls: 'modx-resource-tab',
			layout: 'form',
			labelAlign: 'top',
			labelSeparator: '',
			bodyCssClass: 'tab-panel-wrapper main-wrapper',
			autoHeight: true,
			defaults: {
				border: false,
				msgTarget: 'under',
				width: 400
			},
			items: getSpecsFields()
		};

		tabPanel.insert(0, specsTab);

		//Variations tab configuration
		var variationsTab = {
			title: 'Variations',
			id: 'modx-resource-product-variations-tab',
			cls: 'modx-resource-tab',
			layout: 'form',
			labelAlign: 'top',
			labelSeparator: '',
			bodyCssClass: 'tab-panel-wrapper main-wrapper',
			autoHeight: true,
			defaults: {
				border: false,
				msgTarget: 'under',
				width: 400
			},
			items: getVariationsFields()
		};

		tabPanel.insert(0, variationsTab);

		//Setting tab configuration
		var settingsTab = {
			title: 'Settings',
			id: 'modx-resource-product-settings-tab',
			cls: 'modx-resource-tab',
			layout: 'form',
			labelAlign: 'top',
			labelSeparator: '',
			bodyCssClass: 'tab-panel-wrapper main-wrapper',
			autoHeight: true,
			defaults: {
				border: false,
				msgTarget: 'under',
				width: 400
			},
			items: getProductSettingsFields()
		};

		tabPanel.insert(0, settingsTab);

		//Product tab configuration
		var productTab = {
			title: 'Product',
			id: 'modx-resource-product-tab',
			cls: 'modx-resource-tab',
			layout: 'form',
			labelAlign: 'top',
			labelSeparator: '',
			bodyCssClass: 'tab-panel-wrapper main-wrapper',
			autoHeight: true,
			defaults: {
				border: false,
				msgTarget: 'under'
			},
			items: getProductsTabFields()
		};

		tabPanel.insert(0, productTab);
		tabPanel.setActiveTab(0);
		var tabs = tabPanel.items.items, delTabs = false, ids = [];

		for(n in tabs) {
			if(delTabs) ids.push(tabs[n].id);
			if(!delTabs && tabs[n].title === 'Taxonomies') delTabs = true;
		}

		for(i in ids) tabPanel.remove(Ext.getCmp(ids[i]));
		tabPanel.doLayout();
		Ext.getCmp('modx-resource-content').hide();
	}
}

function renderProductContainer(isProductContainerCreate, config){
	var tabPanel = Ext.getCmp("modx-resource-tabs");

	if(tabPanel!=null){
		//Add store setting tab
		var storeSettingsTab = {
			title: 'Store Settings',
			id: 'modx-resource-StoreSettings',
			cls: 'modx-resource-tab',
			layout: 'form',
			labelAlign: 'top',
			labelSeparator: '',
			bodyCssClass: 'tab-panel-wrapper main-wrapper',
			autoHeight: true,
			defaults: {
				border: false,
				msgTarget: 'under',
				width: 400
			},
			items: getStoreSettingsFields(config)
		};

		tabPanel.insert(0, storeSettingsTab);

		//Add products tab
		var prductsTab = {
			title: 'Products',
			id: 'modx-resource-Products',
			cls: 'modx-resource-tab',
			layout: 'fit',
			labelAlign: 'top',
			labelSeparator: '',
			bodyCssClass: 'tab-panel-wrapper main-wrapper',
			autoHeight: true,
			defaults: {
				border: false,
				msgTarget: 'under',
				width: 400,
				height:400
			},
			items: (isProductContainerCreate)?getCreateProductFields(config):getProductsFields(config)
		};

		tabPanel.insert(0, prductsTab);
		tabPanel.setActiveTab(0);
		tabPanel.doLayout();
	}
}

function getTaxonomiesFields(){
	return [{
		region: 'west',
		collapsible: true,
		title: 'Categories',
		xtype: 'treepanel',
		width: 200,
		autoScroll: true,
		split: true,
		loader: new Ext.tree.TreeLoader(),
		root: new Ext.tree.AsyncTreeNode({
			expanded: true,
			children: [{
				text: 'items 1',
				leaf: true
			}, {
				text: 'items 2',
				leaf: true
			},{
				text: 'items 3',
				leaf: true
			}]
		}),
			rootVisible: false
		},{
			layout:'column',
			columns:2,
			height:40,
			xtype:'panel',
			items:[{
				xtype: 'label',
				width: 200
			},{
				xtype: 'button',
				text:'Add'
			}]
	},{
		xtype: 'label',
		text: 'Tags'
	},{
		xtype: 'spacer',
		cls:'TaxonomiesTab',
		style:'margin: 15px;',
		html: '<div><span style="padding: 10px;">autos</span><span class="selectCom" style="padding: 10px;background: #d2d2d2;border-radius: 10px;">business</span><span style="padding: 10px;">cities</span><span class="selectCom" style="padding: 10px;background: #d2d2d2;border-radius: 10px;">campanies</span></div>'	
	},{
		layout:'column',
		columns:2,
		height:40,
		xtype:'panel',
		items:[{
			xtype: 'label',
			width: 200
		},{
			xtype: 'button',
			text:'Add'
		}]
	}];
}

function getSpecsFields(){
	var store = new Ext.data.Store();

	var cm = new Ext.grid.ColumnModel([{
		header:'Spec',
		resizable: false,
		dataIndex: 'state',
		sortable: true
	},{
		header: 'Value',
		dataIndex: 'filename',
		sortable: true
	},{
		header: 'Description',
		dataIndex: 'note',
		sortable: true
	}]);

	this.SpecsGrid_panel = new Ext.grid.GridPanel({
		ds: store,
		cm: cm,
		style:'margin-top: 10px;',
		layout:'fit',
		region:'center',
		border: true,
		viewConfig: {
		autoFill: true,
		forceFit: true
	},
		bbar : new Ext.Toolbar({
			pageSize: 25,
			store: store,
			displayInfo: true,
			displayMsg: 'Displaying Records {0} - {1} of {2}',
			emptyMsg: "No Records to display"
		})
	});

	return [{
		anchor: '100%',
		border:false,
		layout:'form',
		id: 'modx-resource-specs-columns',
		style: 'border:0px',
		defaults: {
			labelSeparator: '',
			labelAlign: 'top',
			border: false,
			msgTarget: 'under',
			bodyCssClass:'pnl-mo-border'
		},
		items:[this.SpecsGrid_panel]
	}];
}

function getImagesFields(){
	return [{
		layout:'column',
		columns:2,
		height:40,
		xtype:'panel',
		items:[{
			  xtype: 'label',
				width: 200  
			},{
			xtype: 'button',
			text:'Add'
		}]
	}];
}

function getVariationsFields(){
	var store = new Ext.data.Store();

	var cm = new Ext.grid.ColumnModel([{
			header:'Variant',
			resizable: false,
			dataIndex: 'state',
			sortable: true
		},{
			header: 'SKU',
			dataIndex: 'filename',
			sortable: true
		},{
			header: 'Cost',
			dataIndex: 'note',
			sortable: true
		},{
			header: 'Qty In Stock',
			dataIndex: 'note',
			sortable: true
		},{
			xtype: 'actioncolumn',
			items: [{
				xtype: 'button',
				text:'Edit'
			},{
				xtype: 'button',
				text:'View'
			}]
		}
	]);

	this.VariationsGrid_panel = new Ext.grid.GridPanel({
		ds: store,
		cm: cm,
		style:'margin-top: 10px;',
		layout:'fit',
		region:'center',
		border: true,
		viewConfig: {
			autoFill: true,
			forceFit: true
		},
		bbar : new Ext.Toolbar({
			pageSize: 25,
			store: store,
			displayInfo: true,
			displayMsg: 'Displaying Records {0} - {1} of {2}',
			emptyMsg: "No Records to display"
		})
	});

	return [{
			anchor: '100%',
			border:false,
			layout:'form',
			id: 'modx-resource-variations-columns',
			style: 'border:0px',
			defaults: {
				labelSeparator: '',
				labelAlign: 'top',
				border: false,
				msgTarget: 'under',
				bodyCssClass:'pnl-mo-border'
			},
			items:[{
				xtype: 'button',
				text: 'Manage Inventory'
			},this.VariationsGrid_panel]
	}];
}

function getProductSettingsFields(){
	return [{
		anchor: '100%',
		border:false,
		layout:'form',
		labelAlign : 'left',
		labelStyle : 'position: absolute; margin-top: 5px;',
		id: 'modx-resource-productSettings-columns',
		style: 'border:0px',
		defaults: {
			labelSeparator: '',
			border: false,
			width : 280,
			msgTarget: 'under',
			labelStyle : 'position: absolute;',
			bodyCssClass:'pnl-mo-border'
		},
		items:[{
			xtype : 'label',
			style : 'position: absolute; left: 450px; top: 10px; color: silver;',
			text : 'URL segment'
		},{
			xtype: 'textfield',
			fieldLabel: 'Alias',
			name: 'productSettings',
			id: 'modx-resource-productSettings'
		},{
			xtype : 'combo',
			name : 'product_template',
			fieldLabel: 'Template',
			editable: true,
			id : 'defaultTemplates',
			mode : 'remote',
			pageSize : 10,
			typeAhead: false,
			triggerAction: 'all',
			lastQuery: '',
			displayField : 'name',
			valueField : 'id',
			store: getTemplateStore()
		},
		{
			xtype : 'combo',
			name : 'currency',
			fieldLabel: 'Currency',
			editable: true,
			id : 'currency',
			mode : 'remote',
			typeAhead: false,
			triggerAction: 'all',
			lastQuery: '',
			displayField : 'name',
			valueField : 'currency_id',
			store: getCurrencyStore()
		},
		{
			xtype : 'combo',
			fieldLabel: 'Product Type',
			name : 'product_type',
			editable: false,
			triggerAction: 'all',
			mode: 'local',
			typeAhead: true,
			displayField:'name',
			valueField:'value',
			value:'regular',
			store:new Ext.data.ArrayStore({
				autoDestroy: true,
				fields: [
				   {name: 'name'},
				   {name: 'value'}
				],
				data:[['Regular', 'regular'], ['Subscription', 'subscription'], ['Download', 'download']]
			})
		},
		{
			xtype : 'combo',
			name : 'product_container',
			fieldLabel: 'Product Container',
			editable: true,
			id : 'product_container',
			mode : 'remote',
			typeAhead: false,
			triggerAction: 'all',
			lastQuery: '',
			displayField : 'name',
			valueField : 'id',
			store: getProductContainerStore()
		}]
	}];
}

function getSpecs(container){
	Ext.Ajax.request({
		url: connector_url+'json_specs',
		success: function(response, opts) {
			var obj = Ext.decode(response.responseText);
			if(obj && obj.results){
				for(n in obj.results){
					if(obj.results[n].spec_id) container.add({ xtype : 'checkbox', name : 'specs_'+obj.results[n].spec_id, boxLabel : obj.results[n].name });
				}
			}
		}
	});
}

function getTaxonomies(container){
	Ext.Ajax.request({
		url: connector_url+'json_store_taxonomies',
		params : {
			store_id : pid
		},
		method : 'GET',
		success: function(response, opts) {
			if(response.responseText === 'Invalid store. Include valid store_id'){
				return;
			}
			var obj = Ext.decode(response.responseText);
			if(obj && obj.results){
				for(n in obj.results){
					if(obj.results[n].id) container.add({ xtype : 'checkbox', name : 'taxonomies_'+obj.results[n].id, boxLabel : obj.results[n].pagetitle, checked : obj.results[n].is_checked });
				}
			}
		}
	});
}


function getVariations(container){
	Ext.Ajax.request({
		url: connector_url+'json_store_variation_types',
		params : {
			store_id : pid
		},
		method : 'GET',
		success: function(response, opts) {
			var obj = Ext.decode(response.responseText);
			if(obj && obj.results){
				for(n in obj.results){
					if(obj.results[n].vtype_id) {
						container.add({ xtype: 'label', text: obj.results[n].name })
						container.add({
							xtype : 'combo',
							name : 'variations_'+obj.results[n].vtype_id,
							fieldLabel: obj.results[n].name,
							displayField:'name',
							valueField:'value',
							triggerAction: 'all',
							mode: 'local',
							value : 'off',
							store:new Ext.data.ArrayStore({
								autoDestroy: true,
								fields: [
									{ name: 'name' },
									{ name: 'value' }
								],
								data:[['Off', 'off'], ['Option Only', 'option'], ['Variant', 'variant']]
							})
						});
					}
				}
			}
		}
	});
}

function getTemplateStore(){
	var store = Ext.StoreMgr.get('defaultTemplate');
	if(store) return store;
	else return new Ext.data.Store({
		fields: ['id', 'name'],
		autoLoad : true,
		storeId : 'defaultTemplates',
		reader : new Ext.data.JsonReader({
			idProperty: 'id',
			root: 'results',
			totalProperty: 'total',
			fields: [
				{name: 'id'},
				{name: 'name'}
			]
		}),
		proxy : new Ext.data.HttpProxy({
			method: 'GET',
			prettyUrls: false,
			url: connector_url+'json_templates'
		}),
		listeners : {
			load : function(){
				var dt =  Ext.getCmp('defaultTemplates');
				if(dt) {
					if(default_template) dt.setValue(default_template);
					//else dt.setValue(this.items.items[0].data.);
				}
			}
		}
	});
}

function getCurrencyStore(){
	var store = Ext.StoreMgr.get('currencyStore');
	if(store) return store;
	else return new Ext.data.Store({
		fields: ['currency_id', 'name'],
		autoLoad : true,
		storeId : 'currencyStore',
		reader : new Ext.data.JsonReader({
			idProperty: 'currency_id',
			root: 'results',
			totalProperty: 'total',
			fields: [
				{name: 'currency_id'},
				{name: 'name'}
			]
		}),
		proxy : new Ext.data.HttpProxy({
			method: 'GET',
			prettyUrls: false,
			url: connector_url+'json_currencies'
		}),
		listeners : {
			load : function(){
				var c =  Ext.getCmp('currency');
				if(c) {
				}
			}
		}
	});
}

function getProductContainerStore(){
	var store = Ext.StoreMgr.get('productContainerStore');
	if(store) return store;
	else return new Ext.data.Store({
		fields: ['id', 'name'],
		autoLoad : true,
		storeId : 'productContainerStore',
		reader : new Ext.data.JsonReader({
			idProperty: 'id',
			root: 'results',
			totalProperty: 'total',
			fields: [
				{name: 'id'},
				{name: 'name'}
			]
		}),
		proxy : new Ext.data.HttpProxy({
			method: 'GET',
			prettyUrls: false,
			url: connector_url+'json_stores'
		}),
		listeners : {
			load : function(){
				var c =  Ext.getCmp('product_container');
				if(c) {
				}
			}
		}
	});
}

function getStoreSettingsFields(config){
	getTemplateStore();
	config = config || {record:{}};

	return [{
		anchor: '100%',
		border:false,
		monitorValid:true,
		layout:'form',
		id: 'modx-resource-storesettings-columns',
		style: 'border:0px',
		listeners : {
			clientvalidation: function(){
				console.log('validate');
			}
		},
		defaults: {
			labelSeparator: '',
			labelAlign: 'top',
			border: false,
			msgTarget: 'under',
			bodyCssClass:'pnl-mo-border'
		},
		items:[{
			border:false,
			frame:true,
			layout:'table',
			width:'100%',
			style: 'border:0px',
			layoutConfig:{
				tableAttrs:{
					cellspacing:8
				},
				columns:4
			},
			defaults : {
				enableKeyEvents : true,
				listeners : {
					keypress : function(){
						Ext.getCmp('modx-abtn-save').enable(true);
					},
					change : function(){
						Ext.getCmp('modx-abtn-save').enable(true);
					},
					select : function(){
						Ext.getCmp('modx-abtn-save').enable(true);
					}
				}
			},
			items:[
				{
					width:'100'
				},
				{
					width:'200'
				},
				{
					width:'100'
				},
				{
					width:'100'
				},
				{
					xtype: 'label',
					text: 'Product Type'
				},{
					xtype : 'combo',
					fieldLabel: 'Product Type',
					name : 'product_type',
					editable: false,
					triggerAction: 'all',
					mode: 'local',
					typeAhead: true,
					width : 280,
					displayField:'name',
					valueField:'value',
					value:'regular',
					store:new Ext.data.ArrayStore({
						autoDestroy: true,
						fields: [
						   {name: 'name'},
						   {name: 'value'}
						],
						data:[['Regular', 'regular'], ['Subscription', 'subscription'], ['Download', 'download']]
					})
				},
				{
					colspan:2
				},
				{
					xtype: 'label',
					text: 'Default template'
				}, {
					xtype : 'combo',
					name : 'product_template',
					fieldLabel: 'Default Template',
					editable: true,
					id : 'defaultTemplates',
					mode : 'remote',
					pageSize : 10,
					width : 280,
					typeAhead: false,
					triggerAction: 'all',
					lastQuery: '',
					displayField : 'name',
					valueField : 'id',
					store: 'defaultTemplates'
				},{
					colspan:2
				},
				{
					xtype: 'label',
					text: 'Track Inventory'
				},{
					xtype : 'combo',
					name : 'track_inventory',
					fieldLabel: 'Track Inventory',
					editable: false,
					triggerAction: 'all',
					mode: 'local',
					typeAhead: true,
					width : 280,
					displayField:'name',
					valueField:'value',
					value : 0,
					store:new Ext.data.ArrayStore({
						autoDestroy: true,
						fields: [
							{name: 'name'},
							{name: 'value'}
						],
						data:[['Yes', 1], ['No', 0]]
					})
				},
				{
					colspan:2
				},
				{
					xtype: 'label',
					text: 'Default Sort Order'
				},{
					xtype : 'combo',
					name : 'sort_order',
					fieldLabel: 'Default Sort Order',
					editable: false,
					triggerAction: 'all',
					mode: 'local',
					typeAhead: true,
					width : 280,
					displayField:'name',
					valueField:'value',
					value:'sku',
					store:new Ext.data.ArrayStore({
						autoDestroy: true,
						fields: [
							{name: 'name'},
							{name: 'value'}
						],
						data:[['Product Name', 'name'], ['SKU', 'sku'], ['Last Modified', 'timestamp_modified'], ['Manual Sort Order', 'seq']]
					})
				},
				{
					colspan:2
				},
				{
					xtype: 'label',
					text: 'Default Inventory Alert Level'
				},
				{
					xtype : 'textfield',
					name : 'qty_alert',
					fieldLabel: 'Default Inventory Alert Level',
					width : 280
				},
				{
					colspan:2
				},
				{
					colspan:4,
					height:10
				},
				{
					xtype: 'label',
					text: 'Specs',
					style: 'margin-top:20px',
					ctCls:'v-align-top'
				},
				{
					layout:'column',
					border:false,
					columns:4,
					items:[{
						layout:'fit',
						border: false,
						listeners : {
							afterrender : function(f){
								getSpecs(f);
							}
						}
					}]
				},
				{
					xtype: 'label',
					text: 'Taxonomies',
					ctCls:'v-align-top'
				},
				{
					layout:'column',
					border:false,
					columns:4,
					items:[{
						layout:'fit',
						border: false,
						listeners : {
							afterrender : function(f){
								getTaxonomies(f);
							}
						}
					}]
				},{
					colspan:4,
					height:10
				},{
					xtype: 'label',
					text: 'Variations',
					ctCls:'v-align-top'
				},{
					colspan:3,
					layout:'column',
					border:false,
					items: [{
						layout:'table',
						width:'100%',
						layoutConfig:{
							tableAttrs:{
								cellspacing:8
							},
							columns:2
						},
						listeners : {
							afterrender : function(f){
								getVariations(f);
								Ext.getCmp('modx-abtn-save').disable(true);
							}
						}
					}]
				},{
					colspan:3,
					layout:'column',
					border:false,
					hidden : true,
					items: [{
						xtype : 'button',
						text : 'Save',
						handler : function(){
							var fields = Ext.getCmp('modx-resource-storesettings-columns').findByType('field'), values = { specs : {}, taxonomies : {}, variations : {} };
							for(n in fields){
								if(fields[n].getValue) {
									if(fields[n].name.indexOf('specs_') !== -1 || fields[n].name.indexOf('taxonomies_') !== -1 || fields[n].name.indexOf('variations_') !== -1){
										var s = fields[n].name.split('_');
										values[s[0]][s[1]] = s[0] === 'variations' ? fields[n].getValue() : Number(fields[n].getValue());
									} else {
										values[fields[n].name] = fields[n].getValue();
									}
								}
							}
							Ext.Ajax.request({
								url : location.href,
								method : 'POST',
								jsonData : values
							});
						}
					}]
				}]
			}]
	}];
}

function getCreateProductFields(config){
	return [{
		xtype:'panel',
		border:false,
		layout:'border',
		items:[
			{
				xtype:'panel',
				region:'center',
				border:true,
				bodyCssClass:'pnlProductContainerWalk',
				layout:'border',
				items:[
					{
						xtype:'panel',
						region:'center',
						border:false,
						padding:5,
						html:'<object data="http://www.youtube.com/v/tIBxavsiHzM" height="100%" width="100%"></object>'
					},
					{
						xtype:'panel',
						region:'east',
						border:false,
						width:300,
						padding:5,
						html:'<b>Welcome to moxycart.</b></br> This video will walks you through the steps required to setup your store.'
					}
				]
			}
		]
	}];
}




function getProductsTabFields(){
	var categoryStore = new Ext.data.Store({
		fields: ['id', 'name'],
		autoLoad : true,
		storeId : 'categories',
		reader : new Ext.data.JsonReader({
			idProperty: 'id',
			root: 'results',
			totalProperty: 'total',
			fields: [
				{name: 'id'},
				{name: 'name'}
			]
		}),
		proxy : new Ext.data.HttpProxy({
			method: 'GET',
			prettyUrls: false,
			url: connector_url+'json_categories',
		}),
		listeners : {
			load : function(){
				var dt =  Ext.getCmp('categories');
				dt.setValue(this.getAt(0).data.name);
			}
		}
	});
	return [{
		xtype : 'panel',
		layout:'table',
		id: 'modx-resource-products-columns',
		defaults: {
			labelSeparator: '',
			border: false,
			msgTarget: 'under'
		},
		layoutConfig: {
			columns: 4,
			tableAttrs: {
				cellspacing : 8,
				style: {
					width: '100%'
				}
			},
		},
		items : [{
			xtype: 'label',
			width : 150,
			cls : 'product-form-label',
			text: 'Name'
		},{
			xtype: 'textfield',
			width : 205
		},{
			xtype: 'label',
			style : 'color : silver; float: right; padding-top: 8px; margin-right: 8px;',
			text: 'Active?'
		},{
			xtype : 'combo',
			editable: true,
			width:60,
			editable: false,
			triggerAction: 'all',
			mode: 'local',
			typeAhead: true,
			displayField:'name',
			valueField:'value',
			value : 1,
			store:new Ext.data.ArrayStore({
				autoDestroy: true,
				fields: [
					{name: 'name'},
					{name: 'value'}
				],
				data:[['Yes', 1], ['No', 0]]
			})
		},


		{
			xtype: 'label',
			width : 150,
			cls : 'product-form-label',
			text: 'SKU'
		},{
			xtype: 'textfield',
			width : 205
		},{
			xtype: 'label',
			cls : 'product-form-label',
			text: 'Vendor SKU'
		},{
			xtype: 'textfield',
			width : 205
		},


		{
			xtype: 'label',
			width : 150,
			cls : 'product-form-label',
			text: 'Description'
		},{
			xtype: 'textarea',
			anchor: '100%',
			colspan : 3,
			width : 592
		},


		{
			xtype: 'label',
			width : 150,
			cls : 'product-form-label',
			text: 'Price'
		},{
			xtype: 'textfield',
			width : 205
		},{
			xtype: 'label',
			cls : 'product-form-label',
			text: 'Sale Price'
		},{
			xtype: 'textfield',
			width : 205
		},


		{
			xtype: 'label',
			width : 150,
			cls : 'product-form-label',
			html: 'Strike-<br>Through Price'
		},{
			xtype: 'textfield',
			width : 205
		},{
			xtype: 'label',
			cls : 'product-form-label',
			text: 'Sale Start'
		},{
			xtype: 'datefield',
			width : 205
		},


		{
			xtype: 'label',
			width : 150,
			cls : 'product-form-label',
			text: 'Category'
		},{
			xtype : 'combo',
			editable: true,
			id : 'categories',
			mode : 'remote',
			width : 205,
			typeAhead: false,
			triggerAction: 'all',
			lastQuery: '',
			displayField : 'name',
			valueField : 'name',
			store: 'categories'
		},{
			xtype: 'label',
			cls : 'product-form-label',
			text: 'Sale End'
		},{
			xtype: 'datefield',
			width : 205
		},


		{
			xtype: 'label',
			width : 150,
			cls : 'product-form-label',
			text: 'Inventory'
		},{
			xtype: 'textfield',
			width : 205
		},{
			xtype: 'label',
			cls : 'product-form-label',
			text: 'Qty Min'
		},{
			xtype: 'textfield',
			width : 205
		},


		{
			xtype: 'label',
			width : 150,
			cls : 'product-form-label',
			text: 'Alert Qty'
		},{
			xtype: 'textfield',
			width : 205
		},{
			xtype: 'label',
			cls : 'product-form-label',
			text: 'Qty Max'
		},{
			xtype: 'textfield',
			width : 205
		},


		{
			xtype: 'label',
			width : 150,
			cls : 'product-form-label',
			text: 'Backorder Cap'
		},{
			xtype: 'textfield',
			width : 205,
			colspan : 3
		},


		{
			xtype: 'label',
			width : 150,
			cls : 'product-form-label',
			text: 'Content'
		},{
			xtype: 'htmleditor',
			width : 592,
			colspan : 3
		}]

	}];
}



function getProductsFields(config){
	var store = new Ext.data.Store({
		fields: ['id', 'name', 'sku', 'category'],
		autoLoad : true,
		storeId : 'productStore',
		reader : new Ext.data.JsonReader({
			idProperty: 'id',
			root: 'results',
			totalProperty: 'total',
			fields: [
				{name: 'id'},
				{name: 'name'},
				{name: 'sku'},
				{name: 'category'}
			]
		}),
		proxy : new Ext.data.HttpProxy({
			method: 'GET',
			prettyUrls: false,
			url: connector_url+'json_products'
		})
	});

	var cm = new Ext.grid.ColumnModel([{
			header:'Name',
			resizable: false,
			dataIndex: 'name',
			sortable: true
		},{
			header: 'SKU',
			dataIndex: 'sku',
			sortable: true
		},{
			header: 'Category',
			dataIndex: 'category',
			sortable: true
		},{
			header : 'Action',
			dataIndex: 'id',
			sortable: true,
			renderer : function(value, metaData, record, rowIndex, colIndex, store) {
			  return '<button role="edit" class="x-btn">Edit</button><button role="view" class="x-btn">View</button>';
			}
		}
	]);

	this.productsGrid_panel = new Ext.grid.GridPanel({
		ds: store,
		cm: cm,
		layout:'fit',
		region:'center',
		border: true,
		viewConfig: {
			autoFill: true,
			forceFit: true
		},
		listeners : {
			afterrender : function(){
				this.getStore().load();
			},
			cellclick : function(grid, rowIndex, columnIndex, e) {
				var record = grid.getStore().getAt(rowIndex),
					fieldName = grid.getColumnModel().getDataIndex(columnIndex);
				if(fieldName === 'id'){
					if(e.target.innerHTML === 'Edit'){

					} else if(e.target.innerHTML === 'View'){

					}
				}
			}
		},
		bbar: new Ext.PagingToolbar({
			store: store,
			displayInfo: true,
			pageSize: 30,
			prependButtons: true
		})
	});

	return [{
		layout:'card',
		activeItem:0,
		id: 'modx-resource-products-columns',
		defaults: {
			labelSeparator: '',
			labelAlign: 'top',
			border: false,
			msgTarget: 'under'
		},
		items:[{
			layout:'border',
			id: 'modx-resource-productsList-columns',
			defaults: {
				labelSeparator: '',
				labelAlign: 'top',
				border: false,
				msgTarget: 'under'
			},
			items:[{
				region:'north',
				layout:'column',
				columns:6,
				height:60,
				xtype:'panel',
				items:[{
					xtype: 'button',
					text:'Add Product',
					listeners: {
						'click': {fn: function(){
							MODx.loadPage(MODx.action['moxycart:index'], 'f=product_create');
						}, scope: this}
					}
				},{
					xtype: 'button',
					text:'Manage Inventory'
				},{
					xtype: 'button',
					padding : 0,
					cls : 'divided-btn',
					width : 95,
					text:'Set Manual<br>Sort Order'
				},{
					border:false,
					xtype: 'displayfield',
					value:'&nbsp;',
					columnWidth:.20
				},{
					xtype: 'textfield',
					emptyText:'Search..'
				},{
					xtype: 'button',
					text:'Filter'
				},{
					xtype: 'button',
					text:'Show All'
				}]
			},
			this.productsGrid_panel]
		},{
			layout:'form',
			anchor: '100%',
			id: 'modx-resource-Addproducts-columns',
			defaults: {
				labelSeparator: '',
				labelAlign: 'top',
				border: false,
				msgTarget: 'under'
			},
			items:[{
				layout:'column',
				columns:4,
				height:50,
				xtype:'panel',
				items:[{
					xtype: 'label',
					text: 'Name'
				},{
					xtype: 'textfield',
					flex:1,
					fieldLabel:'Name'
				},{
					xtype:'spacer',anchor:'100%',width:'100%'},{
					xtype: 'label',
					text: 'Active?'
				},new Ext.form.ComboBox({fieldLabel: 'Active?',editable: true,flex:1,width:60})]
			},{
				region:'north',
				layout:'column',
				columns:4,
				height:50,
				xtype:'panel',
				items:[{
					xtype: 'label',
					text: 'SKU'
				},{
					xtype: 'textfield',
					flex:1,
					fieldLabel:'SKU'
				},{
					xtype: 'label',
					text: 'Vendor SKU'
				},{
					xtype: 'textfield',
					flex:1,
					fieldLabel:'Vendor SKU'
				}]
			},{
				xtype: 'textarea',
				anchor: '100%',
				fieldLabel:'Description'
			},{
				xtype: 'textfield',
				fieldLabel:'Price'
			},{
				 xtype: 'textfield',
				fieldLabel:'Strike Through Price'
			},{
				xtype : 'combo',
				fieldLabel: 'Category',
				editable: true,
				width:60},
			{
				region:'north',
				layout:'column',
				columns:4,
				height:50,
				xtype:'panel',
				items:[{
					xtype: 'label',
					text: 'Inventory'
				},{
					xtype: 'textfield',
					fieldLabel:'Inventory'
				},{
					xtype: 'label',
					text: 'Qty Min.'
				},{
					xtype: 'textfield',
					fieldLabel:'Qty Min.'
				}]
			},{
				region:'north',
				layout:'column',
				columns:4,
				height:50,
				xtype:'panel',
				items:[{
					xtype: 'label',
					text: 'Alert Qty'
				},{
					xtype: 'textfield',
					fieldLabel:'Alert Qty'
				},{
					xtype: 'label',
					text: 'Qty Max.'
				},{
					xtype: 'textfield',
					fieldLabel:'Qty Max.'
				}]
			},{
				xtype: 'textarea',
				name: 'textProduct',
				id: 'textProduct',
				hideLabel: true,
				anchor: '100%',
				height: 400,
				grow: false
			}]
		}]
	}];
}



var triggerDirtyField = function(fld) {
	Ext.getCmp('modx-panel-resource').fieldChangeEvent(fld);
};
MODx.triggerRTEOnChange = function() {
	triggerDirtyField(Ext.getCmp('textProduct'));
};
MODx.fireResourceFormChange = function(f,nv,ov) {
	Ext.getCmp('modx-panel-resource').fireEvent('fieldChange');
};