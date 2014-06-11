<?php
/**
 * @name updateInventory
 * @description Called as a per-product hook from the parseFoxycartDatafeed Snippet, this hook (snippet) will decrement the quantity of a product during each purchase.  The requires that the product_code passed to Foxycart is the Moxycart product ID.
 *
 * INPUT PARAMETERS:
 *  product_code : integer must match Moxycart product id
 *  product_quantity : iteger to indicate how many were purchased.
 *
 * When called as a per-product hook from the parseFoxycartDatafeed Snippet, a whole bunch more options will be passed, but they are 
 * ignored by this Snippet.
 *
 * @return string message indicating completion.
 *
 * @package moxycart
 */

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('updateInventory',$scriptProperties);
 
$product_id = $modx->getOption('product_code', $scriptProperties);
$quantity_purchased = (int) $modx->getOption('product_quantity', $scriptProperties);

$log = array(
    'target'=>'FILE',
    'options' => array(
        'filename'=>'foxycart.log'
    )
);

if (!$Product = $modx->getObject('Product', $product_id)) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Product not found '.$product_id,$log,'UpdateInventory Snippet',__FILE__,__LINE__);
    return false;    
}
// Wrong product?
if ($Product->get('product_id') != $product_id) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Product not found '.$product_id,$log,'UpdateInventory Snippet',__FILE__,__LINE__);
    return false;    
}
$existing_qty = $Product->get('qty_inventory');

$new_qty = $existing_qty - $quantity_purchased;
$Product->set('qty_inventory', $new_qty);

if (!$Product->save()) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Product failed to update '.$product_id,$log,'UpdateInventory Snippet',__FILE__,__LINE__);
    return false;
}

$modx->log(xPDO::LOG_LEVEL_INFO,'Product '.$product_id.' updated to qty_inventory '.$new_qty,$log,'updateInventory Snippet',__FILE__,__LINE__);

return true;


/*EOF*/