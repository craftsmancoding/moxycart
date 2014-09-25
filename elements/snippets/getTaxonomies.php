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
 * @param string $sort column to sort by (default: pagetitle)
 * @param string $dir ASC (default) or DESC
 * @param integer $limit the max number of records
 * @param integer $start the offset
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

$innerTpl = $modx->getOption('innerTpl', $scriptProperties, '<li>[[+pagetitle]]</li>');
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, '<ul>[[+content]]</ul>');
$limit = (int) $modx->getOption('limit', $scriptProperties); 
$start = (int) $modx->getOption('start', $scriptProperties); 
$sort = $modx->getOption('sort', $scriptProperties,'pagetitle');
$dir = $modx->getOption('dir', $scriptProperties,'ASC'); 
$scriptProperties['content_ph'] = $modx->getOption('content_ph',$scriptProperties, 'content');
$criteria = $modx->newQuery('Taxonomy');
$criteria->where(array('class_key'=>'Taxonomy','published'=>true));

if ($limit) {
    $criteria->limit($limit, $start); 
}
$criteria->sortby($sort,$dir);
$results = $modx->getCollection('Taxonomy',$criteria);

if ($results) {
    return $Snippet->format($results,$innerTpl,$outerTpl,$scriptProperties['content_ph']);    
}

$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','Snippet getTaxonomies');