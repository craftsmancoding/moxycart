<div class="moxycart_canvas_inner clearfix">
    <h2 class="moxycart_cmp_heading pull-left">Edit Field <?php print $data['slug']; ?></h2>
    <div class="pull-right">
        <a href="<?php print static::page('fields'); ?>" class="button btn">&laquo; Back to Field List</a>
    </div>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Here you can Create Edit Custom Field.</p></div>

<div class="moxycart_canvas_inner">


<?php
\Formbuilder\Form::setTpl('description','<p class="description-txt">[+description+]</p>');
print \Formbuilder\Form::open($data['baseurl'],array('id'=>'update_field'))
    ->hidden('field_id',$data['field_id'])
    ->text('slug', $data['slug'], array('label'=>'Slug','description'=>'Lowercase alphanumeric identifier with no spaces or special characters.','class'=>'input input-half'))
    ->text('label', $data['label'], array('label'=>'Label','description'=>'Human readable name for this field.','class'=>'input input-half'))
    ->dropdown('type', array('text'=>'Text','textarea'=>'Textarea','checkbox'=>'Checkbox','dropdown'=>'Dropdown','multicheck'=>'Multi-Check'), $data['type'], array('label'=>'Field Type', 'description'=>'Choose what type of field this is.'))
    ->text('description', $data['description'], array('label'=>'Description', 'description'=>'A brief description of the field.','class'=>'input input-half'))
    ->textarea('config', $data['config'], array('label'=>'Configuration','description'=>'Some fields require special customization via a JSON array.'))
    ->html('<br>')
    ->text('group', $data['group'], array('label'=>'Group','description'=>'Fields with the same group value will appear together.','class'=>'input input-half'))
    ->html('<span class="btn moxycart-btn" onclick="javascript:submit_form(\'update_field\', \''.self::url('field','edit').'\',\'fields\');">Save</span>')  
    ->html('<span class="btn btn-cancel" onclick="javascript:paint(\'fields\');">Cancel</span>')  
//    ->submit('','Save',array('class'=>'btn moxycart-btn'))
    ->close();
?>

</div>