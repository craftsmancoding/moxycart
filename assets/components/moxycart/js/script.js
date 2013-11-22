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
		    $.post( url+"&action=create", values, function( data ){
		    	data = $.parseJSON(data);
		    	if(data.success == true) {
		    		window.location.href = redirect_url + data.product_id;
		    	} else{
		    		$('#moxy-result').html('Failed');
		    		$('#moxy-result-msg').html(data.msg);
		    		$(".moxy-msg").delay(3200).fadeOut(300);
		    	}
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

	remove_image : function(e) {
		var remove_img = $('.remove-img');
		remove_img.hide();
		$('.li_product_image').hover(
            function() { $(this).find('a.remove-img').show(); },
            function() { $(this).find('a.remove-img').hide(); }
          );
		var url = connector_url + 'image_save';
		remove_img.on('click',function(){
			if(confirm('Are you sure you want to delete this image?')) {
				var current_img = $(this).parent();
	            var img_id = $(this).data('image_id');
	            var img_file = $(this).data('file');
	            $.post( url+"&action=delete", { image_id: img_id, file: img_file }, function( data ){
			    	data = $.parseJSON(data);
			    	if(data.success == true) {
			    		current_img.remove();
			    	} else{
			    		$('#moxy-result').html('Failed');
			    		$('#moxy-result-msg').html(data.msg);
			    		$(".moxy-msg").delay(3200).fadeOut(300);
			    	}
			    } );
	        }
			return false;
		})
	}

	


}

$(function() {
	INIT.exec_wysiwyg();
	INIT.update_product();
	INIT.create_product();
	INIT.fill_form_fields();
	INIT.remove_image();
	$('#moxytab').tabify();
	$('.datepicker').datepicker();
	$("#product_images").sortable();
    $("#product_images").disableSelection();

});