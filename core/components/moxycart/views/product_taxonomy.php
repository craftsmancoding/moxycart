<label for="taxonomy_<?php print $data['id']; ?>"><?php print $data['pagetitle']; ?></label>
<input class="taxonomy_checkbox" type="checkbox" id="taxonomy_<?php print $data['id']; ?>" name="taxonomies[]" value="<?php print $data['taxonomy_id'] ?>"<?php print $data['is_checked'] ?> />