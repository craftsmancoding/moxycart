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
$core_path = $modx->getOption('moxycart.core_path','',MODX_CORE_PATH);
require_once($core_path.'components/moxycart/model/moxycart/moxycart.class.php');

$Moxycart = new Moxycart($modx);

$old_level = $modx->setLogLevel($log_level);

$args = array_merge($_POST,$_GET); // skip the cookies, more explicit than $_REQUEST

$function = $modx->getOption('f',$_GET,'help');

$results = $Moxycart->$function($args);

// It doesn't work to try and disable smarty:
// $modx->smarty->assign('maincssjs','');
// $this->registerBaseScripts(false);
// Nor this:
//foreach ($this->placeholders as $k => $v) {
//    $this->modx->smarty->assign($k,'');
//}
// The result?  The MODX manager URLs seem to ALWAYS include the base scripts/html. Boo.

$modx->setLogLevel($old_level);
return $results;
/*EOF*/