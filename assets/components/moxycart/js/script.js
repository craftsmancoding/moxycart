INIT = {
/*
	exec_wysiwyg: function(){
      bkLib.onDomLoaded(function() {
            new nicEditor({buttonList : ['bold','italic','underline','left','center','right','justify','ol','ul','fontSize','fontFamily','fontFormat','indent','outdent','image','forecolor','bgcolor']}).panelInstance('content');          
      });
    },
*/

	
	update_product: function(){
		$('#product_update').on('submit',function(e){
            console.log('Updating product.');
            var values = $(this).serializeArray();
            if(use_editor == "1") {
		    	var content_val = $('#content_ifr').contents().find('#tinymce').html();
		    	for (var item in values)
				{
				  if (values[item].name == 'content') {
				    values[item].value = content_val;
				  }
				}
			}
			values = jQuery.param(values);
			//console.log(values);
			//return false;

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
			
			console.log('Creating new product.');
	    	var values = $(this).serializeArray();
	    	 if(use_editor == "1") {
		    	var content_val = $('#content_ifr').contents().find('#tinymce').html();
		    	for (var item in values)
				{
				  if (values[item].name == 'content') {
				    values[item].value = content_val;
				  }
				}
			}
			values = jQuery.param(values);


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

	remove_image : function() {
		var url = connector_url + 'image_save';
		$( document ).on( "click", "a.remove-img", function() {
		  	if(confirm('Are you sure you want to delete this image?')) {
				var current_img = $(this).parents('.li_product_image');
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
		});
	},

	edit_image_modal: function() {

		$( document ).on( "click", "a.edit-img", function() {
			var url_img_update = $(this).attr('href');
			 $.ajax({
                    type: "GET",
                    url: url_img_update,
                    success: function(data)
                    {	
                       var form = $(data).find('#modal-container').html();
                       $("#update-image").html(form);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        $("#update-image").html(errorThrown + ": " + this.url_img_update);
                    },
                    dataType: "html"
                });
		});
	},


}

$(function() {
//	INIT.exec_wysiwyg();
	INIT.update_product();
	INIT.create_product();
	INIT.fill_form_fields();
	INIT.remove_image();
	INIT.edit_image_modal();
	$('#moxytab').tabify();
	$('.datepicker').datepicker();
	$("#product_images").sortable();
    $("#product_images").disableSelection();

	$( document ).on( "mouseenter", ".li_product_image", function() {
 		$(this).find('a.remove-img').show();
	});
	$( document ).on( "mouseleave", ".li_product_image", function() {
 		$(this).find('a.remove-img').hide();
	});


});