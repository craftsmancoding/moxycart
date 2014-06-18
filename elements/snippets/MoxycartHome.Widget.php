<?php
/**
 * @name MoxycartHome
 * @description Show Moxycart Dashboard
 *
 * See : http://rtfm.modx.com/revolution/2.x/administering-your-site/dashboards/dashboard-widget-types/dashboard-widget-type-snippet
 * See : http://rtfm.modx.com/revolution/2.x/administering-your-site/dashboards/creating-a-dashboard-widget
 *
 * On the Content Area of the Widget Create Page Put in
 * ------------------------------------------------------------------
 * MoxycartHome
 *
 * @package moxycart
 **/
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
$assets_url = $modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$modx->regClientCSS($assets_url.'css/moxycart.css');
$Base = new \Moxycart\BaseController($modx);
return $Base->fetchTemplate('main/index.php');