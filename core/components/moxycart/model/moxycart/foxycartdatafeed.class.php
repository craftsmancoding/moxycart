<?php
/**
* Foxycart XML Data Feed Class (Included on Moxycart Modx Extra)
* 
* --------------------------------------------------
* This file will decrypt the posted data from Foxycart Data Feed URL
* Set on your foxycart store dashboard
* and return the decrypted data as an array 
*
* --------------------------------------------------
**/

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
		return $xml;
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
