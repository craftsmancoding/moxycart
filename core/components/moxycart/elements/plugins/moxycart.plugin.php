<?php
/**
 * ProductURLRouting plugin for moxycart extra
 *
 * Copyright 2013 by Everett Griffiths everett@craftsmancoding.com
 * Created on 07-05-2013
 *
 * moxycart is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * moxycart is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * moxycart; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package moxycart
 */

/**
 * Description
 * -----------
 * Handles various things...
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package moxycart
 **/

$cache_dir = 'moxycart';

if ($modx->event->name == 'OnPageNotFound') {
        $core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
        $modx->addPackage('moxycart',$core_path.'components/moxycart/model/','moxy_');

        $uri = substr($_SERVER['REQUEST_URI'], 1);
        $refresh = false; // used if you want to turn off caching (good for testing)
        $Product = $modx->getObject('Product',array('uri'=>$uri)); // ??? how can you tell the requested URI?

        $lifetime = 0;
        $cache_opts = array(xPDO::OPT_CACHE_KEY => $cache_dir); 

        if (!$Product) {
            return;  // it's a real 404
        } 

        //$modx->log(MODX_LOG_LEVEL_ERROR, 'Not Found Product Test: ' . print_r($Product,true));
        $fingerprint = $Product->get('product_id');

        $out = $modx->cacheManager->get($fingerprint, $cache_opts);

        // Cache our custom browser-specific version of the page.
        if ($refresh || empty($out)) {

            // Create our new "fake" resource.  ??? how does this handle TVs? B/c products don't have the same attributes as resources
            $modx->resource = $modx->newObject('modResource');
            $product_attributes = $Product->toArray();
            foreach ($product_attributes as $k => $v) {
                $modx->resource->set($k, $v);    
            }
            // or?
            $modx->resource->set('template', $Product->get('template_id'));    

            // Disable built-in caching, otherwise the process method will return the cached version of the page
            $modx->resource->set('cacheable',false);
            $out = $modx->resource->process();
            $modx->cacheManager->set($fingerprint, $out, $lifetime, $cache_opts);
        }
        echo $out;
        die();
}

if ($modx->event->name == 'OnBeforeCacheUpdate') {
    $dir = MODX_CORE_PATH .'cache/'.$cache_dir;
    $objects = scandir($dir);
    foreach ($objects as $object) {
        if ($object != '.' && $object != '..') {
            if (filetype($dir.'/'.$object) != 'dir') {
                @unlink($dir.'/'.$object);
            } 
        }
    }
    reset($objects);
}