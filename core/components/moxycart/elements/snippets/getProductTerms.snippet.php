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
 * @param string $outerTpl Format the Outer Wrapper of List
 * @param string $innerTpl Format the Inner Item of List
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

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
$class_path = $core_path . 'components/moxycart/model/moxycart/moxycart.snippets.class.php';
require_once($class_path);

$moxySnippet = new MoxycartSnippet($modx);
$out = $moxySnippet->execute('json_product_terms',$scriptProperties);
return $out;