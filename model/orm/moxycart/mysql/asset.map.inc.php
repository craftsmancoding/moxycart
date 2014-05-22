<?php
$xpdo_meta_map['Asset']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'assets',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'asset_id' => NULL,
    'product_id' => NULL,
    'content_type_id' => NULL,
    'title' => NULL,
    'alt' => NULL,
    'url' => NULL,
    'thumbnail_url' => NULL,
    'path' => NULL,
    'width' => NULL,
    'height' => NULL,
    'size' => NULL,
    'length' => NULL,
    'seq' => NULL,
    'is_active' => 1,
  ),
  'fieldMeta' => 
  array (
    'asset_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'product_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
    ),
    'content_type_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'alt' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'thumbnail_url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'path' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'width' => 
    array (
      'dbtype' => 'int',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => false,
    ),
    'height' => 
    array (
      'dbtype' => 'int',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => false,
    ),
    'size' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'comment' => 'In Bytes',
    ),
    'length' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'comment' => 'for videos and sound files: round to seconds',
    ),
    'seq' => 
    array (
      'dbtype' => 'int',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => false,
    ),
    'is_active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
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
        'asset_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'url' => 
    array (
      'alias' => 'url',
      'primary' => false,
      'unique' => false,
      'columns' => 
      array (
        'url' => 
        array (
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'path' => 
    array (
      'alias' => 'path',
      'primary' => false,
      'unique' => false,
      'columns' => 
      array (
        'path' => 
        array (
          'collation' => 'A',
          'null' => true,
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
    'ContentType' => 
    array (
      'class' => 'modContentType',
      'local' => 'content_type_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
  'validation' => 
  array (
    'rules' => 
    array (
      'url' => 
      array (
        'minlength' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'xPDOMinLengthValidationRule',
          'value' => '3',
          'message' => 'URL must be at least 3 characters.',
        ),
      ),
      'path' => 
      array (
        'minlength' => 
        array (
          'type' => 'xPDOValidationRule',
          'rule' => 'xPDOMinLengthValidationRule',
          'value' => '3',
          'message' => 'Path must be at least 3 characters.',
        ),
      ),
    ),
  ),
);
