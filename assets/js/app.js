/**
 * This is the javascript that supports our thin HTML "client" to help it 
 * interface with our REST API.
 *
 *
 * @package moxycart
 */
 
if (typeof jQuery == 'undefined') {
    alert('jQuery is not loaded. Moxycart HTML client cannot load.');
}
else {
    console.debug('[moxycart html client]: jQuery loaded.');
}

/**
 * Open colorbox
 *
 * @param target css selector where value (i.e. the asset_id) should be written
 */
function open_thumbail_modal(asset_id,url_target,val_target) {
    console.log('[open_thumbail_modal]',asset_id,val_target);
    console.log('Thumb dimensions: %sx%s',settings.thumbnail_width,settings.thumbnail_height)
    var displayed = 0;
    jQuery.colorbox({
        inline:false, 
        width:"50%",
        height:settings.thumbnail_height,
        html:function(){
            console.log('generating colorbox html');
            var preview = '';
            for(var asset_id in product.RelData.Asset){
                if (asset_id){
                    var A = product.RelData.Asset;
                    if (typeof A[asset_id] !== "undefined") {
                        A[asset_id].url_target = url_target;
                        A[asset_id].val_target = val_target;
                        A[asset_id].thumbnail_width = settings.thumbnail_width;
                        A[asset_id].thumbnail_height = settings.thumbnail_height;
                        console.log('Parsing:', A[asset_id]);
                        preview = preview + parse_tpl("thumbnail_image_tpl",A[asset_id]);
                    }
                }
            }
            return preview;
        }
    });
    
}

/**
 * In its own function in case anything changes with
 * routing.
 * @param c string classname
 * @param m string methodname
 */
function controller_url(c,m) {
    return moxycart.controller_url+'&class='+c+'&method='+m;
}

/**
 * Update HTML on the page
 */
function replace_me(target,data) {
    console.debug('[replace_me] target: ',target);
    if(jQuery('#'+target).length ==0) {
        console.error('[replace_me] Invalid target id %s. Selector failed', target);
    }
    else {
        jQuery('#'+target).html(data);
    }
}

/**
 * Grabs a value from src id and appends it to dst id
 * @param string src DOM ID
 * @param string dst DOM ID
 */ 
function append_me(src,dst) {
    console.debug('[append_me] src: '+src+' dst: '.dst);
    var data = jQuery('#'+src).val();
    jQuery('#'+dst).append(data);
}

/**
 * Removes an element from its location. We use "event" here so we can determine
 * where exactly the thing to be removed is located.
 *
 * E.g. place it in a <td> to remove the containing row:
 * onclick="javascript:remove_me.call(this,event,'tr');"
 */
function remove_me(event,parent) {
    console.debug('[remove_me] parent: '+parent);
    jQuery(this).closest(parent).remove();
}

/**
 * Paint the canvas with the data received from the specified page 
 * @param string page name of function in the pageConteroller
 * @param object data (optional) default {}
 * @param string target (optional) DOM ID where to write result
 */
function paint(page,data,target) {
    target = typeof target !== 'undefined' ? target : 'moxycart_canvas'; // default
    data = typeof data !== 'undefined' ? data : {}; // default
    data._nolayout = 1; // omits header/footer wrapping
    console.debug('[paint]',page,data,target);
    var url = controller_url('page',page);
    jQuery.get(url, data, function( response ) {    
        replace_me(target,response);
        jQuery('#moxycart_msg').html('');
        jQuery('#moxycart_msg').show();
        page_init();
    })
    .fail(function() {
        console.error('[paint] get request to %s failed', url);
        return show_error('Get request failed: '+url);
    });
}

/**
 * Given JSON, populate a form with it.  IDs of form fields should correspond
 * to the keys of the JSON data.
 *
 * @param object data
 */
function populate_form(data) {
	jQuery.each(data, function(name, val){
        var $el = jQuery('#'+name),
            type = $el.attr('type');	    
        switch(type){
            case "checkbox":
                $el.attr("checked", "checked");
                break;
            case "radio":
                $el.filter('[value="'+val+'"]').attr("checked", "checked");
                break;
            default:
                $el.val(val);
        }
    });
}

