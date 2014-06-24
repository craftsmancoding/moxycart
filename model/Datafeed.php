<?php
/**
 * Foxycart XML Data Feed Class 
 * 
 * We have this isolated here partly because we're lazy and haven't moved this 
 * and partly in hopes that we could test the decryption process separately.
 * 
 * Most of the work here happens inherently at the ORM level: because these objects
 * are defined there with the attribute names that match the XML *exactly*, we can 
 * leverage the fromArray() function and array type-casting to quickly convert the 
 * SimpleXMLElement Objects into MODX database records.
 *
 * @package moxycart
 */
namespace Foxycart;
class Datafeed {

    // Required classes:
    public $modx;
	public $rc4crypt;
    public $callbacks_raw = array();
    public $callbacks = array();

    
    /** 
     * Some dependency injection here would be nice, but we can't really inject 
     * the SimpleXMLElement or DOMDocument classes due to how we need to pass
     * runtime arguments to their constructors.  They are really operating more like
     * functions in this regard.
     *
     * @param object instance modx
     * @param object instance rc4crypt
     *
     */
	public function __construct(\modX &$modx,\rc4crypt $rc4crypt) {
        $this->modx = $modx;
		$this->rc4crypt = $rc4crypt;
	}
	
    /**
	 * Decrypt posted data to XML string using rc4crypt class.
	 *
	 * @param string $data encrypted
	 * @param string $api_key (used to decrypt the $data)
	 * @return xml $xml
     */
    public function post2xml($data,$api_key) {
    	$FoxyData_encrypted = urldecode($data);
		$FoxyData_decrypted = $this->rc4crypt->decrypt($api_key,$FoxyData_encrypted);
		$xml = new \SimpleXMLElement($FoxyData_decrypted);
		$dom = new \DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());

