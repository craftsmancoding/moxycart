<div class="modal-container">
	 <script>
	 $(function(){
	 	alert('test');
	 });
	 </script>
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title" id="myModalLabel">Update Image</h4>
	      </div>
	      <form enctype="multipart/form-data" method="POST" action="#"class="form-horizontal">
		      <div class="modal-body">
					
				 <div class="form-group">
				    <label for="title" class="control-label">Title</label>
				     <input type="text" class="form-control" id="title" value="<?php print $data['title']; ?>">
				 </div>
				 <div class="form-group">
				    <label for="alt" class="control-label">Alt</label>
				     <input type="text" class="form-control" id="alt" value="<?php print $data['alt']; ?>">
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