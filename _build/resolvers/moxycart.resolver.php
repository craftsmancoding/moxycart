<?php
/**
 * Resolver for moxycart extra
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
 * @package moxycart
 * @subpackage build
 
 Add to extension_packages
 [{"moxycart":{"path":"[[++core_path]]components/moxycart/model/"}},{"articles":{"path":"[[++core_path]]components/articles/model/"}}]
 */

/* @var $object xPDOObject */
/* @var $modx modX */

/* @var array $options */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
            // $modx->addExtensionPackage($package_name,"[[++core_path]]components/$package_name/model/");
            // $manager = $modx->getManager();
            // Add a field to an existing container
            // $modx->getManager()->addField('fieldname')     
            break;
        case xPDOTransport::ACTION_INSTALL:
            $modx->addExtensionPackage($package_name,"[[++core_path]]components/$package_name/model/");
            $manager = $modx->getManager();
            $manager->createObjectContainer('Currency');
            $manager->createObjectContainer('Product');
            $manager->createObjectContainer('Unit');
            $manager->createObjectContainer('VariationType'); 
            $manager->createObjectContainer('VariationTerm');
            $manager->createObjectContainer('ProductVariationTypes');
            $manager->createObjectContainer('ProductVariantTerm');
            $manager->createObjectContainer('Taxonomy');
            $manager->createObjectContainer('Term');
            $manager->createObjectContainer('ProductTerms');
            $manager->createObjectContainer('Category');
            $manager->createObjectContainer('Cart');
            $manager->createObjectContainer('Image');       
            
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $modx->removeExtensionPackage($package_name);
            break;
    }
}

return true;