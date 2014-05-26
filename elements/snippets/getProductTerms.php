<?php
/**
 * @name getProductTerms
 * @description Returns a list of product_terms.
 * 
 * Available Placeholders
 * ---------------------------------------
 * id, product_id, term_id,term,properties
 * use as [[+term]] on Template Parameters
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
 * [[!getProductTerms? &product_id=`[[+product_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &limit=`0`]]
 *
 * @package moxycart
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getProductTerms',$scriptProperties);

/*
$scriptProperties['innerTpl'] = $modx->getOption('innerTpl',$scriptProperties, 'ProductTerm');

$moxySnippet = new Moxycart\Snippet($modx);
$out = $moxySnippet->execute('json_product_terms',$scriptProperties);
return $out;
*/