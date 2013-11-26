<div id="modal-container">
		<script>
			$(function(){
      
				$('#update-img-msg').hide();
				$('#image_update_form').on('submit',function(e){
		           	var values = $(this).serialize();
		           	var image_id = $('#image_id').val();
					/*var fileInput = document.getElementById('file');
					var file = fileInput.files[0];
					var formData = new FormData();
					formData.append('file', file);*/
		           	//console.log(connector_url+"image_save&action=update&image_id="+image_id);
				    $.post( connector_url+"image_save&action=update&image_id="+image_id, values, function( data ){
				    	//console.log(data);
				    	data = $.parseJSON(data);

				    	if(data.success == true) {
				    		$('#update-img-msg').addClass('alert-success').html(data.msg).show();
				    	} else{
				    		$('#update-img-msg').addClass('alert-danger').html(data.msg).show();
				    	}
				    	$("#update-img-msg").delay(3200).fadeOut(300);
				    } );
				    e.preventDefault();
			    });
			})
		</script>
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title" id="myModalLabel">Update Image</h4>
	      </div>
	      <form id="image_update_form" enctype="multipart/form-data" method="POST" action="#" class="form-horizontal">
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

				  <div class="form-group">
				    <label for="file" class="control-label">&nbsp;</label>
					 <input type="file" id="file" name="file">
				 </div>

				<div class="form-group">
					<label for="is_active" class="control-label">Is Active</label>
				     <input type="checkbox" class="form-control" id="is_active" <?php if($data['is_active'] == 1){ print 'checked'; } ?>>
				 </div>
				

		      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        <input type="submit" class="btn btn-custom" name="submit" value="Save changes">
			      </div>
	      	</form>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
</div>