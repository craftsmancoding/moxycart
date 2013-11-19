<div id="modx-panel-workspace" class="x-plain container">
	<div class="moxy-header clearfix">
		<div class="moxy-header-title">
			<h2>Product Update</h2>
		</div>
			
		<div class="moxy-buttons-wrapper">
			<button class="btn">Save</button>
			<button class="btn">View</button>
			<button class="btn">Close</button>
		</div>
	</div>
	
	
	<ul id="moxytab" class="menu">
		<li class="active"><a href="#product">Product</a></li>
		<li><a href="#settings">Settings</a></li>
		<li><a href="#variations">Variations</a></li>
		<li><a href="#specs">Specs</a></li>
		<li><a href="#images">Images</a></li>
		<li><a href="#taxonomies">Taxonomies</a></li>
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
						</td>
						<td>
							<label for="is_active">Active</label>
						</td>
						<td>
							<select name="is_active" id="is_active">
								<option value="1">Yes</option>
								<option value="1">No</option>
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
								<option value="default">Default</option>
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
							<textarea name="content" id="content" style="width:80%;height:120px;"></textarea>
						</td>
					</tr>

						
				</tbody>
			</table>
	</div>

	<div id="settings" class="content">
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
								<option value="default">Default</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label for="currency_id">Currency</label>
						</td>
						<td>
							<select name="currency_id" id="currency_id">
								<option value="default">Default</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label for="type">Product Type</label>
						</td>
						<td>
							<select name="type" id="type">
								<option value="default">Default</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label for="product_container">Product Container</label>
						</td>
						<td>
							<select name="product_container" id="product_container">
								<option value="default">Default</option>
							</select>
						</td>
					</tr>
						
				</tbody>
			</table>
	</div>

	<div id="variations" class="content"><br>
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
	
	<div id="specs" class="content">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Spec</th>
					<th>Value</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="3">No Spec Found</td></tr>
			</tbody>
		</table>
	</div>

	<div id="images" class="content">
		<a id="moxy_add_image" class="btn">Add Image</a>
	</div>
	<div id="taxonomies" class="content">
		<a id="moxy_add_categories" class="btn">Add Category</a><br>
		<a id="moxy_add_tags" class="btn">Add Tag</a><br>
	</div>

</div>