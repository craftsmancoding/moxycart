<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Create New Option Type</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Here you can Create New Option Types.</p></div>

<div class="moxycart_canvas_inner">


<?php
\Formbuilder\Form::setTpl('description','<p class="description-txt">[+description+]</p>');
print \Formbuilder\Form::open($data['baseurl'],array('id'=>'create_optiontype'))
    ->text('slug', $data['slug'], array('label'=>'Slug','description'=>'Lowercase alphanumeric identifier with no spaces or special characters.','class'=>'input input-half'))
    ->text('name', $data['name'], array('label'=>'Name','description'=>'Human readable name for this list.','class'=>'input input-half'))
    ->text('description', $data['description'], array('label'=>'Description', 'description'=>'A brief description of the field.','class'=>'input input-half'))
    ->html('<span class="btn moxycart-btn" onclick="javascript:submit_form(\'create_optiontype\', \''.self::url('optiontype','create').'\',\'options\');">Save</span>')
    ->html('<span class="btn btn-cancel" onclick="javascript:paint(\'options\');">Cancel</span>')
    //->submit('','Save',array('class'=>'btn moxycart-btn'))
    ->close();
?>

</div>