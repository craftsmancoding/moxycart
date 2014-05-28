<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

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
<h2 class="moxycart_cmp_heading">Create New Option Type</h2>
<?php
print \Formbuilder\Form::open($data['baseurl'])
    ->text('slug', $data['slug'], array('label'=>'Slug','description'=>'Lowercase alphanumeric identifier with no spaces or special characters.'))
    ->text('name', $data['name'], array('label'=>'Name','description'=>'Human readable name for this list.'))
    ->text('description', $data['description'], array('label'=>'Description', 'description'=>'A brief description of the field.'))
    ->submit('','Save')
    ->close();
?>

<div>
    <a href="<?php print static::url('optiontype','index'); ?>" class="btn btn-cancel">Cancel</a>
</div>

<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>