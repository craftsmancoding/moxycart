<?php
$xpdo_meta_map['CustomField']= array (
  'package' => 'foxycart',
  'version' => '1.0',
  'table' => 'customfields',
  'extends' => 'xPDOObject',
  'comment' => 'Foxycart transaction customfields',
  'fields' => 
  array (
    'customfield_id' => NULL,
    'transaction_id' => NULL,
    'custom_field_name' => NULL,
    'custom_field_value' => NULL,
    'custom_field_is_hidden' => 0,
  ),
  'fieldMeta' => 
  array (
    'customfield_id' => 
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
    'custom_field_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'custom_field_value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'custom_field_is_hidden' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
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
        'customfield_id' => 
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
