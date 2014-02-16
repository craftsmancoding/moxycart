<!--  
@name ProductInnerTpl
@description Formats single Product
Placeholders: product_id,alias,content,name,sku,type,track_inventory,qty_inventory,qty_alert,price,category,uri,is_active,seq,calculated_price,calculated_price,
-->
<div class="isotope-item color6 size2 cat3" data-date="January 1, 2013" data-popular="10" data-rating="3.5">
<!--SHOP FEATURED ITEM -->
<div class="shop-item shop-item-featured overlay-element">
  <div class="overlay-wrapper">
    <a href="#">

    	[[getProductImages:isequalto=``:then=`
			<img src="[[++moxycart.assets_url]]components/moxycart/images/templates/images/content/prod-002.jpg" alt=" ">
    	`:else=`[[getProductImages? &product_id=`[[+product_id]]` &innerTpl=`ProductImageFeature` &is_active=`1` &limit=`1`]]`? &product_id=`[[+product_id]]` &innerTpl=`ProductImageFeature` &is_active=`1` &limit=`1`]]
    
    </a>
    <div class="overlay-contents">
    	<div class="shop-item-actions">
	    	<a href="[[++site_url]][[+uri]]" class="btn btn-default btn-block">View details</a>
    	</div>
    </div>
  </div>
  <div class="item-info-name-price">
    <h4><a href="#">[[+name]]</a></h4>
    <span class="price">$[[+calculated_price]]</span>
  </div>
</div>
<!--!SHOP FEATURED ITEM -->
</div>