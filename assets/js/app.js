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
 * Draw a product's images and assets, obeying the system settings for thumb dimensions
 * See also Asset Manager's app.js file & its draw_tab func.
 */
function draw_assets() {
    console.log('[draw_assets]');
    // var data = parse_tpl("product_image",response.data.fields);
    
    jQuery('#product_assets').html('');
    // TODO: filtering

    // JS Hashes do not preserve order. Thus the "Order" array
    moxycart.product.AssetGroups = [];
    var arrayLength = moxycart.product.Assets.length;
    for (var i = 0; i < arrayLength; i++) {
        var Asset = moxycart.product.Assets[i];
        jQuery('#product_assets').append( moxycart.tpls.product_asset(Asset) );
        if (Asset.group) {
            moxycart.product.AssetGroups.push(Asset.group);
        }
    }
/*    
    //Groups.push(Assets[asset_id].group);
    Groups = array_unique(Groups);
    
    jQuery('#asset_category_filters').html('<li class="all first"><a href="#">All</a></li>');
    var arrayLength = Groups.length;
    for (var i = 0; i < arrayLength; i++) {
        if (Groups[i]) {
            jQuery('#asset_category_filters').append( category_tpl({"group": Groups[i]}));
        }
    }  

	jQuery("#product_assets").ele();
*/
    jQuery("#product_assets").disableSelection();
    jQuery(".sortable").sortable({
        connectWith: ".connectedSortable",
    }).disableSelection();


/*
    // Filter product_assets
    // Clone product_assets items to get a second collection for Quicksand plugin (image gallery)
    var $portfolioClone = $("#product_assets").clone();
    
    // Attempt to call Quicksand on every click event handler
    jQuery("#asset_category_filters a").click(function(e){
        
        jQuery("#asset_category_filters li").removeClass("current");
        jQuery("#asset_category_filters li").removeClass("first"); 
        
        // Get the class attribute value of the clicked link
        var $filterClass = $(this).parent().attr("class");

        if ( $filterClass == "all" ) {
            var $filteredPortfolio = $portfolioClone.find("li");
        } else {
            var $filteredPortfolio = $portfolioClone.find("li[data-type~=" + $filterClass + "]");
        }
        
        // Call quicksand
        jQuery("#product_assets").quicksand( $filteredPortfolio, { 
            duration: 800, 
            easing: 'swing' 
        });

        jQuery(this).parent().addClass("current");
    })
*/

}

/**
 * Open Asset colorbox
 * This lets users edit a specific Asset
 *
 * @param integer asset_id
 * @param url_target css selector where thumbnail img is to be shown
 * @param val_target css selector where asset_id is to be written
 */
function open_asset_modal(asset_id) {
    console.log('[open_asset_modal]',asset_id);
    var displayed = 0;
    jQuery.colorbox({
        inline:false, 
        width: "850",
        //innerWidth:settings.thumbnail_width+30,
        height: "90%",
        //innerHeight:settings.thumbnail_height+10,
        html:function(){

            var arrayLength = moxycart.product.Assets.length;
            for (var i = 0; i < arrayLength; i++) {
                if (moxycart.product.Assets[i].asset_id == asset_id) {
                    return moxycart.tpls.asset_modal(moxycart.product.Assets[i]);
                }
            }
        }
    });
}

/**
 * Open Thumbnail colorbox
 * This lets users select a product thumbnail or select an image for a custom image field.
 * It's a "film strip" modal.
 *
 * @param integer asset_id
 * @param url_target css selector where thumbnail img is to be shown
 * @param val_target css selector where asset_id is to be written
 */
function open_thumbail_modal(asset_id,url_target,val_target) {
    console.log('[open_thumbail_modal]',asset_id,val_target);
//    console.log('Thumb dimensions: %sx%s',settings.thumbnail_width,settings.thumbnail_height)
    jQuery.colorbox({
        inline:false, 
        width: "50%",
        //innerWidth:settings.thumbnail_width+30,
        height: "50%",
        //innerHeight:settings.thumbnail_height+10,
        html:function(){
            var preview = '';
            var arrayLength = moxycart.product.Assets.length;
            for (var i = 0; i < arrayLength; i++) {
                var Asset = moxycart.product.Assets[i];
                Asset.url_target = url_target;
                Asset.val_target = val_target;
                preview = preview + moxycart.tpls.thumbnail_image(Asset);
            }
            return preview;
        }
    });
}

/**
 * Select the given thumbnail: write the asset id back to the specified target 
 */
function select_image(asset_id,url,url_target,val_target) {
    console.log('[select_image] asset_id: %s thumb url: %s target: %s',asset_id,url,url_target,val_target);
    jQuery('#'+val_target).val(asset_id);
    jQuery('#'+url_target).html('<img src="'+url+'" />');
    jQuery.colorbox.close();
}

/**
 * Update an asset and its related data with data in the referenced form
 */
function update_asset(form_id) {
    var ModalData = form2js(form_id, '.', false);
    console.log('[update_asset] Modal Data:',ModalData);
    var arrayLength = moxycart.product.Assets.length;
    for (var i = 0; i < arrayLength; i++) {
        if (moxycart.product.Assets[i].asset_id == ModalData.asset_id) {
            console.log('Updating Asset: '+ModalData.asset_id);
            
            // This data here is specific to the Asset (not to the ProductAsset relation)
            mapi('asset','edit',ModalData.Asset);
            
            for (var key in ModalData.Asset) {
                moxycart.product.Assets[i].Asset[key] = ModalData.Asset[key];
            }
            delete ModalData.Asset;
            
            for (var key in ModalData) {
                moxycart.product.Assets[i][key] = ModalData[key];
            }
            break;
        }
    }
    draw_assets();
    jQuery.colorbox.close();
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


