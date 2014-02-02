<?php
class Product extends xPDOObject {

    /**
     * Override to provide calculated fields
     */
    public function __construct(xPDO & $xpdo) { 
        parent::__construct($xpdo);
        $this->_fields['calculated_price'] = $this->get('calculated_price');
    }
    
    /**
     * Override to provide calculated fields
     */
    public function get($k, $format = null, $formatTemplate= null) {
        if ($k=='calculated_price') {
            $now = strtotime(date('Y-m-d H:i:s'));
            $sale_start = strtotime($this->get('sale_start'));
            $sale_end = strtotime($this->get('sale_end'));
        
            $lifetime = 3600; // cache 
        
             $calculated_price = $this->get('price');
            // if on sale use price sale
            if($sale_start <= $now && $sale_end >= $now) {
                $calculated_price = $this->get('price_sale');
                $lifetime = $sale_end - $now;
            } 

            return $calculated_price;            
        
        }
        else {
            return parent::get($k, $format, $formatTemplate);
        }
    }
    
    /**
     * Used to calculate how long a product could be cached for.
     * If there is a sale, the cache is good until the end of the 
     * sale. Otherwise, the product may be cached indefinitely (0).
     *
     * @return integer
     */
    public function get_lifetime() {
        
            $now = strtotime(date('Y-m-d H:i:s'));
            $sale_end = strtotime($this->get('sale_end'));
        
            if ($sale_end && $sale_end >= $now) {
                return $sale_end - $now;                
            }
                        
            return 0;
    }
}