/**
 * @param integer offset
 * @param string sort column name
 * @param string dir ASC|DESC 
 */
function get_products(offset,sort,dir) {
    offset = typeof offset !== "undefined" ? offset : 0;
    sort = typeof sort !== "undefined" ? sort : "Product.seq";
    dir = typeof dir !== "undefined" ? dir : "ASC";
    var searchterm = jQuery('#searchterm').val();
    var url = moxycart.controller_url+"&class=page&method=storeproducts&offset="+offset+"&sort="+sort+"&dir="+dir+"&store_id="+moxycart.store_id+"&_nolayout=1";
    if (searchterm) {
        url = url + '&searchterm='+searchterm;
    }
    console.log("[Moxycart get_products()] requesting URL",url);
	Ext.Ajax.request({
        url: url,
        params: {},
        async:false,
        success: function(response){
            console.log("Success: Data received from "+url);
            Ext.fly("store_products").update(response.responseText);
        },
        failure: function(response){
            console.error("The request to "+url+" failed.", response);
        }
    });                
}

/**
 * Get the settings for the current store
 */
function get_store_settings() {
    var url = moxycart.controller_url+"&class=page&method=storesettings&store_id="+moxycart.store_id+"&_nolayout=1";
    console.log("[Moxycart get_store_settings()] requesting URL",url);
	Ext.Ajax.request({
        url: url,
        params: {},
        async:false,
        success: function(response){
            console.log("Success: Data received from "+url);
            Ext.fly("store_settings").update(response.responseText);
        },
        failure: function(response){
            console.error("The request to "+url+" failed.", response);
        }
    });                
}

function show_all_products() {
    jQuery('#searchterm').val('');
    return get_products(0);
}



/**
 * Can't remember what this does... I think it affects the "Save" button highlighting
 */
var triggerDirtyField = function(fld) {
	Ext.getCmp('modx-panel-resource').fieldChangeEvent(fld);
};
MODx.triggerRTEOnChange = function() {
	triggerDirtyField(Ext.getCmp('textProduct'));
};
MODx.fireResourceFormChange = function(f,nv,ov) {
	Ext.getCmp('modx-panel-resource').fireEvent('fieldChange');
};
