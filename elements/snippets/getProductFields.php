<?php
/**
 * @name getProductFields
 * @description Returns a list of values of custom fields (useful if the custom fields in use varies greatly from product to product, otherwise simply add placeholders to your template corresponding to the field slugs).
 * 
 * Available Placeholders
 * ---------------------------------------
 *  [[+value]]
 *  [[+Field.slug]]
 *  [[+Field.label]]
 * 
 * Parameters
 * -----------------------------
 * @param int $product_id specifies the product whose custom field values you want. Defaults to current product.
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
 * [[!getProductFields? &product_id=`[[+product_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl`]]
 * 
 * @package moxycart
 */

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getProductFields',$scriptProperties);

$product_id = $modx->getOption('product_id',$scriptProperties, $modx->getPlaceholder('product_id'));

if (!$product_id) {
    return 'Missing Product ID';
}

// Formatting Arguments:
$innerTpl = $modx->getOption('innerTpl',$scriptProperties, '<li>[[+Field.label]]: [[+value]]</li>');
$outerTpl = $modx->getOption('outerTpl',$scriptProperties, '<ul>[[+content]]</ul>');

$c = $modx->newQuery('ProductField');
$c->where(array(
    'product_id' => $product_id,
    'Field.is_active' => true
));
$c->sortby('Field.seq','ASC');    

$results = $modx->getCollectionGraph('ProductField','{"Field":{}}',$c);

if ($results) {
    return $Snippet->format($results,$innerTpl,$outerTpl);    
}

$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','getProductFields');