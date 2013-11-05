function renderProduct(){
	var tabPanel = Ext.getCmp("modx-resource-tabs");
	
	if(tabPanel!=null){
	
		//Taxonomies tab configuration
		
		var taxonomiesTab = {
            title: 'Taxonomies'
            ,id: 'modx-resource-product-taxonomies-tab'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
            }
            ,items: getTaxonomiesFields()//[]
        };	
		
		tabPanel.insert(0, taxonomiesTab);		
	
		//Images tab configuration
		
		var imagesTab = {
            title: 'Images'
            ,id: 'modx-resource-product-images-tab'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
            }
            ,items: getImagesFields()//[]
        };	
		
		tabPanel.insert(0, imagesTab);	
	
		//Specs tab configuration
		
		var specsTab = {
            title: 'Specs'
            ,id: 'modx-resource-product-specs-tab'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
            }
            ,items: getSpecsFields()//[]
        };	
		
		tabPanel.insert(0, specsTab);	
	
		//Variations tab configuration
		
		var variationsTab = {
            title: 'Variations'
            ,id: 'modx-resource-product-variations-tab'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
            }
            ,items: getVariationsFields()//[]
        };	
		
		tabPanel.insert(0, variationsTab);		
		
		//Setting tab configuration
		
		var settingsTab = {
            title: 'Settings'
            ,id: 'modx-resource-product-settings-tab'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
            }
            ,items: getProductSettingsFields()//[]
        };	
		
		tabPanel.insert(0, settingsTab);	
		
		//Product tab configuration
		var productTab = {
            title: 'Product'
            ,id: 'modx-resource-product-tab'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
            }
            ,items: getProductsTabFields()//[]
        };	
		
		tabPanel.insert(0, productTab);
		tabPanel.setActiveTab(0);
		tabPanel.doLayout();
		
	}
}
function renderProductContainer(isProductContainerCreate, config){
	var tabPanel = Ext.getCmp("modx-resource-tabs");
	
	if(tabPanel!=null){
	
		//Add store setting tab
		var storeSettingsTab = {
            title: 'Store Settings'
            ,id: 'modx-resource-StoreSettings'
            ,cls: 'modx-resource-tab'
            ,layout: 'form'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400

            }
            ,items: getStoreSettingsFields(config)
        };
		
		tabPanel.insert(0, storeSettingsTab);
		
		//Add products tab
		
		var prductsTab = {
            title: 'Products'
            ,id: 'modx-resource-Products'
            ,cls: 'modx-resource-tab'
            ,layout: 'fit'
            ,labelAlign: 'top'
            ,labelSeparator: ''
            ,bodyCssClass: 'tab-panel-wrapper main-wrapper'
            ,autoHeight: true
            ,defaults: {
                border: false
                ,msgTarget: 'under'
                ,width: 400
				, height:400
            }
            ,items: (isProductContainerCreate)?getCreateProductFields(config):getProductsFields(config)
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
            }, {
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
    
    var cm = new Ext.grid.ColumnModel([
      {
        header:'Spec',
        resizable: false,
        dataIndex: 'state',
        sortable: true
      },
      {
        header: 'Value',
        dataIndex: 'filename',
        sortable: true
      },
      {
        header: 'Description',
        dataIndex: 'note',
        sortable: true
      }
    ]);
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
			border:false
			,layout:'form'			
            ,id: 'modx-resource-specs-columns'
			,style: 'border:0px'
            ,defaults: {
                labelSeparator: ''
                ,labelAlign: 'top'
                ,border: false
                ,msgTarget: 'under'
				,bodyCssClass:'pnl-mo-border'
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
    
    var cm = new Ext.grid.ColumnModel([
      {
        header:'Variant',
        resizable: false,
        dataIndex: 'state',
        sortable: true
      },
      {
        header: 'SKU',
        dataIndex: 'filename',
        sortable: true
      },
      {
        header: 'Cost',
        dataIndex: 'note',
        sortable: true
      },{
        header: 'Qty In Stock',
        dataIndex: 'note',
        sortable: true
      },
      {
          xtype: 'actioncolumn',
         items: [ {
                xtype: 'button',
               text:'Edit'
          },
          {
                 xtype: 'button',
               text:'View'
           }
        ]
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
			border:false
			,layout:'form'			
            ,id: 'modx-resource-variations-columns'
			,style: 'border:0px'
            ,defaults: {
                labelSeparator: ''
                ,labelAlign: 'top'
                ,border: false
                ,msgTarget: 'under'
				,bodyCssClass:'pnl-mo-border'
            },
            items:[{ 
				 xtype: 'button'
				,text: 'Manage Inventory'
				//,listeners: {
					//'click': {fn: this.clearFilter, scope: this}
				//}
			},this.VariationsGrid_panel]
  }];
}
function getProductSettingsFields(){
  return [{
            anchor: '100%',
			border:false
			,layout:'form'			
            ,id: 'modx-resource-productSettings-columns'
			,style: 'border:0px'
            ,defaults: {
                labelSeparator: ''
                ,labelAlign: 'top'
                ,border: false
                ,msgTarget: 'under'
				,bodyCssClass:'pnl-mo-border'
            },
            items:[{
			 xtype: 'textfield'
            ,fieldLabel: 'Alias'
            ,name: 'productSettings'
            ,id: 'modx-resource-productSettings'
            ,anchor: '100%'
			},
			new Ext.form.ComboBox({fieldLabel: 'Template',store: new Ext.data.ArrayStore({
				fields: [
					'displayText'
				],
				data: [['2-Col Responsive']]
			}),
			valueField: 'displayText',
			displayField: 'displayText'}),
			new Ext.form.ComboBox({fieldLabel: 'Currency',data: [[1, 'USD']]}),
			new Ext.form.ComboBox({fieldLabel: 'Product type',data: [['regular', 'Regular'], ['subscription', 'Subscription'], ['download', 'Download']]}),
			new Ext.form.ComboBox({fieldLabel: 'Product Container',data: [[1, 'My Store']]})
			]
  }];
}
function getStoreSettingsFields(config){
       config = config || {record:{}};
        return [{
            anchor: '100%',
			border:false
			,layout:'form'			
            ,id: 'modx-resource-storesettings-columns'
			,style: 'border:0px'
            ,defaults: {
                labelSeparator: ''
                ,labelAlign: 'top'
                ,border: false
                ,msgTarget: 'under'
				,bodyCssClass:'pnl-mo-border'
            },
            items:[ 
				   {
						border:false,
						frame:true,
						layout:'table',
						width:'100%',
						style: 'border:0px',
						layoutConfig:{
							tableAttrs:{
								cellspacing:8,								
								style: {
									//padding: '3px',									
									//border:0
								}
							},								            			
							columns:4
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
								text: 'Product type'
							},
							new Ext.form.ComboBox({fieldLabel: 'Product type',editable: true}),
							{
								colspan:2
							},
							{
								xtype: 'label',
								text: 'Default template'
							},
							new Ext.form.ComboBox({fieldLabel: 'Default template',editable: true}),
							{
								colspan:2
							},
							{
								xtype: 'label',
								text: 'Track Inventory'
							},
							new Ext.form.ComboBox({fieldLabel: 'Track Inventory',editable: true}),
							{
								colspan:2
							},
							{
								colspan:4,
								height:10
							},							
							{
								xtype: 'label',
								text: 'Units',
								style: 'margin-top:20px',
								ctCls:'v-align-top'
							},
							{
								//colspan:2,
								layout:'column',
								border:false,
								columns:4,
								//region:'north',
								items:[												
										
										{
											 layout:'fit',
											 border: false,
											 items: [
												{
													xtype: 'checkbox',
													boxLabel: 'Weight',
												},
												{
													height:10
												},
												{
													xtype: 'checkbox',
													boxLabel: 'Length'
												},
												{
													height:10
												},
												{
													xtype: 'checkbox',
													boxLabel: 'width'
												},
												{
													height:10
												},
												{
													xtype: 'checkbox',
													boxLabel: 'Volume'
												}
											]
										}										
									]
							},
							{
								xtype: 'label',
								text: 'Taxonomies',
								ctCls:'v-align-top'
							},
							{
								//colspan:2,
								layout:'column',
								border:false,
								columns:4,
								//region:'north',
								items:[										
										
										{
											layout:'fit',
											border: false,
											items: [
											{
												xtype: 'xcheckbox',
												boxLabel: 'Tags'
											},
											{
													height:10
											},
											{
												xtype: 'xcheckbox',
												boxLabel: 'Category'
											}
											]
										}
									]
							 },
							 {
								colspan:4,
								height:10
							},
							 {
								xtype: 'label',
								text: 'Variations',
								ctCls:'v-align-top'
							 },
							 {
								 colspan:3,
								 layout:'column',								 
								 border:false,
								 items: [
								 {
									layout:'table',
									width:'100%',									
									layoutConfig:{
										tableAttrs:{
											cellspacing:8,								
											style: {
												
											}
										},								            			
										columns:2
									},
									items:[
										{
											xtype: 'label',
											text: 'Size'
										},
										new Ext.form.ComboBox({fieldLabel: 'Size', editable: true}),
										{
											xtype: 'label',
											text: 'Color'
										},
										new Ext.form.ComboBox({fieldLabel: 'Color',editable: true}),
										{
											xtype: 'label',
											text: 'Material'
										},
										new Ext.form.ComboBox({fieldLabel: 'Material',editable: true})
									]										
								 }]				   
							   }
						]
				   }	
				   
				   
				]
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
	return [{
				layout:'form'
				,anchor: '100%'
				,id: 'modx-resource-products-columns'
				,defaults: {
					labelSeparator: ''
					,labelAlign: 'top'
					,border: false
					,msgTarget: 'under'
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
				},new Ext.form.ComboBox({fieldLabel: 'Category',editable: true,width:60}),
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
					 anchor: '100%',
				    fieldLabel:'Content'
				}] 
	  }];
}

function getProductsFields(config){
	var store = new Ext.data.Store();
    
    var cm = new Ext.grid.ColumnModel([
      {
        header:'Name',
        resizable: false,
        dataIndex: 'state',
        sortable: true
      },
      {
        header: 'SKU',
        dataIndex: 'filename',
        sortable: true
      },
      {
        header: 'Category',
        dataIndex: 'note',
        sortable: true
      },
      {
          xtype: 'actioncolumn',
         items: [ {
                xtype: 'button',
               text:'Edit'
          },
          {
                 xtype: 'button',
               text:'View'
           }
        ]
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
         bbar : new Ext.Toolbar({
            pageSize: 25,
            store: store,
            displayInfo: true,
            displayMsg: 'Displaying Records {0} - {1} of {2}',
            emptyMsg: "No Records to display"
           })
        });
	return [{ 
			layout:'card',
			activeItem:0,
			id: 'modx-resource-products-columns',
			defaults: {
				labelSeparator: ''
				,labelAlign: 'top'
				,border: false
				,msgTarget: 'under'
			},
			items:[{
				 layout:'border',
			     id: 'modx-resource-productsList-columns'
				,defaults: {
					labelSeparator: ''
					,labelAlign: 'top'
					,border: false
					,msgTarget: 'under'
				},
				items:[
					{
						region:'north',
						layout:'column',
						columns:6,
						height:60,
						xtype:'panel',
						items:[
							{
								xtype: 'button',
							   text:'Add Product',
							   listeners: {
									'click': {fn: function(){
										document.location='index.php?id=2&a=79&parent=2&type=regular';
									}, scope: this}
								}
							 },{
								xtype: 'button',
								text:'Manage Inventory'
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
							 }
						]
					},
					this.productsGrid_panel
				] 
		   },{
				layout:'form'
				,anchor: '100%'
				,id: 'modx-resource-Addproducts-columns'
				,defaults: {
					labelSeparator: ''
					,labelAlign: 'top'
					,border: false
					,msgTarget: 'under'
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
						},{xtype:'spacer',anchor:'100%',width:'100%'},{
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
				},new Ext.form.ComboBox({fieldLabel: 'Category',editable: true,width:60}),
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
					xtype: 'textarea'
					,name: 'textProduct'
					,id: 'textProduct'
					,hideLabel: true
					,anchor: '100%'
					,height: 400
					,grow: false
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