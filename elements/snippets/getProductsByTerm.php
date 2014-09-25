<?php
/**
 * @name getProductsByTerm
 * @description Returns a list of products associated with the given Taxonomical Term ID (Requires the Taxonomies AddOn)
 * 
 *
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param int $term_id (optional: defaults to the current page id)
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
 *   [[!getProductsByTerm? 
        &term_id=`447` 
        &innerTpl=`<li><a href="[[+Product.uri]]">[[+Product.name]] ([[+Product.sku]])</a> Full sized: [[+Product.Image.url]] Thumbnail: [[+Product.Image.thumbnail_url]]</li>`]]
 *
 * @package taxonomies
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getByTerm',$scriptProperties);

$term_id = $modx->getOption('term_id', $scriptProperties, $modx->resource->get('id'));
$exclude_id = $modx->getOption('exclude_id', $scriptProperties,0);
$innerTpl = $modx->getOption('innerTpl', $scriptProperties, '<li><a href="[[+Product.uri]]">[[+Product.name]] ([[+Product.sku]])</a></li>'); 
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, '<ul>[[+content]]</ul>'); 
$noResult = $modx->getOption('noResult', $scriptProperties, 'No Products found for term_id.'); 
$scriptProperties['content_ph'] = $modx->getOption('content_ph',$scriptProperties, 'content');
$c = $modx->newQuery('ProductTerm');
$c->where(array(
    'Term.published'=>true,
    'ProductTerm.term_id'=>$term_id,
    'Product.is_active'=>1,
    'Product.product_id:!='=>$exclude_id,
));
$c->sortby('Product.seq','ASC');

$Products = $modx->getCollectionGraph('ProductTerm', '{"Product":{"Image":{}},"Term":{}}',$c);

//return $c->toSQL();
if ($Products) {
    // Get Custom Fields
    foreach ($Products as $P) {
        $c = $modx->newQuery('ProductField');
        $c->where(array(
            'product_id' => $P->get('product_id'),
            'Field.is_active' => 1
        ));
        $c->sortby('Field.seq','ASC');    
        
        if($fields = $modx->getCollectionGraph('ProductField','{"Field":{}}',$c)) {
            foreach ($fields as $f) {
                $slug = $f->Field->get('slug');
                $P->set($slug, $f->get('value'));
            }
        }
    }
    return $Snippet->format($Products,$innerTpl,$outerTpl,$scriptProperties['content_ph']);
}


return $noResult;