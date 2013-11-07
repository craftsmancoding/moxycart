<?php
$product_taxonomies = array();

    $pages = $modx->getCollection('modResource',array('class_key'=>'Taxonomy'));
        
        echo '<pre>';
        print_r($pages);
        die();

/*
$results = $modx->query("SELECT * FROM modx_site_content WHERE class_key='Taxonomy'");
while ($r = $results->fetch(PDO::FETCH_ASSOC)) {
     echo '<pre>';
        print_r($r);
}
die();*/

$product_taxonomies[] = array(
    'product_id' => '',
    'taxonomy_id' => '',
    'seq' => 1
);

return $product_taxonomies;
/*EOF*/