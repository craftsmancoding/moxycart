<?php
/**
 * A Variation has a Product as a parent (via parent_id)
 * It is similar, but not the same as a Product Option Modifier. 
 *
 * So it is a product with the added restriction of it must have the parent_id set.
 * Everything else can be inherited from the parent product.
 */
namespace Moxycart;
class Variation extends Product {

    public $xclass = 'Product';
    public $default_sort_col = 'name';


    /**
     * Given an array of option-type:option-term values, convert that into
     * a valid URL.
     *
     */
    public function getAlias($matrix) {
    
    }
    
}
/*EOF*/