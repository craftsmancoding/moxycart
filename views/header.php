<?php
/*
There are 2 types of links:
    href: self::page('pagename');  -- will force a page reload (can be slooooow), but you can bookmark it
    paint: javascript:paint('pagename') -- ajax refresh. Much faster, but you cannot bookmark it.

Valid pagename can be any function in the PageController (minus the get/post prefix)
*/
?>
<div id="moxycart_header" class="clearfix">
    <ul id="moxycart_nav">
        <li class="moxycart_nav_item moxycart_nav_home">
            <!--span class="linklike" onclick="javascript:paint('index');">Home</span-->
            <a href="<?php print self::page('index'); ?>">Home</a>
        </li>
        <li class="moxycart_nav_item">
            <!--span class="linklike" onclick="javascript:paint('products');">Products</span-->
            <a href="<?php print self::page('products'); ?>">Products</a>
        
        </li>
        <li class="moxycart_nav_item">
            <!--span class="linklike" onclick="javascript:paint('assets');">Assets</span-->
            <a href="<?php print self::page('assets'); ?>">Assets</a>
        </li>
        <li class="moxycart_nav_item">
            <!--span class="linklike" onclick="javascript:paint('fields');">Custom Fields</span-->
            <a href="<?php print self::page('fields'); ?>">Custom Fields</a>
        </li>
        <li class="moxycart_nav_item">
            <!--span class="linklike" onclick="javascript:paint('options');">Options</span-->
            <a href="<?php print self::page('options'); ?>">Options</a>
        </li>
        <li class="moxycart_nav_item">
            <!--span class="linklike" onclick="javascript:paint('reviews');">Reviews</span-->
            <a href="<?php print self::page('reviews'); ?>">Reviews</a>
        </li>
        <li class="moxycart_nav_item">
            <!--span class="linklike" onclick="javascript:paint('reports');">Reports</span-->
            <a href="<?php print self::page('reports'); ?>">Reports</a>
        </li>
        <li class="moxycart_nav_item">
            <!--span class="linklike" onclick="javascript:paint('settings');">Settings</span-->
            <a href="<?php print self::page('settings'); ?>">Settings</a>
        </li>
    </ul>
</div>

<div id="moxycart_msg"></div>

<div id="moxycart_canvas">


    
