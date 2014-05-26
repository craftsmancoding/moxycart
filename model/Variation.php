<?php
/**
 * A Variation has a Product as a parent (via parent_id)
 * 
 */
namespace Moxycart;
class Variation extends Product {

    public $xclass = 'Product';
    public $default_sort_col = 'name';

    
}
/*EOF*/