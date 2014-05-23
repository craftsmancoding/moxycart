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
    array(
        'key'  =>     'moxycart.api_key',
		'value'=>     '',
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
    array(
        'key'  =>     'moxycart.product_columns',
		'value'=>     '',
		'xtype'=>     'textfield',
		'namespace' => 'moxycart',
		'area' => 'moxycart:default'
    ),
);
/*EOF*/