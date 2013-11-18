function renderProductSortPanel(){

	var productSortStore = new Ext.data.Store({
		autoLoad:true,
		url: connector_url + 'json_products',
		sortInfo:{
			field:'seq',
			direction: 'ASC'
		},
		reader:new Ext.data.JsonReader({
			idProperty: 'product_id',
			root: 'results',
			totalProperty: 'total',
			fields:[
				{name: 'product_id'},
				{name: 'name'},
				{name: 'sku'},
				{name: 'type'},
				{name: 'qty_inventory'},
				{name: 'qty_alert'},
				{name: 'price'},
				{name: 'category'},
				{name: 'uri'},
				{name: 'is_active'},
				{name: 'seq'}
			]
		})
	});

	var productSortContainer = new Ext.Panel({
		title:'Product Sort Order',
		renderTo:'moxycart_canvas',
		layout:{
			type:'border'
		},
		height:500,		
		items:[
			{
				region:'center',
				xtype:'grid',
				id:'pnlProductSortGrid',
				store:productSortStore,
				layout:'fit',
				autoExpandColumn: 'name',
				selModel:new Ext.grid.RowSelectionModel({
					singleSelect:true
				}),				
				loadMask:true,
				enableDragDrop:true,
				ddGroup:'productSortDDGroup',
				enableHdMenu:false,
				viewConfig: {
					autoFill: true,
					forceFit: true,
					getRowClass:function(record, rowIndex, rp, ds){
						return 'moxycart-grid-row';
					}
				},				
				cm:new Ext.grid.ColumnModel([
					  {
						header:'Name',
						resizable: false,
						dataIndex: 'name'
					  },
					  {
						header: 'SKU',
						dataIndex: 'sku'
					  },
					  {
						header: 'Category',
						dataIndex: 'category'
					  }
				]),
				listeners:{
					dblclick:function(){},
					render:function(){
					
						var grid = Ext.getCmp('pnlProductSortGrid');
					
						var ddrow = new Ext.dd.DropTarget(grid.container, {
							ddGroup : 'productSortDDGroup',
							copy:false,
							notifyDrop : function(dd, e, data){

								var grid = Ext.getCmp('pnlProductSortGrid');
								var ds = grid.store;

								var sm = grid.getSelectionModel();
								var rows = sm.getSelections();

								if(dd.getDragData(e)) {
																
									var cindex=dd.getDragData(e).rowIndex;
									if(typeof(cindex) != "undefined") {
										for(i = 0; i <  rows.length; i++) {
											ds.remove(ds.getById(rows[i].id));
										}
										ds.insert(cindex,data.selections);
										sm.clearSelections();
										
										for(Icount=0;Icount<ds.getCount();Icount++){
										
											var record = ds.getAt(Icount);
										
											if(record.get('seq')!=Icount){												

												var values = {};
												Ext.applyIf(values, record.data);
												values.action = 'update';
												values.seq = Icount;
												
												
												Ext.Ajax.request({
												   url: connector_url + 'product_save',
												   params:values,
												   success: function(response, options){					
												   },
												   failure: function(response, options){					
												   }
												});												
											}
											
										}				
									}
								}
							}
						}); 
					
					}
				}
			},
			{
				region:'north',
				height:30,
				xtype:'panel',
				border:false
			},
/*
			{
				region:'south',
				height:30,
				border:false
			},
*/
			{
				region:'south',
				height:90,
				xtype:'panel',
				bodyStyle:'padding:10px 0px 10px 30px;',
				border:false,
				layout:{
					type:'hbox',
					pack:'center',
					align:'middle'
				},				
				items:[
					{
						xtype:'button',
						text:'Done',
						handler:function(){						
							backToParent();
						}
					}
				]
			},

			{
				region:'east',
				width:30,
				border:false
			},
			{
				region:'west',
				width:30,
				border:false
			}			
		]
	});
	
}

/**
 * See Moxycart::product_sort_order()
 */
function backToParent() {
    window.location = back_url;
}