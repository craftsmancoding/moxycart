<?php
$xpdo_meta_map['Field']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'fields',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'field_id' => NULL,
    'slug' => NULL,
    'label' => NULL,
    'description' => NULL,
    'config' => '',
    'seq' => NULL,
    'group' => NULL,
    'type' => 'text',
    'timestamp_created' => 'CURRENT_TIMESTAMP',
    'timestamp_modified' => NULL,
  ),
  'fieldMeta' => 
  array (
    'field_id' => 
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
    'label' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'null' => false,
      'comment' => 'Human readable, translated.',
    ),
    'description' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'config' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'seq' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '3',
      'phptype' => 'integer',
      'null' => true,
    ),
    'group' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => true,
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'default' => 'text',
      'comment' => 'Formbuilder argument',
    ),
    'timestamp_created' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
      'default' => 'CURRENT_TIMESTAMP',
    ),
    'timestamp_modified' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
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
        'field_id' => 
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
    'Products' => 
    array (
      'class' => 'ProductField',
      'local' => 'field_id',
      'foreign' => 'field_id',
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
      'type' => 
      array (
        'validchars' => 
        array (
          'type' => 'preg_match',
          'rule' => '/^[a-z_][a-z0-9_]+$/i',
          'message' => 'Contains invalid characters.',
        ),
      ),
    ),
  ),
);
