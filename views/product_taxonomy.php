<div class="checkbox">
  <label for="taxonomy_<?php print $data['id']; ?>">
    <input class="taxonomy_checkbox" type="checkbox" id="taxonomy_<?php print $data['id']; ?>" name="taxonomies[]" value="<?php print $data['id'] ?>"<?php print $data['is_checked'] ?> />
    <?php print $data['pagetitle']; ?>
  </label>
</div>