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

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
$class_path = $core_path . 'components/moxycart/model/moxycart/moxycart.snippets.class.php';
require_once($class_path);
$moxySnippet = new MoxycartSnippet($modx);
$out = $moxySnippet->get_rate_average('json_reviews',$scriptProperties);
return $out;