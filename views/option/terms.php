<script>
jQuery(function() {
    jQuery('#option_terms tbody').sortable();
    jQuery('#option_terms tbody').disableSelection();
});
  
function add_term() {
    jQuery('#no_terms_found').hide();
    var data = jQuery('#template').val();
    jQuery('#option_terms tbody').append(data);
    jQuery('#option_terms tbody').sortable();
}
function remove_term(event) {
    jQuery(this).closest('tr').remove();
}

</script>

<div class="moxycart_canvas_inner clearfix">
    <h2 class="moxycart_cmp_heading pull-left">Manage Terms: <?php print $data['slug']; ?></h2>
    <div class="pull-right">
        <span class="btn btn-cancel" onclick="javascript:paint('options');">&laquo; Back to Options</span>
    </div>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Define which terms should appear in your option list.  Optionally, you can trigger modifications to the price, weight, SKU code, or Foxycart category when a specific term is selected.</p></div>

<div class="moxycart_canvas_inner">


<?php
print $this->getMsg();
?>

<span class="button btn moxycart-btn" onclick="javascript:add_term();">Add Term</span>

<?php
/*
print \Formbuilder\Form::open($data['baseurl'])
    ->hidden('otype_id',$data['otype_id'])
    ->text('slug', $data['slug'], array('label'=>'Slug','description'=>'Lowercase alphanumeric identifier with no spaces or special characters.'))
    ->text('name', $data['name'], array('label'=>'Name','description'=>'Human readable name for this list.'))
    ->text('description', $data['description'], array('label'=>'Description', 'description'=>'A brief description of the field.'))
    ->submit('','Save')
    ->close();
*/
?>
<form action="<?php print $data['baseurl']; ?>" method="post" id="option_terms">
    <input type="hidden" name="otype_id" value="<?php print $data['otype_id']; ?>" />
    <table id="option_terms" class="classy">
        <thead>
            <tr>
                <td class="unmodifier" colspan="2">&nbsp;</td>
                <td class="modifier" colspan="4">Modfiers</td>
                <td class="unmodifier">&nbsp;</td>
            </tr>
            <tr>
                <th>Slug</th>
                <th>Name</th>
                <th class="modifier-th">Price</th>
                <th class="modifier-th">Weight</th>
                <th class="modifier-th">Code</th>
                <th class="modifier-th">Category</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>

            <?php if(!empty($data['terms'])) : ?>
                <?php foreach ($data['terms'] as $t): ?>
                    <tr>
                        <td>
                            <input type="hidden" name="oterm_id[]" value="<?php print $t->oterm_id; ?>" />
                            <input type="hidden" name="seq[]" value="<?php print $t->oterm_id; ?>" />
                            <input type="text" name="slug[]" placeholder="slug" value="<?php print htmlentities($t->slug); ?>" />
                        </td>
                        <td>
                            <input type="text" name="name[]" placeholder="Name" value="<?php print htmlentities($t->name); ?>" />
                        </td>
                        <td>
                            <input type="text" name="mod_price[]"  class="input-half" placeholder="0" value="<?php print htmlentities($t->mod_price); ?>" />
                        </td>
                        <td>
                            <input type="text" name="mod_weight[]" class="input-half"  placeholder="0" value="<?php print htmlentities($t->mod_weight); ?>" />
                        </td>    
                        <td>
                            <input type="text" name="mod_code[]" placeholder="SKU" value="<?php print htmlentities($t->mod_code); ?>" />
                        </td>
                        <td>
                            <input type="text" name="mod_category[]" placeholder="Default" value="<?php print htmlentities($t->mod_category); ?>" />
                        </td>        
                        <td>
                            <span class="btn" onclick="javascript:remove_term.call(this,event);">x</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
            <tr id="no_terms_found"><td colspan="7"><div class="danger">No terms Found...</div></td></tr>
            <?php endif; ?>

        </tbody>
    </table>
    <br>
    <span class="btn moxycart-btn" onclick="javascript:submit_form('option_terms', '<?php print self::url('optiontype','terms'); ?>','options');">Save</span>
    <span class="btn btn-cancel" onclick="javascript:paint('options');">Cancel</span>

</form>

<?php
/*
This is here as a template for each row that gets added dynamically.
By putting the html here in a hidden field, we don't need to make extra ajax
requests to get it.
*/
?>
<textarea id="template" style="display:none;">
<tr>
    <td>
        <input type="hidden" name="oterm_id[]" value="" />
        <input type="text" name="slug[]" placeholder="slug" value="" />
    </td>
    <td>
        <input type="text" name="name[]" placeholder="Name" value="" />
    </td>
    <td>
        <input type="text" name="mod_price[]" class="input-half" placeholder="0" value="" />
    </td>
    <td>
        <input type="text" name="mod_weight[]" class="input-half" placeholder="0" value="" />
    </td>    
    <td>
        <input type="text" name="mod_code[]" placeholder="SKU" value="" />
    </td>
    <td>
        <input type="text" name="mod_category[]" placeholder="Default" value="" />
    </td>        
    <td>
        <span class="btn" onclick="javascript:remove_term.call(this,event);">x</span>
    </td>
</tr>
</textarea>

</div>