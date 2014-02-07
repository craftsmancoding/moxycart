<script>
jQuery(document).ready(function() {
	$("#product_rows tbody").sortable();
    $("#product_rows tbody").disableSelection();
});

function product_save_seq() {
    var values = jQuery('#products-sort').serialize();
    jQuery.post( connector_url+"product_save_seq", values, function( data ){
    	data = jQuery.parseJSON(data);
    	if(data.success == true) {
    		$('#moxy-result').html('Success');
    		$('#moxy-result-msg').html(data.msg);
    		$(".moxy-msg").delay(3200).fadeOut(300);
            setTimeout(function() {
                window.location.href = back_url;
            }, 1000);

    	} else{
    		$('#moxy-result').html('Failed');
    		$('#moxy-result-msg').html(data.msg);
    		$(".moxy-msg").delay(3200).fadeOut(300);
    	}
    });
}
</script>

<div class="moxy-msg">
    <div id="moxy-result"></div>
    <div id="moxy-result-msg"></div>
</div>

<form id="products-sort">


<div id="modx-panel-workspace" class="x-plain container">

    <div class="moxy-header clearfix">
        <div class="moxy-header-title">
           <h2>Set Manual Sort Order</h2>
        </div>
            
        <div class="moxy-buttons-wrapper">
             <button class="btn" onclick="javascript:product_save_seq(); return false;">Save</button>
            <a class="btn" href="<?php print $data['back_url']; ?>">Close</a>
        </div>
    </div>

    <div class="well">

        <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
                <p>Drag and drop your products into any order.</p>
            </div>
            <br>

        
            <table class="table classy" id="product_rows">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($data['results'] as $p):
                    ?>
                        <tr>
                            <td><?php print $p['name']; ?>
                                <input type="hidden" name="seq[]" value="<?php print $p['product_id']; ?>" />
                            </td>
                            <td><?php print $p['sku']; ?></td>
                            <td><?php print number_format($p['price'],2); ?></td>
                        </tr>
                    <?php
                    endforeach;
                    ?>
                </tbody>
            </table>

           
        

    </div>
</div>
</form>

