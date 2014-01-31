<?php
/**
 * @name ProductQtyAlert
 * @description List all Product which set to track their inventory.
 *
 * @package moxycart
 **/
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
require_once $core_path . 'components/moxycart/model/moxycart/moxycart.class.php';
$moxycart = new Moxycart($modx);
$Products = $moxycart->json_products(array('limit'=>0,'track_inventory'=>1),true);
if($Products['total'] == 0) {
	return 'No Product Found';
}

$out ="<table class='classy' style='width: 100%;'><thead><tr>
			<th>Product</th>
			<th>Alert Qty</th>
			<th>Current Qty</th>
		</tr></thead><tbody>";
foreach ($Products['results'] as $product) {
		if($product['qty_inventory'] <= $product['qty_alert']) {
			$out .= "<tr>
			    <td>".$product['name']."</td>
			    <td>".$product['qty_alert']."</td>
			    <td>".$product['qty_inventory']."</td>
			</tr>";
		}
		
}
$out .= "</tbody></table>";
return $out;