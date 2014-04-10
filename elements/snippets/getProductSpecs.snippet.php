<?php
/**
 * @name getProductSpecs
 * @description Returns a list of product_specs
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


$scriptProperties['innerTpl'] = $modx->getOption('innerTpl',$scriptProperties, 'ProductSpec');

$moxySnippet = new Moxycart\Snippet($modx);
$out = $moxySnippet->execute('json_product_specs',$scriptProperties);
return $out;