<?php
/**
 * @name getProductReviews
 * @description Returns a list of product_reviews.
 *
 * 
 * Available Placeholders
 * ---------------------------------------
 * id, product_id, author_id, name, email, rating, content, state
 * use as [[+name]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param string $state retrieve the records with specific state (pending,approved,archived)
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
 * [[!getProductReviews? &product_id=`[[+product_id]]` &state=`approved` &outerTpl=`sometpl` &innerTpl=`othertpl` &limit=`0`]]
 *
 * @package moxycart
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');


$moxySnippet = new Moxycart\Snippet($modx);
$out = $moxySnippet->execute('json_reviews',$scriptProperties);

return $out;