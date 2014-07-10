<?php
class Product extends xPDOObject {

    /**
     * Override to provide calculated fields
     */
    public function __construct(xPDO & $xpdo) { 
        parent::__construct($xpdo);
        $this->_fields['change_inventory'] = $this->get('change_inventory');
        $this->_fields['calculated_price'] = $this->get('calculated_price');
        $this->_fields['cache_lifetime'] = $this->get('cache_lifetime');
    }
    
    /**
     * Modifiers: overrides to provide calculated fields
     */
    public function get($k, $format = null, $formatTemplate= null) {
        // Return the sale price if the product is on sale
        if ($k=='calculated_price') {
            $this->xpdo->log(modX::LOG_LEVEL_DEBUG, 'Calculating virtual field: calculated_price','',__CLASS__);
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
        // Determines how long we can cache this for
        elseif($k=='cache_lifetime') {
            $this->xpdo->log(modX::LOG_LEVEL_DEBUG, 'Calculating virtual field: cache_lifetime','',__CLASS__);
            $now = time();
            $sale_end = strtotime($this->get('sale_end'));
            if ($sale_end && $sale_end >= $now) {
                return $sale_end - $now;                
            }
                        
            return 0;        
        }
        elseif($k=='sale_start') {
            $v = parent::get($k, $format, $formatTemplate);
            if ($v == '0000-00-00 00:00:00') {
                return '';
            }
            return $v;
        }
        elseif($k=='sale_end') {
            $v = parent::get($k, $format, $formatTemplate);
            if ($v == '0000-00-00 00:00:00') {
                return '';
            }
            return $v;
        }
        else {
            return parent::get($k, $format, $formatTemplate);
        }
    }

    /**
     * Mutators
     * We use "change_inventory"
     */
    public function set($k, $v= null, $vType= '') {     
        if ($k == 'change_inventory') {
            $v = (int) $v;
            $qty = $this->get('qty_inventory');
            return $this->set('qty_inventory', $qty + $v);
        }
        else {
            return parent::set($k, $v, $vType);
        }
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
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Invalid Store ID','',__CLASS__);
            return false;
            //$this->xpdo->error->failure('Invalid Store ID');
        }
        return parent::save($cacheFlag);
    }

}