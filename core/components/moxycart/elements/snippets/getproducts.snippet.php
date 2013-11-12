<?php
/**
 * getProducts snippet for moxycart extra
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
 * Returns a list of products.
 * 
 * Available Placeholders
 * ---------------------------------------
 * id, name, sku, type, qty_inventory, qty_alert, price, category, uri, is_active
 * use as [[+name]] on Template Parameters
 * 
 * Parameters
 * -----------------------------
 * @param string $outerTpl Format the Outer Wrapper of List
 * @param string $innerTpl Format the Inner Item of List
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package moxycart
 **/

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
$class_path = $core_path . 'components/moxycart/model/moxycart/moxycart.snippets.class.php';
require_once($class_path);

$moxySnippet = new MoxycartSnippet($modx);
$out = $moxySnippet->execute('json_products',$scriptProperties);
return $out;

