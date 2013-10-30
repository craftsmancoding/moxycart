<?php
/**
 * ProductURLRouting plugin for moxycart extra
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
 * Handles various things...
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package moxycart
 **/
switch ($modx->event->name) {
    case 'OnManagerPageInit':
        $assetsUrl = $modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        $modx->regClientCSS($assetsUrl.'components/moxycart/css/mgr.css');
        break;
/*
    case 'OnPageNotFound':
        $corePath = $modx->getOption('articles.core_path',null,$modx->getOption('core_path').'components/articles/');
        require_once $corePath.'model/articles/articlesrouter.class.php';
        $router = new ArticlesRouter($modx);
        $router->route();
        return;
*/        
}