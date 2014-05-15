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
<h2>Create New Currency</h2>
<?php
print \Formbuilder\Form::open($data['baseurl'])
    ->text('code', $data['code'], array('label'=>'Code'))
    ->text('name', $data['name'], array('label'=>'Name'))
    ->text('symbol', $data['symbol'], array('label'=>'Symbol'))
    ->checkbox('is_active', $data['is_active'], array('label'=>'Active?'))
    ->text('seq', $data['seq'], array('label'=>'Sequence'))
    ->submit('','Save')
    ->close();
?>

<div>
    <a href="<?php print static::url('currency','index'); ?>" class="btn btn-cancel">Cancel</a>
</div>