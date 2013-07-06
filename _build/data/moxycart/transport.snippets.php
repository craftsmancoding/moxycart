<?php
/**
 * snippets transport file for moxycart extra
 *
 * Copyright 2013 by Everett Griffiths everett@craftsmancoding.com
 * Created on 07-05-2013
 *
 * @package moxycart
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => '1',
    'description' => 'Returns a list of products.',
    'name' => 'getProducts',
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/getproducts.snippet.php'));

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => '2',
    'description' => 'Shows products related to the given product.',
    'name' => 'getRelatedProducts',
), '', true, true);
$snippets[2]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/getrelatedproducts.snippet.php'));

return $snippets;
