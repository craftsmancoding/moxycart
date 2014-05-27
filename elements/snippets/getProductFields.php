<?php
/**
 * @name getProductFields
 * @description Returns a list of product_fields
 * 
 * Available Placeholders
 * ---------------------------------------
 * product, spec, value
 * use as [[+spec]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param int $product_id get records for this specific product
 * @param int $limit Limit the result
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
  * Usage
 * ------------------------------------------------------------
 * [[!getProductSpecs? &product_id=`[[+product_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &limit=`0`]]
 * 
 * @package moxycart
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getProductFields',$scriptProperties);


// Formatting Arguments:
$innerTpl = $modx->getOption('innerTpl',$scriptProperties, 'ProductInnerTpl');
$outerTpl = $modx->getOption('outerTpl',$scriptProperties, 'ProductOuterTpl');

// Default Arguments:
// $scriptProperties['is_active'] = $modx->getOption('is_active',$scriptProperties, 1);

// Filter out formatting/control arguments:
unset($scriptProperties['log_level']);
unset($scriptProperties['log_target']);
unset($scriptProperties['innerTpl']);
unset($scriptProperties['outerTpl']);


$F = new \Moxycart\Field($modx);

$results = $F->all($scriptProperties);
return $Snippet->format($results, $innerTpl,$outerTpl);