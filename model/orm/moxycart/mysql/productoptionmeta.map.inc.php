<?php
$xpdo_meta_map['ProductOptionMeta']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'product_option_meta',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'productoption_id' => NULL,
    'product_id' => NULL,
    'option_id' => NULL,
    'oterm_id' => NULL,
    'is_override' => 0,
    'mod_price_type' => '+',
    'mod_price' => 0,
    'mod_weight_type' => '+',
    'mod_weight' => 0,
    'mod_code_type' => '+',
    'mod_code' => '',
    'mod_category_type' => '+',
    'mod_category' => '',
    'asset_id' => NULL,
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
    'product_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'option_id' => 
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
    'is_override' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'comment' => 'If checked, apply the mods',
    ),
    'mod_price_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '3',
      'phptype' => 'string',
      'null' => false,
      'default' => '+',
      'comment' => 'control the modification type: +,-,:',
    ),
    'mod_price' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'mod_weight_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '3',
      'phptype' => 'string',
      'null' => false,
      'default' => '+',
      'comment' => 'control the modification type: +,-,:',
    ),
    'mod_weight' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'mod_code_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '3',
      'phptype' => 'string',
      'null' => false,
      'default' => '+',
      'comment' => 'control the modification type: +,-,:',
    ),
    'mod_code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'mod_category_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '3',
      'phptype' => 'string',
      'null' => false,
      'default' => '+',
      'comment' => 'control the modification type: +,-,:',
    ),
    'mod_category' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'asset_id' => 
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
        'product_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'option_id' => 
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
    'Product' => 
    array (
      'class' => 'Product',
      'local' => 'product_id',
      'foreign' => 'product_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Option' => 
    array (
      'class' => 'Option',
      'local' => 'option_id',
      'foreign' => 'option_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Term' => 
    array (
      'class' => 'OptionTerm',
      'local' => 'oterm_id',
      'foreign' => 'oterm_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Asset' => 
    array (
      'class' => 'Asset',
      'local' => 'asset_id',
      'foreign' => 'asset_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
