function renderVariationTypes(){

	var variationTypesStore = new Ext.data.Store({
		autoLoad:true,
		url: connector_url + 'json_variation_types',
		reader:new Ext.data.JsonReader({
			idProperty: 'vtype_id',
			root: 'results',
			totalProperty: 'total',
			fields:[
				{name: 'vtype_id'},
				{name: 'seq'},
				{name: 'name'},
				{name: 'description'}
			]
		})
	});

	var variationTypesContainer = new Ext.Panel({
		title:'Manage Variation Types',
		renderTo:'moxycart_canvas',
		layout:'border',
		height:500,		
		items:[
			{
				region:'center',
				xtype:'grid',
				store:variationTypesStore,
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
						header: 'Description',
						dataIndex: 'description'
					  },
					  {
						header: 'Terms',
						dataIndex: 'terms'
					  },
					  {
						header:'',
						dataIndex: 'note',
						width:100,
						align:'center',
						renderer:function(value, metaData, record, rowIndex, colIndex, store){
							var html = '<input type="button" value="Manage Terms" class="x-btn x-btn-noicon x-box-item" style="height:30px;width:90px;" onclick="return onManageTerms(' + record.get('vtype_id') + ');"/>';
							
							html += '&nbsp;&nbsp;';
							
							html += '<input type="button" value="Delete" class="x-btn x-btn-noicon x-box-item" style="height:30px;width:90px;"  onclick="return onVariationTypesDelete(' + record.get('vtype_id') + ');"/>';
							
							return html;
						}
					  }
				]),
				 bbar : new Ext.PagingToolbar({
					pageSize: 25,
					store: variationTypesStore,
					displayInfo: true,
					displayMsg: 'Displaying Records {0} - {1} of {2}',
					emptyMsg: "No Records to display"
				})				
			},
			{
				region:'north',
				height:60,
				xtype:'panel',
				bodyStyle:'padding:10px 0px 10px 30px;',
				border:false,
				layout:{
					type:'hbox',
					align:'middle'
				},				
				items:[
					{
						xtype:'button',
						text:'Create Variation Type',
						handler:function(){
						
							var createVariationTypWin = new Ext.Window({
								title:'Create Variation Type',
								modal:true,
								height:300,
								width:600,
								layout:'fit',
								items:[
									{
										xtype:'form',
										layout:'form',
										itemId:'frmVariationType',
										border:false,
										labelSeparator:'',
										bodyStyle:'padding:30px 30px 30px 30px;',
										items:[
											{
												xtype:'textfield',
												fieldLabel:'Name',
												width:'70%'
											},
											{
												xtype:'textarea',
												fieldLabel:'Description',
												width:'100%'
											}											
										]
									}
								],
								buttons:[
									{
										text:'Save',
										handler:function(btn){
											var frmVariationType = createVariationTypWin.getComponent('frmVariationType');
											
											frmVariationType.getForm().submit({
												url: moxycart_connector_url + '?f=spec_save',
												success: function(form, action) {
												
													Ext.Msg.show({
													   title:'Success',
													   msg: action.result.msg,
													   buttons: Ext.Msg.OK,
													   fn: function(){
														createVariationTypWin.destroy();
													   },
													   icon: Ext.MessageBox.INFO
													});
																										
												},
												failure: function(form, action) {
													
													var msg = '';
													
													switch (action.failureType) {
														case Ext.form.Action.CLIENT_INVALID:
															msg = 'Form fields may not be submitted with invalid values';
															break;
														case Ext.form.Action.CONNECT_FAILURE:
															msg = 'Ajax communication failed';
															break;
														case Ext.form.Action.SERVER_INVALID:
														   msg = action.result.msg;
												   }												
												
													Ext.Msg.show({
													   title:'Success',
													   msg: msg,
													   buttons: Ext.Msg.OK,
													   fn: function(){
														createVariationTypWin.destroy();
													   },
													   icon: Ext.MessageBox.INFO
													});
													
												}
											});
											
											
										}
									},
									{
										text:'Cancel',
										handler:function(){
											 createVariationTypWin.destroy();
										}										
									}									
								]
							});
						
							createVariationTypWin.show();
						}
					}
				]
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
	
}

function onManageTerms(vTypeID){
	MODx.loadPage(MODx.action['moxycart:index'], 'f=variation_terms_manage&vtype_id=' + vTypeID);
}

function onVariationTypesDelete(vTypeID){
	alert(vTypeID);
}