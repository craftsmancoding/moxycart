<?php
class OptionTerm extends xPDOObject {

    /**
     * Override to provide calculated fields
     */
    public function __construct(xPDO & $xpdo) { 
        parent::__construct($xpdo);
        $this->_fields['modifiers'] = $this->get('modifiers');
    }
    
    /**
     * Modifiers: overrides to provide calculated fields
     * See https://wiki.foxycart.com/v/0.6.0/getting_started/adding_links_and_forms#emptying_the_cart_before_adding_a_product
     *
     *      p for price
     *      w for weight
     *      c for product code
     *      y for category (v070+ only)
     *
     *      + add to existing value
     *      - subtract from existing value
     *      : set existing value
     */
    public function get($k, $format = null, $formatTemplate= null) {
        // 
        if ($k=='modifiers') {
            $this->xpdo->log(modX::LOG_LEVEL_DEBUG, 'Calculating virtual field: modifiers','',__CLASS__);
            if ($this->get('mod_price') || $this->get('mod_weight') || $this->get('mod_code') || $this->get('mod_category')) {
                $mods = array();
                    if ($this->get('mod_price')) {
                        $price = $this->get('mod_price');
                        if ($this->get('mod_price_type') == '-') {
                            $mods[] = 'p-'.$price;
                        }
                        elseif ($this->get('mod_price_type') == ':') {
                            $mods[] = 'p:'.$price;                        
                        }
                        else {
                            $mods[] = 'p+'.$price;
                        }
                    }
                    if ($this->get('mod_weight')) {
                        $weight = $this->get('mod_weight');
                        if ($this->get('mod_weight_type') == '-') {
                            $mods[] = 'w-'.$weight;
                        }
                        elseif ($this->get('mod_weight_type') == ':') {
                            $mods[] = 'w:'.$weight;                        
                        }
                        else {
                            $mods[] = 'w+'.$weight;
                        }
                    }
                    if ($this->get('mod_code')) {
                        $code = $this->get('mod_weight');
                        if ($this->get('mod_code_type') == '-') {
                            $mods[] = 'c-'.$code;
                        }
                        elseif ($this->get('mod_code_type') == ':') {
                            $mods[] = 'c:'.$code;                        
                        }
                        else {
                            $mods[] = 'c+'.$code;
                        }
                    }
                    if ($this->get('mod_category')) {
                        $cat = $this->get('mod_category');
                        if ($this->get('mod_category_type') == '-') {
                            $mods[] = 'y-'.$cat;
                        }
                        elseif ($this->get('mod_category_type') == ':') {
                            $mods[] = 'y:'.$cat;                        
                        }
                        else {
                            $mods[] = 'y+'.$cat;
                        }
                    }
                    
                    return '{'.implode('|',$mods).'}';
            }

            return '';
        
        }
        else {
            return parent::get($k, $format, $formatTemplate);
        }
    }


}