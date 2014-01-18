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

$log_level = $modx->getOption('log_level',$_GET, $modx->getOption('log_level'));
$old_level = $modx->setLogLevel($log_level);

$function = $modx->getOption('f',$_GET,'help');
unset($_GET['f']);
$args = array_merge($_POST,$_GET); // skip the cookies, more explicit than $_REQUEST
$modx->log(MODX_LOG_LEVEL_DEBUG, print_r($args,true),'','',__FILE__,__LINE__);


$core_path = $modx->getOption('moxycart.core_path','',MODX_CORE_PATH);
require_once($core_path.'components/moxycart/controllers/moxycartcontroller.class.php');

$Moxycart = new MoxycartController($modx);



$results = $Moxycart->$function($args);

if ($results===false) {
    header('HTTP/1.0 401 Unauthorized');
    print 'Operation not allowed.';
    exit;
}

$modx->setLogLevel($old_level);
print $results;
exit;
/*EOF*/