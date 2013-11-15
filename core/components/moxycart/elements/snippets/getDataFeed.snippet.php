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
$class_path = $core_path . 'components/moxycart/model/moxycart/rc4crypt.class.php';
require_once($class_path);

$rc4crypt = new rc4crypt();
// ======================================================================================
// CHANGE THIS DATA:
// Set the key you entered in your FoxyCart.com admin.
// ======================================================================================
$api_key = $modx->getOption('moxycart.api_key'); // your foxy cart datafeed key


// The filename that you'd like to write to.
$cache_dir = 'moxycart_datafeed';
$lifetime = 0;
$cache_opts = array(xPDO::OPT_CACHE_KEY => $cache_dir); 

// You can change the following data if you want to customize what data gets written.
if($modx->getOption('FoxyData', $_POST)) {
	// Get the raw data and initialize variables
	$FoxyData_encrypted = urldecode($_POST["FoxyData"]);
	$FoxyData_decrypted = $rc4crypt->decrypt($api_key,$FoxyData_encrypted);
	$xml = new SimpleXMLElement($FoxyData_decrypted);

	foreach ($xml->transactions->transaction as $transaction) {

		// Loop through to get the product code, name, customer name, date, and transaction ID
		$transaction_customer_name = $transaction->customer_last_name . ', ' . $transaction->customer_first_name;

		$transaction_date = $transaction->date;
		$transaction_id = $transaction->id;
		foreach ($transaction->transaction_details->transaction_detail as $product) {

			// Get the product details
			$product_code = $product->product_code;
			$product_name = $product->product_name;
			$product_quantity = $product->product_quantity;
			if ($product_code == '') {
				$product_code = $product_name;
			}
			/*
			* Processed the data here 
			* Store to DB tbl etc
			*/
			/*
			$output .= 'Customer: ' . $transaction_customer_name . "\n";
			$output .= 'QTY: ' .  $product_quantity. "\n";
			$output .= 'Product Name: ' .  $product_name. "\n";
			$output .= 'Product Code: ' .  $product_code. "\n";
			$output .= '--------------------------------------------' . "\n";
			*/
		}
	}


	// store files on local dir
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($xml->asXML());
	
    $encrypted_cache_key = 'encrypted_txn_'.$transaction_id;
    $modx->cacheManager->set($encrypted_cache_key, $FoxyData_encrypted, $lifetime, $cache_opts);

    $decrypted_cache_key = 'decrypted_txn_'.$transaction_id;
    $modx->cacheManager->set($decrypted_cache_key, $dom->saveXML(), $lifetime, $cache_opts);

	return 'foxy';

} else {
	$log_target = array(
	    'target'=>'FILE',
	    'options' => array(
	        'filename'=>'foxycart.log'
	    )
	); 
	$modx->log(xPDO::LOG_LEVEL_ERROR,'Failed to Proccessed Data Feed. Please check Error Logs on Foxycart Admin',$log_target);
	return 'error';
}