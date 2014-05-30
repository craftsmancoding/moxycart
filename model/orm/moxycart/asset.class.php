<?php
class Asset extends xPDOObject {

    /**
     * Override to provide calculated fields
     */
/*
    public function __construct(xPDO & $xpdo) { 
        parent::__construct($xpdo);
        $this->_fields['calculated_price'] = $this->get('calculated_price');
        $this->_fields['cache_lifetime'] = $this->get('cache_lifetime');
    }
*/
    
    /**
     * Modifiers: 
     * 
     * Special behavior here for thumbnails.  If the asset is a remote asset 
     * (e.g. a full http link to imgur.com etc), then no thumbnail should be 
     * 
     * We need to do the mods here at the lowest level so that they will work
     * when complex queries (e.g. getCollectionGraph) are run.
     *
     */
/*
    public function get($k, $format = null, $formatTemplate= null) {
        // Return the sale price if the product is on sale
        if ($k=='thumbnail_url') {
            $raw  = parent::get($k, $format, $formatTemplate);
            // MODX_ASSETS_PATH . self::$modx->getOption('moxycart.upload_dir');
            // Passthru if the user has set a full URL
            if(filter_var($raw, FILTER_VALIDATE_URL)) {
                return $raw;
            }
            
        }
        else {
            return parent::get($k, $format, $formatTemplate);
        }
    }
*/

}