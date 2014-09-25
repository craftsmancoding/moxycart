<?php
/**
 * @name getRelatedProducts
 * @description Shows products related to the given product.
 *
 * @param integer $product_id defaults to current product
 * @param string $type to specify what kind of relations should be listed.  Defaults to "related"
 * @param boolean $show_hidden if true, shows related products which have "in_menu" set to false.  Default: false
 *
 * @package moxycart
 */
 
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getRelatedProducts',$scriptProperties);

$product_id = $modx->getOption('product_id',$scriptProperties, $modx->getPlaceholder('product_id'));
$type = $modx->getOption('type',$scriptProperties, 'related');
$in_menu = !$modx->getOption('show_hidden',$scriptProperties, false); // in_menu = Opposite of show_hidden
// Formatting:
$innerTpl = $modx->getOption('innerTpl', $scriptProperties, '<li><a href="[[+Relation.uri]]">[[+Relation.name]] ([[+Relation.sku]])</a></li>'); 
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, '<ul>[[+content]]</ul>'); 

$scriptProperties['content_ph'] = $modx->getOption('content_ph',$scriptProperties, 'content');

if (!$product_id) {
    return 'Missing Product ID';
}

$c = $modx->newQuery('ProductRelation');
$c->where(array(
    'Relation.is_active' => true,
    'Relation.in_menu' => $in_menu,  
    'ProductRelation.type'=>$type,
));
$c->sortby('ProductRelation.seq','ASC');

$Products = $modx->getCollectionGraph('ProductRelation', '{"Relation":{"Image":{}}',$c);

//return $c->toSQL();
if ($Products) {
    return $Snippet->format($Products,$innerTpl,$outerTpl,$scriptProperties['content_ph']);
}