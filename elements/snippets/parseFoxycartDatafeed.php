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
 * [[!parseFoxycartDatafeed? &product_hooks=`UpdateInventory` &transaction_hooks=`createUser`]]
 *
 * PARAMS
 *
 * &product_hooks (string) comma-separated Snippet(s) to execute for each product
 *       that comes through the datafeed.
 * &transaction_hooks (string) comma-separated Snippet(s) to execute for each transaction
 *      that comes through the datafeed.
 * &postback_hooks (string) comma-separated Snippet(s) to execute for each post-back.
 *
 * &api_key (string) not the actual key, but the name of the System Setting containing the
 *      API key.  Default: moxycart.api_key
 *
 * &log_level (int) use this to provide more verbose logging for this snippet. 
 *      Defaults to the system log_level setting.
 *
 * @package moxycart
 */
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('parseFoxycartDatafeed',$scriptProperties);

$modx->addPackage('foxycart',$core_path.'model/orm/','foxy_');

$product_hooks_tmp = $modx->getOption('product_hooks',$scriptProperties);
$transaction_hooks_tmp = $modx->getOption('transaction_hooks',$scriptProperties);
$postback_hooks_tmp = $modx->getOption('postback_hooks',$scriptProperties);
$api_key = $modx->getOption('api_key', $scriptProperties, $api_key = $modx->getOption('moxycart.api_key'));

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

// For the log
$msg = "parseFoxycartDatafeed running with the following parameters:\n";
$msg .= "core_path: {$core_path}\n";
$msg .= "log_level {$log_level}\n";
$msg .= "product_hooks: ".print_r($product_hooks,true)."\n";
$msg .= "transaction_hooks: ".print_r($transaction_hooks,true)."\n";
$msg .= "postback_hooks: ".print_r($postback_hooks,true)."\n";

$modx->log(modX::LOG_LEVEL_DEBUG, $msg, $log, 'parseFoxycartDatafeed',__FILE__,__LINE__);

if(empty($api_key)) {
	$err_msg = 'moxycart.api_key is not set in your System Settings. Paste your Foxycart API key there before continuing.';
    $modx->log(modX::LOG_LEVEL_ERROR,$err_msg,$log,'parseFoxycartDatafeed',__FILE__,__LINE__);
    return $err_msg;
}

// Other tests go here ??

// Check for the post back
// Note: the subscription data feed sets this attribute: FoxySubscriptionData
if($encrypted_data = (isset($modx->request->parameters['POST']['FoxyData']))? $modx->request->parameters['POST']['FoxyData']:null) {
    $Datafeed = new \Foxycart\Datafeed($modx, new \rc4crypt());
    foreach ($postback_hooks as $hook) {
        $Datafeed->registerCallback('postback',array($modx,'runSnippet'), array($hook));
    }
    foreach ($transaction_hooks as $hook) {
        $Datafeed->registerCallback('transaction',array($modx,'runSnippet'), array($hook));
    }
    foreach ($product_hooks as $hook) {
        $Datafeed->registerCallback('product',array($modx,'runSnippet'), array($hook));
    }
    
    $xml = $Datafeed->post2xml($encrypted_data, $api_key);
    return $Datafeed->saveFoxyData($xml,$api_key);
}
else {
    // This won't be set during command-line testing
    $url = ($modx->resource) ? $modx->makeUrl($modx->resource->get('id'),'','','full') : '#';
    return '<!-- random string for testing: vmTsGsATTX6XrRfEwqpAnk8DHqjBhGPZD -->
    <div style="margin:10px; padding:20px; border:1px solid green; background-color:#00CC66; border-radius: 5px; width:800px;">Welcome to <a href="https://github.com/craftsmancoding/moxycart/wiki/Datafeed">Moxycart</a>.  This page is contains the parseFoxycartDatafeed. In your 
    Foxycart dashboard, point the datafeed to this URL: <br/>
    <input type="text" value="'.$url.'" size="100"/></div>';
}