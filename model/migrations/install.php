<?php
$core_path = $modx->getOption('moxycart.core_path','',MODX_CORE_PATH.'components/moxycart/');

// Add the package to the MODX extension_packages array
// TODO: read the table prefix from config
$modx->addExtensionPackage($object['namespace'],"{$core_path}model/orm/", array('tablePrefix'=>'moxy_'));
$modx->addPackage('moxycart',"{$core_path}model/orm/",'moxy_');
$modx->addPackage('foxycart',"{$core_path}model/orm/",'foxy_');

$manager = $modx->getManager();

// Moxycart Stuff
$manager->createObjectContainer('Product');
$manager->createObjectContainer('Field');
$manager->createObjectContainer('Unit');
$manager->createObjectContainer('Option'); 
$manager->createObjectContainer('OptionTerm');
$manager->createObjectContainer('ProductOption');
$manager->createObjectContainer('ProductOptionMeta');
$manager->createObjectContainer('ProductTerm');
$manager->createObjectContainer('ProductTaxonomy');
$manager->createObjectContainer('ProductField');
$manager->createObjectContainer('ProductRelation');
// $manager->createObjectContainer('Cart'); // future
$manager->createObjectContainer('Asset');
$manager->createObjectContainer('Review');
$manager->createObjectContainer('ProductAsset');

// Foxycart Stuff
$manager->createObjectContainer('Foxydata');
$manager->createObjectContainer('Transaction');
$manager->createObjectContainer('Tax');
$manager->createObjectContainer('Discount');
$manager->createObjectContainer('CustomField');
$manager->createObjectContainer('Attribute');
$manager->createObjectContainer('TransactionDetail');
$manager->createObjectContainer('TransactionDetailOption');
$manager->createObjectContainer('ShiptoAddress');
