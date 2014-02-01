<?php
/**
 * @name ProductSales
 * @description Show All Time Sales.
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