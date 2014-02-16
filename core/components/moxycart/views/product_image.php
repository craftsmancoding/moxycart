<li class="li_product_image <?php print ($data['is_active'] == 0) ? 'inactive' : ''; ?>" id="product-image-<?php print $data['image_id']; ?>">
	<div class="img-info-wrap">
	    <a class="edit-img" href="<?php print $this->getUrl('image_update'); ?>&image_id=<?php print $data['image_id']; ?>" data-image_id="<?php print $data['image_id']; ?>" data-toggle="modal" data-target="#update-image">
		  <img src="<?php print (!empty($data['thumbnail_url'])) ? $data['thumbnail_url'] : $data['url'] ; ?>?rand=<?php print uniqid(); ?>" alt="<?php print $data['alt'] ?>" width="<?php //print $data['thumb_width']; ?>" />
		</a>
	    <input type="hidden" name="images[]" value="<?php print $data['image_id']; ?>" />
	    <!-- Button trigger modal -->
		
		<!-- Modal-->
	</div>
</li>