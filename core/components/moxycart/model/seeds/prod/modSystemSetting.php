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
    array(
        'key'  =>     'moxycart.upload_dir',
		'value'=>     '',
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
);
/*EOF*/