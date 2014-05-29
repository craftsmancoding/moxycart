<?php
class Product extends xPDOObject {
    public static $x;
    /**
     * Override to provide calculated fields
     */
    public function __construct(xPDO & $xpdo) { 
        parent::__construct($xpdo);
        $this->_fields['calculated_price'] = $this->get('calculated_price');
    }
    
    /**
     * Modifiers: overrides to provide calculated fields
     */
    public function get($k, $format = null, $formatTemplate= null) {
        // Return the sale price if the product is on sale
        if ($k=='calculated_price') {
            $now = strtotime(date('Y-m-d H:i:s'));
            $sale_start = strtotime($this->get('sale_start'));
            $sale_end = strtotime($this->get('sale_end'));
        
            $calculated_price = $this->get('price');
            // if on sale use price sale
            if(($sale_start <= $now) && ($sale_end >= $now)) {
                $calculated_price = $this->get('price_sale');
            } 

            return $calculated_price;            
        
        }
        // Termines how long we can cache this for
        elseif($k=='cache_lifetime') {
            $now = strtotime(date('Y-m-d H:i:s'));
            $sale_end = strtotime($this->get('sale_end'));
        
            if ($sale_end && $sale_end >= $now) {
                return $sale_end - $now;                
            }
                        
            return 0;        
        }
        else {
            return parent::get($k, $format, $formatTemplate);
        }
    }

    /**
     * Get the default values for a new product by inheriting from the parent store
     *
     * @param integer $store_id
     * @return array
     */
    public function get_defaults($store_id=null) {

        $data = $this->xpdo->getFields('Product');
        if (!$store_id) {
            $data['template_id'] = $this->xpdo->getOption('default_template');
            return $data;
        }
        // Set defaults from the parent Store
        if ($Store = $this->xpdo->getObject('Store', $store_id)) {
            if ($properties = $Store->get('properties')) {
                $data['template_id'] = (isset($properties['moxycart']['product_template'])) ? $properties['moxycart']['product_template'] : $this->xpdo->getOption('default_template');
                $data['product_type'] = (isset($properties['moxycart']['product_type'])) ? $properties['moxycart']['product_type'] : 'regular';
                $data['sort_order'] = (isset($properties['moxycart']['sort_order'])) ? $properties['moxycart']['sort_order'] : 'name';
                $data['qty_alert'] = (isset($properties['moxycart']['qty_alert'])) ? $properties['moxycart']['qty_alert'] : 0;
                $data['track_inventory'] = (isset($properties['moxycart']['track_inventory'])) ? $properties['moxycart']['track_inventory'] : 0;
                $data['specs'] = (isset($properties['moxycart']['specs'])) ? $properties['moxycart']['specs'] : array();
                $data['taxonomies'] = (isset($properties['moxycart']['taxonomies'])) ? $properties['moxycart']['taxonomies'] : array();
            }
        }
        else {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'store_id does not exist',__CLASS__);
        } 
        
        return $data;
    }
            
    /** 
     * We intercept this so we can ensure that the product always grab's the URI from the parent store
     * TODO: cache the lookup
     */
    public function save($cacheFlag= null) {
        // No store id?
        // $this->xpdo->error->failure($this->xpdo->lexicon('permission_denied'));
        if ($Store = $this->xpdo->getObject('Store', $this->get('store_id'))) {
            $this->set('uri', $Store->get('uri').$this->get('alias'));
        }
        else {
            // TODO: fire off an xpdo validator error
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Invalid Store ID',__CLASS__);
            return false;
            //$this->xpdo->error->failure('Invalid Store ID');
        }
        return parent::save($cacheFlag);
    }

}