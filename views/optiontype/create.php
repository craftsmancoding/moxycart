<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Create New Option Type</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Here you can Create New Option Types.</p></div>

<div class="moxycart_canvas_inner">


<?php
\Formbuilder\Form::setTpl('description','<p class="description-txt">[+description+]</p>');
print \Formbuilder\Form::open($data['baseurl'])
    ->text('slug', $data['slug'], array('label'=>'Slug','description'=>'Lowercase alphanumeric identifier with no spaces or special characters.','class'=>'input input-half'))
    ->text('name', $data['name'], array('label'=>'Name','description'=>'Human readable name for this list.','class'=>'input input-half'))
    ->text('description', $data['description'], array('label'=>'Description', 'description'=>'A brief description of the field.','class'=>'input input-half'))
    ->submit('','Save',array('class'=>'btn moxycart-btn'))
    ->close();
?>

<div>
    <a href="<?php print static::url('optiontype','index'); ?>" class="btn btn-cancel">Cancel</a>
</div>

</div>
<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>