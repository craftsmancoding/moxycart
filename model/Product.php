<?php
/**
 * Model
 * This is a bit tricky because we already have an ORM layer: that Product class already
 * handles newObject, save, etc.  The getCollection 
 *
 * This model class should define a unified interface that define the graphs that represent
 * an object data in its entirety.
 */
namespace Moxycart;
class Product extends BaseModel {

    public $xclass = 'Product';
    public $default_sort_col = 'name';

    public function addAssetFromFile($path) {
    
    }

    //------------------------------------------------------------------------------
    public function addAssets(array $array) {
    
    }

    public function removeAssets(array $array) {
    
    }
    
    public function dictateAssets(array $array) {
    
    }

    //------------------------------------------------------------------------------
    public function addReview() {
    
    }

    //------------------------------------------------------------------------------
    public function addRelations(array $array) {
    
    }

    public function removeRelations(array $array) {
    
    }
    
    public function dictateRelations(array $array) {

    }    

    //------------------------------------------------------------------------------
    public function addTaxonomies(array $array) {
    
    }

    public function removeTaxonomies(array $array) {
    
    }
    
    public function dictateTaxonomies(array $array) {
    
    }

    //------------------------------------------------------------------------------
    public function addTerms(array $array) {
    
    }

    public function removeTerms(array $array) {
    
    }
    
    public function dictateTerms(array $array) {
    
    }    
    
    
    
}
/*EOF*/