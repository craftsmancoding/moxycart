<?php
/**
 * @name getProductReviewsRating
 * @description Returns a Product Reviews Average Rate
 * 
 * Parameters
 * -----------------------------
 * @param int $product_id get records for this specific product
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
   * Usage
 * ------------------------------------------------------------
 * [[!getProductReviewsRating? &product_id=`[[+product_id]]`]]
 * 
 * @return decimal product review ratign average
 * 
 * @package moxycart
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getProducts',$scriptProperties);

/*
$moxySnippet = new Moxycart\Snippet($modx);
$out = $moxySnippet->get_rate_average('json_reviews',$scriptProperties);
return $out;
*/