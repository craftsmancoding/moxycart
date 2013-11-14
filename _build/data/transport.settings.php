<?php
/**
 * systemSettings transport file for moxycart extra
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
/* @var xPDOObject[] $systemSettings */


$systemSettings = array();

$systemSettings[1] = $modx->newObject('modSystemSetting');
$systemSettings[1]->fromArray(array(
    'key' => 'moxycart.foxycart_store_subdomain',
    'name' => 'Foxycart Store Subdomain',
    'description' => 'The URL subdomain for your Foxycart store.',
    'namespace' => 'moxycart',
    'xtype' => 'textfield',
    'value' => '',
    'area' => 'api',
), '', true, true);
return $systemSettings;
