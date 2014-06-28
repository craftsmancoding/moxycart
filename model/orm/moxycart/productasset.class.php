<?php
class ProductAsset extends xPDOSimpleObject {

    /** 
     * Make sure the relations actually exist.
     */
    public function save($cacheFlag= null) {

        if (!$Asset = $this->xpdo->getObject('Asset', $this->get('asset_id'))) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Invalid Asset ID: '.$this->get('asset_id'),'',__CLASS__);
            throw new \Exception('Invalid Asset ID specified for PageAsset');
        }
        if (!$Product = $this->xpdo->getObject('Product', $this->get('product_id'))) {
            $this->xpdo->log(\modX::LOG_LEVEL_ERROR, 'Invalid Product ID: '.$this->get('product_id'),'',__CLASS__);
            throw new \Exception('Invalid Product ID specified for ProductAsset');
        }
        
        return parent::save($cacheFlag);
    }

}