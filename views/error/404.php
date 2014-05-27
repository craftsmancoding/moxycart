<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

<h2 class="moxycart_cmp_heading">404 Page Not Found</h2>

<p>Sorry, the page you requested could not be found.</p>

<?php print $this->getMsg(); ?>

<?php if (isset($data['msg'])): ?>
    <div style="border: 1px dotted grey; background:pink; width: 50%;">
        <?php print $data['msg']; ?>
    </div>
<?php endif; ?>

<?php include dirname(dirname(__FILE__)).'/footer.php';  ?>