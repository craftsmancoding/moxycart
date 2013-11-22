<li class="taxonomy_term <?php $data['class'];?>">
    <div class="checkbox">
	  <label for="product_term_<?php print $data['term_id']; ?>">
	    <input class="term_checkbox" id="product_term_<?php print $data['term_id']; ?>" type="checkbox" name="terms[]" value="<?php print $data['term_id']; ?>" /<?php print $data['is_checked']; ?>>
	    <?php print $data['pagetitle']; ?>
	  </label>
	</div>
    <?php print $data['terms']; //any sub-terms ?>
</li>


