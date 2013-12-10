<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<style>
#sortable { list-style-type: none; margin: 0; padding: 0; width: 450px; }
#sortable li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; height: 90px; font-size: 4em; text-align: center; }
</style>

<div class="moxy-msg">
	<div id="moxy-result"></div>
	<div id="moxy-result-msg"></div>
</div>



<form method="POST" id="<?php print $data['product_form_action']; ?>" action="#">
<div id="modx-panel-workspace" class="x-plain container">
	<div class="moxy-header clearfix">
		<div class="moxy-header-title">
			<h2>Product Update</h2>
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
		<li class="images-link" ><a href="#images_tab">Images</a></li>
		<li class="product-link" ><a href="#taxonomies_tab">Taxonomies</a></li>
	</ul>

	<div id="product" class="content">
            <table class="table no-top-border">
				<tbody>
					<tr>
						<td>
							<label for="name">Name</label>
						</td>
						<td>
							<input type="text" name="name" id="name" value="">
							<input type="hidden" name="product_id" id="product_id" value="">
						</td>
						<td>
							<label for="is_active">Active</label>
						</td>
						<td>
							<select name="is_active" id="is_active">
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>
						</td>

					</tr>

					<tr>
						<td>
							<label for="title">Browser Title</label>
						</td>
						<td>
							<input type="text" name="title" id="title" value="">
						</td>
						<td>
							<label for="alias">Alias</label>
						</td>
						<td>
							<input type="text" name="alias" id="alias" value="">
						</td>

					</tr>

					<tr>
						<td>
							<label for="category">Category</label>
						</td>
						<td>
							<select name="category" id="category">
                                <?php print $data['categories']; ?>
							</select>
						</td>
						<td>
							<label for="template_id">Template</label>
						</td>
						<td>

							<select name="template_id" id="template_id">
                                <?php print $data['templates']; ?>
							</select> 
						</td>
					</tr>
					<tr><td colspan="4">&nbsp;</td></tr>
					<tr>
						<td>
							<label for="description">Description</label>
						</td>
						<td colspan="3">
							<textarea name="description" id="description" style="width:680px;height:70px;"></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<label for="description">Content</label>
						</td>
						<td colspan="3">
							<textarea name="content" id="content" class="modx-richtext" style="width:700px;height:120px;"></textarea>
						</td>
					</tr>

						
				</tbody>
			</table>

	</div>

	<div id="settings_tab" class="content">
		 <table class="table no-top-border">
				<tbody>
					<tr>
						<td>
							<label for="sku">SKU</label>
						</td>
						<td>
							<input type="text" name="sku" id="sku" value="">
						</td>
						<td>
							<label for="sku_vendor">Vendor SKU</label>
						</td>
						<td>
							<input type="text" name="sku_vendor" id="sku_vendor" value="">
						</td>
					</tr>
					<tr>
						<td>
							<label for="price">Price</label>
						</td>
						<td>
							<input type="text" name="price" id="price" value="">
						</td>
						<td>
							<label for="price_sale">Sale Price</label>
						</td>
						<td>
							<input type="text" name="price_sale" id="price_sale" value="">
						</td>

					</tr>

					<tr>
						<td>
							<label for="price_strike_thru">Strike-Through Price</label>
						</td>
						<td>
							<input type="text" name="price_strike_thru" id="price_strike_thru" value="">
						</td>
						<td>
							<label for="sale_start">Sale Start</label>
						</td>
						<td>
							<div class="input-append date datepicker" data-date="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
									  <input type="text" name="sale_start" id="sale_start" class="span2" maxlength="10" value="">
									  <span class="add-on"><i class="icon icon-calendar"></i></span>
							</div>
						</td>
					</tr>

					<tr>
						<td>
							<label for="currency_id">Currency</label>
						</td>
						<td>
							<select name="currency_id" id="currency_id">
                                <?php print $data['currencies']; ?>
							</select>
						</td>
						<td>
							<label for="sale_end">Sale End</label>
						</td>
						<td>
							<div class="input-append date datepicker" data-date="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
									  <input type="text" name="sale_end" id="sale_end" class="span2" maxlength="10" value="">
									  <span class="add-on"><i class="icon icon-calendar"></i></span>
							</div>
						</td>
					</tr>

					<tr>
						<td>
							<label for="qty_inventory">Inventory</label>
						</td>
						<td>
							<input type="text" name="qty_inventory" id="qty_inventory" value="">
						</td>
						<td>
							<label for="qty_min">Qty Min</label>
						</td>
						<td>
							<input type="text" name="qty_min" id="qty_min" value="">
						</td>

					</tr>
					<tr>
						<td>
							<label for="qty_alert">Alert Qty</label>
						</td>
						<td>
							<input type="text" name="qty_alert" id="qty_alert" value="">
						</td>
						<td>
							<label for="qty_max">Qty Max</label>
						</td>
						<td>
							<input type="text" name="qty_max" id="qty_max" value="">
						</td>

					</tr>

					<tr>
						<td>
							<label for="track_inventory">Track Inventory</label>
						</td>
						<td>
							<select name="track_inventory" id="track_inventory">
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>
						</td>
						<td>
							<label for="back_order_cap">Back Order Cap</label>
						</td>
						<td colspan="3">
							<input type="text" name="back_order_cap" id="back_order_cap" value="">
						</td>

					</tr>
					<tr>
						<td>
							<label for="type">Product Type</label>
						</td>
						<td>
							<select name="type" id="type">
                                <?php print $data['types']; ?>
							</select>
						</td>
						<td>
							<label for="store_id">Product Container</label>
						</td>
						<td>
							<select name="store_id" id="store_id">
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
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Spec</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
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
		<select>
            <?php print $data['specs']; ?>
		</select>
		<button onclick="alert('add a row to the table with this spec'); return false;">Attach Spec</button>
	</div>

	<div id="images_tab" class="content">		

		<ul class="clearfix" id="product_images"><?php print isset($data['images']) ? $data['images'] : ''; ?></ul>

        <div class="dropzone-wrap" id="image_upload">
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

