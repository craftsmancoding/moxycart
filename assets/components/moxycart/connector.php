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

// Dammit why can't this be easier... it's hard to find stuff when you're developing
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

// Get parameters sent here from the Grid controls
// The limit and start parameters are configurable,
// but the sort and dir are less so (?)
$args = array();
$args['limit'] = (int) $modx->getOption('limit',$_POST,10);
$args['start'] = (int) $modx->getOption('start',$_POST,0);
$args['sort'] = $modx->getOption('sort',$_POST,'id');
$args['dir'] = $modx->getOption('dir',$_POST,'ASC');

$debug = false;

//$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);

// Being explicit about the path makes this fail.  WTF?
//$modx->getService('moxycart','moxycart', $core_path.'components/moxycart/model/');
// Yet this works.
if (!$modx->getService('moxycart')) {
   die('Unable to load moxycart service.'); 
}

$function = $modx->getOption('f',$_GET,'help');

// Use the log for debugging Ajax
if ($debug) {
    $args['function'] = $function;
    $modx->log(MODX_LOG_LEVEL_ERROR,print_r($args,true));
}

// Blacklist any functions whose name begins with "_" (underscore)
if (substr($function, 1, 1) == '_') {
    die('Function not allowed.');
}

$results = $modx->moxycart->$function($args);

if ($results===false) {
    header('HTTP/1.0 401 Unauthorized');
    print 'Operation not allowed.';
    exit;
}

print $results;

/*
$criteria = $modx->newQuery('modResource');
//$criteria->where();
$total_pages = $modx->getCount('modResource',$criteria);

$criteria->limit($limit, $start); 
$criteria->sortby($sort,$dir);
$pages = $modx->getCollection('modResource',$criteria);

// Init our array
$data = array(
    'results'=>array(),
    'total' => $total_pages,
);
foreach ($pages as $p) {
    $data['results'][] = $p->toArray();
}
// Use the log for debugging Ajax
$modx->log(1,print_r($_POST,true));
print json_encode($data);
*/

/*EOF*/