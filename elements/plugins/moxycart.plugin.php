<?php
/**
 * @name Moxycart
 * @description Multi-purpose plugin for Moxycart handling URL routing and manager customizations
 * @PluginEvents OnManagerPageInit,OnPageNotFound,OnBeforeCacheUpdate
 *
 */
 
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
include_once $core_path .'vendor/autoload.php';
$cache_dir = 'moxycart';
//$modx->setLogLevel(4);
$modx->log(modX::LOG_LEVEL_DEBUG,'Bang: '.$modx->event->name);
switch ($modx->event->name) {

    //------------------------------------------------------------------------------
    //! OnManagerPageInit
    //  Load up custom CSS for the manager
    //------------------------------------------------------------------------------
    case 'OnManagerPageInit':
        $assets_url = $modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');
        $modx->log(modX::LOG_LEVEL_DEBUG,'Registering '.$assets_url.'css/moxycart.css','','moxycart Plugin:OnManagerPageInit');
        $modx->regClientCSS($assets_url.'css/moxycart.tree.css');
        break;
        
    //------------------------------------------------------------------------------
    //! OnPageNotFound
    //  Query for our custom product and format it using a MODX template
    //------------------------------------------------------------------------------
    case 'OnPageNotFound':

        // Trim the base url off the front of the request uri
        $uri = preg_replace('/^'.preg_quote(MODX_BASE_URL,'/').'/','', $_SERVER['REQUEST_URI']);
    
        $modx->log(modX::LOG_LEVEL_DEBUG,'[moxycart plugin] URI requested : '.$uri,'','moxycart Plugin:OnPageNotFound');
        
        $modx->addPackage('moxycart',$core_path.'model/orm/','moxy_');
        
        
        $refresh = !$modx->getOption('moxycart.product_cache'); // used if you want to turn off caching (good for testing)        

        $cache_opts = array(xPDO::OPT_CACHE_KEY => $cache_dir); 
        $fingerprint = 'product/'.$uri;

        $product_attributes = $modx->cacheManager->get($fingerprint, $cache_opts);

        // Cache our custom browser-specific version of the page.
        if ($refresh || empty($product_attributes)) {
            $modx->log(modX::LOG_LEVEL_DEBUG,'[moxycart plugin] Refresh requested or no cached data detected.','','moxycart Plugin refresh');
            
            
            $Product = $modx->getObjectGraph('Product','{"Fields":{"Field":{}}}',array('uri'=>$uri));

            if (!$Product) {
                $modx->log(modX::LOG_LEVEL_INFO,'[moxycart plugin] No Product found for uri '.$uri);
                return;  // it's a real 404
            } 

            $product_attributes = $Product->toArray();

            foreach ($Product->Fields as $F) {
                $product_attributes[$F->Field->get('slug')] = $F->get('value');
            }
          
            
            if (!$Template = $modx->getObject('modTemplate', $Product->get('template_id'))) {
                print 'No template for product '.$Product->get('product_id');
                exit;
            }

            $lifetime = $Product->get_lifetime();
            $modx->log(modX::LOG_LEVEL_DEBUG,'[moxycart plugin] Product cache liftetime = '.$lifetime);
            $modx->cacheManager->set($fingerprint, $product_attributes, $lifetime, $cache_opts);
            
        }
        // We spin up a resource with the minimal attributes
        $modx->setPlaceholders($product_attributes);
        $modx->resource = $modx->newObject('modResource');
        $modx->resource->set('contentType', 'text/html');
        $modx->resource->set('template' , $product_attributes['template_id']);
        $modx->resource->set('pagetitle', $product_attributes['title']);
        $modx->resource->set('description', $product_attributes['description']);        
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
        $modx->log(modX::LOG_LEVEL_DEBUG,'[moxycart plugin]','OnBeforeCacheUpdate');
        $modx->cacheManager->clean(array(xPDO::OPT_CACHE_KEY => $cache_dir));
        break;
}