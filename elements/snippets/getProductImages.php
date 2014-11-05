<?php
/**
 * @name getProductImages
 * @description Returns a list of images associated with the given product.
 *
 * 
 * Available Placeholders
 * ---------------------------------------
 * e.g. to format the large image: 
 *      <img src="[[+Asset.url]]" width="[[+Asset.width]]" height="[[+Asset.height]]" alt="[[+Asset.alt]]" />
 * Thumbnail:
 *      <img src="[[+Asset.thumbnail_url]]" />
 *
 * If needed, include the System Settings (double ++) :
 *      [[++moxycart.thumbnail_width]]
 *      [[++moxycart.thumbnail_height]]
 * e.g. <img src="[[+Asset.thumbnail_url]]" width="[[++moxycart.thumbnail_width]]" height="[[++moxycart.thumbnail_width]]" alt="[[+Asset.alt]]"/>
 * 
 * Parameters
 * -----------------------------
 * @param integer $product_id of the product whose images you want. Defaults to the current product (if used in a product template)
 * @param string $group name of the group - if set, results will be filtered to this group
 * @param string $outerTpl Format the Outer Wrapper of List (Optional)
 * @param string $innerTpl Format the Inner Item of List
 * @param boolean $is_active Get all active records only
 * @param int $limit Limit the records to be shown (if set to 0, all records will be pulled)
// * @param int $firstClass set CSS class name on the first item (Optional)
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * Usage
 * ------------------------------------------------------------
 * To get all Images on certain product
 * [[!getProductImages? &product_id=`[[+product_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &firstCLass=`first` &is_active=`1` &limit=`0`]]
 * [[!getProductImages? &product_id=`[[+product_id]]` &outerTpl=`sometpl` &innerTpl=`othertpl` &is_active=`1` &limit=`1`]]
 *
 * @package moxycart
 */

$assman_corepath = $modx->getOption('assman.core_path', null, MODX_CORE_PATH.'components/assman/');
require_once $assman_corepath .'vendor/autoload.php';

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('getProductImages',$scriptProperties);


// Formatting Arguments:
$innerTpl = $modx->getOption('innerTpl', $scriptProperties, '<li><img src="[[+Asset.url]]" width="[[+Asset.width]]" height="[[+Asset.height]]" alt="[[+Asset.alt]]" /></li>');
$outerTpl = $modx->getOption('outerTpl', $scriptProperties, '<ul>[[+content]]</ul>');

// Default Arguments:
$scriptProperties['is_active'] = (bool) $modx->getOption('is_active',$scriptProperties, 1);
$scriptProperties['limit'] = (int) $modx->getOption('limit',$scriptProperties, null);
$scriptProperties['content_ph'] = $modx->getOption('content_ph',$scriptProperties, 'content');

$product_id = (int) $modx->getOption('product_id',$scriptProperties, $modx->getPlaceholder('product_id'));
$js_paths = $modx->getOption('js_paths',$scriptProperties,null);
$css_paths = $modx->getOption('css_paths',$scriptProperties,null);

if (!$product_id) {
    return 'product_id is required.';
}

$criteria = array(
    'product_id'=>$product_id,
    'is_active'=>true,
    'Asset.is_image' => true,
);
if (isset($scriptProperties['group'])) {
    $criteria['ProductAsset.group'] = $scriptProperties['group'];
}
$c = $modx->newQuery('ProductAsset');
$c->where($criteria);


$c->sortby('ProductAsset.seq','ASC');
if ($scriptProperties['limit']) {
    $c->limit($scriptProperties['limit']);
}
$ProductAssets = $modx->getCollectionGraph('ProductAsset','{"Asset":{}}', $c);

if ($ProductAssets) {
	if(!is_null($js_paths)) {
		$js_paths = explode(',', $js_paths);
		if (!empty($js_paths)) {
			foreach ($js_paths as $js) {
				$modx->regClientScript($js);
			}
			
		}
	}

	if(!is_null($css_paths)) {
		$css_paths = explode(',', $css_paths);
		if (!empty($css_paths)) {
			foreach ($css_paths as $cs) {
				$modx->regClientCSS($cs);
			}
			
		}
	}
	
	
	
	
    return $Snippet->format($ProductAssets,$innerTpl,$outerTpl,$scriptProperties['content_ph']);    
}


$modx->log(\modX::LOG_LEVEL_DEBUG, "No results found",'','getProducts',__LINE__);

return;
//return 'No images found.';