<?php
/**
* Foxycart XML Data Feed Class (Included on Moxycart Modx Extra)
* 
* --------------------------------------------------
* This file will decrypt the posted data from Foxycart Data Feed URL
* Set on your foxycart store dashboard
* and processed the decrypted data to store on database tables
*
* For testing, this class will also create a cache files like
* decrypted and encrypted data files so that we can easily pass this cache key 
* and processed the data without going through foxycart Ecommerce Process
* --------------------------------------------------
**/
class FC_Datafeed {

	/** @var $modx modX */
    public $modx;

    public $lifetime;
    public $cache_opts;

    public $error_msgs = array();

    private $core_path;
    private $log_target;


    public function __construct(&$modx) 
    {
        $this->modx =& $modx;       
		$this->core_path = $this->modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
		$cache_dir = 'moxycart_datafeed';
		$this->lifetime = 0;
		$this->cache_opts = array(xPDO::OPT_CACHE_KEY => $cache_dir);
		$this->log_target = array(
		    'target'=>'FILE',
		    'options' => array(
		        'filename'=>'foxycart.log'
		    )
		); 
		$this->__required_class();
    }

    /**
	* Private function __required_class
	* This will include rc4crypt, SimpleXMLElement adn DomDocument Class
	* Set error message on Log file
    **/
    private function __required_class() 
    {
    	$required_classes = array('rc4crypt','SimpleXMLElement','DomDocument');
    	$rc4crypt_path = $this->core_path . 'components/moxycart/model/moxycart/rc4crypt.class.php';
		require_once($rc4crypt_path);

    	foreach($required_classes as $c) {
    		if( !class_exists($c)) {
    		  $this->__log_errors('Missing Required '. $c .' Class');
			}
    	}
    }

    /**
	* Private function __required_class
	* This will include rc4crypt, SimpleXMLElement adn DomDocument Class
	* Set error message on Log file
    **/
    private function __log_errors($err_msg,$level=1) 
    {
		$this->error_msgs[] = $err_msg;
		$this->modx->log($level,$err_msg,$this->log_target,__CLASS__.'::'.__FUNCTION__,__FILE__,__LINE__);
    }

    /**
	* decrypt function
	* Decrypt data using rc4crypt class
	* @param string $data
	* @return xml $xml
    **/
    public function decrypt($data,$api_key) {
    	$rc4crypt = new rc4crypt();
    	$FoxyData_encrypted = urldecode($data);
		$FoxyData_decrypted = $rc4crypt->decrypt($api_key,$FoxyData_encrypted);
		$xml = new SimpleXMLElement($FoxyData_decrypted);
		return $xml;
    }
    /**
	* get_datafeed function
	* get the posted data from foxycart datafeed url
	* URL was set on your foxycart stopre dashboard
	* create a cache files for encrypted and decrypted data
	* @return string 'foxy' if success or errors if failed
    **/
    public function get_datafeed() 
    {
    	$api_key = $this->modx->getOption('moxycart.api_key'); // your foxy cart datafeed key
    	if(empty($api_key)) {
    		$this->__log_errors('Foxycart API Key is empty on your System Setting');
    	}

		// You can change the following data if you want to customize what data gets written.
		if($data = $this->modx->getOption('FoxyData', $_POST)) {
			$xml = $this->decrypt($data,$api_key);

			// processed the xml
			$this->parseXML($xml);

			return 'foxy';

		} else {
			$this->__log_errors('Failed to Proccessed Transaction Data Feed.Please check Error Logs on Foxycart Admin');
			return 'error';
		}
    }

    /**
    * parse_xml function
    * Parsed and Processed the passed xml data
    * @param xml $xml
    **/
    public function parseXML($xml) {
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

			// create cache files
			$this->cached_files();
		}
    }


    public function cached_files($xml,$transaction_id) {
    	// store files on local dir
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($xml->asXML());
			
		    $encrypted_cache_key = 'encrypted_txn_'.$transaction_id;
		    $this->modx->cacheManager->set($encrypted_cache_key, $FoxyData_encrypted, $$this->lifetime, $$this->cache_opts);

		    $decrypted_cache_key = 'decrypted_txn_'.$transaction_id;
		    $this->modx->cacheManager->set($decrypted_cache_key, $dom->saveXML(), $$this->lifetime, $this->$cache_opts);
    }


}
