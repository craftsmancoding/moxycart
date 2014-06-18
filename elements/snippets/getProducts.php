<?php
/**
 * @name getProducts
 * @description Returns a list of products.
 *
 * 
 * Available Placeholders
 * ---------------------------------------
 * product_id,alias,content,name,sku,type,track_inventory,qty_inventory,qty_alert,price,category,uri,is_active,seq,calculated_price,calculated_price,
 * use as [[+name]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param boolean $is_active Get all active records only
 * @param integer $log_level 4 = debug. Defaults to system setting
 * @param mixed $log_target Defaults to system setting.
 * @param int $limit Limit the records to be shown (if set to 0, all records will be pulled)
 * @param int $firstClass set class name on the first item (Optional)
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * Usage
 * ------------------------------------------------------------
 * [[!getProducts? &outerTpl=`sometpl` &innerTpl=`othertpl` &limit=`0`]]
 *
 * @package moxycart
 **/
// Call your snippet like this: [[mySnippet? &log_level=`4`]]
// Override global log_level value

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getProducts',$scriptProperties);

// Formatting Arguments:
$innerTpl = $modx->getOption('innerTpl',$scriptProperties, 'ProductInnerTpl');
$outerTpl = $modx->getOption('outerTpl',$scriptProperties, 'ProductOuterTpl');

// Default Arguments:
$scriptProperties['is_active'] = $modx->getOption('is_active',$scriptProperties, 1);

// Filter out formatting/control arguments:
unset($scriptProperties['log_level']);
unset($scriptProperties['log_target']);
unset($scriptProperties['innerTpl']);
unset($scriptProperties['outerTpl']);

$P = new \Moxycart\Product($modx);


if ($results = $P->all($scriptProperties)) {
    return $Snippet->format($results,$innerTpl,$outerTpl);    
}

$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','getProducts',__LINE__);