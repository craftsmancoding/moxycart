<?php
/** 
 * This file handles Ajax requests made by Moxycart.  It provides stores of data
 * for controllers in the manager.  This file is normally accessed via post,
 * but it can also be accessed directly for debugging purposes, e.g. 
 * http://yoursite.com/assets/components/moxycart/connector.php
 *
 * PARAMETERS
 *
 *  @param integer limit -- limit the number of results returned.
 *  @param integer start -- offset for query, used for pagination
 *  @param string sort -- column name to be used for default sorting
 *  @param string dir (ASC|DESC) -- sort direction
 *  @param string 
 */
if (php_sapi_name() !== 'cli') {
    die('CLI access only.');
}

// It's hard to find stuff when you're developing
// We climb up the dir structure looking for config.core.php...
$docroot = dirname(dirname(dirname(dirname(__FILE__))));
while (!file_exists($docroot.'/config.core.php')) {
    if ($docroot == '/') {
        die('Failed to locate config.core.php');
    }
    $docroot = dirname($docroot);
}
if (!file_exists($docroot.'/config.core.php')) {
    die('Failed to locate config.core.php');
}

include_once $docroot . '/config.core.php';

if (!defined('MODX_API_MODE')) {
    define('MODX_API_MODE', false);
}

include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
 
$modx = new modX();
$modx->initialize('mgr');

//require_once $core_path.'components/moxycart/model/moxycart/moxycart.class.php';
//$Moxycart = new Moxycart($modx);


$P = $modx->newObject('Product');

$P->set('name', 'test');
$P->set('sku', 'X');
$P->set('alias', 'x');

$P->set('in_menu', 0);

$P->save();
/*
$many = array();
$many[0] = $modx->newObject('ProductSpec');
$many[0]->set('value', 1234);
$S = $modx->getObject('Spec', 1);
$many[0]->addOne($S);

$many[1] = $modx->newObject('ProductSpec');
$many[1]->set('value', 5678);
$S = $modx->getObject('Spec', 2);
$many[1]->addOne($S);

$P->addMany($many);

$P->save();
*/



/*EOF*/