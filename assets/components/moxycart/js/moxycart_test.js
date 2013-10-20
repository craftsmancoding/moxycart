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


Moxycart.grid.MyGrid = function( config ) {
    /* Class parent constructor */
    Moxycart.grid.MyGrid.superclass.constructor.call( this, config );
};
Ext.extend( Moxycart.grid.MyGrid, MODx.grid.Grid, {
    /* Class members will go here */
} );
/* Register "moxycart-grid-mygrid" as an xtype */
Ext.reg( "moxycart-grid-mygrid", Moxycart.grid.MyGrid );

Moxycart.grid.MyGrid = function( config ) {
    config = config || {};
 
    /* Grid configuration options */
    Ext.applyIf( config, {
        id : "moxycart-grid-mygrid",
        title : _( "my_grid" ),
        url : Moxycart.config.connectors_url + "list.php",
        baseParams : {
            action : "getlist"
        },
        paging : true,
        autosave : true,
        remoteSort : true,
        /* Store field list */
        fields : [ {
            name : "id",
            type : "int"
        }, {
            name : "name",
            type : "string"
        }, {
            name : "menu"
        } ],
        /* Grid ColumnModel */
        columns : [ {
            header : _( "id" ),
            dataIndex : "id",
            sortable : true
        }, {
            header : _( "name" ),
            dataIndex : "name",
            sortable : true
        } ],
        /* Top toolbar */
        tbar : [ {
            xtype : "button",
            text : _( "create" ),
            handler : {
                xtype : "moxycart-window-create",
                blankValues : true
            },
            scope : this
        } ]
    } );
 
    /* Class parent constructor */
    Moxycart.grid.MyGrid.superclass.constructor.call( this, config );
};
 
Ext.extend( Moxycart.grid.MyGrid, MODx.grid.Grid, {
    /* Class members will go here */
} );
 
/* Register "moxycart-grid-mygrid" as an xtype */
Ext.reg( "moxycart-grid-mygrid", Moxycart.grid.MyGrid );