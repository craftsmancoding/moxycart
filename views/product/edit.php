<?php
    /**
     * Load up your guns
     *

     */
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

	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
		var product = '.$data['result']->toJson().';            
		var controller_url = "'.$this->config['controller_url'].'";
        var use_editor = "'.$this->modx->getOption('use_editor').'";
        var assets_url = "'.$this->config['assets_url'].'";    		
        var variation_url = "'.$this->config['controller_url'].'&parent_id='.$data['result']->product_id.'";

        jQuery(document).ready(function() {
                var myDropzone = new Dropzone("div#image_upload", {url: controller_url+"&class=asset&method=upload&product_id='.$data['result']->product_id.'"});
                
                // Refresh the list on success (append new tile to end)
                myDropzone.on("success", function(file,response) {

                    console.log(response);
                    response = jQuery.parseJSON(response);
                    console.log(response);
                    if (response.success) {
                       
                        var url = controller_url + "&class=asset&method=view&asset_id=" + response.asset_id;
                        jQuery.post( url, function(data){
                            jQuery("#product_images").append(data);
                            jQuery(".dz-preview").remove();
                        });
                   } 
                   // TODO: better formatting
                   else {                           
                        $(".dz-success-mark").hide();
                        $(".dz-error-mark").show();
                        $(".moxy-msg").show();
                        $("#moxy-result").html("Failed");
                        $("#moxy-result-msg").html(response.msg);
                        $(".moxy-msg").delay(3200).fadeOut(400);
                   }
                });
        });

		Ext.onReady(function() {   		
			// renderProductVariationProductsGrid();
		});
		</script>
	');
    if ($this->modx->getOption('use_editor')) {
        $this->_load_tinyMCE();
    }
?>
<script>
function add_relation(product_id,name,sku) {
    var tpl = <?php print json_encode($data['related_products.tpl']); ?>;
    tpl = tpl.replace(/\[\[\+related_id\]\]/g, product_id );
    tpl = tpl.replace(/\[\[\+name\]\]/g, name );
    tpl = tpl.replace(/\[\[\+sku\]\]/g, sku );
    jQuery('#product_relations').append(tpl);
    // Grey out original
    jQuery('#product_'+product_id+ ' span').hide();
    jQuery('#product_'+product_id+' strong').css("color","gray");
    jQuery('#related_products_msg').hide();
}

function remove_relation(product_id) {
    jQuery('#product_relation_'+product_id).remove();
    jQuery('#product_'+product_id+ ' span').show();
    jQuery('#product_'+product_id+' strong').css("color","black");    
}
</script>

<div class="moxy-msg">
	<div id="moxy-result"></div>
	<div id="moxy-result-msg"></div>
</div>


