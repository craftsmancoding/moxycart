<?php
/**
 *
 * @name parseFoxycartDatafeed
 * @description Parses the FoxyCart XML Data Feed into local database records. Place this Snippet on one page and paste its URL into your Foxycart admin panel. Logs to foxycart.log
 * 
 * Parses the FoxyCart XML Data Feed into local database records. The data objects are 
 * structured hierarchically, but the save() event is delayed until the very end. 
 * (See all tables using the "foxy_" prefix).
 * This snippet logs to the foxycart.log 
 *
 * You can tie any snippet you want to hook into any of 3 events:
 *  per-product (via &product_hooks)
 *  per-transaction (via &transaction_hooks)
 *  per-postback (via &postback_hooks)
 *
 *  The Snippet will receive $scriptParameters corresponding to the relevant node of XML
 *  and the Snippet must return a message that evaluates to true on success, boolean false
 *  on fail.
 *
 * USAGE
 *
 * [[!parseFoxycartDatafeed? &product_hooks=`UpdateInventory` &transaction_hooks=`CreateUser`]]
 *
 * PARAMS
 *
 * &product_hooks (string) comma-separated Snippet(s) to execute for each product
 *       that comes through the datafeed.
 * &transaction_hooks (string) comma-separated Snippet(s) to execute for each transaction
 *      that comes through the datafeed.
 * &log_level (int) use this to provide more verbose logging for this snippet. 
 *      Defaults to the system log_level setting.
 *
 * @package moxycart
 */
$core_path = $modx->getOption('moxycart.core_path','',MODX_CORE_PATH);
require_once($core_path . 'components/moxycart/model/foxycart/rc4crypt.class.php');
$modx->addPackage('foxycart',$core_path.'components/moxycart/model/','foxy_');

$product_hooks_tmp = $modx->getOption('product_hooks',$scriptProperties);
$transaction_hooks_tmp = $modx->getOption('transaction_hooks',$scriptProperties);
$postback_hooks_tmp = $modx->getOption('postback_hooks',$scriptProperties);

$log_level = $modx->getOption('log_level',$scriptProperties, $modx->getOption('log_level'));

// "Dear Developer: exploding a null does NOT make an empty array!  FU. Sincerely, PHP"
$product_hooks = ($product_hooks_tmp) ? explode(',',$product_hooks_tmp) : array(); 
$transaction_hooks = ($transaction_hooks_tmp) ? explode(',',$transaction_hooks_tmp) : array();
$postback_hooks = ($postback_hooks_tmp)? explode(',',$postback_hooks_tmp) : array();

// Set up Logging
$modx->setLogLevel($log_level);
$log = array(
    'target'=>'FILE',
    'options' => array(
        'filename'=>'foxycart.log'
    )
);

// For the record
$msg = "parseFoxycartDatafeed running with the following parameters:\n";
$msg .= "core_path: {$core_path}\n";
$msg .= "log_level {$log_level}\n";
$msg .= "product_hooks: ".print_r($product_hooks,true)."\n";
$msg .= "transaction_hooks: ".print_r($transaction_hooks,true)."\n";
$msg .= "postback_hooks: ".print_r($postback_hooks,true)."\n";

$modx->log(modX::LOG_LEVEL_DEBUG, $msg, $log, 'parseFoxycartDatafeed',__FILE__,__LINE__);

