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

require_once($core_path . 'components/moxycart/model/moxycart/foxycartdatafeed.class.php');
require_once($core_path . 'components/moxycart/model/moxycart/rc4crypt.class.php');
$rc4crypt = new rc4crypt();
$fc_datafeed = new FC_Datafeed($rc4crypt);

$error_msgs = array();

$api_key = $modx->getOption('moxycart.api_key'); // your foxy cart datafeed key
if(empty($api_key)) {
	$err_msg = 'Foxycart API Key is empty on your System Setting';
	$error_msgs[] = $err_msg;
	$modx->log($level,$err_msg,$this->log_target,__CLASS__.'::'.__FUNCTION__,__FILE__,__LINE__);
}

// You can change the following data if you want to customize what data gets written.
if($data = $modx->getOption('FoxyData', $_POST)) {
	$xml = $fc_datafeed->decrypt($data,$api_key);

	// processed the xml
	$fc_datafeed->parseXML($xml);

	return 'foxy';

} else {
	$err_msg = 'Failed to Proccessed Transaction Data Feed.Please check Error Logs on Foxycart Admin';
	$error_msgs[] = $err_msg;
	$modx->log($level,$err_msg,$this->log_target,__CLASS__.'::'.__FUNCTION__,__FILE__,__LINE__);
	return 'error';
}
