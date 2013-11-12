function renderVariationTerms(vtype_id){

	var specsStore = new Ext.data.Store({
		autoLoad:true,
		url: connector_url + 'json_variation_terms&vtype_id=' + vtype_id,
		reader:new Ext.data.JsonReader({
			idProperty: 'vterm_id',
			root: 'results',
			totalProperty: 'total',
			fields:[
				{name: 'vtype_id'},
				{name: 'vterm_id'},
				{name: 'variation_type'},
				{name: 'sku_suffix'},
				{name: 'sku_prefix'},
				{name: 'seq'},
				{name: 'name'}
			]
		})
	});

	var specsContainer = new Ext.Panel({
		title:'Manage Variation Terms',
		renderTo:'moxycart_canvas',
		layout:'border',
		height:500,		
		items:[
			{
				region:'center',
				xtype:'grid',
				store:specsStore,
				layout:'fit',
				id:'vinod',
				viewConfig: {
					autoFill: true,
					forceFit: true
				},				
				cm:new Ext.grid.ColumnModel([
					  {
						header:'Name',
						resizable: false,
						dataIndex: 'name'
					  },
					  {
						header: 'SKU Prefix',
						dataIndex: 'sku_prefix'
					  },
					  {
						header: 'SKU Suffix',
						dataIndex: 'sku_suffix'
					  },
					  {
						header:'',
						dataIndex: 'note',
						width:100,
						align:'center',
						renderer:function(){
							return '<input type="button" value="Delete" class="x-btn x-btn-noicon x-box-item" style="height:30px;width:90px;"   onclick="return onManageTermsDelete(' + record.get('vterm_id') + ');"/>';
						}
					  }
				]),
				 bbar : new Ext.PagingToolbar({
					pageSize: 25,
					store: specsStore,
					displayInfo: true,
					displayMsg: 'Displaying Records {0} - {1} of {2}',
					emptyMsg: "No Records to display"
				})				
			},
			{
				region:'north',
				height:30,
				border:false
			},
			{
				region:'south',
				height:30,
				border:false
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
	
}function onManageTermsDelete(vterm_id){
}