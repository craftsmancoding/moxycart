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
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
$assets_url = $modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');

$modx->regClientStartupScript($assets_url . 'js/jquery-2.0.3.min.js');
$modx->regClientStartupScript($assets_url . 'js/Chart.min.js');

$props = array();

$sql = "SELECT YEAR( transaction_date ) AS SalesYear, MONTHNAME( transaction_date ) AS SalesMonth, SUM( order_total ) AS TotalSales FROM foxy_transactions
		WHERE transaction_date >= date_sub(now(), interval 12 month)
		AND is_test = 0
		GROUP BY YEAR( transaction_date ) , MONTH( transaction_date ) 
		ORDER BY YEAR( transaction_date ) , MONTH( transaction_date )";
$result = $modx->query($sql);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

if(empty($rows)) {
	return 'Your store does not have any transactions yet.';
}

foreach ($rows as $sale) {
	$sales_data[] = (int) $sale['TotalSales'];
}

foreach ($rows as $sale_date) {
	$month_year[] = $sale_date['SalesMonth']. '-' .$sale_date['SalesYear'];
}


$modx->regClientStartupHTMLBlock('<script type="text/javascript">
    var sales_data = '.json_encode($sales_data).';
    var month_year = '.json_encode($month_year).';
    
	</script>
');

$tpl = file_get_contents($core_path.'components/moxycart/elements/chunks/ProductSales.tpl');

$chunk = $modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
$chunk->setCacheable(false);
$out = $chunk->process(array(), $tpl);

return $out;