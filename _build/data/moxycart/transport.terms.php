<?php
/**
 * terms transport file for moxycart extra
 *
 * Copyright 2013 by Everett Griffiths everett@craftsmancoding.com
 * Created on 07-05-2013
 *
 * @package moxycart
 * @subpackage build
 */

/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $terms */

$terms = array();

$taxonomies = $modx->getCollection('Taxonomy',array('class_key'=>'Taxonomy'));
if($taxonomies) {
    foreach ($taxonomies as $tax) {
        $parent = $tax->get('id');
        $terms[] = array(
            'pagetitle' => 'Sample Term ' . $parent,
            'description' => 'Some Term ' . $parent . ' Category',
            'alias' => 'sample-term-'.$parent,
            'published' => 1,
            'template'  => '1',
            'class_key' => 'Term',
            'hide_menu' => 1,
            'parent'    => $parent
        );

    }
}

return $terms;