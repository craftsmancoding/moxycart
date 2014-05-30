<div class="moxycart_canvas_inner clearfix">
    <h2 class="moxycart_cmp_heading pull-left">Edit Review</h2>
    <div class="pull-right">
        <a href="<?php print static::page('reviews'); ?>" class="button btn">&laquo; Back to Review List</a>
    </div>    
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p></p></div>

<div class="moxycart_canvas_inner">
<div class="textlabel"><strong>Created:</strong></div> <p><?php print date($this->modx->getOption('manager_date_format'),strtotime($data['timestamp_created'])); ?></p><br>
<div class="textlabel"><strong>Modified:</strong></div> <p><?php print $data['timestamp_modified']; ?></p><br><br>

<?php
\Formbuilder\Form::setTpl('description','<p class="description-txt">[+description+]</p>');
print \Formbuilder\Form::open('',array('id'=>'update_review'))
    ->hidden('review_id',$data['review_id'])
    ->hidden('author_id',$data['author_id'])
    ->text('name', $data['name'], array('description'=>'&nbsp;','label'=>'Reviewer Name','class'=>'input input-half'))
    ->text('email', $data['email'], array('description'=>'&nbsp;','label'=>'Reviewer Email','class'=>'input input-half'))
    ->dropdown('state', array('pending'=>'Pending','approved'=>'Approved','archived'=>'Archived'), $data['state'], array('description'=>'&nbsp;','label'=>'State'))
    ->textarea('content', $data['content'], array('label'=>'Review Content'))
    ->html('<br>') 
    ->html('<span class="btn moxycart-btn" onclick="javascript:submit_form(\'update_review\', \''.self::url('review','edit').'\',\'reviews\');">Save</span>')  
    ->html('<span class="btn btn-cancel" onclick="javascript:paint(\'reviews\');">Cancel</span>')  
//    ->submit('','Save',array('class'=>'btn moxycart-btn'))
    ->close();
?>


</div>