<?php
$xpdo_meta_map['Term']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'terms',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'term_id' => NULL,
    'taxonomy_id' => NULL,
    'name' => NULL,
    'slug' => NULL,
    'seq' => NULL,
  ),
  'fieldMeta' => 
  array (
    'term_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'taxonomy_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'slug' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'comment' => 'URL-friendly',
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
        'taxonomy_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Taxonomy' => 
    array (
      'class' => 'Taxonomy',
      'local' => 'taxonomy_id',
      'foreign' => 'taxonomy_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
