<?php
$xpdo_meta_map['ProductOptionMeta']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'product_option_meta',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'productoption_id' => NULL,
    'oterm_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'productoption_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'oterm_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
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
        'id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'productoptionmeta' => 
    array (
      'alias' => 'productoptionmeta',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'productoption_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'oterm_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Term' => 
    array (
      'class' => 'OptionTerm',
      'local' => 'oterm_id',
      'foreign' => 'oterm_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
