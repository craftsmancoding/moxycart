<?php include dirname(dirname(__FILE__)).'/header.php';  ?>

<div class="moxycart_canvas_inner">
	<h2 class="moxycart_cmp_heading">Welcome to Moxycart!</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>We're glad you're here.</p></div>

<div class="moxycart_canvas_inner">

<div id="moxycart_buttons">
<ul>
    <li class="assets">
        <a href="<?php print self::url('asset','index'); ?>" title="Manage Assets">
            <span class="icon"></span>
            <span class="headline">Manage Assets</span>
            <span class="subline">Create/Manage Assets</span>
        </a>
    </li>
    <li class="fields">
        <a href="<?php print self::url('field','index'); ?>" title="Manage Custom Fields">
            <span class="icon"></span>
            <span class="headline">Custom Fields</span>
            <span class="subline">Manage Custom Fields</span>
        </a>
    </li>
    <li class="product">
        <a href="<?php print self::url('optiontype','index'); ?>" title="Manage Product Options">
            <span class="icon"></span>
            <span class="headline">Product Options</span>
            <span class="subline">Manage Product Options</span>
        </a>
    </li>

     <li class="reviews">
        <a href="<?php print self::url('review','index'); ?>" title="Manage Product Options">
            <span class="icon"></span>
            <span class="headline">Reviews</span>
            <span class="subline">Manage Reviews</span>
        </a>
    </li>

     <li class="setting">
        <a href="<?php print self::url('main','settings'); ?>" title="Manage Product Options">
            <span class="icon"></span>
            <span class="headline">Settings</span>
            <span class="subline">Manage Settings</span>
        </a>
    </li>
</ul>
</div>


<?php include dirname(dirname(__FILE__)).'/footer.php'; ?>