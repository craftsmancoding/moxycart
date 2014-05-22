<?php
/**
 * Asset
 */
namespace Moxycart;
class Asset extends BaseModel {

    public $xclass = 'Asset';
    public $default_sort_col = 'name';

    /**
     * Create a new asset from a given path (or lookup the asset at that location)
     *
     * @return integer primary key
     */
    public function fromPath($path, $props=array()) {
    
    }
}
/*EOF*/