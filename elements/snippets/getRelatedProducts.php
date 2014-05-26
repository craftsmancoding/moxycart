<?php
/**
 * @name getRelatedProducts
 * @description Shows products related to the given product.
 *
 * TODO...
 *
 * @package moxycart
 */
 
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getRelatedProducts',$scriptProperties); 