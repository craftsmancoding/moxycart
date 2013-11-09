<?php
/**
 * getProductTerms snippet for moxycart extra
 *
 * Copyright 2013 by Everett Griffiths everett@craftsmancoding.com
 * Created on 07-05-2013
 *
 * moxycart is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * moxycart is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * moxycart; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package moxycart
 */

/**
 * Description
 * -----------
 * Returns a list of product_terms.
 *
 * Parameters
 * -----------------------------
 * @param int $product_id Format the Outer Wrapper of  List
 * @param string $outerTpl Format the Outer Wrapper of  List
 * @param string $innerTpl Format the Inner Item of  List
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package moxycart
 **/

$outerTpl = $modx->getOption('outerTpl',$scriptProperties,'MoxyOuterTpl');
$innerTpl = $modx->getOption('innerTpl',$scriptProperties,'MoxyInnerTpl');
$product_id = (int) $modx->getOption('product_id',$scriptProperties);


$modx->getService('moxycart');
$product_terms = $modx->moxycart->json_product_terms($scriptProperties, true);
$product_terms = json_decode($product_terms,true);

echo '<pre>';
print_r($product_terms);
die();
$innerOut = '';
$output = '';
if (isset($product_terms['results']) && is_array($product_terms['results'])) {
	foreach ($product_terms['results'] as $row) {
   		$innerOut .= $modx->getChunk($innerTpl,$row);
	}
}


$innerPlaceholder = array('moxy.items' => $innerOut);
$output = $modx->getChunk($outerTpl,$innerPlaceholder); 
return $output;
