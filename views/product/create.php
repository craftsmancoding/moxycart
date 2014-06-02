<?php
/*
// WTF? Stuff doesn't load correctly if I load it here (?!?)

$this->modx->regClientCSS($this->config['assets_url'] . 'css/mgr.css');
$this->modx->regClientCSS($this->config['assets_url'] . 'css/dropzone.css');
$this->modx->regClientCSS($this->config['assets_url'].'css/datepicker.css');
$this->modx->regClientCSS('//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');

$this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-2.0.3.min.js');
$this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-ui.js');
$this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery.tabify.js');

$this->modx->regClientStartupScript($this->config['assets_url'].'js/dropzone.js');
$this->modx->regClientStartupScript($this->config['assets_url'].'js/bootstrap.js');
$this->modx->regClientStartupScript($this->config['assets_url'].'js/multisortable.js');

$this->modx->regClientStartupScript($this->config['assets_url'].'js/script.js');        
$this->modx->regClientStartupScript($this->config['assets_url'].'js/handlebars.js');

$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
	var product = '.$data['result']->toJson().';            
    var use_editor = "'.$this->modx->getOption('use_editor').'";
    var assets_url = "'.$this->config['assets_url'].'"; 
    // Document read stuff has to be in here
    jQuery(document).ready(function() {
        product_init();
    });
	</script>');
if ($this->modx->getOption('use_editor')) {
    $this->_load_tinyMCE();
}
*/
?>
<style>
/* http://jsfiddle.net/thirtydot/NTKK3/ */
.asset_thumbnail_container {
    height: 190px;
    width: 480px;
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
}

