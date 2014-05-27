<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

<h2 class="moxycart_cmp_heading">Welcome to Moxycart!</h2>

<p>We're glad you're here.</p>

<ul>
    <li><a href="<?php print self::url('asset','index'); ?>">Manage Assets</a></li>
    <li><a href="<?php print self::url('field','index'); ?>">Manage Custom Fields</a></li>
    <li><a href="<?php print self::url('optiontype','index'); ?>">Manage Product Options</a></li>
    <li><a href="<?php print self::url('review','index'); ?>">Manage Reviews</a></li>
    <li><a href="<?php print self::url('main','settings'); ?>">Manage Settings</a></li>
</ul>

<?php include dirname(dirname(__FILE__)).'/footer.php'; ?>