$api_key = $modx->getOption('moxycart.api_key'); // your foxy cart datafeed key
if(empty($api_key)) {
	$err_msg = 'moxycart.api_key is not set in your System Settings. Paste your Foxycart API key there before continuing.';
    $modx->log(modX::LOG_LEVEL_ERROR,$err_msg,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
    return $err_msg;
}

// Other tests go here ??

// Check for the post back
if($encrypted_data = $modx->getOption('FoxyData', $_POST)) {

    $modx->log(modX::LOG_LEVEL_DEBUG,'FoxyData detected',$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
    
    $core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);

    // Decrypt the posted Data : Todo try/catch?
    $rc4crypt = new rc4crypt();
	$FoxyData_decrypted = $rc4crypt->decrypt($api_key,urldecode($encrypted_data));
	$xml = new SimpleXMLElement($FoxyData_decrypted);
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($xml->asXML());
	$FoxyData_decrypted = $dom->saveXML();
	
	
	// uniquely identifies the payload so we don't store the same thing twice
    $md5 = md5($FoxyData_decrypted); 
    $Foxydata = $modx->getObject('Foxydata', array('md5'=>$md5));
    
    if ($Foxydata) {
        $msg = 'Existing FoxyData detected ('.$Foxydata->get('foxydata_id').'). Data will NOT be re-parsed.  
        This condition might have been caused due to a fatal error in this script or any referenced hooks
        before the "foxy" success message was returned. Reparsing data could create problems with your inventory.';
        $modx->log(modX::LOG_LEVEL_ERROR,$msg,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
        // If you're here, it means your transactions either failed to save
        // or you DID save them and did not return the "foxy" success message.
        // Either way it's a problem.  Reparsing could create problems with your inventory.
        return $msg;
    }
    
    // We don't have this stored yet!  Create a copy of the  data.
    
    $modx->log(modX::LOG_LEVEL_DEBUG,'New data signature detected: '.$md5,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
        
    // Start storing the data (maybe put this into the datafeed class?)
    $Foxydata = $modx->newObject('Foxydata');
    $Foxydata->set('md5', $md5);
    $Foxydata->set('xml', $FoxyData_decrypted);
    
    $transactions = array();
    
    $xml = simplexml_load_string($FoxyData_decrypted, null, LIBXML_NOCDATA);
    
    foreach($xml->transactions->transaction as $t) {
        $Transaction  = $modx->newObject('Transaction');
        $Transaction->fromArray((array)$t);
        $Transaction->set('foxydata_id', $Foxydata->get('foxydata_id'));
        
        // See http://rtfm.modx.com/xpdo/2.x/class-reference/xpdoobject/related-object-accessors/addmany
        // These all have a one-to-many relationship with transactions, e.g. one transation -> many discounts
        $taxes = array();
        $discounts = array();
        $custom_fields = array();
        $attributes = array();
        $details = array();
        $shipto_addresses = array();

        if (isset($t->taxes->tax)) {
            foreach($t->taxes->tax as $tax) {
                $Tax = $modx->newObject('Tax');
                $Tax->fromArray((array) $tax);
                $taxes[] = $Tax;
            }
        }
        $Transaction->addMany($taxes);

        if (isset($t->discounts->discount)) {
            foreach($t->discounts->discount as $discount) {
                $Discount = $modx->newObject('Discount');
                $Discount->fromArray((array) $discount);
                $discounts[] = $Discount;
            }
        }
        $Transaction->addMany($discounts);        
        
        if (isset($t->custom_fields->custom_field)) {
            foreach($t->custom_fields->custom_field as $cf) {
                $CustomField = $modx->newObject('CustomField');
                $CustomField->fromArray((array) $cf);
                $custom_fields[] = $CustomField;
            }            
        }
        $Transaction->addMany($custom_fields);

        if (isset($t->attributes->attribute)) {
            foreach($t->attributes->attribute as $a) {
                $Attribute = $modx->newObject('Attribute');
                $Attribute->fromArray((array) $a);
                $attributes[] = $Attribute;
            }            
        }
        $Transaction->addMany($attributes);
        
        if (isset($t->shipto_addresses->shipto_address)) {
            foreach($t->shipto_addresses->shipto_address as $sta) {
                $ShiptoAddress = $modx->newObject('ShiptoAddress');
                $ShiptoAddress->fromArray((array) $sta);
                $shipto_addresses[] = $ShiptoAddress;
            }            
        }
        $Transaction->addMany($shipto_addresses);
        
        // our products
        foreach( $t->transaction_details->transaction_detail as $d) {
            $TransactionDetail = $modx->newObject('TransactionDetail');
            $TransactionDetail->fromArray((array) $d);
            $options = array();
            if (isset($d->transaction_detail_options->transaction_detail_option)) {
                foreach($d->transaction_detail_options->transaction_detail_option as $o) {
                    $TransactionDetailOption = $modx->newObject('TransactionDetailOption');
                    $TransactionDetailOption->fromArray((array) $o);
                    $options[] = $TransactionDetailOption;
                }
                $TransactionDetail->addMany($options);
            }
            $details[] = $TransactionDetail;

        }
        $Transaction->addMany($details);

        $transactions[] = $Transaction;


        //! Hooks (run only after successful save)
        // Call per-product hooks
        foreach ($details as $TD) {
            foreach ($product_hooks as $hook) {
                $modx->log(modX::LOG_LEVEL_DEBUG,'Calling product-hook '.$hook,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
                if (!$msg = $modx->runSnippet(trim($hook),$TD->toArray())) {
                    $modx->log(modX::LOG_LEVEL_ERROR,'product-hook failed to execute: '.$hook,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
                }
                $modx->log(modX::LOG_LEVEL_DEBUG,'Completed product-hook '.$hook.' with result: '.$msg,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
            }
        }
        // Call per-transaction hooks
        foreach ($transaction_hooks as $hook) {
            $modx->log(modX::LOG_LEVEL_DEBUG,'Calling transaction-hook '.$hook,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
            if (!$msg = $modx->runSnippet(trim($hook),$Transaction->toArray())) {
                $modx->log(modX::LOG_LEVEL_ERROR,'transaction-hook failed to execute: '.$hook,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);            
            }
            $modx->log(modX::LOG_LEVEL_DEBUG,'Completed transaction-hook '.$hook.' with result: '.$msg,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
        }

        $modx->log(modX::LOG_LEVEL_DEBUG,'Transaction ('.$Transaction->getPrimaryKey().') saved successfully with all related data.',$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
    }

    // Call per-postback hooks
    foreach ($postback_hooks as $hook) {    
        $modx->log(modX::LOG_LEVEL_DEBUG,'Calling postback-hook '.$hook,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
        if (!$msg = $modx->runSnippet(trim($hook),$Foxydata->toArray())) {
            $modx->log(modX::LOG_LEVEL_ERROR,'postback-hook failed to execute: '.$hook,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);  
        }
        $modx->log(modX::LOG_LEVEL_DEBUG,'Completed postback-hook '.$hook.' with result: '.$msg,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);    
    }
    
    $Foxydata->addMany($transactions);

    if (!$Foxydata->save()) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Foxydata failed to save!',$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
        return 'Failed to save Foxydata post!';
    }    

    $modx->log(modX::LOG_LEVEL_DEBUG,'Success. foxy.'.$Foxydata->get('id'),$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
    
    // Per Foxycart's rules
    return 'foxy';
}
else {
    $url = $modx->makeUrl($modx->resource->get('id'),'','','full');
    return '<div style="margin:10px; padding:20px; border:1px solid green; background-color:#00CC66; border-radius: 5px; width:500px;">Welcome to <a href="https://github.com/craftsmancoding/moxycart/wiki/Datafeed">Moxycart</a>.  This page is contains the parseFoxycartDatafeed. In your 
    Foxycart dashboard, point the datafeed to this URL: <br/>
    <input type="text" value="'.$url.'" size="100"/></div>';
}