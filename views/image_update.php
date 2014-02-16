<div id="modal-container">
		<script src="<?php print $data['jcrop_js']; ?>" type="text/javascript"></script>
		<script>
			$(function(){
				
				$(".loader-ajax").hide();
				$('#update-image').on('hidden.bs.modal', function (e) {
					$('.update-container').empty();
					 $(".loader-ajax").show();	
				});

				/**
				* Update thumb on images tab
				* Once the image got cropped, bewly cropped must also appears on thumb
				**/
				function update_thumb(image_id) {
					$.get(connector_url+"get_image&image_id="+image_id, function( data ) {
						var img_markup = $(data).filter('#product-image-'+image_id);

					 	$('#product-image-'+image_id).replaceWith(img_markup);
					});
				}

				$('#update-img-msg').hide();
				$('#update-save').on('click',function(e){
		           	var values = $('#image_update_form').serialize();
		           	var image_id = $('#image_id').val();
		           
					$.ajax({
		                type: "POST",
		                url: connector_url+"image_save&action=update&image_id="+image_id,  
		                data: values,  
		                success: function( data )  
		                {
		                     data = $.parseJSON(data);

					    	if(data.success == true) {
					    		$('#update-img-msg').addClass('alert-success').html(data.msg).show();
					    		$("#update-img-msg").delay(1000).fadeOut(300);
					    		window.setTimeout(function(){
								     $('#update-image').modal('hide');
								}, 1000);
								update_thumb(image_id);
					    	} else{
					    		$('#update-img-msg').addClass('alert-danger').html(data.msg).show();
					    		$("#update-img-msg").delay(3200).fadeOut(300);
					    	}
					    	
		                }
		           });
				    e.preventDefault();
			    });

			    $('#remove-img-modal').on('click', function(){
					var url = connector_url + 'image_save';
					var img_id = $(this).data('image_id');
	            	var img_file = $(this).data('file');
				  	if(confirm('Are you sure you want to delete this image?')) {
			            $.post( url+"&action=delete", { image_id: img_id, file: img_file }, function( data ){
			            	console.log(data)
					    	data = $.parseJSON(data);
					    	if(data.success == true) {
					    		$('#update-image').modal('hide');
					    		$('#product-image-'+img_id).remove();
					    	} else{
					    		$('#update-img-msg').addClass('alert-danger').html(data.msg).show();
					    		$(".moxy-msg").delay(3200).fadeOut(300);
					    	}
					    } );
			        }
			        return false;
				});


				$('#close-update').on('click', function(){
					if($(this).hasClass('has-cropped')) {

						var image_id = $(this).data('image_id');
						update_thumb(image_id);
					}
				});

				

			});
			


			$(document).on('click', '.image-nav', function() {

					var seq = $(this).data('seq');
					var	nav_dir = $(this).data('nav_dir');
					var	product_id = $(this).data('product_id');
					console.log(connector_url+"get_image&image_navigate=1&nav_dir="+nav_dir+"&seq="+seq+"&product_id="+product_id);
					 $.get(connector_url+"get_image&image_navigate=1&nav_dir="+nav_dir+"&seq="+seq+"&product_id="+product_id, function( data ) {
					 	//console.log(data);
						$('.form-update-wrapper').replaceWith(data);
					});
					return false;
			});

			$(document).on('click',"#edit-in-modal",function() {
				$(this).text('Crop Image').addClass('crop-now');
			  	$('#jcrop_target').Jcrop({
		    		onChange: set_coords,
		    		onSelect: set_coords
		        });
			});

			$(document).on('click',".crop-now",function() {
				crop();
				$(this).text('Edit Image').removeClass('crop-now');
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
	    	
	    	$('#thumbnail_preview_static').hide();
	    	$('#thumbnail_preview_dynamic').show();
	    	
	    	var rx = 100 / c.w;
        	var ry = 100 / c.h;
            $('#thumbnail_preview_dynamic').css({
                width: Math.round(rx * 500) + 'px',
                height: Math.round(ry * 370) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
            
	    	
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
	                $('#close-update, #update-save').addClass('has-cropped');
	            }
	            else {
	            	$('#update-img-msg').addClass('alert-danger').html(data.msg).show();
	            }
	            $("#update-img-msg").delay(3200).fadeOut(300);
		    });
		    //e.preventDefault();
	//    })
	    }

		</script>
	 
	      <div id="update-img-msg" class="alert"></div>
	      <div class="form-update-wrapper">
		      <div class="image-nav">
		      	<a href="#" class="image-nav prev" data-seq="<?php print $data['seq'] ?>" data-product_id="<?php print $data['product_id'] ?>" data-nav_dir="prev" >Previous</a>
		      	<a href="#" data-seq="<?php print $data['seq'] ?>" data-product_id="<?php print $data['product_id'] ?>" data-nav_dir="next" class="image-nav next">Next</a>
		      </div>
		      
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
						<label for="checkbox_id" class="control-label">Is Active</label>
						<input type="checkbox" class="form-control" name="is_active" id="checkbox_id" <?php if($data['is_active'] == 1){ print 'checked'; } ?> value="1" /> 
					</div>
					
					<div class="form-group">
					    <label class="control-label">Dimensions  <?php print $data['width']; ?> x <?php print $data['height']; ?></label>
	                </div>

	                <div id="thumbnail_preview_container">
	                    <span>Thumbnail Preview</span>

	                        
	                        <div style="width:<?php print $data['moxycart.thumbnail_width'] ;?>px;height:<?php print $data['moxycart.thumbnail_height'] ;?>px;overflow:hidden;margin-left:5px;">
	                            <img id="thumbnail_preview_static" src="<?php print $data['thumbnail_url']; ?>" />
	                            <img id="thumbnail_preview_dynamic" src="<?php print $data['url']; ?>" style="display:none;"/>
	                        </div>
	                    </div>
	            
	                
					<div class="clearfix" id="image_stuff">
						<span class="btn crop-btn" id="edit-in-modal">Edit Image</span>
						
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
	                    
	                </div>

			      </div>
				      
		      	</form>
	      	
	      	<div class="modal-footer">
			        <button type="button" data-image_id="<?php print $data['image_id']; ?>" class="btn btn-default" id="close-update" data-dismiss="modal">Close</button>
			        <button type="button" id="remove-img-modal" data-image_id="<?php print $data['image_id']; ?>"  data-file="<?php print $data['url']; ?>" class="btn btn-default">Delete</button>
			        <button type="submit" id="update-save" class="btn btn-custom">Save changes</button>
			      </div>
</div>
<form action="/file-upload"
      class="dropzone"
      id="my-awesome-dropzone"></form>

</div>