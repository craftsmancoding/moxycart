<?php
/**
 * Note: if you have renamed classes and table names, then xPDO won't know where to find them!
 * You'll get errors like "Could not load class: OldClassName from mysql.oldclassname."
 * For cleanup of legacy table names, you'll need to run a raw query.
 *
 */
$core_path = $modx->getOption('moxycart.core_path','',MODX_CORE_PATH.'components/moxycart/');

$modx->addPackage('moxycart',"{$core_path}model/orm/",'moxy_');
$modx->addPackage('foxycart',"{$core_path}model/orm/",'foxy_');

$manager = $modx->getManager();


// Moxycart
$manager->removeObjectContainer('Currency');
$manager->removeObjectContainer('Product');
$manager->removeObjectContainer('Field');
$manager->removeObjectContainer('VariationType'); 
$manager->removeObjectContainer('VariationTerm');
$manager->removeObjectContainer('ProductVariationTypes');
$manager->removeObjectContainer('ProductTaxonomy');
$manager->removeObjectContainer('ProductTerm');
$manager->removeObjectContainer('ProductField'); 
$manager->removeObjectContainer('ProductRelation');
$manager->removeObjectContainer('Cart');
$manager->removeObjectContainer('Asset'); 
$manager->removeObjectContainer('Review');
$manager->removeObjectContainer('ProductAsset');

// Foxycart
$manager->removeObjectContainer('Foxydata');
$manager->removeObjectContainer('Transaction');
$manager->removeObjectContainer('Tax');
$manager->removeObjectContainer('Discount');
$manager->removeObjectContainer('CustomField');
$manager->removeObjectContainer('Attribute');
$manager->removeObjectContainer('TransactionDetail');
$manager->removeObjectContainer('TransactionDetailOption');
$manager->removeObjectContainer('ShiptoAddress');

// Cleanup of Legacy table names for which classes are no longer defined:
$legacy = array('moxy_carts','moxy_images','moxy_product_specs','moxy_specs');
foreach ($legacy as $l) {
    $removed = $modx->exec('DROP TABLE IF EXISTS '.$l);
    if ($removed === false && $modx->errorCode() !== '' && $modx->errorCode() !== PDO::ERR_NONE) {
        $msg ='Could not drop table '.$l.'! ERROR: ' . print_r($modx->pdo->errorInfo(),true); 
        $modx->log(modX::LOG_LEVEL_ERROR, $msg);
    } 
    else {
        $modx->log(modX::LOG_LEVEL_INFO, 'Legacy table dropped: '.$l);
    }
}

// See https://github.com/modxcms/revolution/issues/829
if ($Setting = $modx->getObject('modSystemSetting',array('key' => 'extension_packages'))) {
    $modx->removeExtensionPackage($object['namespace']);
}