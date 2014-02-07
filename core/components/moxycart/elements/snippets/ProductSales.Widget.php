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

$modx->regClientStartupScript($assets_url . 'components/moxycart/js/jquery-2.0.3.min.js');
$modx->regClientStartupScript($assets_url . 'components/moxycart/js/Chart.min.js');

$props = array();

$sql = "SELECT YEAR( ft.transaction_date ) AS SalesYear, MONTHNAME( ft.transaction_date ) AS SalesMonth, SUM( ft.order_total ) AS TotalSales
		FROM (SELECT * from foxy_transactions WHERE is_test = 0) AS ft
		WHERE DATE( ft.transaction_date ) 
		BETWEEN DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 12 MONTH)), INTERVAL 1 DAY) 
		AND LAST_DAY(DATE_SUB(NOW(), INTERVAL 0 MONTH))
		GROUP BY YEAR( ft.transaction_date ) , MONTH( ft.transaction_date ) 
		ORDER BY YEAR( ft.transaction_date ) , MONTH( ft.transaction_date )";
$result = $modx->query($sql);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

echo '<pre>';
print_r($rows);
die();
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