<?php
/**
 * @name getProductImages
 * @description Returns a list of product_images.
 *
 * 
 * Available Placeholders
 * ---------------------------------------
 * images_id, product_id, title, alt, url, path, width, height, seq, is_active
 * use as [[+url]] on Template Parameters
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
 * To get all Images on certain product
 * [[!getProductImages? &product_id=`[[+product_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &firstCLass=`first` &is_active=`1` &limit=`0`]]
 * [[!getProductImages? &product_id=`[[+product_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &is_active=`1` &limit=`1`]]
 *
 * @package moxycart
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getProductImages',$scriptProperties);

return;
/*
$scriptProperties['innerTpl'] = $modx->getOption('innerTpl',$scriptProperties, 'ProductImage');

$moxySnippet = new MoxycartSnippet($modx);
$out = $moxySnippet->execute('json_images',$scriptProperties);
return $out;
*/