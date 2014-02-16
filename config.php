<?php
/**
 * Config File for Repoman
 *
 * @return array
 */
return array(
    'package_name' => 'Moxycart',
    'namespace' => 'moxycart',
    'description' => "A turnkey FoxyCart eCommerce solution for MODX Revolution.",
    'version' => '0.8.0',
    'release' => 'dev',
    'author_name' => 'Everett Griffiths',
    'author_email' => 'everett@craftsmancoding.com',
    'author_site' => 'Craftsman Coding',    
    'author_url' => 'http://craftsmancoding.com/',
    'documentation_url' => 'https://github.com/craftsmancoding/moxycart/wiki',
    'repo_url' => 'https://github.com/craftsmancoding/moxycart',   
    'clone_url' => 'git@github.com:craftsmancoding/moxycart.git',
    'copyright' => date('Y'),
    
    'category' => 'Moxycart',
    'seed' => array('base'),
    'packages' => array(
        array('moxycart', $pkg_root_dir.'/core/components/moxycart/model/','moxy_'),
        array('foxycart', $pkg_root_dir.'/core/components/moxycart/model/','foxy_'),
    ),
    'build_attributes' => array(
        'Currency' => array(
            'preserve_keys' => true,
            'update_object' => true, 
            'unique_key' => array('code'),
        ),
        'Product' => array(
            'preserve_keys' => true,
            'update_object' => true, 
            'unique_key' => array('sku'),
        ),
        'Image' => array(
            'preserve_keys' => true,
            'update_object' => true, 
            'unique_key' => array('url'),
        ),
        'Spec' => array(
            'preserve_keys' => true,
            'update_object' => true, 
            'unique_key' => array('identifier'),
        ),
        'ProductSpec' => array(
            'preserve_keys' => true,
            'update_object' => true, 
            'unique_key' => array('product_id','spec_id'),
        ),
        'VariationTerm' => array(
            'preserve_keys' => true,
            'update_object' => true, 
            'unique_key' => array('vtype_id','name'),
        ),
        'VariationType' => array(
            'preserve_keys' => true,
            'update_object' => true, 
            'unique_key' => array('name'),
        ),    
        'Store' => array(
            'preserve_keys' => true,
            'update_object' => true, 
            'unique_key' => array('uri'),
            'related_objects' => true,
            'related_object_attributes' => array (            
                'Products' => array(
                    'preserve_keys' => true,
                    'update_object' => true, 
                    'unique_key' => array('sku'),
                    'related_objects' => true,
                    'related_object_attributes' => array (            
                        'Specs' => array(
                            'preserve_keys' => true,
                            'update_object' => true, 
                            'unique_key' => array('product_id','store_id'),
                            'related_objects' => true,
                            'related_object_attributes' => array (            
                                'Spec' => array(
                                    'preserve_keys' => false,
                                    'update_object' => true, 
                                    'unique_key' => array('identifier'),
                                )    
                            )                            
                        ),           
                        'Template' => array(
                            'preserve_keys' => false,
                            'update_object' => true,
                            'unique_key' => 'templatename',
                        )
                    ),
                ),
            ),            
        ),
        'Review' => array(
            'preserve_keys' => true,
            'update_object' => true, 
            'unique_key' => array('email'),
        ),        

/*        'modDashboardWidgetPlacement'   => array(
            'preserve_keys' => false,
            'update_object' => true, 
            'unique_key' => array('dashboard','widget','rank'),
            'related_objects' => true,
            'related_object_attributes'    => array(
                'Widget'    =>  array (
                    'preserve_keys' => false,
                    'update_object' => true, 
                    'unique_key' => array('name'),
                )
            )
        ),  */ 

        'Transaction' => array(
            'preserve_keys' => true,
            'update_object' => true, 
            'unique_key' => array('transaction_id'),
        ),    
    ),
);
/*EOF*/
