<?php
class ProductField extends xPDOSimpleObject {

    /** 
     * Make sure the relations actually exist.
     */
    public function save($cacheFlag= null) {

        if (!$Field = $this->xpdo->getObject('Field', $this->get('field_id'))) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Invalid Field ID: '.$this->get('field_id'),'',__CLASS__);
            throw new \Exception('Invalid Field ID specified for ProductField');
        }
        if (!$Product = $this->xpdo->getObject('Product', $this->get('product_id'))) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Invalid Product ID: '.$this->get('product_id'),'',__CLASS__);
            throw new \Exception('Invalid Product ID specified for ProductAsset');
        }
        
        return parent::save($cacheFlag);
    }
}