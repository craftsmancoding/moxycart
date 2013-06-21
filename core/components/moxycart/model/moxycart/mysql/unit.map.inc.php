<?php
$xpdo_meta_map['Unit']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'units',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'unit_id' => NULL,
    'name' => NULL,
    'abbreviation' => NULL,
    'description' => NULL,
    'type' => NULL,
  ),
  'fieldMeta' => 
  array (
    'unit_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => false,
    ),
    'abbreviation' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'type' => 
    array (
      'dbtype' => 'enum',
      'precision' => '\'mass\',\'volume\',\'length\',\'area\',\'other\'',
      'phptype' => 'string',
      'null' => true,
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
        'unit_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Products' => 
    array (
      'class' => 'Product',
      'local' => 'unit_id',
      'foreign' => 'volume_unit_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