/**
 * mapi : Moxycart API
 *
 * This is the primary function that drives our simple HTML client. This function
 * can dynamically load/replace parts of a page (sorta a "javascript include"), 
 * and it can approximate the effect of clicking on a standard <a> link, but it's 
 * all Ajax-REST based.
 *
 * @param string classname controller class to be requested for a JSON response
 * @param string methodname 
 * @param hash data any additional data to be included in the request to the controller 
 */
function mapi(classname,methodname,data,callback) {
    data = typeof data !== 'undefined' ? data : {}; // default
    
    console.debug('[mapi]',classname,methodname,data);
    
    // We need to set some POST data, otherwise routing will fail.
    data._moxycart = Math.random()*10000000000000000;
    // Ajax post
    var url = controller_url(classname,methodname);    
    jQuery.post(url, data, function( response ) {
        console.debug(response);
        if(response.status == 'fail') {
            console.log(response.data.errors);
            var msg = 'Error:<br/>';
            for(var fieldname in response.data.errors) {
                msg = msg + response.data.errors[fieldname] + '<br/>';
            }
            return show_error(msg); 
        }
        else if (response.status == 'success') {
            show_success(response.data.msg);
            if (callback != void 0) {
                callback(response);
            }
        }
    },'json')
    .fail(function() {
        console.error('[mapi] post to %s failed', url);
        return show_error('Request failed.');
    });
}

/**
 *
 *
 */
function submit_form(formid,url,redirect) {
    console.debug('[submit_form]',formid,url);
    var data = jQuery('#'+formid).serialize();
    jQuery.post(url, data,function( response ) {
        console.debug('Response:', response);
        if(response.status == 'fail') {
            console.log('The operation failed!', response.data.errors);
            var msg = 'Error:<br/>';
            for(var fieldname in response.data.errors) {
                msg = msg + response.data.errors[fieldname] + '<br/>';
            }
            return show_error(msg); 
        }
        else if (response.status == 'success') {
            console.log('[submit_form] Success!', response.data.msg);
            show_success(response.data.msg);
            paint(redirect);
        }
    },'json');
}

function handle_response(response){
    if(response.status == 'fail') {
        console.log(response.data.errors);
        var msg = 'Error:<br/>';
        for(var fieldname in response.data.errors) {
            msg = msg + response.data.errors[fieldname] + '<br/>';
        }
        return show_error(msg); 
    }
    else if (response.status == 'success') {
        show_success(response.data.msg);
        paint(redirect);
    }
}

/**
 * Submitting a search form and repainting the page
 *
 */
function searchform(formid,page) {
    var data = jQuery('#'+formid).serialize();
    console.debug('[searchform] refresh page: '+page,data);
    return paint(page,data);
}

/**
 * Show a simple error message, then fade it out and clear it so we can reuse the div.
 */
function show_error(msg) {
    jQuery('#moxycart_msg').html('<div class="danger">'+msg+'</div>');
}


/**
 * Show a success message, then fade it out and clear it so we can reuse the div.
 */
function show_success(msg) {
    jQuery('#moxycart_msg').html('<div class="success">'+msg+'</div>')
    .delay(3000).fadeOut(function() {
        jQuery(this).html('');
        jQuery(this).show(); 
    });
}


/**
 * @param integer offset
 * @param string sort column name
 * @param string dir ASC|DESC 
 */
function get_products(offset,sort,dir) {
    offset = typeof offset !== "undefined" ? sort : 0;
    sort = typeof sort !== "undefined" ? sort : "name";
    dir = typeof dir !== "undefined" ? dir : "ASC";
    var searchterm = jQuery('#searchterm').val();
    var url = moxycart.connector_url+"&class=page&method=storeproducts&offset="+offset+"&sort="+sort+"&dir="+dir+"&store_id="+moxycart.store_id+"&_nolayout=1";
    if (searchterm) {
        url = url + '&searchterm='+searchterm;
    }
    console.log("[Moxycart get_data()] requesting URL",url);
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

function show_all_products() {
    jQuery('#searchterm').val('');
    return get_products(0);
}



function page_init() {
    console.debug('[page_init]');
    jQuery(".ui-sortable").sortable();
}


