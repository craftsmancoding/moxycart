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
 * @param string $outerTpl Format the Outer Wrapper of List
 * @param string $innerTpl Format the Inner Item of List
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package moxycart
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
$class_path = $core_path . 'components/moxycart/model/moxycart/moxycart.snippets.class.php';
require_once($class_path);

$moxySnippet = new MoxycartSnippet($modx);
$out = $moxySnippet->execute('json_product_specs',$scriptProperties);
return $out;

