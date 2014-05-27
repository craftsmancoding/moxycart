<?php
/**
 * @name getProductFields
 * @description Returns a list of product_fields
 * 
 * Available Placeholders
 * ---------------------------------------
 * product, field, value
 * use as [[+field]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param int $product_id get records for this specific product
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
  * Usage
 * ------------------------------------------------------------
 * [[!getProductFields? &product_id=`[[+product_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl`]]
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

$criteria = $modx->newQuery('ProductField');
        
if ($product_id) {
    $criteria->where(array('product_id'=>$product_id));
}

$results = $modx->getCollectionGraph('ProductField','{"Field":{},"Product":{}}',$criteria);

return $Snippet->format($results, $innerTpl,$outerTpl);