<?php
/* Reused by Ajax request when image is updated 
uniqid to bust cache.
*/
?>
<?php print ($data['wide_load'])? '<div class="warning">'.$data['wide_load'].'</div>': ''; ?>
 <img src="<?php print $data['url']; ?>?rand=<?php print uniqid(); ?>" 
height="<?php print $data['visible_height']; ?>" 
width="<?php print $data['visible_width']; ?>" id="jcrop_target"/>