function renderManageCurrencies(){

	var currenciesStore = new Ext.data.Store({
		autoLoad:true,
		url: connector_url + 'json_currencies',
		sortInfo:{
			field:'name',
			direction: 'ASC'
		},
		reader:new Ext.data.JsonReader({
			idProperty: 'currency_id',
			root: 'results',
			totalProperty: 'total',
			fields:[
				{name: 'currency_id'},
				{name: 'name'},
				{name: 'code'},
				{name: 'symbol'},
				{name: 'is_active'}
			]
		})
	});

	var currenciesContainer = new Ext.Panel({
		title:'Manage Currencies',
		renderTo:'moxycart_canvas',
		layout:{
			type:'border'
		},
		height:500,		
		items:[
			{
				region:'center',
				xtype:'grid',
				id:'pnlCurrenciesGrid',
				store:currenciesStore,
				layout:'fit',
				autoExpandColumn: 'name',
				selModel:new Ext.grid.RowSelectionModel({
					singleSelect:true
				}),				
				loadMask:true,
				enableDragDrop:true,
				ddGroup:'currencyDDGroup',
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
						dataIndex: 'name',
						sortable:true
					  },
					  {
						header: 'Code',
						dataIndex: 'code',
				        sortable:true
					  },
					  {
						header: 'Symbol',
						dataIndex: 'symbol',
				        sortable:true
					  },
					  {
						header: 'Active',
						dataIndex: 'is_active',
						renderer: function(value) {
                            return "<input type='checkbox' disabled='disabled' " + (value ? "checked='checked'" : "") + ">";
                        },
                        sortable:true
					  },
					  {
						header:'',
						dataIndex: 'currency_id',
						editable:false,
						width:200,
						fixed:true,
						align:'center',
						renderer:function(value, metaData, record, rowIndex, colIndex, store){
							var html =  '<input type="button" value="Edit" class="x-btn x-btn-noicon x-box-item" style="height:20px;width:60px;" onclick="return onCurrenciesEdit(' + record.get('currency_id') + ');"/>';
							
							html += '&nbsp;&nbsp;';
							
							html += '<input type="button" value="Delete" class="x-btn x-btn-noicon x-box-item" style="height:20px;width:60px;" onclick="return onCurrenciesDelete(' + record.get('currency_id') + ');"/>';
												
							return html;
						}
					  }
				]),
				listeners:{
				    // Double-clicking = Edit Row
					dblclick:function(){
					   onDblClickGrid();
					},
					render:function(){
						var grid = Ext.getCmp('pnlCurrenciesGrid');
					}
				},
				bbar: new Ext.PagingToolbar({
        			store: currenciesStore,
        			displayInfo: true,
        			pageSize: 30,
        			prependButtons: true
        		})
			},
            {
				region:'north',
				height:60,
				xtype:'panel',
				bodyStyle:'padding:10px 0px 10px 30px;',
				border:false,
				layout:{
					type:'table',
					columns:2
				},				
				items:[
					{
						xtype:'button',
						text:'Create Currency',
						handler:function(){
							createUpdateCurrency(null);
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

function createUpdateCurrency(record){

	var isUpdate = false;
	if(record!=null){
		isUpdate=true;
	}

	var createCurrencyWin = new Ext.Window({
		title:(isUpdate?'Update Currency : ' + record.get('name'):'Create Currency'),
		modal:true,
		height:300,
		width:600,
		layout:'fit',
		items:[
			{
				xtype:'form',
				layout:'form',
				itemId:'frmCurrencies',
				border:false,
				labelSeparator:'',
				bodyStyle:'padding:30px 30px 30px 30px;',
				items:[
					{
						xtype:'hidden',
						name:'seq'
					},				
					{
						xtype:'hidden',
						name:'currency_id'
					},
					{
						xtype:'hidden',
						name: 'action',
						value:'create'
					},
					{
						xtype:'textfield',
						fieldLabel:'Name',
						width:'70%',
						name: 'name',
						allowBlank:false
					},
					{
						xtype:'textfield',
						fieldLabel:'Code',
						width:'20%',
						name: 'code'
					},
					{
						xtype:'textfield',
						fieldLabel:'Symbol',
						width:'20%',
						name: 'symbol'
					},
					/*cheap trick to always pass a value*/
					{
						xtype:'hidden',
						name: 'is_active',
						value:0
					}					
					,{
                         xtype: 'checkbox'
                        ,id:'is_active' 
                        ,name : 'is_active'
                        ,fieldLabel: 'Active?'
                        ,inputValue: 1    
                    }								
				]
			}
		],
		buttons:[
			{
				text:(isUpdate?'Update':'Save'),
				handler:function(btn){
					var frmCurrencies = createCurrencyWin.getComponent('frmCurrencies');
					
					if(!frmCurrencies.getForm().isValid()){
						Ext.Msg.show({
						   title:'Error',
						   msg: 'Please fill all required fields.',
						   buttons: Ext.Msg.OK,
						   fn: function(){
						   },
						   icon: Ext.MessageBox.ERROR
						});											
						return false;
					}
					
					var progressBar = new ProgressBar( (isUpdate?'Update Currencies':'Create Currency'), 'Processing currencies, please wait...');					
					progressBar.showProgress();
					
					frmCurrencies.getForm().submit({
						url: connector_url + 'currency_save',
						success: function(form, action) {
							progressBar.hideProgress();
							if(action.result.success){
								createCurrencyWin.destroy();
							}
							else{
								Ext.Msg.show({
								   title:'Error',
								   msg: action.result.msg,
								   buttons: Ext.Msg.OK,
								   fn: function(){
									createCurrencyWin.destroy();
								   },
								   icon: Ext.MessageBox.ERROR
								});													
							}
							
							Ext.getCmp('pnlManageInventory').getStore().reload();
						},
						failure: function(form, action) {
							progressBar.hideProgress();
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
							   title:'Error',
							   msg: msg,
							   buttons: Ext.Msg.OK,
							   fn: function(){
								createCurrencyWin.destroy();
							   },
							   icon: Ext.MessageBox.ERROR
							});
							
						}
					});
					
					
				}
			},
			{
				text:'Cancel',
				handler:function(){
					createCurrencyWin.destroy();
				}										
			}									
		]
	});

	createCurrencyWin.show();
	
	if(isUpdate){
		createCurrencyWin.getComponent('frmCurrencies').getForm().loadRecord(record);
		createCurrencyWin.getComponent('frmCurrencies').getForm().findField('action').setValue('update');
	}
	else{
    createCurrencyWin.getComponent('frmCurrencies').getForm().findField('seq').setValue(Ext.getCmp('pnlCurrenciesGrid').getStore().getCount());
	}
	
	createCurrencyWin.getComponent('frmCurrencies').getForm().isValid();
	

}

function onCurrenciesEdit(currency_id){
	onDblClickGrid();
}

function onCurrenciesDelete(currency_id){

	Ext.Msg.show({
	   title:'Confirm',
	   msg: 'Are you sure want to delete this currency?',
	   buttons: Ext.Msg.YESNO,
	   fn: function(buttonId ){
			if(buttonId=='yes'){
			
				var progressBar = new ProgressBar('Delete Currency', 'Processing specs, please wait...');
				progressBar.showProgress();
				
				Ext.Ajax.request({
				   url: connector_url + 'currency_save',
				   params:{
						action:'delete',
						currency_id:currency_id
				   },
				   success: function(response, options){
						progressBar.hideProgress();
						var results = Ext.decode(response.responseText);
						
						if(results.success){				
						}
						else{
							Ext.Msg.show({
							   title:'Error',
							   msg: results.msg,
							   buttons: Ext.Msg.OK,
							   icon: Ext.MessageBox.ERROR
							});						
						}
						
						Ext.getCmp('pnlCurrenciesGrid').getStore().reload();
				   },
				   failure: function(response, options){
						progressBar.hideProgress();
						var results = Ext.decode(response.responseText);
						
						if(results.success){
							Ext.Msg.show({
							   title:'Success',
							   msg: results.msg,
							   buttons: Ext.Msg.OK,
							   icon: Ext.MessageBox.INFO
							});						
						}
						else{
							Ext.Msg.show({
							   title:'Error',
							   msg: results.msg,
							   buttons: Ext.Msg.OK,
							   icon: Ext.MessageBox.ERROR
							});						
						}
						
						Ext.getCmp('pnlCurrenciesGrid').getStore().reload();
				   }
				});					
			}
	   },
	   icon: Ext.MessageBox.WARNING
	});											
	return false;

}

function onDblClickGrid(){

	var pnlCurrenciesGrid = Ext.getCmp('pnlCurrenciesGrid');
	var selModel = pnlCurrenciesGrid.getSelectionModel();
	
	if(selModel!=null && selModel.hasSelection()){
		var record  = selModel.getSelected();
		createUpdateCurrency(record);
	}
	else{
		Ext.Msg.show({
		   title:'Error',
		   msg: 'Please select a row to edit currency.',
		   buttons: Ext.Msg.OK,
		   icon: Ext.MessageBox.ERROR
		});		
	}

}