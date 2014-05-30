<tr>
    <td><?php print $data['label'];?> (<?php print $data['slug']; ?>)</td>
    <td>
        <input type="hidden" name="Fields[field_id][]" value="<?php print $data['field_id']; ?>" />
    <?php
        print \Formbuilder\Form::text('Fields[value][]');
    ?>
    </td>
    <td><?php print $data['description'];?></td>
    <td><span class="btn" onclick="javascript:remove_me.call(this,event,'tr');">Remove</span></td>
</tr>