<?php 
$this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-2.0.3.min.js');
$this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-ui.js');
include dirname(dirname(__FILE__)).'/header.php';  
?>

<script>
jQuery(function() {
    jQuery('#option_terms tbody').sortable();
    jQuery('#option_terms tbody').disableSelection();
});
  
function add_term() {
    var data = jQuery('#template').val();
    jQuery('#option_terms tbody').append(data);
    jQuery('#option_terms tbody').sortable();
}
function remove_term(event) {
    jQuery(this).closest('tr').remove();
}

</script>
<style>
label {
    display:block;
    font-weight:bold;
}
label.checkboxlabel {
    display:inline;
}
input {
    display: block;
    margin-bottom:10px;    
}
input.checkbox {
    display:inline;
}
</style>
<h2 class="moxycart_cmp_heading">Manage Terms: <?php print $data['slug']; ?></h2>

<?php
print $this->getMsg();
?>

<span class="button" onclick="javascript:add_term();">Add Term</span>

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
<form action="<?php print $data['baseurl']; ?>" method="post" >
    <input type="hidden" name="otype_id" value="<?php print $data['otype_id']; ?>" />
    <table id="option_terms">
        <thead>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td colspan="4">Modfiers</td>
            </tr>
            <tr>
                <th>Slug</th>
                <th>Name</th>
                <th>Price</th>
                <th>Weight</th>
                <th>Code</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['terms'] as $t): ?>
                <tr>
                    <td>
                        <input type="hidden" name="oterm_id[]" value="<?php print $t->oterm_id; ?>" />
                        <input type="text" name="slug[]" placeholder="slug" value="<?php print htmlentities($t->slug); ?>" />
                    </td>
                    <td>
                        <input type="text" name="name[]" placeholder="Name" value="<?php print htmlentities($t->name); ?>" />
                    </td>
                    <td>
                        <input type="text" name="mod_price[]" placeholder="0" value="<?php print htmlentities($t->mod_price); ?>" />
                    </td>
                    <td>
                        <input type="text" name="mod_weight[]" placeholder="0" value="<?php print htmlentities($t->mod_weight); ?>" />
                    </td>    
                    <td>
                        <input type="text" name="mod_code[]" placeholder="SKU" value="<?php print htmlentities($t->mod_code); ?>" />
                    </td>
                    <td>
                        <input type="text" name="mod_category[]" placeholder="Default" value="<?php print htmlentities($t->mod_category); ?>" />
                    </td>        
                    <td>
                        <span class="button" onclick="javascript:remove_term.call(this,event);">Remove</span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <input type="submit" value="Save Terms"/>
</form>
<div>
    
    <a href="<?php print static::url('field','index'); ?>" class="btn btn-cancel">Cancel</a>
</div>

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
        <input type="text" name="mod_price[]" placeholder="0" value="" />
    </td>
    <td>
        <input type="text" name="mod_weight[]" placeholder="0" value="" />
    </td>    
    <td>
        <input type="text" name="mod_code[]" placeholder="SKU" value="" />
    </td>
    <td>
        <input type="text" name="mod_category[]" placeholder="Default" value="" />
    </td>        
    <td>
        <span class="button" onclick="javascript:remove_term.call(this,event);">Remove</span>
    </td>
</tr>
</textarea>

<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>