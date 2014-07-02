<?php
/**
 * @name addToCartButton
 * @description Generates an "Add to Cart" button for the current product. This will respond intelligently according to inventory, options, and bundled products.
 *
 * USAGE
 *
 * Remember that you must supply a FULL URL if you want to use images for your "soldout" or "submit" buttons, so if you are 
 * referencing a local image, you'll want to use the assets_url System Setting with the *full* URL scheme:
 *
 *  [[addToCartButton? &submit=`[[++assets_url? &scheme=`full`]]images/purchase.png`]]
 *
 * @param integer $product_id (defaults to current product)
 * @param string $submit text/image to show as the submit button. If an image, a full URL with http:// must be specified. (default: Add to Cart)
 * @param string $soldout text/image to show if inventory tracking is enabled and the qty is below the backorder max. If an image, a full URL with http:// must be specified.  (default: Sold Out)
 * @param string $cssClassSoldout optional class for the soldout image
 * @param string $cssClassSubmit optional class for the submit
 * @param string $cssClassOptionLabel optional class for the label around the option label
 * @param string $cssClassOptionSelect optional class for the option selects 
 * @param string $tpl name of formatting chunk. (default: BuyButton)
 * @param integer log_level -- you can set the logging level in your snippet to (temporarily) override the system default.
 *
 * @package moxycart
 */
 
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
$assets_url = $modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('addToCartButton',$scriptProperties);

// Add script for dynamic pricing
$modx->regClientScript($assets_url.'js/AddToCartButton.js');

$product_id = $modx->getOption('product_id', $scriptProperties, $modx->getPlaceholder('product_id'));
$submit = $modx->getOption('submit', $scriptProperties, 'Add to Cart');
$soldout = $modx->getOption('soldout', $scriptProperties, 'Sold Out');
$tpl = $modx->getOption('tpl', $scriptProperties, 'BuyButton');
$cssClassSoldout = $modx->getOption('cssClassSoldout', $scriptProperties);
$cssClassSubmit = $modx->getOption('cssClassSubmit', $scriptProperties);
$cssClassOptionLabel = $modx->getOption('cssClassOptionLabel', $scriptProperties);
$cssClassOptionSelect = $modx->getOption('cssClassOptionSelect', $scriptProperties);

$P = $modx->getObject('Product', array('product_id'=>$product_id));
if (!$P) {
    $modx->log(modX::LOG_LEVEL_ERROR,'Product ID not found: '.$product_id,'','addToCartButton');
    return '<script>alert("Product ID not found.");</script>';
}
$properties = $P->toArray();

// Watch out for low inventory
$inventory = (int) $P->get('qty_inventory');
$backorder_max = (int) $P->get('qty_backorder_max');
$modx->log(modX::LOG_LEVEL_DEBUG,'Product '.$product_id.'; Track Inventory: '.$P->get('track_inventory').' Inventory: '.$inventory.' Backorder max: '.$backorder_max,'','addToCartButton');
if ($P->get('track_inventory')) {
    if(($inventory + $backorder_max) <=  0) {
        if(filter_var($soldout, FILTER_VALIDATE_URL)) {
            $modx->log(modX::LOG_LEVEL_INFO,'Sold Out of product '.$product_id.'; Inventory: '.$inventory.' Backorder max: '.$backorder_max,'','addToCartButton');
            $soldout = sprintf('<img src="%s" alt="Sold Out" class="%s"/>',$soldout,$cssClassSoldout);    
        }
        return $soldout;
    }
}

if(filter_var($submit, FILTER_VALIDATE_URL)) {
    $properties['submit'] = sprintf('<input type="image" src="%s" class="%s" alt="Add to Cart"/>',$submit, $cssClassSubmit);    
}
else {
    $properties['submit'] = sprintf('<input type="submit" value="%s" class="%s"/>',$submit, $cssClassSubmit);
}


$c = $modx->newQuery('ProductOption');
$c->where(array('ProductOption.product_id' => $product_id));
$c->sortby('Option.seq','ASC');
//$c->bindGraph('{"Option":{"Terms":{}},"Meta":{}}');
//$c->prepare();
//return <pre>'.$c->toSQL().'</pre>';
$properties['options'] = '';

if ($Options = $modx->getCollectionGraph('ProductOption','{"Option":{}}',$c)) {

    foreach ($Options as $o) {
        if (!is_object($o->Option)) {
            $modx->log(modX::LOG_LEVEL_ERROR,'Product ID ('.$product_id.') tied to option ('.$o->get('option_id').') that does not exist?','','addToCartButton');
            continue;
        }
        $opt = '<label for="'.$o->Option->get('slug').'" class="'.$cssClassOptionLabel.'">'.$o->Option->get('name').'</label>'
            .'<select id="'.$o->Option->get('slug').'" name="'.$o->Option->get('slug').'" onchange="javascript:onchange_price(this);" class="cart-default-select '.$cssClassOptionSelect.'">';      
      
        // all_terms,omit_terms,explicit_terms
        if ($o->get('meta') == 'all_terms') {
            $Terms = $modx->getCollection('OptionTerm', array('option_id'=>$o->get('option_id')));
            foreach ($Terms as $t) {
                $opt .= '<option value="'.$t->get('slug').$t->get('modifiers').'">'.$t->get('name').'</option>';
            }
        }
        elseif ($o->get('meta') == 'omit_terms') {
            $Meta = $modx->getCollection('ProductOptionMeta', array('product_id' => $product_id, 'option_id'=>$o->get('option_id')));
            $omit = array();
            foreach ($Meta as $m) {
                $omit[] = $m->get('oterm_id');
            }
            $c = $modx->newQuery('OptionTerm');
            $c->where(array('option_id' => $o->get('option_id'),'oterm_id:NOT IN' => $omit));            
            $Terms = $modx->getCollection('OptionTerm', $c);
            foreach ($Terms as $t) {
                if (!in_array($t->get('oterm_id'), $omit)) {
                    $opt .= '<option value="'.$t->get('slug').$t->get('modifiers').'">'.$t->get('name').'</option>';
                }
            }        
        }
        elseif ($o->get('meta') == 'explicit_terms') {
            $Meta = $modx->getCollection('ProductOptionMeta', array('product_id' => $product_id, 'option_id'=>$o->get('option_id')));
            $explicit = array();
            foreach ($Meta as $m) {
                $explicit[ $m->get('oterm_id') ] = $m->toArray();
            }
            $Terms = $modx->getCollection('OptionTerm', array('option_id'=>$o->get('option_id')));
            foreach ($Terms as $t) {
                if (isset($explicit[ $t->get('oterm_id') ])) {
                    // setting the values on the OptionTerm object will tie into the custom logic in the xpdo optionterm.class.php
                    if ($explicit[ $t->get('oterm_id') ]['is_override']) {
                        $t->fromArray($explicit[ $t->get('oterm_id') ]);
                    }
                    $opt .= '<option value="'.$t->get('slug').$t->get('modifiers').'">'.$t->get('name').'</option>';
                }
            }   
        }

        $opt .= '</select>';
        $properties['options.'.$o->Option->get('slug')] = $opt;
        $properties['options'] = $properties['options'] . $opt;
    }
}

return $modx->getChunk($tpl, $properties);