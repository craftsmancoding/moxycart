<?php
/**
 * ProductRelation
 */
namespace Moxycart;
class ProductRelation extends BaseModel {

    public $xclass = 'ProductRelation';
    public $default_sort_col = '`type`, `seq`';

    public function getTypes() {
        return array(
            'related'=>'Related',
            'bundle-1:1' => 'Bundled Product',
            //'bundle-1:order' => 'One-Per-Order Bundle',
        );
    }
}
/*EOF*/