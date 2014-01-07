<?php
/**
 * @name getProducts
 * @description Returns a list of products.
 *
 * 
 * Available Placeholders
 * ---------------------------------------
 * id, name, sku, type, qty_inventory, qty_alert, price, category, uri, is_active
 * use as [[+name]] on Template Parameters
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
require_once $core_path . 'components/moxycart/model/moxycart/moxycart.snippets.class.php';

$moxySnippet = new MoxycartSnippet($modx);
$out = $moxySnippet->execute('json_products',$scriptProperties);
return $out;