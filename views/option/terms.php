<script>
jQuery(function() {
    jQuery('#option_terms tbody').sortable();
    jQuery('#option_terms tbody').disableSelection();
});
  
function add_term() {
    jQuery('#no_terms_found').hide();
    var data = jQuery('#term_tpl').html();
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


<span class="button btn moxycart-btn btn-primary" onclick="javascript:add_term();">Add Term</span>

<?php
/*
print \Formbuilder\Form::open($data['baseurl'])
    ->hidden('option_id',$data['option_id'])
    ->text('slug', $data['slug'], array('label'=>'Slug','description'=>'Lowercase alphanumeric identifier with no spaces or special characters.'))
    ->text('name', $data['name'], array('label'=>'Name','description'=>'Human readable name for this list.'))
    ->text('description', $data['description'], array('label'=>'Description', 'description'=>'A brief description of the field.'))
    ->submit('','Save')
    ->close();
*/
?>
<form action="<?php print $data['baseurl']; ?>" method="post" id="option_terms">
    <input type="hidden" name="option_id" value="<?php print $data['option_id']; ?>" />
    <table id="option_terms" class="classy">
        <thead>
            <tr>
                <td class="unmodifier" colspan="2">&nbsp;</td>
                <td class="modifier" colspan="4">Modfiers</td>
                <td class="unmodifier">&nbsp;</td>
            </tr>
            <tr>
                <th>Name</th>
                <th>Slug</th>
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
                            <input type="text" name="name[]" placeholder="Name" style="width:100px;" value="<?php print htmlentities($t->name); ?>" />
                        </td>
                        <td>
                            <input type="text" name="slug[]" placeholder="slug" style="width:100px;" value="<?php print htmlentities($t->slug); ?>" />
                        </td>
                        <td>
                            <?php print \Formbuilder\Form::dropdown('mod_price_type[]', \Moxycart\OptionTerm::types(), $t->mod_price_type,array('style'=>'width: 40px;')); ?>
                            <input type="text" name="mod_price[]"  class="input-half" style="width:60px;" placeholder="0" value="<?php print htmlentities($t->mod_price); ?>" />
                        </td>
                        <td>
                            <?php print \Formbuilder\Form::dropdown('mod_weight_type[]', \Moxycart\OptionTerm::types(), $t->mod_weight_type,array('style'=>'width: 40px;')); ?>
                            <input type="text" name="mod_weight[]" class="input-half"  style="width:60px;" placeholder="0" value="<?php print htmlentities($t->mod_weight); ?>" />
                        </td>    
                        <td>
                            <?php print \Formbuilder\Form::dropdown('mod_code_type[]', \Moxycart\OptionTerm::types(), $t->mod_code_type,array('style'=>'width: 40px;')); ?>
                            <input type="text" name="mod_code[]" style="width:100px;" placeholder="SKU" value="<?php print htmlentities($t->mod_code); ?>" />
                        </td>
                        <td>
                            <?php print \Formbuilder\Form::dropdown('mod_category_type[]', \Moxycart\OptionTerm::types(), $t->mod_category_type,array('style'=>'width: 40px;')); ?>
                            <input type="text" name="mod_category[]" style="width:100px;" placeholder="Default" value="<?php print htmlentities($t->mod_category); ?>" />
                        </td>        
                        <td>
                            <span class="btn btn-info btn-mini" onclick="javascript:remove_term.call(this,event);">x</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
            <tr id="no_terms_found"><td colspan="7"><div class="danger">No terms Found...</div></td></tr>
            <?php endif; ?>

        </tbody>
    </table>
    <br>
    <span class="btn moxycart-btn" onclick="javascript:submit_form('option_terms', '<?php print self::url('option','terms'); ?>','options');">Save</span>
    <span class="btn btn-cancel" onclick="javascript:paint('options');">Cancel</span>

</form>

<?php
/*
This is here as a template for each row that gets added dynamically.
By putting the html here in a hidden field, we don't need to make extra ajax
requests to get it.
*/
?>
<script id="term_tpl" type="text/x-handlebars-template">
<tr>
    <td>
        <input type="hidden" name="oterm_id[]" value="" />
        <input type="text" name="name[]" placeholder="Name" value="" />        
    </td>
    <td>

        <input type="text" name="slug[]" placeholder="slug" value="" />
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
</script>

</div>