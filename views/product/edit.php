<script>
/**
 * Handlebars Parsing
 *
 * @param string src of a handlebars id :<script id="entry-template" type="text/x-handlebars-template"> 
 * @param object data key/value pairs
 */
function parse_tpl(src,data) {
    var source   = jQuery('#'+src).html();
    var template = Handlebars.compile(source);
    return template(data);    
}

function remove_relation(product_id) {
    jQuery('#product_relation_'+product_id).remove();
    jQuery('#product_'+product_id+ ' span').show();
    jQuery('#product_'+product_id+' strong').css("color","black");    
}

/**
 * We can't put any jQuery(document).ready stuff in the open here.
 */
function product_init() {
    console.log('[product_init]');
    
    // Compile handlebars templates
    moxycart['tpls'] = {};
    moxycart.tpls.related_product = Handlebars.compile(jQuery('#related_product_tpl').html());
    moxycart.tpls.product_asset = Handlebars.compile(jQuery('#product_asset_tpl').html());
    moxycart.tpls.thumbnail_image = Handlebars.compile(jQuery('#thumbnail_image_tpl').html());
    moxycart.tpls.asset_modal = Handlebars.compile(jQuery('#asset_modal_tpl').html());
    
    populate_form(moxycart.product);
	jQuery('#moxytab').tabify();
	jQuery('.datepicker').datepicker();
    draw_assets();


    // Dropzone for Assets 
    var myDropzone = new Dropzone("div#image_upload", {url: moxycart.assets_url});    
    // Refresh the list on success (append new tile to end)
    myDropzone.on("success", function(file,response) {
        console.log('Dropzone Response',response);
//        response = jQuery.parseJSON(response);
        if (response.status == "success") {
            console.log('Dropzone Success - response fields:',response.data.fields);
            moxycart.product.Assets.push({
                asset_id: response.data.fields.asset_id,
                is_active: 1,
                Asset: response.data.fields 
            });
            draw_assets();
            jQuery(".dz-preview").remove();
            save_product(moxycart.product_save_method);
       } 
       else {                           
            $(".dz-success-mark").hide();
            $(".dz-error-mark").show();
            $(".moxy-msg").show();
            $("#moxy-result").html("Failed");
            $("#moxy-result-msg").html(response.data.msg);
            $(".moxy-msg").delay(3200).fadeOut(400);
       }
    });

    // ProductRelation Autocomplete
    // customizations here for compatibility 
    jQuery('#search_products').autocomplete({
        source: (function() {
//            console.log('Autocomplete...');
            var xhr;
            var url = controller_url("product","search");
            return function(request, response) {
                if (xhr) {
                    xhr.abort();
                }
                xhr = jQuery.ajax({
                    url: url,
                    data: {
                        //"searchterm:like": request.term
                        "searchterm": request.term
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function(data) {
                        console.log('Autocomplete search success.',data);
                        // Tricky because our data structure contains {"data":{"results":[{}]}}
                        response(data.data.results);
                    },
                    error: function() {
                        console.log('Autocomplete search error.');
                        response([]);
                    }
                });
            }
        })(),
        select: function(event, ui) {
            var content = parse_tpl('related_product_tpl',ui.item)
            jQuery('#product_relations').append(content);
            jQuery('#search_products').val('');
            event.preventDefault(); // clear out text
        }
    }); 

    // Trash Can
	jQuery( "#trash-can" ).droppable({
		
		over: function( event, ui ) {
			jQuery(this).addClass('over-trash');
		},
		out: function(event, ui) {
			var id = jQuery(ui.draggable).attr('id');
			jQuery(this).removeClass('over-trash');
		},
	    drop: function( event, ui ) {
	      	var id = jQuery(ui.draggable).attr('id');
	      	var asset_id = jQuery(ui.draggable).find('input').val();	      
	      	delete_asset(asset_id);
		    jQuery(this).removeClass('over-trash');

		    return false;
	    }
    });

    // Delete Asset
    jQuery( "#delete_asset_modal" ).dialog({
        autoOpen: false,
        open: function( event, ui ) {
            jQuery.colorbox.close();     
        },
        height: 330,
        width: 500,
        modal: true,
        closeOnEscape: true,
        buttons: {
            "Delete": function() {
                var asset_id = jQuery(this).data('asset_id');
                var product_id = moxycart.product.product_id;
                mapi('productasset','delete',{
                    asset_id: asset_id,
                    product_id: product_id
                });
                var arrayLength = moxycart.product.Assets.length;
                for (var i = 0; i < arrayLength; i++) {
                    if (moxycart.product.Assets[i].asset_id == asset_id) {
                        moxycart.product.Assets.splice(i,1); // unset
                    }
                }
          		jQuery('#product-asset-'+asset_id).remove();
          		draw_assets();
                jQuery( this ).dialog( "close" );
            },
            "Remove from Product": function() {
                var asset_id = jQuery(this).data('asset_id');
                var product_id = moxycart.product.product_id;
                mapi('productasset','remove',{
                    asset_id: asset_id,
                    product_id: product_id
                });
                var arrayLength = moxycart.product.Assets.length;
                for (var i = 0; i < arrayLength; i++) {
                    if (moxycart.product.Assets[i].asset_id == asset_id) {
                        moxycart.product.Assets.splice(i,1); // unset
                    }
                }
          		jQuery('#product-asset-'+asset_id).remove();
          		draw_assets();
                jQuery( this ).dialog( "close" );
            },
            "Cancel": function() {
                jQuery( this ).dialog( "close" );
            }
        }   
    });

        
    // Custom Field Selection Modal
    jQuery( "#custom_fields_form" ).dialog({
        autoOpen: false,
        height: 330,
        width: 500,
        modal: true,
        closeOnEscape: true,
        buttons: {
            "Ok": function() {
                //Fields[field_id][]
                var field_ids = [];
                jQuery('#custom_fields_form input:checked').each(function() {
                    field_ids.push(jQuery(this).attr('value'));
                });
//                field_ids.reverse();
                jQuery('#product_fields').html(''); // Blank it out
                console.debug('Attaching field ids: ', field_ids);
                var field_ids_cnt = field_ids.length;
                for (var i = 0; i < field_ids_cnt; i++) {
                    var field_id = field_ids[i];
                    console.log('Generating field '+field_id);
                    // Had to customize mapi...
                    // mapi('field','generate',{"field_id":field_id,"name":"Fields[field_id][]"});
                    // This MUST be outside of the .post call!!! Otherwise it will always be written with the last 
                    // field_id because js will execute BEFORE the postback occurs!!!
                    jQuery('#product_fields').append('<input type="hidden" name="Fields[field_id][]" value="'+field_id+'" />'); 
                    var url = controller_url('field','generate');    
                    jQuery.post(url, {"field_id":field_id,"name":"Fields[value][]","product_id":moxycart.product.product_id}, function( response ) {
                            if(response.status == 'fail') {                            
                                var msg = 'Error:<br/>'+ response.data.error;
                                return show_error(msg); 
                            }
                            else if (response.status == 'success') {
                                console.debug('Drawing field.');
                                jQuery('#product_fields').append(response.data); 
                            }
                        },'json')
                        .fail(function() {
                            console.error('[mapi] post to %s failed', url);
                        });                        
                }
                
                jQuery( this ).dialog( "close" );
            },
            "Cancel": function() {
                jQuery( this ).dialog( "close" );
            }
        }   
    });


    jQuery("#taxonomy-modal").dialog({
        autoOpen: false,
        height: 300,
        width: 500,
        modal: true,
        closeOnEscape: true
    });


    // on page load, initialize fieldset state
    jQuery('.parent-term-option').each(function () {
        var fs_child_id = jQuery(this).data('fs_child_id');
        if(jQuery(this).prop("checked")) {
            if(jQuery('#Options_meta__'+fs_child_id+'_').val() != 'all_terms') {
                jQuery('.fset-'+fs_child_id).show();
            }
        } else {
            jQuery('.fset-'+fs_child_id).hide();
        }
    });

};


function save_product(method) {
    if (moxycart.settings.load_tinymce) {
        jQuery('#content').val(tinyMCE.activeEditor.getContent());
    }

    var values = jQuery('#product_form').serialize();
    console.log('[save_product] '+method, values);
    if (method == 'create') {
        mapi('product','create',values, function(response) {
            console.debug('Redirecting after successful create.');
            window.location.href = controller_url('page','productedit')+'&product_id='+response.data.id;    
        });

    }
    else {
        mapi('product',method,values);
    }
}

function delete_product(product_id,redirect) {
    console.log('[delete_product] '+product_id);
    if(confirm("Are you sure? This cannot be undone."))
    {
        mapi('product','delete',{product_id:product_id});
        window.location = redirect;
        return;
    }
}


jQuery(document).ready(function() {
    // on change of the select terms option, hide/show fieldset
    jQuery('.term-option-wrap select').on('change',function() {
        if(jQuery(this).val() != 'all_terms') {
            jQuery(this).parent().next().show();
        } else {
            jQuery(this).parent().next().hide();
        }
    });
});
</script>

<!-- !related_product_tpl -->
<script id="related_product_tpl" type="text/x-handlebars-template">
<tr>
    <td>
        <input type="hidden" name="Relations[related_id][]" value="{{id}}"/>
        <a href="<?php print static::page('productedit',array('product_id'=>'')); ?>{{id}}">{{value}}</a>
    </td>
    <td>
    <?php 
    print \Formbuilder\Form::dropdown('Relations[type][]', $data['relation_types']); 
    ?>
    </td>
    <td>
        <span class="btn" onclick="javascript:remove_me.call(this,event,'tr');">Remove</span>
    </td>
</tr>
</script>

<!-- !product_asset_tpl 
onclick="javascript:jQuery('#asset_edit_form').data('asset_id', '{{asset_id}}').dialog('open');"
-->
<script id="product_asset_tpl" type="text/x-handlebars-template">
<li class="li_product_asset sortable" id="product-asset-{{Asset.asset_id}}">
	<div class="img-info-wrap">
        <img src="{{Asset.thumbnail_url}}" alt="{{Asset.alt}}" width="{{Asset.thumbnail_width}}" height="{{Asset.thumbnail_height}}" onclick="javascript:open_asset_modal('{{Asset.asset_id}}');" class="{{#unless is_active}}inactive{{/unless}}" style="cursor:pointer;"/>
	    <input type="hidden" id="asset_asset_id_{{Asset.asset_id}}" name="Assets[asset_id][]" value="{{Asset.asset_id}}"/>
	    <input type="hidden" id="asset_group_{{Asset.asset_id}}" name="Assets[group][]" value="{{group}}"/>
	    <input type="hidden" id="asset_is_active_{{Asset.asset_id}}" name="Assets[is_active][]" value="{{is_active}}"/>	
        <div class="img-info-inner">
            <p class="asset-id-ph"><span id="asset_title_{{Asset.asset_id}}">{{title}}</span> ({{Asset.asset_id}})</p>
            <p class="asset-title-ph" id="asset_group_vis_{{Asset.asset_id}}"><strong>{{Asset.basename}}</strong></p>
        </div>    
	</div>
</li>
</script>

<!-- !thumbnail_image_tpl -->
<script id="thumbnail_image_tpl" type="text/x-handlebars-template">
<div class="asset_thumbnail_item-wrap" style=" background: #fff;border: 1px solid #ddd;width: {{Asset.thumbnail_width}}px;height:{{Asset.thumbnail_height}}px;float:left;margin: 5px;">
    <div class="asset_thumbnail_item img-info-wrap">
        <img src="{{Asset.thumbnail_url}}" 
            alt="{{Asset.alt}}" 
            width="{{Asset.thumbnail_width}}" 
            height="{{Asset.thumbnail_height}}"
            onclick="javascript:select_image({{asset_id}},'{{{Asset.thumbnail_url}}}','{{url_target}}','{{val_target}}');"/>
        <div class="img-info-inner">
            <p class="asset-id-ph"><span id="asset_title_{{Asset.asset_id}}">{{title}}</span> ({{Asset.asset_id}})</p>
            <p class="asset-title-ph" id="asset_group_vis_{{Asset.asset_id}}"><strong>{{Asset.basename}}</strong></p>
        </div>
    </div>
</div>
</script>

<!-- !asset_modal_tpl -->
<script id="asset_modal_tpl" type="text/x-handlebars-template">
    <form id="asset_modal_form">
        <div id="asset_modal_form-inner">
        	<h3>Edit Asset ({{asset_id}})</h3>
        	
            <input type="hidden" name="asset_id" value="{{asset_id}}"/>
            <input type="hidden" name="Asset.asset_id" value="{{asset_id}}"/>
            
                <div class="asset-edit-inner">
                    
                    <div class="clearfix">
                        <div class="span70 pull-left">
                            <div class="row-input">
                                 <label class="row-lbl" for="modal_asset_title">Title</label>
                                 <input class="row-field" type="text" name="Asset.title" id="modal_asset_title" value="{{Asset.title}}" />
                            </div>
                           
                            
                            <div class="row-input">
                                 <label class="row-lbl" for="modal_asset_alt">Alt</label>
                                <input class="row-field" type="text" name="Asset.alt" id="modal_asset_alt" value="{{Asset.alt}}" />
                            </div>
        
                            <div class="row-input">
                                <label class="row-lbl" for="modal_asset_group">Group</label>
                                <input class="row-field" type="text" name="group" id="modal_asset_group" value="{{group}}" />
                            </div>
                           
                            
                            <div class="row-input">
                                <label class="row-lbl" for="modal_asset_is_active">Is Active?</label>
                                <input type="hidden" name="is_active" value=""/>
                                <input class="row-field" type="checkbox" name="is_active" id="modal_asset_is_active" value="1" {{#if is_active}}checked="checked"{{/if}}/>
                            </div>
        
                            <!--div class="row-input">
                                 <label class="row-lbl" for="modal_asset_thumbnail_override">Thumbnail Override</label>
                                <input class="row-field" type="text" name="Asset.thumbnail_url" id="modal_asset_thumbnail_override" value="{{Asset.thumbnail_url}}" placeholder="http://"/>
                            </div-->
        
                        </div>
        
                        <div class="span20 pull-left">
                            <div class="row-input">
                                <span id="modal_asset_thumb"><img src="{{Asset.thumbnail_url}}" /></span>
                            </div>
        
                        </div>
                    </div>
                    
                    {{#if Asset.is_image}}
                    <div class="span100">
                        <div class="row-input">
                            <label class="row-lbl">Full Dimensions:</label> 
                            <div class="non-input"><span id="modal_asset_width">{{Asset.width}}</span> x <span id="modal_asset_height">{{Asset.height}}</span></div>
                        </div> 
        
                        <div class="row-input">
                            <span id="modal_asset_img"><img src="{{Asset.url}}" width="{{Asset.width}}" height="{{Asset.height}}" /></span>
                        </div>
        
                    </div>
                    {{/if}}
                </div>
        	</div>
        </div>
    	<div class="moxy-modal-controls">
            <span class="btn" onclick="javascript:update_asset('asset_modal_form');">Save</span>
            <span class="btn" onclick="javascript:jQuery.colorbox.close();">Cancel</span>
            <div style="float:right; padding-right:20px;">
                <span class="btn" onclick="javascript:jQuery('#delete_asset_modal').data('asset_id', '{{asset_id}}').dialog('open');">Delete</span>
            </div>
        </div>
    	
    	
    </form>
</script>



<div class="moxycart_canvas_inner clearfix">

    <h2 class="moxycart_cmp_heading pull-left"><?php print $data['pagetitle']; ?></h2>

        <div class="pull-right">
            <?php
            if ($data['product_form_action'] == 'product_update'):
            ?>
                <span class="btn" onclick="javascript:delete_product(<?php print $data['product_id']; ?>,'<?php print static::page('products'); ?>');">Delete</span>
                <button class="btn" id="product_update" onclick="javascript:save_product('edit'); return false;">Save</button>
                <a class="btn" href="<?php print static::page('productpreview',array('product_id'=>$data['product_id'])); ?>" target="_blank">View</a>
            <?php    
            else:
            ?>
                <button class="btn" id="product_update" onclick="javascript:save_product('create'); return false;">Save</button>
            <?php
            endif;
            ?>
            <!--span class="button btn" onclick="javascript:paint('products');">&laquo; Back to Product List</span-->
            <?php if ($data['store_id']) : ?>
                <a href="<?php print MODX_MANAGER_URL .'?a=30&id='.$data['store_id']; ?>" class="button btn">&laquo; Back to Product List</a>            
            <?php else: ?>
                <a href="<?php print static::page('products'); ?>" class="button btn">&laquo; Back to Product List</a>
            <?php endif; ?>
        </div>

</div>

<form method="post" id="product_form" action="#">



<div class="moxycart_canvas_inner">


<div class="moxy-msg">
	<div id="moxy-result"></div>
	<div id="moxy-result-msg"></div>
</div>

		
	<ul id="moxytab" class="menu">
		<li class="product-link active"><a href="#product">Product</a></li>
		<li class="settings-link" ><a href="#settings_tab">Settings</a></li>
        <li class="options-link" ><a href="#options_tab">Options</a></li>
		<?php if($this->modx->getOption('moxycart.enable_variations')):?>
    		<li class="variations-link" ><a href="#variations_tab">Variations</a></li>
		<?php endif; ?>
		<li class="fields-link" ><a href="#fields_tab">Custom Fields</a></li>
		<?php if ($this->modx->getOption('moxycart.enable_related')): ?>
		<li class="related-link" ><a href="#related_tab">Related</a></li>
		<?php endif; ?>
		<?php if($this->modx->getOption('moxycart.enable_reviews')):?>
            <li class="reviews-link" ><a href="#reviews_tab">Reviews</a></li>
        <?php endif; ?>
		<li class="assets-link" ><a href="#assets_tab">Assets</a></li>
		<?php if($this->modx->getOption('moxycart.enable_taxonomies')):?>
    		<li class="taxonomies-link" ><a href="#taxonomies_tab">Taxonomies</a></li>
		<?php endif; ?>
		<li class="orders-link" ><a href="#orders_tab">View Orders</a></li>
	</ul>

	<div id="product" class="content">
		  <table class="table no-top-border">
                    <tbody>
                         <tr>
                            <td style="width:70%;vertical-align: top;">
                                 <label for="name">Name</label>
                                <input type="text"  id="name" style="width:94%;" name="name" value=""/>
                                <input type="hidden" name="product_id" id="product_id" value="">
                                 <label for="title">Browser Title</label>
                                <input type="text" style="width:94%;" id="title" name="title" value=""/>
                                <label for="alias">Alias</label>
                                <input type="text"  style="width:94%;" name="alias" id="alias" value="">
                                <label for="content">Description</label>
                                <textarea id="description" style="width:94%;" rows="3" name="description"></textarea>

                                <label for="sku">SKU</label>
                                <input type="text" style="width:94%;" id="sku" name="sku" value=""/>

                                <label for="price">Price</label>
                                <input type="text" style="width:94%;" id="price" name="price" value=""/>

                                <label for="price_strike_thru">Strike-Through Price</label>
                                <input type="text" style="width:94%;" id="price_strike_thru" name="price_strike_thru" value=""/>
                                
                            </td>
                            <td style="width:30%;vertical-align: top;">
                            
								<label for="thumbnail">Primary Image</label>

								<div id="thumbnail" style="border:1px dotted grey;width:<?php print $this->modx->getOption('moxycart.thumbnail_width'); ?>px;height:<?php print $this->modx->getOption('moxycart.thumbnail_height'); ?>px;" onclick="javascript:open_thumbail_modal('<?php $data['asset_id']; ?>','asset_thumbnail','asset_id');" style="cursor:pointer;">
								    <input type="hidden" name="asset_id" id="asset_id" value=""/>
								    <span id="asset_thumbnail">
								    <img id="thumbnail_img" 
								        src="<?php print $data['thumbnail_url']; ?>" 
								        width="<?php print $this->modx->getOption('moxycart.thumbnail_width'); ?>" 
								        height="<?php print $this->modx->getOption('moxycart.thumbnail_height'); ?>"/>
								    </span>
								</div>

								<?php /* ======== MODAL DIALOG BOX ======*/ ?>
								<div id="generic_thumbnail_form" title="Select Thumbnail">
								    <div class="asset_thumbnail_container" id="generic_thumbnail_form_container">
								    </div>
								    
								</div>
								
								                            
                            	<label for="category">In Menu</label>
                                <select name="in_menu" id="in_menu">
                                   <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>

                            	<label for="category">Foxycart Category</label>
                                <?php
								print \Formbuilder\Form::dropdown('category', $data['categories'], $data['category']);
								?>
								
                            	<label for="is_active">Active</label>
                               	<select name="is_active" id="is_active">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
								<label for="template_id">Template</label>
								<?php
								print \Formbuilder\Form::dropdown('template_id', $data['templates'], $data['template_id']);
								?>

								
                            </td>
                        </tr>
                        <tr>
                          <td colspan="2">
                              <legend>Content</legend>
                              <textarea id="content" style="width:95%;" class="modx-richtext" rows="7" name="content"></textarea>
                          </td>
                        </tr>
                    </tbody>
                </table>
	</div>

	<div id="settings_tab" class="content">

		 <table class="table no-top-border">
                    <tbody>
                         <tr>
                            <td style="width:70%;vertical-align: top;">
								
								<label for="qty_inventory">Inventory</label>
                                <input type="text" style="width:94%;" id="qty_inventory" name="qty_inventory" value=""/>

                                <label for="qty_alert">Alert Qty</label>
                                <input type="text" style="width:94%;" id="qty_alert" name="qty_alert" value="<?php print isset($data['qty_alert']) ? $data['qty_alert'] : ''; ?>"/>

                                <label for="track_inventory">Track Inventory</label>
								<select name="track_inventory" style="width:40%;" id="track_inventory">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>

								<label for="type">Product Type</label>
								
								<?php
								print \Formbuilder\Form::dropdown('type', $data['types'], $data['type']);
								?>
				
								<label for="weight">Weight</label>
								<input type="text" style="width:50%;" id="weight" name="weight" value=""/>
                                
                               
    
								
                            </td>
                            <td style="width:30%;;vertical-align: top;">
                            	<label for="sku_vendor">Vendor SKU</label>
                                <input type="text" style="width:90%;" name="sku_vendor" id="sku_vendor" value="">

                                <label for="price_sale">Sale Price</label>
                                <input type="text" style="width:90%;" name="price_sale" id="price_sale" value="">

                                <label for="sale_start">Sale Start</label>
								<div class="input-append date datepicker" data-date="<?php print date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
											<span class="add-on"><i class="icon icon-calendar"></i></span>
										  <input type="text" name="sale_start" id="sale_start" class="span3" maxlength="10" value="">
										  
								</div>

								<label for="sale_end">Sale End</label>
								<div class="input-append date datepicker" data-date="<?php print date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
									<span class="add-on"><i class="icon icon-calendar"></i></span>
										  <input type="text" name="sale_end" id="sale_end" class="span3" maxlength="10" value="">
										  
								</div>

								<label for="qty_min">Qty Min</label>
                                <input type="text" style="width:90%;" name="qty_min" id="qty_min" value="">

                                <label for="qty_max">Qty Max</label>
                                <input type="text" style="width:90%;" name="qty_max" id="qty_max" value="">

                                 <label for="back_order_cap">Back Order Cap</label>
                                <input type="text" style="width:90%;" name="back_order_cap" id="back_order_cap" value="">

                            
                                <label for="store_id">Product Container</label>
								<?php
								print \Formbuilder\Form::dropdown('store_id', $data['stores'], $data['store_id']);
								?>
                            	
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
	</div>

    <div id="options_tab" class="content">
       <div class="product-option-wrap">

                                     <p>Allow your visitors to select variations in your product.</p><br>
                                    <?php
                                    //print '<pre>'; print_r($data['Options']); print '</pre>'; exit;
                                    // @#$%@#. Special stuff here: we gotta force the field names to ensure that arrays are in sync.
                                    foreach ($data['Options'] as $o): 
                                        $option_id = $o['option_id'];
                                    ?>
                                        <div class="term-option-wrap">
                                        <?php
                                        print \Formbuilder\Form::checkbox("Options[checked][$option_id]", 
                                        $data['product_options'][$option_id]['checked'], 
                                        array('label'=>sprintf('%s (%s)', 
                                            $o['name'], 
                                            $o['slug'])
                                        ), '<input type="hidden" name="[+name+]" value="[+unchecked_value+]"/>
            <input type="checkbox" name="[+name+]" id="[+id+]" value="[+checked_value+]" class="[+class+] parent-term-option" data-fs_child_id='.$option_id.' style="[+style+]" [+is_checked+][+extra+]/> [+label+]
            [+description+]');    
                                        print \Formbuilder\Form::dropdown("Options[meta][$option_id]", array('all_terms'=>'All Terms','omit_terms'=>'Omit Selected Terms','explicit_terms'=>'Specify Terms'), $data['product_options'][$option_id]['meta']);
                                        
                                        ?>
                                        </div>
                                       
                                        <fieldset class="po-fset fset-<?php print $option_id; ?>">
                                            <table class="classy sub-terms">
                                                <thead>
                                                    <tr>
                                                        <th>&nbsp;</th>
                                                        <th>Override?</th>
                                                        <th>&nbsp;</th>
                                                        <th>Price</th>
                                                        <th>&nbsp;</th>
                                                        <th>Weight</th>
                                                        <th>&nbsp;</th>
                                                        <th>Code</th>
                                                        <th>&nbsp;</th>
                                                        <th>Category</th>
                                                        <th>Thumb</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                
                                            <?php
                                            // Option Meta Data
                                            foreach ($data['product_option_meta'][$option_id]['Terms'] as $oterm_id => $m):
                                                // We gotta ref an arbitrary integer as a placeholder in the POST array to keep arrays in sync
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php
                                                            print \Formbuilder\Form::hidden("Meta[option_id][$oterm_id]", $option_id);
                                                            print \Formbuilder\Form::hidden("Meta[oterm_id][$oterm_id]", $oterm_id);
                                                            print \Formbuilder\Form::checkbox("Meta[checked][$oterm_id]", $m['checked'], array('label'=>$m['name']));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        print \Formbuilder\Form::checkbox("Meta[is_override][$oterm_id]", $m['is_override'], array());
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        print \Formbuilder\Form::dropdown("Meta[mod_price_type][$oterm_id]", \Moxycart\OptionTerm::types(), $m['mod_price_type'],array('style'=>'width: 40px;'));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        print \Formbuilder\Form::text("Meta[mod_price][$oterm_id]", $m['mod_price'], array('style'=>'width: 50px;'));

                                                        ?>
                                                    </td>                                                    
                                                    <td>
                                                        <?php
                                                        print \Formbuilder\Form::dropdown("Meta[mod_weight_type][$oterm_id]", \Moxycart\OptionTerm::types(), $m['mod_weight_type'],array('style'=>'width: 40px;'));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        print \Formbuilder\Form::text("Meta[mod_weight][$oterm_id]", $m['mod_weight'], array('style'=>'width: 30px;'));

                                                        ?>
                                                    </td>                                                    
                                                    <td>
                                                        <?php
                                                        print \Formbuilder\Form::dropdown("Meta[mod_code_type][$oterm_id]", \Moxycart\OptionTerm::types(), $m['mod_code_type'],array('style'=>'width: 40px;'));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        print \Formbuilder\Form::text("Meta[mod_code][$oterm_id]", $m['mod_code'], array('style'=>'width: 80px;'));
                                                        ?>
                                                    </td>                                                    

                                                    <td>
                                                        <?php
                                                        print \Formbuilder\Form::dropdown("Meta[mod_category_type][$oterm_id]", \Moxycart\OptionTerm::types(), $m['mod_category_type'],array('style'=>'width: 40px;'));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        print \Formbuilder\Form::text("Meta[mod_category][$oterm_id]", $m['mod_category'], array('style'=>'width: 150px;'));
                                                        ?>
                                                    </td>                                                    
                                                    <td>
                                                        <img src="http://placehold.it/60x40" />
                                                    </td>
                                                </tr>
                                            <?php
                                            endforeach;
                                            ?>
                                            </tbody>
                                            </table>
                                        </fieldset>
                                        
                                    <?php
                                    endforeach;
                                    ?>
                                </div>
    </div>

    <?php if ($this->modx->getOption('moxycart.enable_variations')): ?>
    	<div id="variations_tab" class="content"><br>
    		<?php if(isset($data['product_id'])) : ?>
    			<a id="manage_inventory" class="btn" href="<?php print self::url('product', 'inventory',array('product_id'=>$data['product_id'])); ?>">Manage Variation Inventory</a>
    		<?php endif; ?>
    		<div id="product_variations" style="padding-top:10px;">
    		</div>
        </div>
	<?php endif; //moxycart.enable_variations ?>
	
	<div id="fields_tab" class="content">
			<div id="product_fields">
	                <?php if (!$data['product_fields']) : ?>
				        <div class="danger" id="no_specs_msg">No Custom Fields have been added to this product. </div>	                
	                <?php endif; ?>
	                
                    <?php foreach ($data['product_fields'] as $field_id => $f): ?>
                        <input type="hidden" name="Fields[field_id][]" value="<?php print $field_id; ?>" />
                        <?php print $f; ?>

                    <?php endforeach; ?>
            </div>
            
        <span class="btn" onclick="javascript:jQuery('#custom_fields_form').dialog('open');">Show / Hide Fields</span>
        
		<?php /* ======== MODAL DIALOG BOX ======*/ ?>
		<div id="custom_fields_form" title="Select Custom Fields">
            <?php
    		print \Formbuilder\Form::multicheck('', $data['fields'],array_keys($data['product_fields']),array('id'=>'field_id'));
    		?>
		</div>		

	</div>
	
	
	<?php if ($this->modx->getOption('moxycart.enable_related')): ?>
	<div id="related_tab" class="content">		
        <table class="table no-top-border">
            <tr>
                <td style="vertical-align:top;">
                    <legend>Related Products  <input placeholder="Add related products..." id="search_products" value=""/></legend>
                    
                    <?php /* scrollable div here ... */ ?>
                    <div>
                        <table class="table table-striped">
                            <thead>
                              <tr>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody id="product_relations">
                                <?php if (!$data['related_products']): ?>
                                <tr id="related_products_msg"><td class="alert alert-danger" colspan="3">You have not defined any related products yet.</td></tr>
                                <?php else: 
                                    foreach($data['related_products'] as $pr):
                                ?>      
                                <tr>
                                    <td>
                                        <input type="hidden" name="Relations[related_id][]" value="<?php print $pr->Relation->get('product_id'); ?>"/>
                                        <a href="<?php print static::page('productedit',array('product_id'=>$pr->Relation->get('product_id'))); ?>"><?php print $pr->Relation->get('name'); ?></a>
                                    </td>
                                    <td>
                                        <?php 
                                        print \Formbuilder\Form::dropdown('Relations[type][]', $data['relation_types'], $pr->get('type')); 
                                        ?>
                                    </td>
                                    <td>
                                        <span class="btn" onclick="javascript:remove_me.call(this,event,'tr');">Remove</span>
                                    </td>
                                </tr>                                
                                <?php
                                    endforeach; 
                                endif; ?>
                            </tbody>
                          </table>


                    </div>
                </td>
            </tr>
        </table>
	</div>
	<?php endif; //related_tab ?>

    <?php if($this->modx->getOption('moxycart.enable_reviews')):?>
    <div id="reviews_tab" class="content">
            <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder" id="ext-gen68">
                <p>Here you can Published/Unpublished Reviews.</p>
            </div><br>
            <?php print $data['review_pagination_links']; ?>
            <table id="reviews_list" class="table table-hover">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Review</th>
                        <th>Rating</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php if (!isset($data['reviews']['total']) || !$data['reviews']['total'] ): ?>
                            <tr><td class="alert alert-danger" colspan="5"> No Reviews Found for this Product.</td></tr>
                        <?php else : ?>
                            <?php                 
                            foreach ($data['reviews']['results'] as $r): 
                            ?>
                                 <tr class="review-row">
                                   <td><?php print $r['id']; ?></td>
                                    <td><?php print $r['name']; ?></td>
                                    <td><?php 
                                        print (strlen($r['content']) >= 50) ? substr($r['content'],0,50) .'&#8230;': $r['content'];
                                        ?></td>
                                    <td><?php print $r['rating']; ?></td>
                                    <td>
                                        <form action="#" method="post" id="review-form">
                                            <select data-review_id="<?php print $r['id']; ?>" name="state" id="state">
                                                <option value="pending" <?php print ($r['state']=='pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="approved" <?php print ($r['state']=='approved') ? 'selected' : ''; ?>>Approved</option>
                                                <option value="archived" <?php print ($r['state']=='archived') ? 'selected' : ''; ?>>Archived</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>                               
                    </tbody>
                  </table>
    </div>
    <?php endif; // moxycart.enable_reviews ?>
    
	<div id="assets_tab" class="content">	
        <div class="dropzone-wrap" id="image_upload">

        	<ul class="clearfix ui-sortable" id="product_assets"></ul>

        	<div class="dz-default dz-message"><span>Drop files here to upload</span></div>

             <div id="trash-can" class="drop-delete">
                <span>Drag Image Here to Delete</span>
            </div>

        </div>

        
		<?php /* ======== DELETE DIALOG BOX ======*/ ?>
		<div id="delete_asset_modal" title="Delete/Remove Asset">
            <p>This asset might be used by other products or pages!</p>
            <p>You can <strong>remove</strong> the image from this product,<br/>
            or you can <strong>delete</strong> the asset.</p>
            <p class="danger">Deleting cannot be undone!</p>
		</div>
        
	</div>

    <?php if($this->modx->getOption('moxycart.enable_taxonomies')):?>
    	<div id="taxonomies_tab" class="content"><br>

                <div id="taxonomy_terms">
                    <?php print \Formbuilder\Form::multicheck('Terms',$data['terms'],$data['product_terms']); ?>
                </div>    

                <br/>
                
                <span class="btn" onclick="javascript:jQuery('#taxonomy-modal').dialog('open');" id="taxonomy-btn">Show / Hide Taxonomies</span>
        
                <?php /* ======== MODAL DIALOG BOX ======*/ ?>
                <div id="taxonomy-modal" style="display:none;" title="Select Taxonomy">
                   <legend>Enable Taxonomies</legend>
                    <div id="taxonomy_list">
                        <?php print \Formbuilder\Form::multicheck('Taxonomies',$data['taxonomies'],$data['product_taxonomies']); ?>
                    </div>
                </div> 
    	</div>
    <?php endif; // moxycart.enable_taxonomies ?>

    <div id="orders_tab" class="content">
        <table class="classy sub-terms">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Shipping Address</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['product_orders'] as $o): ?>
                <tr>
                    <td><?php print $o['TransactionDetail']['Transaction']['customer_first_name']; ?> <?php print $o['TransactionDetail']['Transaction']['customer_last_name']; ?><br/>
                    <?php print $o['TransactionDetail']['Transaction']['shipping_address1']; ?><br/>
                    <?php print $o['TransactionDetail']['Transaction']['shipping_address2']; ?><br/>
                    <?php print $o['TransactionDetail']['Transaction']['shipping_city']; ?> <?php print $o['TransactionDetail']['Transaction']['shipping_state']; ?>, <?php print $o['TransactionDetail']['Transaction']['shipping_postal_code']; ?>
                    </td>
                    <td><?php print $o['TransactionDetail']['Transaction']['shipping_first_name']; ?> <?php print $o['TransactionDetail']['Transaction']['shipping_last_name']; ?><br/>
                    <?php print $o['TransactionDetail']['Transaction']['customer_email']; ?></td>
                    <td><?php print number_format($o['TransactionDetail']['product_price'],2); ?></td>
                    <td><?php print $o['TransactionDetail']['product_quantity']; ?></td>
                    <td><a href="<?php print $o['TransactionDetail']['Transaction']['receipt_url']; ?>">Click</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        
        </table>
    </div>    

</form>

</div>
