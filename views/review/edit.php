<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Edit Review</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p></p></div>

<div class="moxycart_canvas_inner">

<strong>Created:</strong> <?php print $data['timestamp_created']; ?><br/>
<strong>Modified:</strong> <?php print $data['timestamp_modified']; ?><br/>

<?php
//\Formbuilder\Form::setTpl('description','<p class="description-txt">[+description+]</p>');
print \Formbuilder\Form::open('',array('id'=>'update_review'))
    ->hidden('review_id',$data['review_id'])
    ->hidden('author_id',$data['author_id'])
    ->text('name', $data['name'], array('label'=>'Reviewer Name'))
    ->text('email', $data['email'], array('label'=>'Reviewer Email'))
    ->textarea('content', $data['content'], array('label'=>'Review Content'))
    ->dropdown('state', array('pending'=>'Pending','approved'=>'Approved','archived'=>'Archived'), $data['state'], array('label'=>'State'))
    ->text('description', $data['description'], array('label'=>'Description', 'description'=>'A brief description of the field.'))

    ->html('<br>')
    ->text('group', $data['group'], array('label'=>'Group','description'=>'Fields with the same group value will appear together.'))
    ->html('<span class="btn moxycart-btn" onclick="javascript:submit_form(\'update_review\', \''.self::url('review','edit').'\',\'reviews\');">Save</span>')    
//    ->submit('','Save',array('class'=>'btn moxycart-btn'))
    ->close();
?>
<div>
    <span class="btn btn-cancel" onclick="javascript:paint('reviews');">Cancel</span>
</div>

</div>