.asset_thumbnail_item {
    border: 1px solid #E5E5E5;
    height: 190px;
    padding: 5px;
    width: 250px;
    
    display: inline-block;
    /* for ie7 */
    *display: inline;
    zoom: 1;
}
</style>


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
    console.log('[init]');

    populate_form(product);
	jQuery('#moxytab').tabify();
	// jQuery('.datepicker').datepicker("setValue", new Date()); // <-- always writes current date
    // jQuery('.datepicker').datepicker(); // <-- shows "-001-11-30 00:00:00" for the default date
	jQuery('.datepicker').datepicker();
	jQuery("#product_images").sortable();
    jQuery("#product_images").disableSelection();
    jQuery(".sortable").sortable({
        connectWith: ".connectedSortable",
    }).disableSelection();


    // Dropzone for Assets 
    var myDropzone = new Dropzone("div#image_upload", {url: controller_url("asset","create")});    
    // Refresh the list on success (append new tile to end)
    myDropzone.on("success", function(file,response) {

        response = jQuery.parseJSON(response);
        console.log(response);
        if (response.status == "success") {
            var data = parse_tpl("product_image",response.data.fields);
            jQuery("#product_images").append(data);
            jQuery(".dz-preview").remove();
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
            var xhr;
            var url = controller_url("product","search");
            return function(request, response) {
                if (xhr) {
                    xhr.abort();
                }
                xhr = jQuery.ajax({
                    url: url,
                    data: {
                        "name:like": request.term
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function(data) {
                        // Tricky because our data structure contains {"data":{"results":[{}]}}
                        response(data.data.results);
                    },
                    error: function() {
                        response([]);
                    }
                });
            }
        })(),
        select: function(event, ui) {
            var content = parse_tpl('related_product_template',ui.item)
            jQuery('#product_relations').append(content);
            jQuery('#search_products').val('');
            event.preventDefault(); // clear out text
        }
    }); 

    // Trash Can
	jQuery( "#trash-can" ).droppable({
		
		over: function( event, ui ) {
			$(this).addClass('over-trash');
		},
		out: function(event, ui) {
			var id = $(ui.draggable).attr('id');
			$(this).removeClass('over-trash');
		},
	    drop: function( event, ui ) {
	      	var id = jQuery(ui.draggable).attr('id');

	      	//var url = connector_url + 'image_save';
	      	//var asset_id = $(ui.draggable).find('a').data('asset_id');	      	
	      	var asset_id = jQuery(ui.draggable).find('input.asset_asset_id').val();	      
	      	if (confirm("Are you Sure you want to Delete this Image?")) {
	      		jQuery(this).removeClass('over-trash');
	      		mapi('asset','delete',{"asset_id":asset_id});
	      		$('#product-asset-'+asset_id).remove();
		    }
		    jQuery(this).removeClass('over-trash');

		    return false;
	    }
    });
    
    // Thumbnail Selection Modal
    jQuery( "#thumbnail_form" ).dialog({
        autoOpen: false,
        height: 330,
        width: 500,
        modal: true,
        buttons: {
            "Done": function() {
                $( this ).dialog( "close" );
            }
        }   
    });
    
    // Custom Field Selection Modal
    jQuery( "#custom_fields_form" ).dialog({
        autoOpen: false,
        height: 330,
        width: 500,
        modal: true,
        buttons: {
            "Define New Field": function() {
                $( this ).dialog( "close" );
            },
            "Redraw Fields": function() {
                //Fields[field_id][]
                var field_ids = [];
                var field_id;
                jQuery('#custom_fields_form input:checked').each(function() {
                    field_ids.push(jQuery(this).attr('value'));
                });
                jQuery('#product_fields').html(''); // Blank it out
                console.debug('Attaching field ids: ', field_ids);
                var field_ids_cnt = field_ids.length;
                for (var i = 0; i < field_ids_cnt; i++) {
                    var field_id = field_ids[i];
                    // Had to customize mapi...
                    //mapi('field','generate',{"field_id":field_id,"name":"Fields[field_id][]"});
                    // This MUST be outside of the .post call!!! Otherwise it will always be written with the last 
                    // field_id because js will execute BEFORE the postback occurs!!!
                    jQuery('#product_fields').append('<input type="hidden" name="Fields[field_id][]" value="'+field_id+'" />'); 
                    var url = controller_url('field','generate');    
                    jQuery.post(url, {"field_id":field_id,"name":"Fields[value][]"}, function( response ) {
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
                
                $( this ).dialog( "close" );
            },
            "Done": function() {
                $( this ).dialog( "close" );
            }
        }   
    });
    
    // Edit Asset Form
    jQuery( "#asset_edit_form" ).dialog({
        autoOpen: false,
        height: 500,
        width: 800,
        modal: true,
        open: function(event, ui) {
            // Sent the asset_id when the link is clicked, e.g. via
            // onclick="javascript:jQuery('#asset_edit_form').data('asset_id', 123).dialog('open');"
            var asset_id = $("#asset_edit_form").data('asset_id')
            //console.log('opened...'+ asset_id);
            console.debug(product.RelData.Asset[asset_id]);
            // Write all values temporarily to the modal
            jQuery('#modal_asset_title').val(product.RelData.Asset[asset_id].title);
            jQuery('#modal_asset_alt').val(product.RelData.Asset[asset_id].alt);
            jQuery('#modal_asset_width').text(product.RelData.Asset[asset_id].width);
            jQuery('#modal_asset_height').text(product.RelData.Asset[asset_id].height);
            jQuery('#modal_asset_img').html('<img src="'+product.RelData.Asset[asset_id].url+'" />');
            if (product.RelData.Asset[asset_id].is_active == 1) {  
                jQuery('#modal_asset_is_active').prop('checked', true);
            }
        },
        buttons: {
            "Save": function() {
                // For meta-data specific to the *relation* (i.e. ProductAsset), write the values back to the form (ugh)
                // For data specific to the *asset*, we have to fire off an Ajax request
                var asset_id = $("#asset_edit_form").data('asset_id');
                var title = jQuery('#modal_asset_title').val();
                var alt = jQuery('#modal_asset_alt').val();
                var is_active = jQuery('#modal_asset_is_active').val();
                
                // And back to the JSON (double-ouch)
                product.RelData.Asset[asset_id].title = title;
                product.RelData.Asset[asset_id].alt = alt;
                product.RelData.Asset[asset_id].is_active = is_active;
                jQuery('#asset_is_active_'+asset_id).val(is_active);

                // This data here is specific to the Asset
                mapi('asset','edit',{"asset_id":asset_id,"title":title,"alt":alt});
                
                $( this ).dialog( "close" );
            },
            "Cancel": function() {
                $( this ).dialog( "close" );
            }
        }   
    });
};

/**
 * TODO: update API to get field form
 */
function get_field_instance() {
    var field_id = jQuery('#field_selector').val();
    console.debug('[get_field_instance] field_id %s',field_id);
    var url = controller_url('field','row');    
    jQuery.get(url, {field_id:field_id}, function( response ) {
        jQuery('#no_specs_msg').hide();
        jQuery('#fields').append(response);
    });
}

function save_product(method) {
    console.log('[save_product] '+method);
    var values = jQuery('#product_form').serialize();
    mapi('product',method,values);
}

function select_thumb(asset_id,url) {
    console.log('[select_thumb] asset_id: %s thumb url: %s',asset_id,url);
    jQuery('#asset_id').val(asset_id);
    jQuery('#thumbnail_img').attr('src', url);
    jQuery( "#thumbnail_form" ).dialog("close"); 
}
// Asset Trash can
//function drag_drop_delete() {

//}


</script>

<?php
//------------------------------------------------------------------------------
// ! Handlebar templates
//------------------------------------------------------------------------------
?>
<script id="related_product_template" type="text/x-handlebars-template">
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

<script id="product_image" type="text/x-handlebars-template">
<li class="li_product_image" id="product-asset-{{asset_id}}">
	<div class="img-info-wrap">  
        <img src="{{thumbnail_url}}?rand=<?php print uniqid(); ?>" alt="{{alt}}" width="" />
	    <input type="hidden" name="Assets[asset_id][]" value="{{asset_id}}" onclick="javascript:jQuery('#asset_edit_form').data('asset_id', '{{asset_id}}').dialog('open');" style="cursor:pointer;"/>
        <input type="hidden" id="asset_asset_id_{{asset_id}}" name="Assets[asset_id][]" class="asset_asset_id" value="{{asset_id}}" />
	</div>
</li>
</script>

<div class="moxycart_canvas_inner clearfix">

    <h2 class="moxycart_cmp_heading pull-left"><?php print $data['pagetitle']; ?></h2>

        <div class="pull-right">
            <?php
            if ($data['product_form_action'] == 'product_update'):
            ?>
                <button class="btn" id="product_update" onclick="javascript:save_product('edit'); return false;">Save</button>
                <a class="btn" href="<?php print static::page('products'); ?>" target="_blank">View</a>
            <?php    
            else:
            ?>
                <button class="btn" id="product_update" onclick="javascript:save_product('create'); return false;">Save</button>
            <?php
            endif;
            ?>
            <!--span class="button btn" onclick="javascript:paint('products');">&laquo; Back to Product List</span-->
            <a href="<?php print static::page('products'); ?>" class="button btn">&laquo; Back to Product List</a>
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
		<li class="settings-link" ><a href="#settings_tab">Product Settings</a></li>
		<?php if($this->modx->getOption('moxycart.enable_variations')):?>
    		<li class="variations-link" ><a href="#variations_tab">Variations</a></li>
		<?php endif; ?>
		<li class="fields-link" ><a href="#fields_tab">Custom Fields</a></li>
		<li class="related-link" ><a href="#related_tab">Related</a></li>
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
                            
								<label for="thumbnail">Thumbnail</label>

								<div id="thumbnail" style="border:1px dotted grey;width:240px;height:180px;" onclick="javascript:jQuery('#thumbnail_form').dialog('open');">
								    <input type="hidden" name="asset_id" id="asset_id" value=""/>
								    <img id="thumbnail_img" 
								        src="<?php print $data['thumbnail_url']; ?>" 
								        width="<?php print $this->modx->getOption('moxycart.thumbnail_width'); ?>" 
								        height="<?php print $this->modx->getOption('moxycart.thumbnail_height'); ?>"/>
								</div>
								<?php /* ======== MODAL DIALOG BOX ======*/ ?>
								<div id="thumbnail_form" title="Select Product Thumbnail">
								    <div class="asset_thumbnail_container">
								        <?php foreach ($data['product_assets'] as $a): ?>
								            <div class="asset_thumbnail_item">
    								            <img src="<?php print $a->Asset->get('thumbnail_url'); ?>" 
    								                alt="<?php print $a->Asset->get('alt'); ?>" 
    								                width="<?php print $this->modx->getOption('moxycart.thumbnail_width'); ?>" 
    								                height="<?php print $this->modx->getOption('moxycart.thumbnail_height'); ?>"
    								                onclick="javascript:select_thumb(<?php print $a->get('asset_id'); ?>,'<?php print htmlentities($a->Asset->get('thumbnail_url')); ?>');"/>
								            </div>
								        <?php endforeach?>
								    </div>
								    
								</div>
								
								                            
                            	<label for="category">In Menu</label>
                                <select name="in_menu" id="in_menu">
                                   <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>

                            	<label for="category">Category</label>
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
                                
                                <div class="product-option-wrap">
                                     <h2>Product Options</h2>
                                     <p>Allow your visitors to select variations in your product.</p>
                                    <?php
                                    print \Formbuilder\Form::multicheck('OptionTypes', $data['OptionTypes'], $data['product_option_types'],array(),'[+error+]
            <input type="checkbox" name="[+name+][]" id="[+id+]" value="[+value+]" class="[+class+]"[+is_checked+] [+extra+]/> [+option+]<br/>');
                                    ?>
                                </div>
                               
    
								
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
				        <span id="no_specs_msg">No Custom Fields Found</span>	                
	                <?php endif; ?>
	                
                    <?php foreach ($data['product_fields'] as $f):?>

                    <?php
                        $type = $f->Field->get('type');
                        $value = $f->get('value');
                        $label = $f->Field->get('label');
                        $description = $f->Field->get('description');
                        print \Formbuilder\Form::$type('Fields[value][]',$value, array('label'=>$label,'description'=>$description));
                    ?>

                    <?php endforeach; ?>
            </div>
            
        <span class="btn" onclick="javascript:jQuery('#custom_fields_form').dialog('open');">Show / Hide Fields</span>
        
		<?php /* ======== MODAL DIALOG BOX ======*/ ?>
		<div id="custom_fields_form" title="Select Custom Fields">
            <?php
            $field_ids = array();
            foreach ($data['product_fields'] as $f) {
                $field_ids[] = $f->get('field_id');
            }
    		print \Formbuilder\Form::multicheck('', $data['fields'],$field_ids,array('id'=>'field_id'));
    		?>
		</div>		

	</div>
	
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

        	<ul class="clearfix" id="product_images">
                <?php 
                foreach ($data['product_assets'] as $a): ?>
                    <li class="li_product_image" id="product-asset-<?php print $a->get('asset_id'); ?>">
                    	<div class="img-info-wrap">
                    	    <!--a class="edit-img" href="#<?php print $a->get('asset_id'); ?>" data-asset_id="<?php print $a->get('asset_id'); ?>" data-toggle="modal" data-target="#update-image"-->
                    		  <img src="<?php print $a->Asset->get('thumbnail_url'); ?>?rand=<?php print uniqid(); ?>" alt="<?php print $a->Asset->get('alt'); ?>" width="" onclick="javascript:jQuery('#asset_edit_form').data('asset_id', <?php print $a->get('asset_id'); ?>).dialog('open');" style="cursor:pointer;"/>
                    		<!--/a-->
                    	    <input type="hidden" id="asset_asset_id_<?php print $a->get('asset_id'); ?>" class="asset_asset_id" name="Assets[asset_id][]" value="<?php print $a->get('asset_id'); ?>" />
                    	    <!-- Button trigger modal -->
                    		
                    		<!-- Modal-->
                    	</div>
                    </li>            
                
                <?php endforeach; ?>
            </ul>


        	<div class="dz-default dz-message"><span>Drop files here to upload</span></div>

             <div id="trash-can" class="drop-delete">
                <span>Drag Image Here to Delete</span>
            </div>

        </div>

        <?php // <span class="btn btn-custom" onclick="javascript:jQuery('#asset_edit_form').dialog('open');">Show / Hide Fields</span> ?>
        
		<?php /* ======== ASSET MODAL DIALOG BOX ======*/ ?>
		<div id="asset_edit_form" title="Edit Asset">
            <div id="asset_being_edited"></div>
            <label for="modal_asset_title">Title</label>
            <input type="text" id="modal_asset_title" value="" />
            <label for="modal_asset_alt">Alt</label>
            <input type="text" id="modal_asset_alt" value="" />
            <label for="modal_asset_is_active">Is Active?</label>
            <input type="checkbox" id="modal_asset_is_active" value="1" /> Is Active?
            <p>Dimensions: <span id="modal_asset_width"></span> x <span id="modal_asset_height"></span></p>
            <span id="modal_asset_img"></span>
		</div>		


		<div class="modal fade" id="update-image">
            <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Update Image</h4>

                    <?php
                    // This spinner image shows while the image is being loaded from ajax.
                    ?>
                    <div class="loader-ajax">
                        <img src="<?php print $this->config['assets_url']; ?>images/gif-load.gif" alt="">
                    </div>
                    
                  </div>

                  <div class="update-container"></div>
                 
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!--/.modal -->

        

	</div>

    <?php if($this->modx->getOption('moxycart.enable_taxonomies')):?>
    	<div id="taxonomies_tab" class="content"><br>
    		<legend>Taxonomy List</legend>
            <div id="taxonomy_list">
                <?php print $data['taxonomies']; ?>
            </div>
            <legend>Term List</legend>
            <div id="taxonomy_terms">
                <?php print $data['product_taxonomies']; ?>
            </div>    
    	</div>
    <?php endif; // moxycart.enable_taxonomies ?>

    <div id="orders_tab" class="content">
    
    </div>    

</form>

</div>
