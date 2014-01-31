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

/*
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
require_once $core_path . 'components/moxycart/model/moxycart/moxycart.snippets.class.php';

$moxySnippet = new MoxycartSnippet($modx);
$out = $moxySnippet->execute('json_products',$scriptProperties);
return $out;
*/

$outerTpl = $modx->getOption('outerTpl',$scriptProperties,'MoxyOuterTpl');
$innerTpl = $modx->getOption('innerTpl',$scriptProperties,'MoxyInnerTpl');

$limit = (int) $modx->getOption('limit',$scriptProperties,0);
$start = (int) $modx->getOption('start',$scriptProperties,0);
$sort = $modx->getOption('sort',$scriptProperties,'product_id');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');


unset($scriptProperties['limit']);
unset($scriptProperties['start']);
unset($scriptProperties['sort']);
unset($scriptProperties['dir']);
unset($scriptProperties['outerTpl']);
unset($scriptProperties['innerTpl']);


$criteria = $modx->newQuery('Product');        
//    print_r($scriptProperties); exit;
if ($scriptProperties) {
    $criteria->where($scriptProperties);
}
//$criteria->limit($limit, $start); 
$criteria->sortby($sort,$dir);

$data = $modx->getCollectionGraph('Product','{"Specs":{"Spec":{}}}'); // , $criteria);

if(!$data) {
	return 'No Record Found.';
}

$innerOut = '';
$output = '';


foreach ($data as $r) {
    $array = $r->toArray();
    foreach ($r->Specs as $s) {
        $array[ $s->Spec->get('identifier') ] = $s->get('value');
    }
    $innerOut .= $modx->getChunk($innerTpl,$array);
}


$innerPlaceholder = array('moxy.items' => $innerOut);
return $modx->getChunk($outerTpl,$innerPlaceholder);