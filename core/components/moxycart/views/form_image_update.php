<div class="form-update-wrapper">
<div class="image-nav">
	      	<a href="#" class="image-nav prev" data-seq="<?php print $data['seq'] ?>" data-nav_dir="prev" >Previous</a>
	      	<a href="#" data-seq="<?php print $data['seq'] ?>" data-nav_dir="next" class="image-nav next">Next</a>
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
					<span class="btn crop-btn" onclick="javascript:crop(); return false;">Crop</span>
					
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
</div>