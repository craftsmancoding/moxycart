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

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');


$scriptProperties['innerTpl'] = $modx->getOption('innerTpl',$scriptProperties, 'ProductInnerTpl');

$moxySnippet = new Moxycart\Snippet($modx);
$out = $moxySnippet->execute('json_products',$scriptProperties);
return $out;