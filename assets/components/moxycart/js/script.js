INIT = {
	
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
        // var product is def'd in the productcontainer.js
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
	            $.post( url+"&action=delete", { image_id: img_id }, function( data ){
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
                       $(".update-container").html(form);

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        $("#update-image").html(errorThrown + ": " + this.url_img_update);
                    },
                    dataType: "html"
                });
		});
	},

	multi_select_drag: function() {
		var selectedClass = 'selected',
        	clickDelay = 600,
	        // click time (milliseconds)
	        lastClick, diffClick; // timestamps


		$("#product_images li").on('click',function(e) {
	    	if (e.ctrlKey) {
	    		$(this).toggleClass('selected');
	    		if($(this).hasClass('ui-draggable')) {
	    			$(this).removeClass('ui-draggable');
	    		}
	    		$(this).draggable({
			        revertDuration: 10,
			        // grouped items animate separately, so leave this number low
			        start: function(e, ui) {
			            ui.helper.addClass(selectedClass);
			        },
			        stop: function(e, ui) {
			            // reset group positions
			            $('.' + selectedClass).css({
			                top: 0,
			                left: 0
			            });
			        },
			        drag: function(e, ui) {
			            // set selected group position to main dragged object
			            // this works because the position is relative to the starting position
			            $('.' + selectedClass).css({
			                top: ui.position.top,
			                left: ui.position.left
			            });
			        }
			    });
		        return false;
		    } 
		})
	},

	drag_drop_delete: function() {

	/*	$( "#product_images" ).sortable({
		  activate: function( event, ui ) {
		  	$('#trash-can').show().slideDown();
		  },
		  deactivate: function( event, ui ) {
		  	$('#trash-can').hide();
		  }
		});*/
		$( "#trash-can" ).droppable({
			over: function() {
				$(this).addClass('over-trash');
			},
			out: function() {
				$(this).removeClass('over-trash');
			},
		    drop: function( event, ui ) {
		      	var id = $(ui.draggable).attr('id');
		      	var url = connector_url + 'image_save';
		      	var img_id = $(ui.draggable).find('a').data('image_id');
		      	
		      	if (confirm("Are you Sure you want to Delete this Image?")) {
		      		$(this).removeClass('over-trash');
		            $.post( url+"&action=delete", { image_id: img_id }, function( data ){
				    	data = $.parseJSON(data);
				    	if(data.success == true) {
				    		$('#'+id).hide();
				    	} else{
				    		$('#moxy-result').html('Failed');
				    		$('#moxy-result-msg').html(data.msg);
				    		$(".moxy-msg").delay(3200).fadeOut(300);
				    	}
				    } );
			    }
			    $(this).removeClass('over-trash');
			    return false;
		    }

	    });
	}

    
}

/**
 * Ajax call to dynamically update the form
 */
function get_spec(spec_id) {
    jQuery('#no_specs_msg').remove();
    jQuery("#spec_id option[value='"+spec_id+"']").attr("disabled","disabled");
    var url = connector_url + "get_spec&spec_id=" + spec_id;
    jQuery.post( url, function(data){
        jQuery("#product_specs").append(data);
    });
}

function remove_spec(spec_id) {
    jQuery('#tr_spec_'+spec_id).remove();
    jQuery("#spec_id option[value='"+spec_id+"']").attr("disabled",false);
}


jQuery(function() {
	INIT.update_product();
	INIT.create_product();
	INIT.fill_form_fields();
	INIT.remove_image();
	INIT.edit_image_modal();
	INIT.drag_drop_delete();
	INIT.multi_select_drag();
	jQuery('#moxytab').tabify();
	jQuery('.datepicker').datepicker();
	//jQuery("#product_images").multisortable();
	jQuery("#product_images").sortable();
    jQuery("#product_images").disableSelection();

	jQuery( document ).on( "mouseenter", ".li_product_image", function() {
 		jQuery(this).find('a.remove-img').show();
	});
	jQuery( document ).on( "mouseleave", ".li_product_image", function() {
 		$(this).find('a.remove-img').hide();
	});
	
    jQuery(function() {
        jQuery(".sortable").sortable({
            connectWith: ".connectedSortable",
        }).disableSelection();
    });  


});