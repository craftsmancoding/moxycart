<?php
/**
 * @name Moxycart
 * @description Multi-purpose plugin for Moxycart handling URL routing and manager customizations
 * @PluginEvents OnManagerPageInit,OnPageNotFound,OnBeforeCacheUpdate,OnDocFormSave
 */
 
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
include_once $core_path .'vendor/autoload.php';
$cache_dir = 'moxycart';

switch ($modx->event->name) {

    //------------------------------------------------------------------------------
    //! OnManagerPageInit
    //  Load up custom CSS for the manager
    //------------------------------------------------------------------------------
    case 'OnManagerPageInit':
        $assets_url = $modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');
        $modx->log(modX::LOG_LEVEL_DEBUG,'Registering '.$assets_url.'css/moxycart.tree.css','','moxycart Plugin:OnManagerPageInit');
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
//        print_r($modx->request->parameters['GET']); exit;
        $P = new \Moxycart\Product($modx);
        $refresh = !$modx->getOption('moxycart.product_cache');
        if (!$product_attributes = $P->request($uri,$refresh,$cache_dir)){
            $modx->log(modX::LOG_LEVEL_INFO,'[moxycart plugin] No Product found for uri '.$uri);
            return;
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
    //------------------------------------------------------------------------------
    //! OnDocFormSave
    //------------------------------------------------------------------------------
    case 'OnDocFormSave':
        if ('Store' == $resource->get('class_key')) {
            if ($storesettings = $resource->get('StoreSettings')) {
                $modx->log(modX::LOG_LEVEL_DEBUG,print_r($storesettings,true),'','Moxycart Plugin:OnDocFormSave');
                //$props = $resource->getProperties('moxycart');
                //$props['moxycart'] = $storesettings;
                $resource->setProperties($storesettings,'moxycart',true);
                $resource->save();
            }
        }
        break;
}        
