
<div class="x-panel-bwrap">
    <div class="x-panel-body x-panel-body-noheader x-panel-body-noborder" >
    <div class=" x-panel container x-panel-noborder">
    <div class="x-panel-bwrap">


    <div id="moxycart_header" class="clearfix">
    <ul id="moxycart_nav">



    </div>

    <div id="moxycart_msg"></div>

    <div id="moxycart_canvas">

<?php
//include dirname(dirname(__FILE__)).'/header.php';
?>


<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Moxycart Errors Detected!</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
    <p>The following errors were detected in your local Moxycart settings:</p>
</div>

<div class="moxycart_canvas_inner">

    <div class="danger">
        <ul>
            <?php foreach ($data['errors'] as $e): ?>            
                    <li><?php print $e; ?></li>
            <?php endforeach; ?>
        </ul>    
        <hr/>
        <p>You must <a href="<?php print MODX_MANAGER_URL . '?a=system/settings'; ?>">edit your System Settings</a>.</p>
    </div>
    
    
    
</div>
<?php
//include dirname(dirname(__FILE__)).'/footer.php';
?>


    </div><!-- /moxycart_canvas -->



    </div>
    </div>
    </div>
</div>