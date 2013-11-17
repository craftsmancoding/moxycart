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
$cache_dir = 'moxycart_datafeed';
$lifetime = 0;
$cache_opts = array(xPDO::OPT_CACHE_KEY => $cache_dir); 

$api_key = $modx->getOption('moxycart.api_key'); // your foxy cart datafeed key
if(empty($api_key)) {
	$err_msg = 'Foxycart API Key is empty on your System Setting';
	$error_msgs[] = $err_msg;
	$modx->log($level,$err_msg,$this->log_target,__CLASS__.'::'.__FUNCTION__,__FILE__,__LINE__);
}

// You can change the following data if you want to customize what data gets written.
if($data = $modx->getOption('FoxyData', $_POST)) {
	$xml = $fc_datafeed->decrypt($data,$api_key);

	// store files on local dir
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($xml->asXML());

	$encrypted_cache_key = 'encrypted_txn_'.time();
    $modx->cacheManager->set($encrypted_cache_key, $data, $lifetime, $cache_opts);
	$decrypted_cache_key = 'decrypted_txn_'.time();
    $modx->cacheManager->set($decrypted_cache_key, $dom->saveXML(), $lifetime, $cache_opts);

    // processed this transactions data
	$transactions = $fc_datafeed->parseXML($xml);

	// store it as cahce file for now
	$transaction_cache_key = 'txn_'.time();
    $modx->cacheManager->set($transaction_cache_key, $transactions, $lifetime, $cache_opts);


	return 'foxy';

} else {
	$err_msg = 'Failed to Proccessed Transaction Data Feed.Please check Error Logs on Foxycart Admin';
	$error_msgs[] = $err_msg;
	$modx->log($level,$err_msg,$this->log_target,__CLASS__.'::'.__FUNCTION__,__FILE__,__LINE__);
	return 'error';
}
