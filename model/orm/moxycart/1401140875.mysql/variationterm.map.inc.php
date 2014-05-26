<?php
$xpdo_meta_map['VariationTerm']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'variation_terms',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'vterm_id' => NULL,
    'vtype_id' => NULL,
    'slug' => NULL,
    'name' => NULL,
    'sku_prefix' => NULL,
    'sku_suffix' => NULL,
    'seq' => NULL,
  ),
  'fieldMeta' => 
  array (
    'vterm_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'vtype_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'slug' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
      'comment' => 'unique lowercase slug',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'sku_prefix' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => false,
    ),
    'sku_suffix' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => false,
    ),
    'seq' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '3',
      'phptype' => 'integer',
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
        'vterm_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'vtypeslug' => 
    array (
      'alias' => 'vtypeslug',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'vtype_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'slug' => 
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
    'Type' => 
    array (
      'class' => 'VariationType',
      'local' => 'vtype_id',
      'foreign' => 'vtype_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
  'validation' => 
  array (
    'rules' => 
    array (
      'slug' => 
      array (
        'minlength' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'xPDOMinLengthValidationRule',
          'value' => '1',
          'message' => 'Field slug must be at least 1 character.',
        ),
        'slugchars' => 
        array (
          'type' => 'preg_match',
          'rule' => '/^[a-z0-9\\-_]+$/i',
          'message' => 'Contains invalid characters.',
        ),
      ),
      'sku_prefix' => 
      array (
        'sku_prefix_chars' => 
        array (
          'type' => 'preg_match',
          'rule' => '/^[a-z0-9\\-_]+$/i',
          'message' => 'Contains invalid characters.',
        ),
      ),
      'sku_suffix' => 
      array (
        'sku_suffix_chars' => 
        array (
          'type' => 'preg_match',
          'rule' => '/^[a-z0-9\\-_]+$/i',
          'message' => 'Contains invalid characters.',
        ),
      ),
      'name' => 
      array (
        'minlength' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'xPDOMinLengthValidationRule',
          'value' => '1',
          'message' => 'Name must be at least 1 character.',
        ),
      ),
    ),
  ),
);