		return $dom->saveXML();
    }

    /** 
     * @param array of SimpleXMLElement objects
     * @return array of MODX Tax objects
     */
    public function getAttributes($array) {
        $attributes = array();
        foreach($array as $a) {
            $Attribute = $this->modx->newObject('Attribute');
            $Attribute->fromArray((array) $a);
            $attributes[] = $Attribute;
        }
        return $attributes;
    }
    
    /** 
     * @param array of SimpleXMLElement objects
     * @return array of MODX Tax objects
     */
    public function getCustomFields($array) {
        $custom_fields = array();
        foreach($array as $cf) {
            $CustomField = $this->modx->newObject('CustomField');
            $CustomField->fromArray((array) $cf);
            $custom_fields[] = $CustomField;
        }            
        return $custom_fields;
    }
    
    /** 
     * @param array of SimpleXMLElement objects
     * @return array of MODX Tax objects
     */
    public function getDiscounts($array) {
        $discounts = array();
        foreach($array as $discount) {
            $Discount = $this->modx->newObject('Discount');
            $Discount->fromArray((array) $discount);
            $discounts[] = $Discount;
        }
        return $discounts;
    }
    
    public function getShiptoAddresses($array) {
        $shipto_addresses = array();
        foreach($array as $sta) {
            $ShiptoAddress = $this->modx->newObject('ShiptoAddress');
            $ShiptoAddress->fromArray((array) $sta);
            $shipto_addresses[] = $ShiptoAddress;
        }            
        return $shipto_addresses;
    }
    
    /** 
     * @param array of SimpleXMLElement objects
     * @return array of MODX Tax objects
     */
    public function getTaxes($array) {
        $taxes = array();
        foreach($array as $tax) {
            $Tax = $this->modx->newObject('Tax');
            $Tax->fromArray((array) $tax);
            $taxes[] = $Tax;
        }
        return $taxes;
    }

    /** 
     * @param array of SimpleXMLElement objects
     * @return array of MODX Tax objects
     */
    public function getTransactions($array) {
        $transactions = array();
        foreach($array as $t) {
            $Transaction  = $this->modx->newObject('Transaction');
            $Transaction->fromArray((array)$t);            
            // Long form here to avoid E_STRICT PHP INFO re "Only variables should be passed by reference"
            if (isset($t->taxes->tax)) {
                $tmp = $this->getTaxes($t->taxes->tax);
                $Transaction->addMany($tmp);
            }
            if (isset($t->discounts->discount)) {
                $tmp = $this->getDiscounts($t->discounts->discount);
                $Transaction->addMany($tmp);
            }
            if (isset($t->custom_fields->custom_field)) {
                $tmp = $this->getCustomFields($t->custom_fields->custom_field);
                $Transaction->addMany($tmp);
            }
            if (isset($t->attributes->attribute)) {
                $tmp = $this->getAttributes($t->attributes->attribute);
                $Transaction->addMany($tmp);
            }
            if (isset($t->shipto_addresses->shipto_address)) {
                $tmp = $this->getShiptoAddresses($t->shipto_addresses->shipto_address);
                $Transaction->addMany($tmp);
            }
            if (isset($t->transaction_details->transaction_detail)) {
                $tmp = $this->getTransactionDetails($t->transaction_details->transaction_detail);
                $Transaction->addMany($tmp);
            }
            $this->executeCallbacks('transaction',$Transaction->toArray());
            $transactions[] = $Transaction;    
        }

        return $transactions;
    }
    
    /** 
     * This node is a bit special because we normalize the transaction_detail_option bits and append them
     * as additional key/value pairs for the TransactionDetail callbacks.
     *
     * @param array of SimpleXMLElement objects
     * @return array of MODX Tax objects
     */
    public function getTransactionDetails($array) {
        $details = array();
        foreach($array as $d) {
            $TransactionDetail = $this->modx->newObject('TransactionDetail');
            $TransactionDetail->fromArray((array) $d);
            $details = $TransactionDetail->toArray();
            $options = array();
            if (isset($d->transaction_detail_options->transaction_detail_option)) {
                foreach($d->transaction_detail_options->transaction_detail_option as $o) {
                    $TransactionDetailOption = $this->modx->newObject('TransactionDetailOption');
                    $TransactionDetailOption->fromArray((array) $o);
                    // product_option_name
                    // product_option_value
                    $details[ $TransactionDetailOption->get('product_option_name') ] 
                        = $TransactionDetailOption->get('product_option_value');
                    $options[] = $TransactionDetailOption;
                }
                $TransactionDetail->addMany($options);
            }
            $this->executeCallbacks('product',$details);
            $details[] = $TransactionDetail;
        }
        
        return $details; 
    }
    
    /**
     *
     * @return array
     */
    public function getTransactionDetailOption($xmlarray) {
        $options = array();
        foreach($xmlarray as $o) {
            $TransactionDetailOption = $this->modx->newObject('TransactionDetailOption');
            $TransactionDetailOption->fromArray((array) $o);
            $options[] = $TransactionDetailOption;
        }
        return $options;
    }
    
    /**
     * 
     * See http://rtfm.modx.com/xpdo/2.x/class-reference/xpdoobject/related-object-accessors/addmany
     * These all have a one-to-many relationship with transactions, e.g. one transation -> many discounts
     *
     * @param string $xml body
     * @param string $api_key to uniquely identify the source of the xml, e.g. by store id, or "test"
     * @return string foxy on success, Error message on fail
     */
    public function saveFoxyData($xml,$api_key='') {

        $Foxydata = $this->modx->newObject('Foxydata');
        $Foxydata->set('md5', md5(uniqid()));
        $Foxydata->set('xml', $xml);
        $Foxydata->set('type', 'FoxyData'); // or FoxySubscriptionData
        $Foxydata->set('api_key', $api_key);
    
        // Converts string to a hierarchy of SimpleXMLElement Objects
        $xml = simplexml_load_string($xml, null, LIBXML_NOCDATA);

        if (!isset($xml->transactions->transaction)) {
            throw new \Exception('Invalid Foxycart XML body');
        }

        $Foxydata->addMany($this->getTransactions($xml->transactions->transaction));
        
        if (!$Foxydata->save()) {
            return 'Failed to save Foxydata post!';
        }
        $this->executeCallbacks('postback',$Foxydata->toArray());
        return 'foxy';
    }



    /**
     * TODO
     *
     */
    public function saveFoxySubscriptionData($xml) {
        return 'foxy';
    }

    /**
     * Register a callback function to fire for different events
     *
     * E.g. to register a MODX snippet:
     *
     *      registerCallback('product',array($this->modx,'runSnippet'),array('MySnippet'));
     *
     * Any supplied $args come *before* the array of data for the given event. E.g. in a MODX snippet
     * hooked to the "product" event (as pictured above), it translates to:
     *
     *      $this->modx->runSnippet('MySnippet', $ProductDetails);
     *
     * In a raw callback, e.g.:
     *
     *      registerCallback('postback','my_callback',array('one','two'));
     *
     * This would translate to:
     *      my_callback('one','two', $Foxydata->toArray());
     *
     * Each callback gets passed the array representing the relevant object.
     *
     * Events:
     *
     *  postback - fires once for every postback from Foxycart
     *  transaction - fires for each transaction in the XML
     *  product - fires for each product 
     *
     * @param string $event postback|transaction|product 
     * @param mixed any valid callback
     * @param array any additional args to pass to the callback. These args will come *before* the 
     *      data from the XML (e.g. before transation data).  This somewhat awkward construct was 
     *      built specifically for the parseFoxycartDatafeed Snippet so it could call other Snippets
     *      as callbacks.  See example above.
     */
    public function registerCallback($event, $callback, $args=array()) {
        if (!is_scalar($event)) {
            throw new \Exception('Invalid callback event');
        }
        if (!is_callable($callback)) {
            throw new \Exception('Invalid callback');
        }
        $event = strtolower($event);
        if (in_array($event, array('postback','transaction','product'))) {
            $this->modx->log(\xPDO::LOG_LEVEL_DEBUG,'Registering callback for event:'.$event,'',__CLASS__,__FUNCTION__);
            $this->callbacks_raw[$event][] = array('callback'=>$callback,'args'=>$args);
        }
        else {
            throw new \Exception('Invalid callback event');
        }
    }
    
    /**
     * 
     * @param string $event name
     * @param array $data for relevent event, e.g. Foxydata->toArray()
     */
    public function executeCallbacks($event,$data) {
        if (!isset($this->callbacks_raw[$event])) {
            $this->modx->log(\xPDO::LOG_LEVEL_DEBUG,'No callbacks registered for event:'.$event,'',__CLASS__,__FUNCTION__);
            return;
        }
        foreach ($this->callbacks_raw[$event] as $cb) {
            $this->modx->log(\xPDO::LOG_LEVEL_INFO,'Executing callback for event:'.$event,'',__CLASS__,__FUNCTION__);
            $cb['args'][] = $data; // push data onto the end.
            call_user_func_array($cb['callback'], $cb['args']);
        }
    }
}
