<?php
/**
 * @name addToCartButton
 * @description Generates an "Add to Cart" button for the current product. This will respond intelligently according to inventory, options, and bundled products.
 *
 * @param integer $product_id (defaults to current product)
 * @param string $submit text/image to show as the submit button. If an image, a full URL with http:// must be specified. (default: Add to Cart)
 * @param string $soldout text/image to show if inventory tracking is enabled and the qty is below the backorder max. If an image, a full URL with http:// must be specified.  (default: Sold Out)
 * @param string $tpl name of formatting chunk. (default: BuyButton)
 * @package moxycart
 */
 
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('addToCartButton',$scriptProperties);

$product_id = $modx->getOption('product_id', $scriptProperties, $modx->getPlaceholder('product_id'));
$submit = $modx->getOption('submit', $scriptProperties, 'Add to Cart');
$soldout = $modx->getOption('soldout', $scriptProperties, 'Sold Out');
$tpl = $modx->getOption('tpl', $scriptProperties, 'BuyButton');

$P = $modx->getObject('Product', array('product_id'=>$product_id));
if (!$P) {
    $modx->log(modX::LOG_LEVEL_ERROR,'Product ID not found: '.$product_id,'','addToCartButton');
    return '<script>alert("Product ID not found.");</script>';
}
$properties = $P->toArray();

// Watch out for low inventory
if ($P->get('track_inventory')) {
    if(($P->get('qty_inventory') + $P->get('qty_backorder_max')) <=  0) {
        if(filter_var($soldout, FILTER_VALIDATE_URL)) {
            $soldout = sprintf('<img src="%s" alt="Sold Out"/>',$soldout);    
        }
        return $soldout;
    }
}

if(filter_var($submit, FILTER_VALIDATE_URL)) {
    $properties['submit'] = sprintf('<input type="image" src="%s" alt="Add to Cart"/>',$submit);    
}
else {
    $properties['submit'] = sprintf('<input type="submit" value="%s" />',$submit);
}


$c = $modx->newQuery('ProductOptionType');
$c->where(array('ProductOptionType.product_id' => $product_id));
$c->sortby('ProductOptionType.Type.seq, ProductOptionType.Type.Terms.seq','ASC');

$properties['options'] = '';
if ($Options = $modx->getCollectionGraph('ProductOptionType','{"Type":{"Terms":{}}}',$c)) {

    foreach ($Options as $o) {
        $opt = '<label for="'.$o->Type->get('slug').'">'.$o->Type->get('name').'</label><select id="'.$o->Type->get('slug').'" name="'.$o->Type->get('slug').'">';
        foreach ($o->Term->Terms as $t) {
            $opt .= '<option value="'.$t->get('slug').$t->get('modifiers').'">'.$t->get('name').'</option>';
        }
        $opt .= '</label>';
        $properties['options.'.$o->Type->get('slug')] = $opt;
        $properties['options'] = $properties['options'] . $opt;
    }
}

return $modx->getChunk($tpl, $properties);