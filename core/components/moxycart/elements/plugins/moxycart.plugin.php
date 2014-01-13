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
        $modx->regClientCSS($assets_url.'components/moxycart/css/mgr.css');
        break;
        
    //------------------------------------------------------------------------------
    //! OnPageNotFound
    //  Query for our custom product and format it using a MODX template
    //------------------------------------------------------------------------------
    case 'OnPageNotFound':

        $core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
        $placeholder_prefix = $modx->getOption('moxycart.placeholder_prefix');
        $modx->addPackage('moxycart',$core_path.'components/moxycart/model/','moxy_');

        $refresh = true; // used if you want to turn off caching (good for testing)
        
        $request = explode('/', $_SERVER['REQUEST_URI']);
        $uris = array_slice($request, -2, 2, true);
        $uri = implode('/', $uris);

        $cache_key = str_replace('/', '_', $uri);
        $cache_opts = array(xPDO::OPT_CACHE_KEY => $cache_dir); 
        $fingerprint = 'product_'.$cache_key;


        $out = $modx->cacheManager->get($fingerprint, $cache_opts);


        // Cache our custom browser-specific version of the page.
        if ($refresh || empty($out)) {

            
            $Product = $modx->getObjectGraph('Product','{"Specs":{"Spec":{}}}',array('uri'=>$uri)); // ??? how can you tell the requested URI?
            if (!$Product) {
                return;  // it's a real 404
            } 
             // Create our new "fake" resource.  ??? how does this handle TVs? B/c products don't have the same attributes as resources
            $modx->resource = $modx->newObject('modResource');
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
            

            
          /*  $modx->log(MODX_LOG_LEVEL_ERROR, 'Sale Start ' . strtotime($product_attributes['sale_start']));
            $modx->log(MODX_LOG_LEVEL_ERROR, 'Sale End ' .  strtotime($product_attributes['sale_end']));
            $modx->log(MODX_LOG_LEVEL_ERROR, 'Today ' .  $now);*/

            // add calculated_price field
            $product_attributes['calculated_price'] = $calculated_price;            

            $criteria = $modx->newQuery('Spec');
            $Specs = $modx->getCollection('Spec',$criteria);
           
           foreach ($Specs as $spec) {
                $value = '';
                foreach ($Product->Specs as $ps) {
                    if($spec->get('spec_id') == $ps->get('spec_id')) {
                        $value =  $ps->get('value');
                    }
                }
                $spec_name = str_replace(' ', '_', strtolower($spec->get('name')));
                $product_attributes[$spec_name] = $value;
            }

            $modx->setPlaceholders($product_attributes,$placeholder_prefix);

            // or?
            $modx->resource->set('template', $Product->get('template_id'));    

            // Disable built-in caching, otherwise the process method will return the cached version of the page
            $modx->resource->set('cacheable',false);
            $out = $modx->resource->process();
            $modx->cacheManager->set($fingerprint, $out, $lifetime, $cache_opts);
        }
        print $out;
        die();
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