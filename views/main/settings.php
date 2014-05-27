<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

<h2 class="moxycart_cmp_heading">Moxycart Settings</h2>

<p>MODX Settings are hierarchical: they can be overridden by Content Settings.</p>
    
    <?php
    $settings = array(
            'moxycart.domain',
            'moxycart.upload_dir',
            'moxycart.api_key',
            'moxycart.enable_reviews',
            'moxycart.auto_approve_reviews',
            'moxycart.enable_variations',
            'moxycart.thumbnail_width',
            'moxycart.thumbnail_height',
            'moxycart.thumbnail_dir',
            'moxycart.categories',
            'moxycart.results_per_page',
            'moxycart.product_columns',
    );
    ?>

    <?php foreach($settings as $s): ?>

        <h3><?php print $s; ?></h3>
        Value: <?php print $this->modx->getOption($s); ?><br/><br/>

    <?php endforeach; ?>

    <a class="button" href="<?php print MODX_MANAGER_URL; ?>?a=70">See all Settings</a>

<?php include dirname(dirname(__FILE__)).'/footer.php'; ?>