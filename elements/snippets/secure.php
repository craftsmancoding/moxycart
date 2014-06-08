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
 * See:
 *  https://wiki.foxycart.com/v/1.1/hmac_validation
 *  https://github.com/FoxyCart/FoxyCart-Cart-Validation--PHP
 *
 */
$modx->log(\modX::LOG_LEVEL_DEBUG, "scriptProperties:\n".print_r($scriptProperties,true),'','Snippet secure');

$api_key = $modx->getOption('moxycart.api_key');

if (empty($api_key)) {
    if ($modx->resource) {
        $modx->log(\modX::LOG_LEVEL_ERROR, 'Missing moxycart.api_key','','Snippet secure', 'Resource: '.$modx->resource->get('id'));
    }
    else {
        $modx->log(\modX::LOG_LEVEL_ERROR, 'Missing moxycart.api_key','','Snippet secure', 'Product: '.$modx->getPlaceholder('product_id'));
    }
    return 'ERROR: moxycart.api_key missing';
}
$var_code = $api_key;
$var_name = $name; // The name, e.g. "price"
$var_value = $input; // The value, e.g. "1.99"

$encodingval = htmlspecialchars($var_code) . htmlspecialchars($var_name) . htmlspecialchars($var_value);
return '||'.hash_hmac('sha256', $encodingval, $api_key).($var_value == '--OPEN--' ? '||open' : ''); 
/*EOF*/