<form method="post" id="<?php print $data['product_form_action']; ?>" action="#">
<div id="modx-panel-workspace" class="x-plain container">
	<div class="moxy-header clearfix">
		<div class="moxy-header-title">
			<h2><?php print $data['pagetitle']; ?></h2>
		</div>
			
		<div class="moxy-buttons-wrapper">
            <?php
            if ($data['product_form_action'] == 'product_update'):
            ?>
                <button class="btn" id="product_update">Save</button>
                <a class="btn" href="<?php print MODX_SITE_URL. $data['uri']; ?>" target="_blank">View</a>
            <?php    
            else:
            ?>
                <button class="btn" id="product_create">Save</button>
            <?php
            endif;
            ?>
			<a class="btn" href="<?php print MODX_MANAGER_URL.'?a=30&id='.$data['store_id']; ?>">Back to Product List</a>
		</div>
	</div>
	
	
	<ul id="moxytab" class="menu">
		<li class="product-link active"><a href="#product">Product</a></li>
		<li class="settings-link" ><a href="#settings_tab">Product Settings</a></li>
		<?php // if($this->modx->getOption('moxycart.enable_variations')):?>
    		<li class="variations-link" ><a href="#variations_tab">Variations</a></li>
		<?php // endif; ?>
		<li class="fields-link" ><a href="#fields_tab">Custom Fields</a></li>
		<li class="related-link" ><a href="#related_tab">Related</a></li>
		<?php // if($this->modx->getOption('moxycart.enable_reviews')):?>
            <li class="reviews-link" ><a href="#reviews_tab">Reviews</a></li>
        <?php // endif; ?>
		<li class="assets-link" ><a href="#assets_tab">Assets</a></li>
		<?php //if($this->modx->getOption('moxycart.enable_taxonomies')):?>
    		<li class="taxonomies-link" ><a href="#taxonomies_tab">Taxonomies</a></li>
		<?php //endif; ?>
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
                            	<label for="category">In Menu</label>
                                <select style="width:90%;" name="in_menu" id="in_menu">
                                   <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>

                            	<label for="category">Category</label>
                                <select style="width:90%;" name="category" id="category">
                                	<?php print $data['categories']; ?>
								</select>

                            	<label for="is_active">Active</label>
                               	<select style="width:90%;" name="is_active" id="is_active">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
								<label for="template_id">Template</label>
                                <select style="width:90%;" name="template_id" id="template_id">
                                    <?php print $data['templates']; ?>
				                </select> 
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
								<select style="width:40%;" name="type" id="type">
	                                <?php print $data['types']; ?>
								</select>

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
								<select style="width:90%;" name="store_id" id="store_id">
									<?php print $data['stores']; ?>
								</select>
                            	
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
	</div>

	<div id="variations_tab" class="content"><br>
		<?php if(isset($data['product_id'])) : ?>
			<a id="manage_inventory" class="btn" href="<?php print self::url('product', 'inventory',array('product_id'=>$data['product_id'])); ?>">Manage Variation Inventory</a>
		<?php endif; ?>
		<div id="product_variations" style="padding-top:10px;">
		</div>
    </div>
	
	<div id="fields_tab" class="content">
			<table class="table table-bordered" id="product_specs">
				<thead>
					<tr>
						<th>Field</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody id="specs">
	                <?php
	                	if ($data['product_specs']) {
	                    	print $data['product_specs'];
		                }
		                else {
						    print '<tr id="no_specs_msg"><td colspan="3">No Specs Found</td></tr>';
						}
					?>
				</tbody>
			</table>

		<select id="spec_id">
            <?php print $data['specs']; ?>
		</select>

		<button class="btn" onclick="javascript:get_spec(jQuery('#spec_id').val()); return false;">Attach Spec</button>
		<a class="btn btn-custom" href="<?php print self::url('field','create');  ?>">Add New Spec</a>

	</div>
	
	<div id="related_tab" class="content">		
        <table class="table no-top-border">
            <tr>
                <td style="vertical-align:top;">
                    <legend>Related Products</legend>
                    <?php /* scrollable div here ... */ ?>
                    <div>
       
                        <table class="table table-striped sortable">
                            <thead>
                              <tr>
                                <th>Product</th>
                                <th>Option</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody id="product_relations">
                                <?php if (!$data['related_products']): ?>
                                <tr id="related_products_msg"><td class="alert alert-danger" colspan="3"> <strong>Heads up!</strong> You have not defined any related products.</td></tr>
                                <?php endif; ?>      
                                    
                                <?php print $data['related_products']; ?>
                            </tbody>
                          </table>


                    </div>
                </td>
                <td style="vertical-align:top;width:400px;">
                    <legend>Find Products</legend>

                  
                    <table class="table table-striped sortable">
                            <thead>
                              <tr>
                                <th>Product</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                                <?php if (!isset($data['products']['total']) || !$data['products']['total'] ): ?>
                                    <tr><td class="alert alert-danger" colspan="3"> There are no other products defined.</td></tr>
                                <?php else : ?>
                                    <?php foreach ($data['products']['results'] as $p): ?>
                                        <tr id="product_<?php print $p['product_id']; ?>">
                                            <td><strong><?php print $p['name']; ?></strong> <?php print !empty($p['sku']) ? '('.$p['sku'].')' : ''; ?></td>
                                            <td><span class="btn" style="height:10px;" onclick="javascript:add_relation(<?php print $p['product_id']; ?>,'<?php print $p['name']; ?>', '<?php print $p['sku']; ?>');">Add</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?> 
                            </tbody>
                          </table>
                </td>
            </tr>
        </table>
        
	</div>

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

	<div id="assets_tab" class="content">	
        <div class="dropzone-wrap" id="image_upload">
            <div class="featured-img">
                <img src="<?php print $this->config['assets_url']; ?>images/featured-img.png" alt=""  title="Primary Thumbnail">
            </div>
        	<ul class="clearfix" id="product_images">
                <?php print isset($data['images']) ? $data['images'] : ''; ?>
                
            </ul>

           
            <div class="clear"></div>

        	<div class="dz-default dz-message"><span>Drop files here to upload</span></div>

             <div id="trash-can" class="drop-delete">
                <span>Drag Image Here to Delete</span>
            </div>

        </div>

		<div class="modal fade" id="update-image">
            <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Update Image</h4>

                    <div class="loader-ajax">
                        <img src="<?php print $this->config['assets_url']; ?>images/gif-load.gif" alt="">
                    </div>
                    
                  </div>

                  <div class="update-container"></div>
                 
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!--/.modal -->

        

	</div>
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

    <div id="orders_tab" class="content">
    
    </div>    
</div>


</form>

