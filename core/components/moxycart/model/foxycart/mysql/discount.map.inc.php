<?php
$xpdo_meta_map['Discount']= array (
  'package' => 'foxycart',
  'version' => '1.0',
  'table' => 'discounts',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'discount_id' => NULL,
    'transaction_id' => NULL,
    'code' => NULL,
    'valid_categories' => NULL,
    'name' => NULL,
    'amount' => 0,
    'display' => NULL,
    'coupon_discount_type' => NULL,
    'coupon_discount_details' => NULL,
  ),
  'fieldMeta' => 
  array (
    'discount_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'transaction_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'valid_categories' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'comment' => '???',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'amount' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '6,4',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'display' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'coupon_discount_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'coupon_discount_details' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'columns' => 
      array (
        'discount_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
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
