<?php
/**
 * @name secure
 * @description Custom output filter for securely signing Foxycart forms.
 *
 * USAGE:
 *
 * Apply this output filter to securely sign single inputs in a Foxycart form.
 *
 * <input type="hidden" name="price" value="[[+price:secure]]" />
 *
 * It is possible to pass the api key via an option, e.g. 
 *  <input type="hidden" name="price" value="[[+price:secure=`your-api-key-here`]]" />
 *
 * This was added for 
 * See:
 *  https://wiki.foxycart.com/v/1.1/hmac_validation
 *  https://github.com/FoxyCart/FoxyCart-Cart-Validation--PHP
 *
 */
$modx->log(\modX::LOG_LEVEL_DEBUG, "scriptProperties:\n".print_r($scriptProperties,true),'','Snippet secure');
if (!$api_key = $options) {
    $api_key = $modx->getOption('moxycart.api_key');
}

if (empty($api_key)) {
    $modx->log(\modX::LOG_LEVEL_ERROR, 'Missing moxycart.api_key','','Snippet secure', 'Resource: '.$modx->resource->get('id'));
    return 'ERROR: moxycart.api_key missing';
}
$var_code = $api_key;
$var_value = $input; // The value, e.g. 1.99
$var_name = $name; // the name, e.g. price
// $options = ''; // anything passed to the filter

$encodingval = htmlspecialchars($var_code) . htmlspecialchars($var_name) . htmlspecialchars($var_value);
return '||'.hash_hmac('sha256', $encodingval, $api_key).($var_value == '--OPEN--' ? '||open' : ''); 
/*EOF*/