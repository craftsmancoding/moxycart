<?php
/**
 * @name getProductTerms
 * @description Returns a list of product_terms, e.g. for a tag cloud or a category list
 * 
 * Available Placeholders
 * ---------------------------------------
 * id, product_id, term_id,term,properties
 * use as [[+term]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param integer $product_id get records for this specific product (default: current product)
 * @param mixed $taxonomy_id if present, results will be restricted to this taxonomy(ies). Array, or comma-separated string
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param int $limit Limit the result
 * @param boolean $debug -- force raw Query to be returned
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

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getProductTerms',$scriptProperties);
$scriptProperties['content_ph'] = $modx->getOption('content_ph',$scriptProperties, 'content');
$innerTpl = $modx->getOption('innerTpl', $scriptProperties, '<li>[[+term_id]]=[[+Term.pagetitle]]</li>'); 
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, '<ul>[[+content]]</ul>'); 

$product_id = $modx->getOption('product_id',$scriptProperties, $modx->getPlaceholder('product_id'));
$taxonomy_id_raw = trim($modx->getOption('taxonomy_id',$scriptProperties));
$debug = $modx->getOption('debug',$scriptProperties, false);

if (!$product_id) {
    return 'Missing Product ID';
}
$taxonomy_ids = array();
if ($taxonomy_id_raw) {
    if (is_array($taxonomy_id)) {
        $taxonomy_ids = $taxonomy_id_raw;
    }
    else {
        $taxonomy_ids = array_map('trim', explode(',', $taxonomy_id_raw));
    }
}


$c = $modx->newQuery('ProductTerm');
$c->where(array(
    'Term.published'=> true,
    'product_id'=>$product_id
));

if ($debug)
{
    $c->prepare();
    return '<h2>getProductTerms</h2><pre>'.$c->toSQL().'</pre>';
}




// TODO: Support taxonomy-filtering of deeply-nested terms.
// WARNING: This will fail when matching nested terms that are not immediate children of the taxonomy
// (i.e. when the term parent is another term)
// Workaround: add the parent term id(s) to the taxonomy_id list.
if ($taxonomy_ids) {
    $c->where(array('Term.parent:IN'=>$taxonomy_ids));
}

$c->sortby('Term.menuindex','ASC');



$ProductTerms = $modx->getCollectionGraph('ProductTerm','{"Term":{}}',$c);
if ($ProductTerms) {
    return $Snippet->format($ProductTerms,$innerTpl,$outerTpl,$scriptProperties['content_ph']);    
}


/*
$scriptProperties['innerTpl'] = $modx->getOption('innerTpl',$scriptProperties, 'ProductTerm');

$moxySnippet = new Moxycart\Snippet($modx);
$out = $moxySnippet->execute('json_product_terms',$scriptProperties);
return $out;
*/