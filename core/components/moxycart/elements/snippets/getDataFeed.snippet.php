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
$api_key = 'fsGnHyMEe1efkAYbLWkGTixU1BgD66BgscuZAbzI2DNd8GiKxxlhFI6wecZb'; // your foxy cart datafeed key


// The filename that you'd like to write to.
$folder = 'moxycart_data/';


// You can change the following data if you want to customize what data gets written.
if($modx->getOption('FoxyData', $_POST)) {
	// Get the raw data and initialize variables
	$output = '';
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
			$output .= 'Customer: ' . $transaction_customer_name . "\n";
			$output .= 'QTY: ' .  $product_quantity. "\n";
			$output .= 'Product Name: ' .  $product_name. "\n";
			$output .= 'Product Code: ' .  $product_code. "\n";
			$output .= '--------------------------------------------' . "\n";
		}
	}

	// Write it to a file for now
	$fh = fopen($folder . time() . '.txt', 'a') or die("Couldn't open file for writing. Check your file and folder ownerships and permissions."); 
	fwrite($fh, $output);
	fclose($fh);

	return 'foxy';

} else {
	$fh = fopen($folder . 'errors.txt', 'a') or die("Couldn't open $file for writing!"); 
	fwrite($fh, 'error occurred on ' . time());
	fclose($fh);
	return 'error';
}