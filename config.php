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
    'seed' => array('prod'),
    'packages' => array(
        array('moxycart', $pkg_root_dir.'/core/components/moxycart/model/','moxy_'),
        array('foxycart', $pkg_root_dir.'/core/components/moxycart/model/','foxy_'),
    ),
    'build_attributes' => array(
        'Currency' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true, 
            xPDOTransport::UNIQUE_KEY => array('code'),
        ),
        'Product' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true, 
            xPDOTransport::UNIQUE_KEY => array('sku'),
        ),
        'Image' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true, 
            xPDOTransport::UNIQUE_KEY => array('url'),
        ),
        'Spec' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true, 
            xPDOTransport::UNIQUE_KEY => array('identifier'),
        ),
        'ProductSpec' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true, 
            xPDOTransport::UNIQUE_KEY => array('product_id','spec_id'),
        ),
        'VariationTerm' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true, 
            xPDOTransport::UNIQUE_KEY => array('vtype_id','name'),
        ),
        'VariationType' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true, 
            xPDOTransport::UNIQUE_KEY => array('name'),
        ),        
    ),
);
/*EOF*/
