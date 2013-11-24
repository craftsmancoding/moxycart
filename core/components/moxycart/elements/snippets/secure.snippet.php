<?php
/**
 * secure
 *
 * Custom output filter for securely signing Foxycart forms.
 *
 * See:
 *  https://wiki.foxycart.com/v/1.1/hmac_validation
 *  https://github.com/FoxyCart/FoxyCart-Cart-Validation--PHP
 *
 * USAGE:
 *
 * Apply this output filter to securely sign single inputs in a Foxycart form.
 *
 * <input type="hidden" name="price" value="[[+price:secure]]" />
 */
$api_key = $modx->getOption('moxycart.api_key');

if (empty($api_key)) {
    return 'ERROR: moxycart.api_key missing';
}
$input = ''; // The value, e.g. 1.99
$name = ''; // the name, e.g. price
$options = ''; // anything passed to the filter

$encodingval = htmlspecialchars($var_code) . htmlspecialchars($var_name) . htmlspecialchars($var_value);
return '||'.hash_hmac('sha256', $encodingval, $api_key).($var_value == '--OPEN--' ? '||open' : ''); 
/*EOF*/