<script>
function add_relation(product_id,name,sku) {
    var tpl = <?php print json_encode($data['related_products.tpl']); ?>;
    tpl = tpl.replace(/\[\[\+product_id\]\]/g, product_id );
    tpl = tpl.replace(/\[\[\+name\]\]/g, name );
    tpl = tpl.replace(/\[\[\+sku\]\]/g, sku );
    jQuery('#product_relations').append(tpl);
    // Grey out original
    jQuery('#product_'+product_id).hide();
    jQuery('#related_products_msg').hide();
}

function remove_relation(product_id) {
    jQuery('#product_relation_'+product_id).remove();
    jQuery('#product_'+product_id).show();
}
</script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />

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
			<a class="btn" href="<?php print $data['manager_url']; ?>">Close</a>
		</div>
	</div>
	
	
	<ul id="moxytab" class="menu">
		<li class="product-link active"><a href="#product">Product</a></li>
		<li class="settings-link" ><a href="#settings_tab">Product Settings</a></li>
		<li class="variations-link" ><a href="#variations_tab">Variations</a></li>
		<li class="specs-link" ><a href="#specs_tab">Specs</a></li>
		<li class="related-link" ><a href="#related_tab">Related</a></li>
		<li class="images-link" ><a href="#images_tab">Images</a></li>
		<li class="product-link" ><a href="#taxonomies_tab">Taxonomies</a></li>
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
                                <label for="content">Description</label>
                                <textarea id="description" style="width:94%;" rows="3" name="description"></textarea>
                            </td>
                            <td style="width:30%;vertical-align: top;">
                            	  <label for="alias">Alias</label>
                                <input type="text"  style="width:90%;" name="alias" id="alias" value="">
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
                              <textarea id="content" class="modx-richtext" rows="7" name="content"></textarea>
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
                                <label for="sku">SKU</label>
                                <input type="text" style="width:94%;" id="sku" name="sku" value=""/>

                                <label for="price">Price</label>
                                <input type="text" style="width:94%;" id="price" name="price" value=""/>

                                <label for="price_strike_thru">Strike-Through Price</label>
                                <input type="text" style="width:94%;" id="price_strike_thru" name="price_strike_thru" value=""/>
								
								 <label for="currency_id">Currency</label>
                                <select style="width:40%;" name="currency_id" id="currency_id">
                                	<?php print $data['currencies']; ?>
								</select>

								 <label for="qty_inventory">Inventory</label>
                                <input type="text" style="width:94%;" id="qty_inventory" name="qty_inventory" value=""/>

                                <label for="qty_alert">Alert Qty</label>
                                <input type="text" style="width:94%;" id="qty_alert" name="qty_alert" value=""/>

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
								<div class="input-append date datepicker" data-date="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
											<span class="add-on"><i class="icon icon-calendar"></i></span>
										  <input type="text" name="sale_start" id="sale_start" class="span3" maxlength="10" value="">
										  
								</div>

								<label for="sale_end">Sale End</label>
								<div class="input-append date datepicker" data-date="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
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
			<a id="manage_inventory" class="btn" href="<?php print $data['mgr_connector_url']; ?>product_inventory&product_id=<?php print $data['product_id']; ?>">Manage Variation Inventory</a>
		<?php endif; ?>
		<div id="product_variations" style="padding-top:10px;">
		</div>
    </div>
	
	<div id="specs_tab" class="content">
			<table class="table table-bordered" id="product_specs">
				<thead>
					<tr>
						<th>Spec</th>
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
						    print '<tr><td colspan="3">No Specs Found</td></tr>';
						}
					?>
				</tbody>
			</table>

		<select id="spec_id">
            <?php print $data['specs']; ?>
		</select>

		<button class="btn" onclick="javascript:get_spec(jQuery('#spec_id').val()); return false;">Attach Spec</button>
		<a class="btn btn-custom" href="<?php echo $data['mgr_connector_url'].'specs_manage';  ?>">Add New Spec</a>

	</div>
	
	<div id="related_tab" class="content">		

        <h3>Related Products</h3>
        <?php /* scrollable div here ... */ ?>
        <div>
            <?php if (!$data['related_products']['total']): ?>
                <div id="related_products_msg" style="display: table-cell; vertical-align: middle; text-align: center; background-color:#D8D8D8; height:30px; width:500px;">You have not defined any related products.</div>
            <?php endif; ?>                

            <ul id="product_relations" class="sortable" style="min-height:30px; width:500px;">
                <?php print $data['related_products']; ?>
            </ul>

        </div>
        
        <h3>Find Products</h3>
        <?php /* scrollable div here ... */ ?>

        <?php if (!$data['products']['total']): ?>
            <p>There are no other products defined.</p>
        <?php else: ?>
            <div style="height: 400px; width:400px; overflow: scroll;">
                <ul class="">
                <?php foreach ($data['products']['results'] as $p): ?>
                    <li id="product_<?php print $p['product_id']; ?>">
                        <strong><?php print $p['name']; ?></strong> (<?php print $p['sku']; ?>) 
                        <span class="btn" style="height:10px;" onclick="javascript:add_relation(<?php print $p['product_id']; ?>,'<?php print $p['name']; ?>', '<?php print $p['sku']; ?>');">Add</span>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
	</div>

	<div id="images_tab" class="content">		

        <div class="dropzone-wrap" id="image_upload">
        	<ul class="clearfix" id="product_images"><?php print isset($data['images']) ? $data['images'] : ''; ?></ul>
        	<div class="dz-default dz-message"><span>Drop files here to upload</span></div>
        </div>
		<div class="modal fade" id="update-image"></div><!--/.modal -->
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

</div>


</form>

