<li class="li_product_image" id="product-image-<?php print $data['asset_id']; ?>">
	<div class="img-info-wrap">
	    <a class="edit-img" href="#<?php print $data['asset_id']; ?>" data-image_id="<?php print $data['asset_id']; ?>" data-toggle="modal" data-target="#update-image">
		  <img src="<?php print $data['thumbnail_url']; ?>?rand=<?php print uniqid(); ?>" alt="<?php print $data['alt']; ?>" width="" />
		</a>
	    <input type="hidden" name="Assets[asset_id][]" value="<?php print $data['asset_id']; ?>" />
	    <!-- Button trigger modal -->
		
		<!-- Modal-->
	</div>
</li>