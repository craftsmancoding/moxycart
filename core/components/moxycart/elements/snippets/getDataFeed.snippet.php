<?php
/** * Parses the FoxyCart XMl Data Feed into local database records 
 * (See all tables using the "foxy_" prefix.
 *
 * 
 * @link http://wiki.foxycart.com/integration:xml:xml_to_simple_csv
 * @package moxycart
 */

$log_level = $modx->getOption('log_level',$scriptProperties, $modx->getOption('log_level'));
$debug = (int) $modx->getOption('debug',$_GET); // Use this to load up some sample XML

$modx->setLogLevel($log_level);

$log = array(
    'target'=>'FILE',
    'options' => array(
        'filename'=>'foxycart.log'
    )
);

$api_key = $modx->getOption('moxycart.api_key'); // your foxy cart datafeed key
if(empty($api_key)) {
	$err_msg = 'moxycart.api_key is not set in your System Settings. Paste your Foxycart API key there before continuing.';
    $modx->log(xPDO::LOG_LEVEL_ERROR,$err_msg,$log,'getDataFeed Snippet',__FILE__,__LINE__);
    return $err_msg;
}

// Other tests go here ??

// Check for the post back
if($encrypted_data = $modx->getOption('FoxyData', $_POST)) {

    $modx->log(xPDO::LOG_LEVEL_DEBUG,'FoxyData detected',$log,'getDataFeed Snippet',__FILE__,__LINE__);
    
    $core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
    $modx->addPackage('moxycart',$core_path.'components/moxycart/model/','moxy_');
    require_once($core_path . 'components/moxycart/model/moxycart/foxycartdatafeed.class.php');
    require_once($core_path . 'components/moxycart/model/moxycart/rc4crypt.class.php');
    
    $rc4crypt = new rc4crypt();
    $fc_datafeed = new FC_Datafeed($rc4crypt);

	$FoxyData_decrypted = $fc_datafeed->decrypt($encrypted_data,$api_key);
	
	// uniquely identifies the payload so we don't store the same thing twice
    $md5 = md5($FoxyData_decrypted); 
    $Foxydata = $modx->getObject('Foxydata', array('md5'=>$md5));
    
    if ($Foxydata) {
        $modx->log(xPDO::LOG_LEVEL_ERROR,'Existing FoxyData detected ('.$Foxydata->get('foxydata_id').'). XML not re-parsed. ',$log,'getDataFeed Snippet',__FILE__,__LINE__);
        // ??? TODO ???
        // If you're here, it means your transactions either failed to save
        // or you DID save them and did not return the "foxy" success message.
    }
    // We don't have this stored!  Create a copy of the  data.
    else {
    
        $modx->log(xPDO::LOG_LEVEL_DEBUG,'New data signature detected: '.$md5,$log,'getDataFeed Snippet',__FILE__,__LINE__);
        
        // Start storing the data (maybe put this into the datafeed class?)
        $Foxydata = $modx->newObject('Foxydata');
        $Foxydata->set('md5', $md5);
        $Foxydata->set('xml', $FoxyData_decrypted);
        if (!$Foxydata->save()) {
            $modx->log(xPDO::LOG_LEVEL_ERROR,'Foxydata failed to save!',$log,'getDataFeed Snippet',__FILE__,__LINE__);
            return 'Failed to save Foxydata post!';
        }
    }
    
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
                    // Call per-product hook
                    $options[] = $TransactionDetailOption;
                }
                $TransactionDetail->addMany($options);
            }
            $details[] = $TransactionDetail;
        }
        $Transaction->addMany($details);

        // Call per-transaction hook                
        if(!$Transaction->save()) {
            $modx->log(xPDO::LOG_LEVEL_ERROR,'Failed to save transaction for Foxydata ('.$Foxydata->get('foxydata_id').').',$log,'getDataFeed Snippet',__FILE__,__LINE__);
            return 'There was a problem saving transactional data.';
        }
        $modx->log(xPDO::LOG_LEVEL_DEBUG,'Transaction ('.$Transaction->getPrimaryKey().') saved successfully with all related data.',$log,'getDataFeed Snippet',__FILE__,__LINE__);
    }
    
    // Per Foxycart's rules
    return 'foxy';
}
else {
    $url = $modx->makeUrl($modx->resource->get('id'),'','','full');
    return '<div style="margin:10px; padding:20px; border:1px solid green; background-color:#00CC66; border-radius: 5px; width:500px;">Welcome to Moxycart.  This page is contains the getDataFeed Snippet. In your 
    Foxycart dashboard, point the datafeed to this URL: '.$url.'</div>';
}
