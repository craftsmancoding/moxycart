<?php
/**
 * @name getTaxonomies
 * @description Returns a list of taxonomies.
 *
 * Available Placeholders
 * ---------------------------------------
 * id, pagetitle
 * use as [[+pagetitle]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * Usage
 * ------------------------------------------------------------
 * [[!getTaxonomies? &outerTpl=`sometpl` &innerTpl=`othertpl` &limit=`0`]]
 *
 * @package moxycart
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getTaxonomies',$scriptProperties);

/*
$scriptProperties['innerTpl'] = $modx->getOption('innerTpl',$scriptProperties, 'TaxonomyTpl');

$moxySnippet = new Moxycart\Snippet($modx);
$out = $moxySnippet->execute('json_taxonomies',$scriptProperties);
return $out;
*/