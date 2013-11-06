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


$terms[] = array(
    'pagetitle' => 'Some Category',
    'description' => 'Some Category',
    'alias' => 'some-category',
    'published' => 1,
    'template'  => '1',
    'class_key' => 'Term',
    'hide_menu' => 1
);

$terms[] = array(
     'pagetitle' => 'Other Category',
    'description' => 'Other Category',
    'alias' => 'other-category',
    'published' => 1,
    'template'  => '1',
    'class_key' => 'Term',
    'hide_menu' => 1
);

return $terms;