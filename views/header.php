<?php
/*
There are 2 types of links:
    href: self::page('pagename');  -- will force a page reload (can be slooooow), but you can bookmark it
    paint: javascript:paint('pagename') -- ajax refresh. Much faster, but you cannot bookmark it.

Valid pagename can be any function in the PageController (minus the get/post prefix)
*/
?>

<div class="x-panel-bwrap">
    <div class="x-panel-body x-panel-body-noheader x-panel-body-noborder" >
        <div class=" x-panel container x-panel-noborder">
            <div class="x-panel-bwrap">


                    <div id="moxycart_header" class="clearfix">
                        <ul id="moxycart_nav">
          
                            <li class="moxycart_nav_item">
                                <!--span class="linklike" onclick="javascript:paint('index');">Home</span-->
                                <a class="<?php print ($_GET['method'] == 'index') ? 'current' : '' ; ?>" href="<?php print self::page('index'); ?>">Manage</a>
                            </li>
                            <li class="moxycart_nav_item">
                                <!--span class="linklike" onclick="javascript:paint('products');">Products</span-->
                                <a class="<?php print ($_GET['method'] == 'products') ? 'current' : '' ; ?>" href="<?php print self::page('products'); ?>">Products</a>
                            
                            </li>
                            <li class="moxycart_nav_item">
                                <!--span class="linklike" onclick="javascript:paint('fields');">Custom Fields</span-->
                                <a class="<?php print ($_GET['method'] == 'fields') ? 'current' : '' ; ?>" href="<?php print self::page('fields'); ?>">Custom Fields</a>
                            </li>
                            <li class="moxycart_nav_item">
                                <!--span class="linklike" onclick="javascript:paint('options');">Options</span-->
                                <a class="<?php print ($_GET['method'] == 'options') ? 'current' : '' ; ?>" href="<?php print self::page('options'); ?>">Options</a>
                            </li>
                            <li class="moxycart_nav_item">
                                <!--span class="linklike" onclick="javascript:paint('reviews');">Reviews</span-->
                                <a class="<?php print ($_GET['method'] == 'reviews') ? 'current' : '' ; ?>" href="<?php print self::page('reviews'); ?>">Reviews</a>
                            </li>
                            <li class="moxycart_nav_item">
                                <!--span class="linklike" onclick="javascript:paint('orders');">Reports</span-->
                                <a class="<?php print ($_GET['method'] == 'orders') ? 'current' : '' ; ?>" href="<?php print self::page('orders'); ?>">Orders</a>
                            </li>
                            <li class="moxycart_nav_item">
                                <!--span class="linklike" onclick="javascript:paint('settings');">Settings</span-->
                                <a class="<?php print ($_GET['method'] == 'settings') ? 'current' : '' ; ?>" href="<?php print self::page('settings'); ?>">Settings</a>
                            </li>
                        </ul>
                    </div>

                    <div id="moxycart_msg"></div>

                    <div id="moxycart_canvas">


    
