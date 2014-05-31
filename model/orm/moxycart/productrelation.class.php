<?php
class ProductRelation extends xPDOSimpleObject {

    /** 
     * 
     */
    public function save($cacheFlag= null) {
        if (!$this->get('product_id') || !$this->get('related_id')) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'ProductRelation cannot be saved: missing product_id or related_id','',__CLASS__);
            return false;
        }
        return parent::save($cacheFlag);
    }
}