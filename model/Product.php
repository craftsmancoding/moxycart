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
     * Verify that the existing product is in fact an existing, valid, persisted product
     * and not a new, unsaved product.
     * @return integer product_id
     */
    private function _verifyExisting() {
    
        if (!$this->modelObj->isNew() && !empty($this->product_id)) {
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

    //------------------------------------------------------------------------------
    public function addReview() {
        // addOne not good enough?
    }


    public function addAssetFromFile($path) {
    
    }

    //------------------------------------------------------------------------------
    //! Assets
    //------------------------------------------------------------------------------
    /**
     * Add assets to the current product.
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array of asset_id's
     */
    public function addAssets(array $array) {
        $this_product_id = $this->_verifyExisting();
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'asset_id'=> $id
            );
            if (!$PA = $this->modx->getObject('ProductAsset', $props)) {
                if (!$A = $this->modx->getObject('Asset', $id)) {
                    throw new \Exception('Invalid asset ID '.$id);    
                }
                $PA = $this->modx->newObject('ProductAsset', $props);
                $PA->save();
            }
        }
        return true;
    }

    /**
     * Remove assets to the current product.
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array of asset_id's
     */
    public function removeAssets(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $asset_id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'asset_id'=> $asset_id,
            );
            if ($PR = $this->modx->getObject('ProductAsset', $props)) {
                $PR->remove();
            }
        }
        return true;
    }
    
    /**
     * Dictate assets to the current product.
     * This will remove all assets not in the given $array, add any new assets from the $array,
     * it will order the assets based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $dictate'd asset_id's
     */
    public function dictateAssets(array $dictate) {
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id, 
            'type' => $type
        );
        
        // Array of asset_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductAsset', $props)) {
            $existing[] = $ExistingColl->get('asset_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $this->removeAssets($to_remove,$type);
        $this->addAssets($to_add,$type);
        $this->orderAssets($dictate);
        
        return true;
    }
    /**
     * Adjust the seq into ascending order for the given asset_ids
     *
     * @param array $dictate'd asset_id's
     * @param string $type name of the type of relation, used for grouping.          
     */    
    public function orderAssets(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        $seq = 0;
        foreach ($array as $asset_id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'asset_id'=> $asset_id,
            );
            if ($PR = $this->modx->getObject('ProductAsset', $props)) {
                $PR->set('seq',$seq);
                $PR->save();
                $seq++;
            }
        }
        
        return true;
    }


    //------------------------------------------------------------------------------
    //! Relations
    //------------------------------------------------------------------------------
    /**
     * Add relations to the current product.
     * Exeptions are thrown if the product ids do not exist.
     * Had trouble getting this to work with addMany.
     * @param array of related_id's
     * @param string $type name of the type of relation, used for grouping.
     */
    public function addRelations(array $array, $type='related') {
        $this_product_id = $this->_verifyExisting();

        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'related_id'=> $id,
                'type' => $type
            );
            if (!$PR = $this->modx->getObject('ProductRelation', $props)) {
                if (!$P = $this->modx->getObject('Product', $id)) {
                    throw new \Exception('Invalid relation ID '.$id);    
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
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Removing relations: '.implode(',',$array). ' from product_id '.$this_product_id,'',__CLASS__,__FILE__,__LINE__);        
        foreach ($array as $related_id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'related_id'=> $related_id,
                'type' => $type
            );
            if ($PR = $this->modx->getObject('ProductRelation', $props)) {
                if (!$PR->remove()) {
                    $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Error removing ProductRelation '.$PR->get('id'),'',__CLASS__,__FILE__,__LINE__);
                }
            }
            else {
                $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Could not find ProductRelation '.print_r($props,true),'',__CLASS__,__FILE__,__LINE__);
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
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Dictating relations: '.implode(',',$dictate),'',__CLASS__,__FILE__,__LINE__);
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
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Existing relations for product_id '.$this_product_id.': '.implode(',',$existing),'',__CLASS__,__FILE__,__LINE__);        
        $to_remove = array_diff($existing,$dictate);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Product relations to be removed from product_id '.$this_product_id.': '.implode(',',$to_remove),'',__CLASS__,__FILE__,__LINE__);
        $to_add = array_diff($dictate,$existing);
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Product relations to be added to product_id '.$this_product_id.': '.implode(',',$to_add),'',__CLASS__,__FILE__,__LINE__);
        $this->removeRelations($to_remove,$type);
        $this->addRelations($to_add,$type);
        $this->save();
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
    //! Taxonomies
    //------------------------------------------------------------------------------
    /** 
     * Add taxonomies to a product
     * @param array $array of taxonomy page ids
     */
    public function addTaxonomies(array $array) {
        $this_product_id = $this->_verifyExisting();

        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'taxonomy_id'=> $id
            );
            if (!$PT = $this->modx->getObject('ProductTaxonomy', $props)) {
                if (!$T = $this->modx->getObject('Taxonomy', $id)) {
                    throw new \Exception('Invalid taxonomy ID '.$id);    
                }
                $PT = $this->modx->newObject('ProductTaxonomy', $props);
                $PT->save();
            }
        }

        return true;    
    }

    /** 
     * Remove taxonomies from a product. We don't care here if the referenced taxonomy ids are valid or not.
     * @param array $array of taxonomy page ids
     */
    public function removeTaxonomies(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'taxonomy_id'=> $id
            );
            if ($PT = $this->modx->getObject('ProductTaxonomy', $props)) {
                $PT->remove();
            }
        }
        return true;
    
    }

    /**
     * Dictate taxonomies to the current product.
     * This will remove all taxonomies not in the given $array, add any new relations from the $array,
     * it will order the relations based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $dictate'd related_id's
     */
    public function dictateTaxonomies(array $dictate) {
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id,
        );
        
        // Array of related_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductTaxonomy', $props)) {
            $existing[] = $ExistingColl->get('taxonomy_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $this->removeTaxonomies($to_remove);
        $this->addTaxonomies($to_add);
        $this->orderTaxonomies($dictate);
        
        return true;
    
    }

    /**
     * Adjust the seq into ascending order for the given taxonomy ids
     *
     * @param array $dictate'd taxonomy id's
     */    
    public function orderTaxonomies(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        $seq = 0;
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'taxonomy_id'=> $id
            );
            if ($PT = $this->modx->getObject('ProductTaxonomy', $props)) {
                $PT->set('seq',$seq);
                $PT->save();
                $seq++;
            }
        }
        
        return true;
    }

    
    //------------------------------------------------------------------------------
    //! Terms
    //------------------------------------------------------------------------------
    /** 
     * Add terms to a product
     * @param array $array of taxonomy page ids
     */
    public function addTerms(array $array) {
        $this_product_id = $this->_verifyExisting();

        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'term_id'=> $id
            );
            if (!$PT = $this->modx->getObject('ProductTerm', $props)) {
                if (!$T = $this->modx->getObject('Term', $id)) {
                    throw new \Exception('Invalid term ID '.$id);    
                }
                $PT = $this->modx->newObject('ProductTerm', $props);
                $PT->save();
            }
        }

        return true;    
    }

    /** 
     * Remove terms from a product. We don't care here if the referenced taxonomy ids are valid or not.
     * @param array $array of taxonomy page ids
     */
    public function removeTerms(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'term_id'=> $id
            );
            if ($PT = $this->modx->getObject('ProductTerm', $props)) {
                $PT->remove();
            }
        }
        
        return true;
    }

    /**
     * Dictate terms for the current product.
     * This will remove all terms not in the given $array, add any new relations from the $array,
     * it will order the relations based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $dictate'd related_id's
     */
    public function dictateTerms(array $dictate) {
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id,
        );
        
        // Array of related_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductTerm', $props)) {
            $existing[] = $ExistingColl->get('term_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $this->removeTerms($to_remove);
        $this->addTerms($to_add);
        
        return true;
    
    }
        
    //------------------------------------------------------------------------------
    //! Fields
    //------------------------------------------------------------------------------
    /** 
     * Add fields to a product
     * @param array $array of taxonomy page ids
     */
    public function addFields(array $array) {
        $this_product_id = $this->_verifyExisting();

        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'field_id'=> $id
            );
            if (!$PF = $this->modx->getObject('ProductField', $props)) {
                if (!$F = $this->modx->getObject('Field', $id)) {
                    throw new \Exception('Invalid field ID '.$id);    
                }
                $PF = $this->modx->newObject('ProductField', $props);
                $PF->save();
            }
        }

        return true;    
    }

    /** 
     * Remove fields from a product. We don't care here if the referenced taxonomy ids are valid or not.
     * @param array $array of taxonomy page ids
     */
    public function removeFields(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'field_id'=> $id
            );
            if ($PT = $this->modx->getObject('ProductField', $props)) {
                $PT->remove();
            }
        }
        return true;
    
    }

    /**
     * Dictate fields for the current product.
     * This will remove all fields not in the given $array, add any new relations from the $array,
     * it will order the relations based on the incoming $array order (seq will be set).
     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $dictate'd related_id's
     */
    public function dictateFields(array $dictate) {
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id,
        );
        
        // Array of related_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductField', $props)) {
            $existing[] = $ExistingColl->get('field_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $this->removeFields($to_remove);
        $this->addFields($to_add);
        
        return true;
    
    }

    //------------------------------------------------------------------------------
    //! OptionTypes
    //------------------------------------------------------------------------------
    /** 
     * Add variation-types to a product. This will update the "is_variant" attribute
     * on all matched rows.
     *
     * @param array $array of otype_ids
     * @param boolean $is_variant triggers super special functionality (default: false)
     */
    public function addOptionTypes(array $array, $is_variant=false) {
        $this_product_id = $this->_verifyExisting();

        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'otype_id'=> $id
            );
            if (!$PVT = $this->modx->getObject('ProductOptionType', $props)) {
                if (!$VT = $this->modx->getObject('OptionType', $id)) {
                    throw new \Exception('Invalid Option Type ID '.$id);    
                }
                $PVT = $this->modx->newObject('ProductOptionType', $props);
            }
            $PVT->set('is_variant', $is_variant);
            $PVT->save();
        }

        return true;    
    }

    /** 
     * Remove variation-types from a product. We don't care here if the referenced ids are valid or not.
     * @param array $array of otype_ids
     */
    public function removeOptionTypes(array $array) {
        $this_product_id = $this->_verifyExisting();
        
        foreach ($array as $id) {
            $props = array(
                'product_id'=> $this_product_id, 
                'otype_id'=> $id
            );
            if ($PVT = $this->modx->getObject('ProductOptionType', $props)) {
                $PVT->remove();
            }
        }
        return true;
    }

    /**
     * Dictate variation-type ids for the current product.
     * This will remove all fields not in the given $array, add any new otype_id's from the $array.

     * Exeptions are thrown if the product ids do not exist.
     *
     * @param array $dictate'd otype_id's
     * @param boolean $is_variant triggers super special functionality (default: false)     
     */
    public function dictateOptionTypes(array $dictate, $is_variant=false) {
        $this_product_id = $this->_verifyExisting();
        
        $props = array(
            'product_id'=> $this_product_id,
        );
        
        // Array of related_id's that are already defined
        $existing = array();
        if($ExistingColl = $this->modx->getObject('ProductOptionType', $props)) {
            $existing[] = $ExistingColl->get('otype_id');   
        }
        
        $to_remove = array_diff($existing,$dictate);
        $to_add = array_diff($dictate,$existing);

        $this->removeOptionTypes($to_remove,$type);
        $this->addOptionTypes($to_add,$type);
        
        return true;
    
    }
    
    //------------------------------------------------------------------------------
    /**
     * Determine whether 2 associative arrays contain the same elements, regardless of
     * order.
     *
     * @param array $h1
     * @param array $h2
     * @return array of differences
     */
    public function hashDelta($h1,$h2) {
        $x = array_diff_assoc($h1,$h2);
        $y = array_diff_assoc($h2,$h1);
        return array_merge($x,$y);
        
    }
    
    /**
     * Inherit values from the parent store and set them onto the $this->modelObj object
     * @param integer $store_id
     */
    public function inheritFromStore($store_id) {

        // Set defaults from the parent Store
        if ($Store = $this->modx->getObject('Store', $store_id)) {
            if ($properties = $Store->get('properties')) {
                $this->template_id = (isset($properties['moxycart']['product_template'])) ? $properties['moxycart']['product_template'] : $this->modx->getOption('default_template');
                $this->type = (isset($properties['moxycart']['product_type'])) ? $properties['moxycart']['product_type'] : 'regular';
                // This is not persisted...wtf
                $this->sort_order = (isset($properties['moxycart']['sort_order'])) ? $properties['moxycart']['sort_order'] : 'name';
                $this->qty_alert = (isset($properties['moxycart']['qty_alert'])) ? $properties['moxycart']['qty_alert'] : 0;
                $this->track_inventory = (isset($properties['moxycart']['track_inventory'])) ? $properties['moxycart']['track_inventory'] : 0;
                // addOne
                //$this->fields = (isset($properties['moxycart']['specs'])) ? $properties['moxycart']['specs'] : array();
                //$this->taxonomies = (isset($properties['moxycart']['taxonomies'])) ? $properties['moxycart']['taxonomies'] : array();
            }
        }
        else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'store_id does not exist',__CLASS__);
        } 
        
    }
    
    /**
     * Override here for special stuff re variations
     */
    public function save() {
        return parent::save();
    }
    
}
/*EOF*/