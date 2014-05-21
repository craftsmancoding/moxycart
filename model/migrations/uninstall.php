<?php
$core_path = $modx->getOption('moxycart.core_path','',MODX_CORE_PATH.'components/moxycart/');

$modx->addPackage('moxycart',"{$core_path}model/orm/",'moxy_');
$modx->addPackage('foxycart',"{$core_path}model/orm/",'foxy_');

$manager = $modx->getManager();

// Moxycart
$manager->removeObjectContainer('Currency');
$manager->removeObjectContainer('Product');
$manager->removeObjectContainer('Spec'); // Legacy name
$manager->removeObjectContainer('Field');
$manager->removeObjectContainer('VariationType'); 
$manager->removeObjectContainer('VariationTerm');
$manager->removeObjectContainer('ProductVariationTypes');
$manager->removeObjectContainer('ProductTaxonomy');
$manager->removeObjectContainer('ProductTerm');
$manager->removeObjectContainer('ProductSpec'); // Legacy name
$manager->removeObjectContainer('ProductField'); 
$manager->removeObjectContainer('ProductRelation');
$manager->removeObjectContainer('Cart');
$manager->removeObjectContainer('Image');
$manager->removeObjectContainer('Review');

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

// See https://github.com/modxcms/revolution/issues/829
if ($Setting = $modx->getObject('modSystemSetting',array('key' => 'extension_packages'))) {
    $modx->removeExtensionPackage($object['namespace']);
}