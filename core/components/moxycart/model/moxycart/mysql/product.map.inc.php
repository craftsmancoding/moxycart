<?php
$xpdo_meta_map['Product']= array (
  'package' => 'moxycart',
  'version' => '1.0',
  'table' => 'products',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'product_id' => NULL,
    'store_id' => NULL,
    'currency_id' => NULL,
    'name' => NULL,
    'description' => NULL,
    'type' => NULL,
    'sku' => NULL,
    'track_inventory' => 0,
    'inventory_qty' => NULL,
    'alert_qty' => NULL,
    'price' => NULL,
    'category_id' => NULL,
    'length' => NULL,
    'width' => NULL,
    'height' => NULL,
    'weight' => NULL,
    'volume' => NULL,
    'length_unit_id' => NULL,
    'weight_unit_id' => NULL,
    'volume_unit_id' => NULL,
    'interval_unit' => NULL,
    'billing_interval' => 1,
    'user_group_id' => NULL,
    'role_id' => NULL,
    'payload_id' => NULL,
    'author_id' => NULL,
    'timestamp_created' => 'CURRENT_TIMESTAMP',
    'timestamp_modified' => NULL,
  ),
  'fieldMeta' => 
  array (
    'product_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'store_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
    ),
    'currency_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => true,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '60',
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
    'type' => 
    array (
      'dbtype' => 'enum',
      'precision' => '\'regular\',\'subscription\',\'download\'',
      'phptype' => 'string',
      'null' => true,
    ),
    'sku' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
    ),
    'track_inventory' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'inventory_qty' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'alert_qty' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'price' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,2',
      'phptype' => 'float',
      'null' => true,
    ),
    'category_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
    ),
    'length' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,4',
      'phptype' => 'float',
      'null' => true,
    ),
    'width' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,4',
      'phptype' => 'float',
      'null' => true,
    ),
    'height' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,4',
      'phptype' => 'float',
      'null' => true,
    ),
    'weight' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,4',
      'phptype' => 'float',
      'null' => true,
    ),
    'volume' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,4',
      'phptype' => 'float',
      'null' => true,
    ),
    'length_unit_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'weight_unit_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'volume_unit_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'interval_unit' => 
    array (
      'dbtype' => 'enum',
      'precision' => '\'hours\',\'days\',\'weeks\',\'years\'',
      'phptype' => 'string',
      'null' => false,
    ),
    'billing_interval' => 
    array (
      'dbtype' => 'int',
      'precision' => '3',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
    ),
    'user_group_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
    ),
    'role_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
    ),
    'payload_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
    ),
    'author_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
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
        'product_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'sku' => 
    array (
      'alias' => 'sku',
      'primary' => false,
      'unique' => true,
      'columns' => 
      array (
        'sku' => 
        array (
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Variants' => 
    array (
      'class' => 'ProductVariant',
      'local' => 'product_id',
      'foreign' => 'product_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Store' => 
    array (
      'class' => 'Store',
      'local' => 'store_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Currency' => 
    array (
      'class' => 'Currency',
      'local' => 'currency_id',
      'foreign' => 'currency_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'WeightUnit' => 
    array (
      'class' => 'Unit',
      'local' => 'weight_unit_id',
      'foreign' => 'unit_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'LengthUnit' => 
    array (
      'class' => 'Unit',
      'local' => 'length_unit_id',
      'foreign' => 'unit_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'VolumeUnit' => 
    array (
      'class' => 'Unit',
      'local' => 'volume_unit_id',
      'foreign' => 'unit_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Payload' => 
    array (
      'class' => 'modResource',
      'local' => 'payload_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Author' => 
    array (
      'class' => 'modUser',
      'local' => 'author_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'UserGroup' => 
    array (
      'class' => 'modUserGroup',
      'local' => 'user_group_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Role' => 
    array (
      'class' => 'modUserGroupRole',
      'local' => 'role_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Category' => 
    array (
      'class' => 'Category',
      'local' => 'category_id',
      'foreign' => 'category_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
