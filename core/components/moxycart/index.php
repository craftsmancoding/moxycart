<?php
/** 
 * This file handles Ajax requests made by Moxycart.  It provides stores of data
 * for controllers in the manager.  This file is normally accessed via post,
 * but it can also be accessed directly for debugging purposes, e.g. 
 * http://yoursite.com/assets/components/moxycart/connector.php
 *
 * PARAMETERS
 *  @param string f function name inside of moxycart.class.php where request gets routed
 *      default: help
 */
if (!$modx->getService('moxycart')) {
    $modx->log(MODX_LOG_LEVEL_ERROR, 'Unable to load MoxyCart service.','','',__FILE__,__LINE__);
    die('Unable to load moxycart service.'); 
}
$log_level = $modx->getOption('log_level',$_GET, $modx->getOption('log_level'));
$old_level = $modx->setLogLevel($log_level);

$args = array_merge($_POST,$_GET); // skip the cookies, more explicit than $_REQUEST

$function = $modx->getOption('f',$_GET,'help');

$results = $modx->moxycart->$function($args);
$modx->setLogLevel($old_level);
return $results;
/*EOF*/