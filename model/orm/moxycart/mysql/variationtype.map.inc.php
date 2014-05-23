<?php
$xpdo_meta_map['VariationType']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'variation_types',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'vtype_id' => NULL,
    'slug' => NULL,
    'name' => NULL,
    'description' => NULL,
    'seq' => NULL,
  ),
  'fieldMeta' => 
  array (
    'vtype_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
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
      'precision' => '32',
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
    'seq' => 
    array (
      'dbtype' => 'int',
      'precision' => '4',
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
        'vtype_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'slug' => 
    array (
      'alias' => 'slug',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'slug' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Terms' => 
    array (
      'class' => 'VariationTerm',
      'local' => 'vtype_id',
      'foreign' => 'vtype_id',
      'cardinality' => 'many',
      'owner' => 'local',
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
        'validchars' => 
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
