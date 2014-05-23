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

    private $product_id;
    
    /**
     * Verify that the existing product is in fact valid, persisted.
     * @return integer product_id
     */
    private function _verifyExisting() {
    
        if (!empty($this->product_id)) {
            return $this->product_id;
        }

        if(!$this->product_id = $this->get('product_id')) {
            throw new \Exception('Product ID not defined');
        }
        // Make sure we're not getting jerked around by an un-persisted product_id
        if (!$P = $this->modx->getObject('Product', $this->product_id)) {
            throw new \Exception('Product does not exist '.$this->product_id);    
        }
        
        return $this->product_id; 
    }

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
        // addOne not good enough?
    }

    //------------------------------------------------------------------------------
    /**
     * Add relations to the current product.
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array of related_id's
     * @param string $type name of the type of relation, used for grouping.
     */
    public function addRelations(array $array, $type='related') {
        $this_product_id = $this->_verifyExisting();

        foreach ($array as $related_id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'related_id'=> $related_id,
                'type' => $type
            );
            if (!$PR = $this->modx->getObject('ProductRelation', $props)) {
                if (!$R = $this->modx->getObject('Product', $related_id)) {
                    throw new \Exception('Invalid product ID '.$related_id);    
                }
                $PR = $this->modx->newObject('ProductRelation', $props);
                $PR->save();
            }
        }

        return true;
    }

    /**
     * Remove relations to the current product.
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array of related_id's
     * @param string $type name of the type of relation, used for grouping.     
     */
    public function removeRelations(array $array, $type='related') {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $related_id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'related_id'=> $related_id,
                'type' => $type
            );
            if ($PR = $this->modx->getObject('ProductRelation', $props)) {
                $PR->remove();
            }
        }
        return true;
    }
    
    /**
     * Dictate relations to the current product.
     * This will remove all relations not in the given $array, add any new relations from the $array,
     * it will order the relations based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $dictate'd related_id's
     * @param string $type name of the type of relation, used for grouping.          
     */
    public function dictateRelations(array $dictate, $type='related') {
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id, 
            'type' => $type
        );
        
        // Array of related_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductRelation', $props)) {
            $existing[] = $ExistingColl->get('related_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $this->removeRelations($to_remove,$type);
        $this->addRelations($to_add,$type);
        $this->orderRelations($dictate);
        
        return true;
    }
    /**
     * Adjust the seq into ascending order for the given related_ids
     *
     * @param array $dictate'd related_id's
     * @param string $type name of the type of relation, used for grouping.          
     */    
    public function orderRelations(array $array, $type='related') {
        $this_product_id = $this->_verifyExisting();
        
        $seq = 0;
        foreach ($array as $related_id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'related_id'=> $related_id,
                'type' => $type
            );
            if ($PR = $this->modx->getObject('ProductRelation', $props)) {
                $PR->set('seq',$seq);
                $PR->save();
                $seq++;
            }
        }
        
        return true;
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