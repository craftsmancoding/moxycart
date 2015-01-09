<?php
/*-----------------------------------------------------------------
For descriptions here, you must create some lexicon entries:
Name: setting_ + $key
Description: setting_ + $key + _desc
-----------------------------------------------------------------*/
return array(
    array(
        'key'  =>     'moxycart.domain',
		'value'=>     '',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    // Relative to MODX_ASSETS_PATH / MODX_ASSETS_URL
    // This is where all downloadable product assets will be stored.
    array(
        'key'  =>     'moxycart.upload_dir',
		'value'=>     'products/',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    // api key must begin with "m42Ccf"
    array(
        'key'  =>     'moxycart.api_key',
		'value'=>     'm42Ccf',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.enable_reviews',
		'value'=>     0,
		'xtype'=>     'combo-boolean',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.auto_approve_reviews',
		'value'=>     0,
		'xtype'=>     'combo-boolean',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.enable_variations',
		'value'=>     0,
		'xtype'=>     'combo-boolean',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.enable_related',
		'value'=>     0,
		'xtype'=>     'combo-boolean',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.enable_taxonomies',
		'value'=>     0,
		'xtype'=>     'combo-boolean',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.thumbnail_width',
		'value'=>     '240',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.thumbnail_height',
		'value'=>     '180',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),

    // Resized images are stored alongside the originals in a special sub-dir
    array(
        'key'  =>     'moxycart.thumbnail_dir',
		'value'=>     'thumbs/',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.categories',
		'value'=>     '["Default"]',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.results_per_page',
		'value'=>     '50',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    // For quick edit
    array(
        'key'  =>     'moxycart.product_columns',
		'value'=>     '{"name":"Name","sku":"SKU","price":"Price"}',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.product_cache',
		'value'=>     0,
		'xtype'=>     'combo-boolean',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ), 
    
    // User Stuff
    array(
        'key'  =>     'moxycart.user_group',
		'value'=>     '',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    array(
        'key'  =>     'moxycart.user_role',
		'value'=>     1, // Member
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    // are users created via the datafeed active?
    array(
        'key'  =>     'moxycart.user_activate',
		'value'=>     1,
		'xtype'=>     'combo-boolean',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
    // does user data get updated if new data comes in via the datafeed?
    array(
        'key'  =>     'moxycart.user_update',
		'value'=>     1,
		'xtype'=>     'combo-boolean',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),

	array(
		'key'  =>     'moxycart.default_product_template',
		'value'=>     null,
		'xtype'=>     'modx-combo-template',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
	),
);
/*EOF*/