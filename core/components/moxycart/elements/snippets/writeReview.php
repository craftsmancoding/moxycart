<?php
/**
*This is the Snippet used to generate a form to the front-end. Reviews can include full text reviews (e.g. "this product is awesome!"), or they can be a simple "star" review where the user simply rates the product as a number (the database supports 1 - 100). The writeReview snippet should handle both cases easily.
*
*&public (boolean) default is zero. If 1 (true), then any public user can leave a review. If the review is not public, then the user must be logged in, otherwise we show a message.
*&publicTpl (string) Chunk name for the message to show if public=0 and the user is not logged in. E.g. "You must log in to leave a review."
*&tpl (string) -- include 2 default options: one for "start" where a simple star rating is included, or "text"
*&approve_reviews (boolean) -- defaults to system setting moxycart.approve_reviews. Default is false: that means the admin must approve the reviews manually.
 */
// Get args
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
require_once $core_path .'components/moxycart/model/htmlpurifier/library/HTMLPurifier.auto.php';

$product_id = $modx->getOption('product_id', $scriptProperties); 
$public = $modx->getOption('public', $scriptProperties,1); 
$pubicTpl = $modx->getOption('publicTpl', $scriptProperties,'You must log in to leave a review.'); 
$tpl = $modx->getOption('tpl', $scriptProperties,'MoxycartProductFullReview'); 
$approve_reviews = $modx->getOption('approve_reviews', $scriptProperties, 0); 


$props = array();


$user = $modx->getUser();
$user_id = $user->get('id');

if(!$public && empty($user_id)) {
	$tpl = $pubicTpl;
}

if ($Chunk = $modx->getObject('modChunk', array('name'=>$tpl))) {
    $tpl = $Chunk->getContent();
}

$Product = $modx->getObject('Product',array('product_id'=>$product_id)); // ??? how can you tell the requested URI?
if (!$Product) {
    return 'No Product Found.';  // it's a real 404
} 
$props = $Product->toArray();
$props['success_msg'] = '';
$props['error_msg'] = '';

if($_POST) {
	$purifier = new HTMLPurifier();

    $Review = $modx->newObject('Review');    
    $Review->set('product_id',$product_id);
    $Review->set('author_id',$user_id);
    $Review->set('name',strip_tags($modx->getOption('name', $_POST)));
    $Review->set('email',strip_tags($modx->getOption('email', $_POST)));
    $Review->set('rating',(int) $modx->getOption('rating', $_POST));
    $Review->set('content',$purifier->purify($modx->getOption('content', $_POST)));
    if (!$Review->save()) {
        $props['error_msg'] = 'Sorry, Failed to Post your Review.';
    } else {
    	$props['success_msg'] = 'Review Successfully Posted.';
    }
	


}

// Create the temporary chunk
$uniqid = uniqid();
$chunk = $modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
$chunk->setCacheable(false);

$output = $chunk->process($props, $tpl);

return $output;