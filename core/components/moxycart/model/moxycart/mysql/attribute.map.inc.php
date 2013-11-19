<?php
$xpdo_meta_map['Attribute']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'attributes',
  'extends' => 'xPDOObject',
  'comment' => 'Foxycart transaction attributes',
  'fields' => 
  array (
    'attribute_id' => NULL,
    'transaction_id' => NULL,
    'name' => NULL,
    'value' => NULL,
  ),
  'fieldMeta' => 
  array (
    'attribute_id' => 
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
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
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
        'attribute_id' => 
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
