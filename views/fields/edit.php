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
<h2>Edit Field <?php print $data['slug']; ?></h2>
<?php
print \Formbuilder\Form::open($data['baseurl'])
    ->hidden('field_id',$data['field_id'])
    ->text('slug', $data['slug'], array('label'=>'Slug','description'=>'Lowercase alphanumeric identifier with no spaces or special characters.'))
    ->text('label', $data['label'], array('label'=>'Label','description'=>'Human readable name for this field.'))
    ->dropdown('type', array('text'=>'Text','textarea'=>'Textarea','checkbox'=>'Checkbox','dropdown'=>'Dropdown','multicheck'=>'Multi-Check'), $data['type'], array('label'=>'Field Type', 'description'=>'Choose what type of field this is.'))
    ->text('description', $data['description'], array('label'=>'Description', 'description'=>'A brief description of the field.'))
    ->textarea('config', $data['config'], array('label'=>'Configuration','description'=>'Some fields require special customization via a JSON array.'))
    ->text('group', $data['group'], array('label'=>'Group','description'=>'Fields with the same group value will appear together.'))
    ->submit('','Save')
    ->close();
?>
<div>
    <a href="<?php print static::url('field','index'); ?>" class="btn btn-cancel">Cancel</a>
</div>