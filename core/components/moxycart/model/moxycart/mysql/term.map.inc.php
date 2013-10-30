<?php
$xpdo_meta_map['Term']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'extends' => 'modResource',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'aggregates' => 
  array (
    'Taxonomy' => 
    array (
      'class' => 'Taxonomy',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
