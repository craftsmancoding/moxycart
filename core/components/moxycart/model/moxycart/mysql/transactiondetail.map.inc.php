<?php
$xpdo_meta_map['TransactionDetail']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'transactiondetails',
  'extends' => 'xPDOSimpleObject',
  'comment' => 'Foxycart transaction details',
  'fields' => 
  array (
    'transaction_id' => NULL,
    'product_name' => NULL,
    'product_price' => 0,
    'product_quantity' => NULL,
    'product_weight' => 0,
    'product_code' => NULL,
    'downloadable_url' => NULL,
    'sub_token_url' => NULL,
    'subscription_frequency' => NULL,
    'subscription_startdate' => '0000-00-00',
    'subscription_nextdate' => '0000-00-00',
    'subscription_enddate' => '0000-00-00',
    'is_future_line_item' => 0,
    'shipto' => NULL,
    'category_description' => NULL,
    'category_code' => NULL,
    'product_delivery_type' => NULL,
  ),
  'fieldMeta' => 
  array (
    'transaction_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'product_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'product_price' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'product_quantity' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'product_weight' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,4',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'product_code' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'comment' => 'This should be a link back to the products table',
    ),
    'downloadable_url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'sub_token_url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'subscription_frequency' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '8',
      'phptype' => 'string',
      'null' => true,
      'comment' => 'e.g. 1m',
    ),
    'subscription_startdate' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'datetime',
      'null' => true,
      'default' => '0000-00-00',
    ),
    'subscription_nextdate' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'datetime',
      'null' => true,
      'default' => '0000-00-00',
    ),
    'subscription_enddate' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'datetime',
      'null' => true,
      'default' => '0000-00-00',
    ),
    'is_future_line_item' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'shipto' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'comment' => '???',
    ),
    'category_description' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'category_code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'product_delivery_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'composites' => 
  array (
    'Options' => 
    array (
      'class' => 'TransactionDetailOption',
      'local' => 'id',
      'foreign' => 'transactiondetail_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Transaction' => 
    array (
      'class' => 'Transaction',
      'local' => 'transaction_id',
      'foreign' => 'transaction_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
