<label for="thumbnail">Thumbnail</label>

<div id="thumbnail" style="border:1px dotted grey;width:240px;height:180px;" onclick="javascript:jQuery('#thumbnail_form').dialog('open');">
    <input type="hidden" name="asset_id" id="asset_id" value=""/>
    <img id="thumbnail_img" 
        src="<?php print $data['thumbnail_url']; ?>" 
        width="<?php print $this->modx->getOption('moxycart.thumbnail_width'); ?>" 
        height="<?php print $this->modx->getOption('moxycart.thumbnail_height'); ?>"/>
</div>
<?php /* ======== MODAL DIALOG BOX ======*/ ?>
<div id="thumbnail_form" title="Select Product Thumbnail">
    <div class="asset_thumbnail_container">
        <?php foreach ($data['product_assets'] as $a): 
                if (empty($a->Asset)) continue;
        ?>
            <div class="asset_thumbnail_item">
	            <img src="<?php print $a->Asset->get('thumbnail_url'); ?>" 
	                alt="<?php print $a->Asset->get('alt'); ?>" 
	                width="<?php print $this->modx->getOption('moxycart.thumbnail_width'); ?>" 
	                height="<?php print $this->modx->getOption('moxycart.thumbnail_height'); ?>"
	                onclick="javascript:select_thumb(<?php print $a->get('asset_id'); ?>,'<?php print htmlentities($a->Asset->get('thumbnail_url')); ?>');"/>
            </div>
        <?php endforeach?>
    </div>
    
</div>