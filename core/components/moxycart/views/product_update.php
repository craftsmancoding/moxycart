<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script type="text/javascript">

// See http://stackoverflow.com/questions/9807426/use-jquery-to-re-populate-form-with-json-data
jQuery( document ).ready(function() {
    jQuery('#moxytab').tabify();
    jQuery("#product_images").sortable();
    jQuery("#product_images").disableSelection();
    $.each(product, function(name, val){
        var $el = $('#'+name),
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

//    var myDropzone = new Dropzone("div#images_tab", { url: connector_url+'image_save',paramName:"image_uploads"});
    jQuery("div#images_tab").dropzone({url: connector_url+'image_save',paramName:"image_uploads"});

});


/**
 * POST data to ?f=product_save 
 */
function submit_form() {
    var url = connector_url + 'product_save';
    alert('Save me to product_save : ' + url);
    return false;
}

</script>

<style>
#sortable { list-style-type: none; margin: 0; padding: 0; width: 450px; }
#sortable li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; height: 90px; font-size: 4em; text-align: center; }
</style>

<div class="moxy-msg">
	<div id="moxy-result"></div>
	<div id="moxy-result-msg"></div>
</div>

<form id="product_update" class="dropzone" action="<?php print $data['connector_url'] ?>image_save" method="post">

<div id="modx-panel-workspace" class="x-plain container">
	<div class="moxy-header clearfix">
		<div class="moxy-header-title">
			<h2>Product Update</h2>
		</div>
			
		<div class="moxy-buttons-wrapper">
			<button class="btn" id="moxy-save">Save</button>
			<button class="btn">View</button>
			<button class="btn">Close</button>
		</div>
	</div>
	
	
	<ul id="moxytab" class="menu">
		<li class="active"><a href="#product">Product</a></li>
		<li><a href="#settings_tab">Settings</a></li>
		<li><a href="#variations_tab">Variations</a></li>
		<li><a href="#specs_tab">Specs</a></li>
		<li><a href="#images_tab">Images</a></li>
		<li><a href="#taxonomies_tab">Taxonomies</a></li>
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
							<label for="description">Description</label>
						</td>
						<td colspan="3">
							<textarea name="description" id="description" style="width:80%;height:70px;"></textarea>
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
							<input type="text" name="sale_start" id="sale_start" value="">
						</td>

					</tr>

					<tr>
						<td>
							<label for="category">Category</label>
						</td>
						<td>
							<select name="category" id="category">
								<script id="categoryTpl" type="text/x-handlebars-template" >
									{{#each this}}
										<option value="{{name}}">{{name}}</option>
									{{/each}}
								</script>
							</select>
						</td>
						<td>
							<label for="sale_end">Sale End</label>
						</td>
						<td>
							<input type="text" name="sale_end" id="sale_end" value="">
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
							<label for="back_order_cap">Back Order Cap</label>
						</td>
						<td colspan="3">
							<input type="text" name="back_order_cap" id="back_order_cap" value="">
						</td>

					</tr>

					<tr>
						<td>
							<label for="description">Content</label>
						</td>
						<td colspan="3">
							<textarea name="content" id="content" style="width:600px;height:120px;"></textarea>
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
							<label for="alias">Alias</label>
						</td>
						<td>
							<input type="text" name="alias" id="alias" value="">
						</td>
					</tr>
					<tr>
						<td>
							<label for="template_id">Template</label>
						</td>
						<td>

							<select name="template_id" id="template_id">
                                <?php print $data['templates']; ?>
							</select> 
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
					</tr>
					<tr>
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
		<a id="manage_inventory" class="btn">Manage Inventory</a>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>SKU</th>
					<th>Category</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="4">No Product Found</td></tr>
			</tbody>
		</table>
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
		<?php /* 
		Not needed...
		<a id="moxy_add_image" class="btn">Add Image</a> 
		*/ ?>
		
		
		<ul id="product_images">
		
            <?php print $data['images']; ?>
        
        </ul>
		
		
		
		
	</div>
	<div id="taxonomies_tab" class="content">
		<a id="moxy_add_categories" class="btn">Add Category</a><br>
		<a id="moxy_add_tags" class="btn">Add Tag</a><br>
	</div>

</div>
</form>
