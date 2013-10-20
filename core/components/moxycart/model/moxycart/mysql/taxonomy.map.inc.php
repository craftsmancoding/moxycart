<?php
$xpdo_meta_map['Taxonomy']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'taxonomies',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'taxonomy_id' => NULL,
    'parent_id' => NULL,
    'name' => NULL,
    'seq' => NULL,
    'grp' => NULL,
  ),
  'fieldMeta' => 
  array (
    'taxonomy_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'parent_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
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
    'grp' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
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
        'taxonomy_id' => 
        array (
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
      'class' => 'Term',
      'local' => 'taxonomy_id',
      'foreign' => 'taxonomy_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Parent' => 
    array (
      'class' => 'Taxonomy',
      'local' => 'parent_id',
      'foreign' => 'taxonomy_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Children' => 
    array (
      'class' => 'Taxonomy',
      'local' => 'taxonomy_id',
      'foreign' => 'parent_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
