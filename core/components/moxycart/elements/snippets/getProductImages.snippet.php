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
$out = $moxySnippet->execute('json_images',$scriptProperties);
return $out;

