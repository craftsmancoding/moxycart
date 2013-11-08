<?php
/**
 * taxonomies transport file for moxycart extra
 *
 * Copyright 2013 by Everett Griffiths everett@craftsmancoding.com
 * Created on 07-05-2013
 *
 * @package moxycart
 * @subpackage build
 */

/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $taxonomies */


$taxonomies = array();


$taxonomies[] = array(
    'pagetitle' => 'Categories',
    'description' => 'Hierarchical list of categories',
    'alias' => 'category',
    'published' => 1,
    'template'  => '1',
    'class_key' => 'Taxonomy',
    'hide_menu' => 1
);

$taxonomies[] = array(
    'pagetitle' => 'Tags',
    'alias' => 'tag',
    'published' => 1,
    'template'  => '1',
    'class_key' => 'Taxonomy',
    'hide_menu' => 1
);

return $taxonomies;