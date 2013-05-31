<?php
/**
 * Override standard MODX resource behavior for our products
 *
 */
class Product extends modResource {

	public $showInContextMenu = true;
	
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','Product');
    }

	public function getContextMenuText() {
		$this->xpdo->lexicon->load('moxycart:default');
		return array(
			'text_create' => $this->xpdo->lexicon('product'),
			'text_create_here' => $this->xpdo->lexicon('product_create_here'),
		);
	}

	public static function getControllerPath(xPDO &$modx) {
	    return $modx->getOption('moxycart.core_path',null,$modx->getOption('core_path').'components/moxycart/').'controllers/';
	}

	public function getResourceTypeName() {
		$this->xpdo->lexicon->load('moxycart:default');
		return $this->xpdo->lexicon('product');
	}

}
