function renderManageSpecs(){

	var specsStore = new Ext.data.Store({
		autoLoad:true,
		url: connector_url + 'json_specs',
		reader:new Ext.data.JsonReader({
			idProperty: 'spec_id',
			root: 'results',
			totalProperty: 'total',
			fields:[
				{name: 'spec_id'},
				{name: 'name'},
				{name: 'description'},
				{name: 'group'}
			]
		})
	});

	var specsContainer = new Ext.Panel({
		title:'Manage Specs',
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
						header: 'Group',
						dataIndex: 'group'
					  },
					  {
						header: 'Description',
						dataIndex: 'description'
					  },
					  {
						header:'',
						dataIndex: 'spec_id',
						width:100,
						align:'center',
						renderer:function(){
							return '<input type="button" value="Delete" class="x-btn x-btn-noicon x-box-item" style="height:30px;width:90px;"/>';
						}
					  }
				])/*,
				 bbar : new Ext.PagingToolbar({
					pageSize: 25,
					store: specsStore,
					displayInfo: true,
					displayMsg: 'Displaying Records {0} - {1} of {2}',
					emptyMsg: "No Records to display"
				})	*/			
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
						text:'Create Spec',
						handler:function(){
						
							var createSpecWin = new Ext.Window({
								title:'Create Spec',
								modal:true,
								height:300,
								width:600,
								layout:'fit',
								items:[
									{
										xtype:'form',
										layout:'form',
										itemId:'frmSpecs',
										border:false,
										labelSeparator:'',
										bodyStyle:'padding:30px 30px 30px 30px;',
										items:[
											{
												xtype:'textfield',
												fieldLabel:'Name',
												width:'70%',
												name: 'name'
											},
											{
												xtype:'textfield',
												fieldLabel:'Group',
												width:'70%',
												name: 'group'
											},
											{
												xtype:'textarea',
												fieldLabel:'Description',
												width:'100%',
												name: 'description'
											}											
										]
									}
								],
								buttons:[
									{
										text:'Save',
										handler:function(btn){
											var frmSpecs = createSpecWin.getComponent('frmSpecs');
											frmSpecs.getForm().submit({
												url: connector_url + 'spec_save',
												success: function(form, action) {
												
													Ext.Msg.show({
													   title:'Success',
													   msg: action.result.msg,
													   buttons: Ext.Msg.OK,
													   fn: function(){
														createSpecWin.destroy();
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
														createSpecWin.destroy();
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
											createSpecWin.destroy();
										}										
									}									
								]
							});
						
							createSpecWin.show();
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