<?php
$product_taxonomies = array();

    $c = $modx->newQuery('modResource');
    $c->where( array('class_key' => 'Taxonomy') );
    $pages = $modx->getCollection('modResource',$c);
        
        echo '<pre>';
        print_r($pages);
        die();

$product_taxonomies[] = array(
    'product_id' => '',
    'taxonomy_id' => '',
    'seq' => 1
);

return $product_taxonomies;
/*EOF*/