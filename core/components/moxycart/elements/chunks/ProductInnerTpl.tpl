<!--  
@name ProductInnerTpl
@description Formats single Product
Placeholders: product_id,alias,content,name,sku,type,track_inventory,qty_inventory,qty_alert,price,category,uri,is_active,seq,calculated_price,calculated_price,
-->
<div class="product-item-[[+product_id]]">
	<h1>[[+name]]</h1>
	<p>[[+price]]</p>
	<div class="product-content">
		[[+content]]
	</div>
</div>