<?php
/**
 * Foxycart XML Data Feed Class 
 * 
 * We have this isolated here partly because we're lazy and haven't moved this 
 * and partly in hopes that we could test the decryption process separately.
 * 
 * @package moxycart
 */

class FC_Datafeed {

	public $rc4crypt;

	public function __construct(rc4crypt $rc4crypt) 
	{
		$this->rc4crypt = $rc4crypt;
	}
    /**
	* decrypt function
	* Decrypt data using rc4crypt class
	* @param string $data
	* @return xml $xml
    **/
    public function decrypt($data,$api_key) {
    	$FoxyData_encrypted = urldecode($data);
		$FoxyData_decrypted = $this->rc4crypt->decrypt($api_key,$FoxyData_encrypted);
		$xml = new SimpleXMLElement($FoxyData_decrypted);

		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());

		return $dom->saveXML();
    }


    /**
    * parse_xml function
    * Parsed and Processed the passed xml data
    * @param xml $xml
    * @return array $transactions
    **/
    public function parseXML($xml) {
    	$transactions = array();
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
				$transactions[] = array(
					'transaction_id'	=> $transaction_id,
					'first_name'	=> $transaction->customer_first_name,
					'last_name'	=> $transaction->customer_last_name,
					'product_code'	=> $product->product_code,
					'product_name'	=> $product->product_name,
					'product_quantity'	=> $product->product_quantity,
				);
			}
		}
		return $transactions;
    }

}
