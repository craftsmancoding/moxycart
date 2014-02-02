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

        $cache_opts = array(xPDO::OPT_CACHE_KEY => $cache_dir); 
        $fingerprint = 'product/'.$uri;

        $out = $modx->cacheManager->get($fingerprint, $cache_opts);

        // Cache our custom browser-specific version of the page.
        if ($refresh || empty($out)) {
            $modx->log(modX::LOG_LEVEL_DEBUG,'[moxycart plugin] Refresh requested or no cached data detected.');
            
            $Product = $modx->getObjectGraph('Product','{"Specs":{"Spec":{}}}',array('uri'=>$uri));

            if (!$Product) {
                $modx->log(modX::LOG_LEVEL_INFO,'[moxycart plugin] No Product found for uri '.$uri);
                return;  // it's a real 404
            } 

            $product_attributes = $Product->toArray();

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
            $lifetime = $Product->get_lifetime();
            $modx->log(modX::LOG_LEVEL_DEBUG,'[moxycart plugin] Product cache liftetime = '.$lifetime);
            $modx->cacheManager->set($fingerprint, $out, $lifetime, $cache_opts);
        }

        // We spin up a resource with the minimal attributes
        $modx->resource = $modx->newObject('modResource');
        $modx->resource->set('contentType', 'text/html');
        $modx->resource->setContent($out);
        if (!$response = $modx->getResponse()) {
            $modx->log(modX::LOG_LEVEL_ERROR,'[moxycart plugin] getResponse failed in moxycart plugin.');
            return; // fall back to regular 404 behavior?
        }
        $modx->response->outputContent();
        break;

    //------------------------------------------------------------------------------
    //! OnBeforeCacheUpdate
    //  Clear out our custom cache files.
    //------------------------------------------------------------------------------
    case 'OnBeforeCacheUpdate':
        print 'asfdasdf'; exit;
//        $modx->log(1, '[moxycart plugin] clearing cache...');
        $modx->cacheManager->clean(array(xPDO::OPT_CACHE_KEY => $cache_dir));
error_log('ha...');
        $dir = MODX_CORE_PATH .'cache/'.$cache_dir;
        if (file_exists($dir) && is_dir($dir)) {
            $modx->log(1, '[moxycart plugin] clearing '.$dir);    
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir.'/'.$object) != 'dir') {
                        unlink($dir.'/'.$object);
                    } 
                }
            }
            reset($objects);
        }        
        break;
}