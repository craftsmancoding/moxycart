<?php
/**
 * MoxyCart
 *
 * Copyright 2013 by Everett Griffiths <everett@craftsmancoding.com>
 *
 * This file is part of MoxyCart for MODx Revolution.
 *
 * MoxyCart is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * MoxyCart is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * MoxyCart; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package MoxyCart
 */
/**
 * @package MoxyCart
 * @subpackage controllers
 */
require_once __DIR__ . '/HyperClient/Client.php';
require_once __DIR__ . '/HyperClient/interfaces/iCache.php';
require_once __DIR__ . '/HyperClient/cache/MODx_Cache.php';
require_once __DIR__ . '/HyperClient/cache/Entry.php';
require_once __DIR__ . '/model/moxycart/moxycart.class.php';

$MoxyCart = new MoxyCart($modx);
return $MoxyCart->initialize('mgr');
	
/*EOF*/