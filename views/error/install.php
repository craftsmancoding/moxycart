<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading">Moxycart Errors Detected!</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
    <p>The following errors were detected in your local Moxycart settings.</p>
</div>

<div class="moxycart_canvas_inner">

    <div class="danger">
        <ul>
            <?php foreach ($data['errors'] as $e): ?>            
                    <li><?php print $e; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>