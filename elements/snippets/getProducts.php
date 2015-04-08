<?php
/**
 * @name getProducts
 * @description Returns a list of products.
 *
 * 
 * Available Placeholders
 * ---------------------------------------
 * product_id,alias,content,name,sku,type,track_inventory,qty_inventory,qty_alert,price,category,uri,is_active,seq,calculated_price,calculated_price,
 * use as [[+name]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List (Optional) [default: ProductOuterTpl]
 * @param string $innerTpl Format the Inner Item of List [default: ProductOuterTpl]
 * @param boolean $is_active Get all active records only [default: 1]
 * @arg integer $log_level 4 = debug. Defaults to system setting
 * @arg mixed $log_target Defaults to system setting.
 * @arg int $limit Limit the records to be shown (if set to 0, all records will be pulled)
 * @param int $firstClass set class name on the first item (Optional)
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * Usage
 * ------------------------------------------------------------
 * [[!getProducts? &outerTpl=`sometpl` &innerTpl=`othertpl` &limit=`0`]]
 *
 * @package moxycart
 **/
// Call your snippet like this: [[mySnippet? &log_level=`4`]]
// Override global log_level value

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getProducts',$scriptProperties);

$help = $modx->getOption('help',$scriptProperties);

// Formatting Arguments:
$innerTpl = $modx->getOption('innerTpl',$scriptProperties, 'ProductInnerTpl');
$outerTpl = $modx->getOption('outerTpl',$scriptProperties, 'ProductOuterTpl');
$content_ph = $modx->getOption('content_ph',$scriptProperties, 'content');
// Default Arguments:
$scriptProperties['is_active'] = $modx->getOption('is_active',$scriptProperties, 1);

// Filter out formatting/control arguments:
unset($scriptProperties['log_level']);
unset($scriptProperties['log_target']);
unset($scriptProperties['innerTpl']);
unset($scriptProperties['outerTpl']);
unset($scriptProperties['content_ph']);

$P = new \Moxycart\Product($modx);

if ($help) {
    $Prod = $modx->newObject('Product');
    $Img = $modx->newObject('Asset');
    $vals = $Prod->toArray();
    $Img = $modx->newObject('Asset');
    $vals2 = $Img->toArray('Image.');
    $vals = array_merge($vals,$vals2);
    $out = '<div style="border:1px dotted grey; padding:10px;"><h3>getProducts Placeholders:</h3><pre>'; //.implode("\n",array_keys($vals)).'</pre>';
    foreach ($vals as $v => $tmp) {
        $out .= '&#91;&#91;&#43;'.$v.'&#93;&#93;'."\n";
    }
    return $out.'</pre>
    <h2>Script Properties</h2><pre>'.print_r($scriptProperties,true).'</pre></div>';
}
$results = $P->all($scriptProperties);

if ($results)
{
    // Get Custom Fields
    foreach ($results as &$r)
    {
        $c = $modx->newQuery('ProductField');
        $c->where(array(
            'product_id' => $r['product_id'],
            'Field.is_active' => true
        ));
        $c->sortby('Field.seq','ASC');    
        
        if($fields = $modx->getCollectionGraph('ProductField','{"Field":{}}',$c))
        {
            foreach ($fields as $f) {
                $r[ $f->Field->get('slug') ] = $f->get('value');
            }
        }
    }
    
    return $Snippet->format($results,$innerTpl,$outerTpl,$content_ph);    
}

$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','getProducts',__LINE__);