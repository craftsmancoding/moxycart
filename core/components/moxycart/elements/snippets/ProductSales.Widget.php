<?php
/**
 * @name ProductSales
 * @description Show All Time Sales. This was entended to be used only as Dashboard widget Content
 *
 * See : http://rtfm.modx.com/revolution/2.x/administering-your-site/dashboards/dashboard-widget-types/dashboard-widget-type-snippet
 * See : http://rtfm.modx.com/revolution/2.x/administering-your-site/dashboards/creating-a-dashboard-widget
 * 
 * On the Content Area of the Widget Create Page Put in
 * ------------------------------------------------------------------
 * ProductSales
 *
 * Required File Chunk
 * ------------------------------------------------------------------
 * /assets/mycomponents/moxycart/core/components/moxycart/elements/chunks/ProductSales.tpl
 * 
 * @package moxycart
 **/
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
$assets_url = $modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);

$modx->regClientStartupScript($assets_url . 'components/moxycart/js/Chart.min.js');


$sql = "SELECT YEAR( transaction_date ) AS SalesYear, MONTH( transaction_date ) AS SalesMonth, SUM( order_total ) AS TotalSales
		FROM foxy_transactions
		GROUP BY YEAR( transaction_date ) , MONTH( transaction_date ) 
		ORDER BY YEAR( transaction_date ) , MONTH( transaction_date )";
$result = $modx->query($sql);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
echo '<pre>';
print_r($rows);
die();

$props = array();

$tpl = file_get_contents($core_path.'components/moxycart/elements/chunks/ProductSales.tpl');

$chunk = $modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
$chunk->setCacheable(false);
$out = $chunk->process($props, $tpl);

return $out;