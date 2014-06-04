<?php
/**
 * @name getProductsByTerm
 * @description Returns a list of products associated with the given term id
 * 

 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param int $term_id (optional: defaults to the current page id)

 *
 * @param int $limit Limit the result
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * Usage
 * ------------------------------------------------------------
 * [[!getProductsByTerm? &outerTpl=`sometpl` &innerTpl=`othertpl`]]
 *
 * @package taxonomies
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getByTerm',$scriptProperties);

$term_id = $modx->getOption('term_id', $scriptProperties, $modx->resource->get('id'));
$innerTpl = $modx->getOption('innerTpl', $scriptProperties, '<li><a href="[[+Product.uri]]">[[+Product.name]] ([[+Product.sku]])</a></li>'); 
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, '<ul>[[+content]]</ul>'); 
$c = $modx->newQuery('ProductTerm');
$c->where(array(
    'Term.published'=>true,
    'ProductTerm.term_id'=>$term_id,
));
$c->sortby('seq','ASC');


if ($Products = $modx->getCollectionGraph('ProductTerm', '{"Product":{},"Term":{}}',$c)) {
    foreach ($Products as $P) {
//        return '<pre>'. print_r($P->toArray('',false,false,true),true).'</pre>';
    }
    return $Snippet->format($Products,$innerTpl,$outerTpl);
}

return 'No Products found.';
