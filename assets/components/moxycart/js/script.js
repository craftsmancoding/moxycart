INIT = {
	exec_wysiwyg: function(){
      bkLib.onDomLoaded(function() {
            new nicEditor({buttonList : ['bold','italic','underline','left','center','right','justify','ol','ul','fontSize','fontFamily','fontFormat','indent','outdent','image','forecolor','bgcolor']}).panelInstance('content');          
      });
    },

	
	update_product: function(){
		$('#product_update').on('submit',function(e){
	    	var values = $(this).serialize();
	    	var url = connector_url + 'product_save';
		    $.post( url+"&action=update", values, function(data){
		    	$('.moxy-msg').show();
		    	data = $.parseJSON(data);
		    	if(data.success == true) {
		    		$('#moxy-result').html('Success');
		    	} else{
		    		$('#moxy-result').html('Failed');
		    	}
		    	$('#moxy-result-msg').html(data.msg);
		    	$(".moxy-msg").delay(3200).fadeOut(300);
		    } );
		    e.preventDefault();
	    })
	},

	create_product: function(){
		$('#product_create').on('submit',function(e){
	    	var values = $(this).serialize();
	    	var url = connector_url + 'product_save';
		    $.post( url+"&action=create", values, function(data){
		    	data = $.parseJSON(data);
		    	window.location.href = redirect_url + data.product_id;
		    } );
		    e.preventDefault();
	    })
	},

	fill_form_fields : function() {
		$.each(product, function(name, val){
	        var $el = $('#'+name),
	            type = $el.attr('type');	    
	        switch(type){
	            case "checkbox":
	                $el.attr("checked", "checked");
	                break;
	            case "radio":
	                $el.filter('[value="'+val+'"]').attr("checked", "checked");
	                break;
	            default:
	                $el.val(val);
	        }
	    });
	},

	


}

$(function() {
	INIT.exec_wysiwyg();
	INIT.update_product();
	INIT.create_product();
	INIT.fill_form_fields();
	$('#moxytab').tabify();
	$("#product_images").sortable();
    $("#product_images").disableSelection();
});