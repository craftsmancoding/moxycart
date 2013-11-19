<?php
$xpdo_meta_map['TransactionDetailOption']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'transactiondetailoptions',
  'extends' => 'xPDOSimpleObject',
  'comment' => 'Foxycart options for transaction detail (i.e. product options)',
  'fields' => 
  array (
    'transactiondetail_id' => NULL,
    'product_option_name' => NULL,
    'product_option_value' => NULL,
    'price_mod' => 0,
    'weight_mod' => 0,
  ),
  'fieldMeta' => 
  array (
    'transactiondetail_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'product_option_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'product_option_value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'price_mod' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,3',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'weight_mod' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,3',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'TransactionDetail' => 
    array (
      'class' => 'TransactionDetail',
      'local' => 'transactiondetail_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
