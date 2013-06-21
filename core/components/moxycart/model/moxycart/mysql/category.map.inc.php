<?php
$xpdo_meta_map['Category']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'categories',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'category_id' => NULL,
    'description' => NULL,
    'code' => NULL,
    'data' => NULL,
  ),
  'fieldMeta' => 
  array (
    'category_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'description' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'data' => 
    array (
      'dbtype' => 'text',
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
        'category_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Products' => 
    array (
      'class' => 'Product',
      'local' => 'category_id',
      'foreign' => 'category_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
