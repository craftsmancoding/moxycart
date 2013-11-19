<?php
$xpdo_meta_map['Foxydata']= array (
  'package' => 'foxycart',
  'version' => '1.0',
  'table' => 'foxydata',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'foxydata_id' => NULL,
    'md5' => NULL,
    'xml' => '',
    'timestamp_created' => 'CURRENT_TIMESTAMP',
  ),
  'fieldMeta' => 
  array (
    'foxydata_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'md5' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'comment' => 'Used to uniquely id the data payload',
    ),
    'xml' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'comment' => 'Decrypted XML body',
    ),
    'timestamp_created' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
      'default' => 'CURRENT_TIMESTAMP',
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
        'foxydata_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'md5' => 
    array (
      'alias' => 'md5',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'md5' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Transactions' => 
    array (
      'class' => 'Transaction',
      'local' => 'foxydata_id',
      'foreign' => 'foxydata_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
