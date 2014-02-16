<?php
/**
 * 
 * @name UpdateInventory
 * @description Called as a hook from the parseFoxycartDatafeed Snippet, this hook (snippet) will decrement the quantity of a product during each purchase.
 *
 * @return string message indicating completion.
 */
 
$product_id = $modx->getOption('product_code', $scriptProperties);
$quantity_purchased = (int) $modx->getOption('product_quantity', $scriptProperties);

$log = array(
    'target'=>'FILE',
    'options' => array(
        'filename'=>'foxycart.log'
    )
);

$modx->log(xPDO::LOG_LEVEL_DEBUG,'UpdateInventory called for product '.$product_id,$log,'UpdateInventory Snippet',__FILE__,__LINE__);

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
$modx->addPackage('moxycart',$core_path.'components/moxycart/model/','moxy_');

if (!$Product = $modx->getObject('Product', $product_id)) {
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

return 'Product '.$product_id.' updated to qty_inventory '.$new_qty;
/*EOF*/