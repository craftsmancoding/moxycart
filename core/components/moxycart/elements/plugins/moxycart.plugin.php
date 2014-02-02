<?php
/**
 * @name Moxycart
 * @description Multi-purpose plugin for Moxycart handling URL routing and manager customizations
 * @PluginEvents OnManagerPageInit,OnPageNotFound,OnBeforeCacheUpdate
 *
 */

$cache_dir = 'moxycart';

switch ($modx->event->name) {

    //------------------------------------------------------------------------------
    //! OnManagerPageInit
    //  Load up custom CSS for the manager
    //------------------------------------------------------------------------------
    case 'OnManagerPageInit':
        $assets_url = $modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        $modx->regClientCSS($assets_url.'components/moxycart/css/moxycart.css');
        break;
        
    //------------------------------------------------------------------------------
    //! OnPageNotFound
    //  Query for our custom product and format it using a MODX template
    //------------------------------------------------------------------------------
    case 'OnPageNotFound':
        // Trim the base url off the front of the request uri
        $uri = preg_replace('/^'.preg_quote(MODX_BASE_URL,'/').'/','', $_SERVER['REQUEST_URI']);
    
        $modx->log(modX::LOG_LEVEL_DEBUG,'[moxycart plugin] URI requested : '.$uri);
        
        $core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
        $placeholder_prefix = $modx->getOption('moxycart.placeholder_prefix');
        $modx->addPackage('moxycart',$core_path.'components/moxycart/model/','moxy_');

        $refresh = true; // used if you want to turn off caching (good for testing)        

        $cache_key = str_replace('/', '_', $uri);
        $cache_opts = array(xPDO::OPT_CACHE_KEY => $cache_dir); 
        $fingerprint = 'product_'.$cache_key;

        $out = $modx->cacheManager->get($fingerprint, $cache_opts);

        // Cache our custom browser-specific version of the page.
        if ($refresh || empty($out)) {

            $Product = $modx->getObjectGraph('Product','{"Specs":{"Spec":{}}}',array('uri'=>$uri));

            if (!$Product) {
                $modx->log(modX::LOG_LEVEL_INFO,'[moxycart plugin] No Product found for uri '.$uri);
                return;  // it's a real 404
            } 

            $product_attributes = $Product->toArray();

            // set date and time (unix)
            $now = strtotime(date('Y-m-d H:i:s'));
            $sale_start = strtotime($product_attributes['sale_start']);
            $sale_end = strtotime($product_attributes['sale_end']);
        
            $lifetime = 3600; // cache 
        
             $calculated_price = $product_attributes['price'];
            // if on sale use price sale
            if($sale_start <= $now && $sale_end >= $now) {
                $calculated_price = $product_attributes['price_sale'];
                $lifetime = $sale_end - $now;
            } 

            if($sale_start >= $now) {
                $lifetime = $sale_start - $now;
            }

            // add calculated_price field
            $product_attributes['calculated_price'] = $calculated_price;            

           foreach ($Product->Specs as $S) {
                $product_attributes[$S->Spec->get('identifier')] = $S->get('value');
            }
            $modx->setPlaceholders($product_attributes,$placeholder_prefix);
            
            if (!$Template = $modx->getObject('modTemplate', $Product->get('template_id'))) {
                print 'No template for product '.$Product->get('product_id');
                exit;
            }
            $tpl = $Template->getContent();
            $uniqid = uniqid();
            $chunk = $modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
            $chunk->setCacheable(false);
            $out = $chunk->process($product_attributes, $tpl);
            $modx->cacheManager->set($fingerprint, $out, $lifetime, $cache_opts);
        }

        // We spin up a resource with the minimal attributes
        $modx->resource = $modx->newObject('modResource');
        $modx->resource->set('contentType', 'text/html');
        $modx->resource->setContent($out);
        if (!$response = $modx->getResponse()) {
            print 'Response did not load.';
            $modx->log(modX::LOG_LEVEL_ERROR,'[moxycart plugin] getResponse failed in moxycart plugin.');
            exit;
        }
        $modx->response->outputContent();
        break;

    //------------------------------------------------------------------------------
    //! OnBeforeCacheUpdate
    //  Clear out our custom cache files.
    //------------------------------------------------------------------------------
    case 'OnBeforeCacheUpdate':
        $dir = MODX_CORE_PATH .'cache/'.$cache_dir;
        if (file_exists($dir) && is_dir($dir)) {
            
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
        break;
}