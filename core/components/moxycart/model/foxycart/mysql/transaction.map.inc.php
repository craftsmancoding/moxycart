<?php
$xpdo_meta_map['Transaction']= array (
  'package' => 'foxycart',
  'version' => '1.0',
  'table' => 'transactions',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'transaction_id' => NULL,
    'foxydata_id' => NULL,
    'id' => NULL,
    'store_id' => NULL,
    'store_version' => NULL,
    'is_test' => 1,
    'is_hidden' => 0,
    'data_is_fed' => 1,
    'transaction_date' => NULL,
    'payment_type' => NULL,
    'payment_gateway_type' => NULL,
    'processor_response' => NULL,
    'processor_response_details' => NULL,
    'purchase_order' => NULL,
    'cc_number_masked' => NULL,
    'cc_type' => NULL,
    'cc_exp_month' => NULL,
    'cc_exp_year' => NULL,
    'cc_start_date_month' => NULL,
    'cc_start_date_year' => NULL,
    'cc_issue_number' => NULL,
    'minfraud_score' => NULL,
    'paypal_payer_id' => NULL,
    'customer_id' => NULL,
    'is_anonymous' => 0,
    'customer_first_name' => NULL,
    'customer_last_name' => NULL,
    'customer_company' => NULL,
    'customer_address1' => NULL,
    'customer_address2' => NULL,
    'customer_city' => NULL,
    'customer_state' => NULL,
    'customer_postal_code' => NULL,
    'customer_country' => NULL,
    'customer_phone' => NULL,
    'customer_email' => NULL,
    'customer_ip' => NULL,
    'shipping_first_name' => NULL,
    'shipping_last_name' => NULL,
    'shipping_company' => NULL,
    'shipping_address1' => NULL,
    'shipping_address2' => NULL,
    'shipping_city' => NULL,
    'shipping_state' => NULL,
    'shipping_postal_code' => NULL,
    'shipping_country' => NULL,
    'shipping_phone' => NULL,
    'shipto_shipping_service_description' => NULL,
    'product_total' => 0,
    'tax_total' => 0,
    'shipping_total' => 0,
    'order_total' => 0,
    'receipt_url' => NULL,
    'customer_password' => NULL,
    'customer_password_salt' => NULL,
    'customer_password_hash_type' => NULL,
    'customer_password_hash_config' => NULL,
    'shipto_addresses' => NULL,
  ),
  'fieldMeta' => 
  array (
    'transaction_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'foxydata_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'comment' => 'Foxycart unique transaction id',
    ),
    'store_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'store_version' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'is_test' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
    'is_hidden' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'data_is_fed' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
    'transaction_date' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
    ),
    'payment_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => false,
    ),
    'payment_gateway_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => false,
    ),
    'processor_response' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'processor_response_details' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'purchase_order' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => false,
    ),
    'cc_number_masked' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
    ),
    'cc_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => false,
    ),
    'cc_exp_month' => 
    array (
      'dbtype' => 'int',
      'precision' => '2',
      'phptype' => 'integer',
      'attributes' => 'ZEROFILL UNSIGNED',
      'null' => false,
    ),
    'cc_exp_year' => 
    array (
      'dbtype' => 'int',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => false,
    ),
    'cc_start_date_month' => 
    array (
      'dbtype' => 'int',
      'precision' => '2',
      'phptype' => 'integer',
      'attributes' => 'ZEROFILL UNSIGNED',
      'null' => true,
    ),
    'cc_start_date_year' => 
    array (
      'dbtype' => 'int',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => true,
    ),
    'cc_issue_number' => 
    array (
      'dbtype' => 'int',
      'precision' => '2',
      'phptype' => 'integer',
      'null' => true,
    ),
    'minfraud_score' => 
    array (
      'dbtype' => 'int',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => true,
    ),
    'paypal_payer_id' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'customer_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'comment' => 'Foxycart customer',
    ),
    'is_anonymous' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'customer_first_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_last_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_company' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_address1' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_address2' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_city' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_state' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_postal_code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_country' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '2',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_phone' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => true,
    ),
    'customer_email' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'null' => true,
    ),
    'customer_ip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => true,
    ),
    'shipping_first_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'shipping_last_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'shipping_company' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'shipping_address1' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'null' => false,
    ),
    'shipping_address2' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'null' => false,
    ),
    'shipping_city' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'shipping_state' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'shipping_postal_code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
    ),
    'shipping_country' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '2',
      'phptype' => 'string',
      'null' => false,
    ),
    'shipping_phone' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => true,
    ),
    'shipto_shipping_service_description' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'null' => true,
    ),
    'product_total' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'tax_total' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'shipping_total' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'order_total' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '8,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'receipt_url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_password' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_password_salt' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_password_hash_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '8',
      'phptype' => 'string',
      'null' => false,
    ),
    'customer_password_hash_config' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '8',
      'phptype' => 'string',
      'null' => false,
      'comment' => 'Not sure what this stores',
    ),
    'shipto_addresses' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
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
        'transaction_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'foxydata_id' => 
    array (
      'alias' => 'foxydata_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'foxydata_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'id' => 
    array (
      'alias' => 'id',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'receipt_url' => 
    array (
      'alias' => 'receipt_url',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'receipt_url' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Taxes' => 
    array (
      'class' => 'Tax',
      'local' => 'transaction_id',
      'foreign' => 'transaction_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Discounts' => 
    array (
      'class' => 'Discount',
      'local' => 'transaction_id',
      'foreign' => 'transaction_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Attributes' => 
    array (
      'class' => 'Attribute',
      'local' => 'transaction_id',
      'foreign' => 'transaction_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'CustomFields' => 
    array (
      'class' => 'CustomField',
      'local' => 'transaction_id',
      'foreign' => 'transaction_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Details' => 
    array (
      'class' => 'TransactionDetail',
      'local' => 'transaction_id',
      'foreign' => 'transaction_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'ShiptoAddresses' => 
    array (
      'class' => 'ShiptoAddress',
      'local' => 'transaction_id',
      'foreign' => 'transaction_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Data' => 
    array (
      'class' => 'Foxydata',
      'local' => 'foxydata_id',
      'foreign' => 'foxydata_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
