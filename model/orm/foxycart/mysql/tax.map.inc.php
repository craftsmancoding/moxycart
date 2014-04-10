<?php
$xpdo_meta_map['Tax']= array (
  'package' => 'foxycart',
  'version' => '1.0',
  'table' => 'taxes',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'tax_id' => NULL,
    'transaction_id' => NULL,
    'tax_rate' => 0,
    'tax_name' => NULL,
    'tax_amount' => 0,
  ),
  'fieldMeta' => 
  array (
    'tax_id' => 
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
    'tax_rate' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '6,4',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'tax_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'tax_amount' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '6,4',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
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
        'tax_id' => 
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
