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
    public function get($k, $format = null, $formatTemplate= null) {
        // Return the sale price if the product is on sale
        if ($k=='thumbnail_url') {
            $raw  = parent::get($k, $format, $formatTemplate);
            // Fallback to placehold.it e.g. http://placehold.it/350x150&text=PDF
            if (empty($raw)) {
                $ext = strtolower(strrchr($this->get('url'), '.'));
                $w = $this->xpdo->getOption('moxycart.thumbnail_width');
                $h = $this->xpdo->getOption('moxycart.thumbnail_height');
                return sprintf('http://placehold.it/%sx%s&text=%s',$w,$h,$ext);
            }
            // Passthru if the user has set a full URL
            elseif(filter_var($raw, FILTER_VALIDATE_URL)) {
                return $raw;
            }

            return MODX_ASSETS_URL . $this->xpdo->getOption('moxycart.upload_dir').$raw;
            
        }
        else {
            return parent::get($k, $format, $formatTemplate);
        }
    }

}