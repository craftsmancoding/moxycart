<div id="modal-container">
		<script src="<?php print $data['jcrop_js']; ?>" type="text/javascript"></script>
		<script>
			$(function(){
				jQuery('#jcrop_target').Jcrop({
		    		onChange: set_coords,
		    		onSelect: set_coords
		        });

				$('#update-img-msg').hide();
				$('#image_update_form').on('submit',function(e){
		           	var values = $(this).serialize();
		           	var image_id = $('#image_id').val();
					/*var fileInput = document.getElementById('file');
					var file = fileInput.files[0];
					var formData = new FormData();
					formData.append('file', file);*/
					$.ajax({
		                type: "POST",
		                url: connector_url+"image_save&action=update&image_id="+image_id,  
		                data: values,  
		                success: function( data )  
		                {
		                     data = $.parseJSON(data);

					    	if(data.success == true) {
					    		$('#update-img-msg').addClass('alert-success').html(data.msg).show();
					    		$('#close-update').addClass('has-saved');
					    	} else{
					    		$('#update-img-msg').addClass('alert-danger').html(data.msg).show();
					    	}
					    	$("#update-img-msg").delay(3200).fadeOut(300);
		                }
		           });
				    e.preventDefault();
			    });

				$('#close-update').on('click', function(){
					if($(this).hasClass('has-saved')) {
						var image_id = $(this).data('image_id');
						$.get(connector_url+"get_image&image_id="+image_id, function( data ) {
							var img_markup = $(data).filter('#product-image-'+image_id).find('.img-info-wrap');
							console.log(img_markup)
						 	$('#product-image-'+image_id).html(img_markup);
						});
					}
				});

			});

		/**
	     * Callback function ref'd by jcrop
	     * this sets values in hidden form fields
	     * so we know how to handle the cropping action.
	     */
	    function set_coords(c) {
	    	jQuery('#x').val(c.x);
	    	jQuery('#y').val(c.y);
	    	jQuery('#x2').val(c.x2);
	    	jQuery('#y2').val(c.y2);
	    	jQuery('#w').val(c.w);
	    	jQuery('#h').val(c.h);        
	    }

	    /**
	     * This triggers the cropping action
	     *
	     *
	     */
	    function crop() {
	        console.log('Cropping image.');
	        var values = jQuery('#image_update_form').serialize();
	    	var url = connector_url + 'image_crop';

		    jQuery.post( url, values, function(data){
		       data = jQuery.parseJSON(data);
	            if (data.success) {
	                //console.log(data.img);
	                jQuery("#target_image").html(data.img);
	                $('#update-img-msg').addClass('alert-success').html(data.msg).show();
	                $('#close-update').addClass('has-saved');
	            }
	            else {
	            	$('#update-img-msg').addClass('alert-danger').html(data.msg).show();
	            }
	            $("#update-img-msg").delay(3200).fadeOut(300);
		    });
		    //e.preventDefault();
	//    })
	    }

	   function remove_image() {
			var url = connector_url + 'image_save';
			  	if(confirm('Are you sure you want to delete this image?')) {
					/*var current_img = $(this).parents('.li_product_image');
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
				    } );*/
		        }
		        return false;
		}

		</script>
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title" id="myModalLabel">Update Image</h4>
	      </div>
	      <div id="update-img-msg" class="alert"></div>
	      <form id="image_update_form"  method="POST" action="#" class="form-horizontal">
		      <div class="modal-body">
					<input type="hidden" name="image_id" id="image_id"  value="<?php print $data['image_id'] ?>">
				 <div class="form-group">
				    <label for="title" class="control-label">Title</label>
				     <input type="text" class="form-control" name="title" id="title" value="<?php print $data['title']; ?>">
				 </div>
				 <div class="form-group">
				    <label for="alt" class="control-label">Alt</label>
				     <input type="text" class="form-control" name="alt" id="alt" value="<?php print $data['alt']; ?>">
				 </div>

<!-- 				  <div class="form-group">
  <label for="file" class="control-label">&nbsp;</label>
					 <input type="file" id="file" name="file">
				 </div> -->


				<div class="form-group">
					<label for="is_active" class="control-label">Is Active</label>
				     <input type="checkbox" class="form-control" id="is_active" <?php if($data['is_active'] == 1){ print 'checked'; } ?>>
				 </div>


				<div class="span6" id="image_stuff">
                     <div id="image_stuff-inner">
                              <div id="target_image">
                                   <?php include dirname(__FILE__).'/image.php'; ?>
                                </div>
                                
                                <input type="hidden" id="x" name="x" />
                                <input type="hidden" id="x2" name="x2" />
                                <input type="hidden" id="y" name="y" />
                                <input type="hidden" id="y2" name="y2" />
                                <input type="hidden" id="w" name="w" />
                                <input type="hidden" id="h" name="h" />

                    </div>
                    <span class="btn crop-btn" onclick="javascript:crop(); return false;">Crop</span>
                </div>
                                

				 

		      </div>
			      <div class="modal-footer">
			        <button type="button" data-image_id="<?php print $data['image_id']; ?>" class="btn btn-default" id="close-update" data-dismiss="modal">Close</button>
			        <button type="button" data-image_id="<?php print $data['image_id']; ?>" class="btn btn-default" onclick="javascript:remove_image(); return false;">Delete</button>
			        <input type="submit" class="btn btn-custom" name="submit" value="Save changes">
			      </div>
	      	</form>

<form action="/file-upload"
      class="dropzone"
      id="my-awesome-dropzone"></form>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
</div>