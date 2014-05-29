<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Edit Option Type <?php print $data['slug']; ?></h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Here you can Edit Option Types.</p></div>

<div class="moxycart_canvas_inner">

<?php
\Formbuilder\Form::setTpl('description','<p class="description-txt">[+description+]</p>');
print \Formbuilder\Form::open($data['baseurl'],array('id'=>'optiontype_edit'))
    ->hidden('otype_id',$data['otype_id'])
    ->text('slug', $data['slug'], array('label'=>'Slug','description'=>'Lowercase alphanumeric identifier with no spaces or special characters.','class'=>'input input-half'))
    ->text('name', $data['name'], array('label'=>'Name','description'=>'Human readable name for this list.','class'=>'input input-half'))
    ->text('description', $data['description'], array('label'=>'Description', 'description'=>'A brief description of the field.','class'=>'input input-half'))
    ->html('<span class="btn moxycart-btn" onclick="javascript:submit_form(\'optiontype_edit\', \''.self::url('optiontype','edit').'\',\'options\');">Save</span>')    
    //->submit('','Save',array('class'=>'btn moxycart-btn'))
    ->close();
?>
<div>
    <span class="btn btn-cancel" onclick="javascript:paint('options');">Cancel</span>
</div>

</div>