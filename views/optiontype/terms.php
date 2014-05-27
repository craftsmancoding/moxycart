<?php 
$this->modx->regClientStartupScript($this->config['assets_url'].'js/jquery-2.0.3.min.js');
include dirname(dirname(__FILE__)).'/header.php';  
?>

<script>
function add_term() {
    console.log('add_term called. Ajax request to <?php print self::url('optionterm','create'); ?>');
    jQuery.get( "<?php print self::url('optionterm','create'); ?>", function( data ) {
        console.log('Data received.');
        jQuery('#option_terms tbody').append(data);
        //$( ".result" ).html( data );
        //alert( "Load was performed." );
    });
}

function remove_term() {

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
        <?php ?>
    </tbody>
</table>


<div>
    <span class="button" onclick="javascript:add_term();">Add Term</span>
    <a href="<?php print static::url('field','index'); ?>" class="btn btn-cancel">Cancel</a>
</div>

<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>