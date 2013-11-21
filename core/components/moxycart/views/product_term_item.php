<li class="taxonomy_term <?php $data['class'];?>">
    <label for="product_term_<?php print $data['term_id']; ?>"><?php print $data['depth']; ?> <?php print $data['pagetitle']; ?></label>
    <input class="term_checkbox" id="product_term_<?php print $data['term_id']; ?>" type="checkbox" name="terms[]" value="<?php print $data['term_id']; ?>" /<?php print $data['is_checked']; ?>>
    <?php print $data['terms']; //any sub-terms ?>
</li>