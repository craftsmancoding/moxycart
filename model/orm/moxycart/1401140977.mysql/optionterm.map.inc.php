<?php
$xpdo_meta_map['OptionTerm']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'option_terms',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'oterm_id' => NULL,
    'otype_id' => NULL,
    'slug' => NULL,
    'name' => NULL,
    'sku_prefix' => NULL,
    'sku_suffix' => NULL,
    'seq' => NULL,
  ),
  'fieldMeta' => 
  array (
    'oterm_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'otype_id' => 
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
        'oterm_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'otypeslug' => 
    array (
      'alias' => 'otypeslug',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'otype_id' => 
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
      'class' => 'OptionType',
      'local' => 'otype_id',
      'foreign' => 'otype_id',
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
