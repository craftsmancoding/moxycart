<?php
/**
 * @name getBundledProducts
 * @description Generates form fields for Product template so that bundled products are added along with the parent product.
 *
 * See https://github.com/craftsmancoding/moxycart/wiki/Foxycart-Forms
 *
 * We need to read the quantity of the parent product because some bundle types are 1-to-1 (quantity is matched)
 * and other bundles are 1-to-order (quantity of the bundled item is always 1).
 *
 * @param integer product_id, default is the current placeholder for product_id
 * @param integer qty, default is the current placeholder set for qty
 */
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getBundledProducts',$scriptProperties);

/*
$product_id = (int) $modx->getOption('product_id', $scriptProperties, $modx->getPlaceholder('product_id'));
$qty = (int) $modx->getOption('product_id', $scriptProperties, $modx->getPlaceholder('qty'));
$specs = $modx->getOption('specs', $scriptProperties);

if (is_scalar($specs)) {
    $specs = explode(',',$specs);
}

$Moxy = new Moxycart($modx);

$tpl = '
    <input type="hidden" name="[[+i]]:quantity" value="[[+qty]]" />
    <input type="hidden" name="[[+i]]:name" value="[[+name]]" />
    <input type="hidden" name="[[+i]]:sku" value="[[+sku]]" />
    <input type="hidden" name="[[+i]]:weight" value="[[+weight]]" />
    <input type="hidden" name="[[+i]]:price" value="[[+price]]" />
    <input type="hidden" name="[[+i]]:code" value="[[+related_id]]" />';
    
$args = array();
$args['limit'] = 0;
$args['product_id'] = $product_id;
$args['type:IN'] = array('bundle-1:order','bundle-1:1');
$data = $Moxy->json_product_relations($args,true);

if (!$data['total']) {
    return '';
}
$out = '';
$i = 2;
foreach ($data['results'] as $r) {
    if ($r['type'] == 'bundle-1:order') {
        $r['qty'] = 1;
    }
    else {
        $r['qty'] = $quantity;
    }

    $r['i'] = $i;
    
    if($ProductSpecs = $modx->getCollectionGraph('ProductSpec','{"Spec":{}}', array('product_id'=>$r['related_id']))) {
        foreach ($ProductSpecs as $PS) {
            $r[ $PS->Spec->get('identifier') ] = $PS->get('value');
        }
    }
    
    $uniqid = uniqid();
    $chunk = $modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
    $chunk->setCacheable(false);
    $out .= $chunk->process($r, $tpl);
    $i++;
}

return $out;
*/

/*EOF*/