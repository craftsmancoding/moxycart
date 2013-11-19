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
$log_level = $modx->getOption('log_level',$scriptProperties, $modx->getOption('log_level'));
$debug = (int) $modx->getOption('debug',$_GET);

$log = array(
    'target'=>'FILE',
    'options' => array(
        'filename'=>'datafeed.log'
    )
);

$cache_dir = 'foxycart_datafeed';
$lifetime = 0;
$cache_opts = array(xPDO::OPT_CACHE_KEY => $cache_dir); 

$api_key = $modx->getOption('moxycart.api_key'); // your foxy cart datafeed key
if(empty($api_key)) {
	$err_msg = 'moxycart.api_key is not set in your System Settings. Paste your Foxycart API key there before continuing.';
    $modx->log($log_level,$err_msg,$log,__CLASS__.'::'.__FUNCTION__,__FILE__,__LINE__);
    return $err_msg;
}

// Other tests go here 

// Check for the post back
if($data = $modx->getOption('FoxyData', $_POST)) {
    
    require_once($core_path . 'components/moxycart/model/moxycart/foxycartdatafeed.class.php');
    require_once($core_path . 'components/moxycart/model/moxycart/rc4crypt.class.php');
    
    $rc4crypt = new rc4crypt();
    $fc_datafeed = new FC_Datafeed($rc4crypt);

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

	// store it as cache file for now
	$transaction_cache_key = 'txn_'.time();
    $modx->cacheManager->set($transaction_cache_key, $transactions, $lifetime, $cache_opts);

    // On success, Foxycart requires that you return this one word (nothing else):
	return 'foxy';

}
elseif ($modx->resource->Template) {
    $modx->log($log_level,'getDataFeed Snippet must be placed on a page that uses an empty template. Page ID '.$modx->resource->get('id'),$log,__CLASS__.'::'.__FUNCTION__,__FILE__,__LINE__);
    return '<div>In order for the getDataFeed Snippet to function properly, the page it is placed on
    cannot use a template: it must use an empty template.</div>';
}
else {
    $url = $modx->makeUrl($modx->resource->get('id'),'','','full');
    return '<div>Welcome to Moxycart.  This page is contains the getDataFeed Snippet. In your 
    Foxycart dashboard, point the datafeed to this URL: '.$url.'</div>';
}
