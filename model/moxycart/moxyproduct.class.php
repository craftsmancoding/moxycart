<?php
/**
 *
 * See http://rtfm.modx.com/display/revolution20/Creating+a+Resource+Class
 */

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class Moxyproduct extends modResource {

	public $showInContextMenu = true;

	/**
	 *
	 * @param
	 */
	function __construct(xPDO & $xpdo) {
		parent :: __construct($xpdo);
		$this->set('class_key','Product');
	}

	/**
	 *
	 */
	public function getContextMenuText() {
		$this->xpdo->lexicon->load('moxycart:default');
		return array(
			'text_create' => $this->xpdo->lexicon('product'),
			'text_create_here' => $this->xpdo->lexicon('create_product'),
		);
	}
	
	/**
	 *
	 */
	public static function getControllerPath(xPDO &$modx) {
		return $modx->getOption('moxycart.core_path',null,$modx->getOption('core_path').'components/moxycart/').'controllers/';
	}
	
	/**
	 *
	 */
	public function getResourceTypeName() {
		$this->xpdo->lexicon->load('moxycart:default');
		return $this->xpdo->lexicon('product');
	}
	
}
/*EOF*/