<?php
/**
 * FoxyCart XMl Data Feed
 * 
 * @link http://wiki.foxycart.com/integration:xml:xml_to_simple_csv
 * @version 0.1a
 */
/*
	DESCRIPTION: =================================================================
	Writes the FoxyCart XML Datafeed to a simple TXT file.
	By default it will write to separate files per transaction
	By default it will record the customer name, product quantity, product code, product name, and transaction ID.
	You can easily modify what fields it writes by editing the code below.
*/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
$class_path = $core_path . 'components/moxycart/model/moxycart/foxycartdatafeed.class.php';
require_once($class_path);
$fc_datafeed = new FC_Datafeed($modx);
return $fc_datafeed->get_datafeed();
