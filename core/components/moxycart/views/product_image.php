<li class="li_product_image">
    <img src="<?php print $data['url']; ?>" height="100" width="100" alt="<?php print $data['alt'] ?>"/>
    <input type="hidden" name="images[]" value="<?php print $data['image_id']; ?>" />
    <a href="#" data-image_id="<?php print $data['image_id']; ?>" data-file="<?php print $data['url']; ?>" class="remove-img" >Remove</a>
    <!-- Button trigger modal -->
	<a href="?a=<?php print $data['action'] ?>&f=image_update&image_id=<?php print $data['image_id']; ?>" class="edit-img" data-toggle="modal" data-target="#update-image">
	  Edit
	</a>
	<!-- Modal-->
</li>