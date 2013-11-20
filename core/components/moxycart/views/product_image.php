<li class="li_product_image">
    <img src="<?php print $data['url']; ?>" height="100" width="100" alt="<?php print $data['alt'] ?>"/>
    <input type="hidden" name="images[<?php print $data['image_id']; ?>][]" value="<?php print $data['seq']; ?>" />
</li>