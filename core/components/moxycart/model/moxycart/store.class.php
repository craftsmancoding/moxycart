<?php
/**
 * Here a Store is a product container.
 *
 */
require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class Store extends modResource {
   public $showInContextMenu = true;

    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','Store');
        $this->set('hide_children_in_tree',true);
    }
    
    public static function getControllerPath(xPDO &$modx) {
        return $modx->getOption('moxycart.core_path',null,$modx->getOption('core_path')).'components/moxycart/controllers/';
    }
    
    public function getContextMenuText() {
        $this->xpdo->lexicon->load('moxycart:default');
        return array(
            'text_create' => $this->xpdo->lexicon('container'),
            'text_create_here' => $this->xpdo->lexicon('container_create_here'),
        );
    }
 
    public function getResourceTypeName() {
        $this->xpdo->lexicon->load('moxycart:default');
        return $this->xpdo->lexicon('container');
    } 

    /**
     * @return array
     */
    public function getContainerSettings() {
        return array();
/*
        $settings = $this->getProperties('moxycart');
        // @var ArticlesContainer $container
        $container = $this->getOne('Container');
        if ($container) {
            $settings = $container->getContainerSettings();
        }
        return is_array($settings) ? $settings : array();
*/
    }
    /**
     * Checks to see if the Resource has children or not. Returns the number of
     * children.
     *
     * @access public
     * @return integer The number of children of the Resource
         public function hasChildren() {
        $c = $this->xpdo->newQuery('modResource');
        $c->where(array(
            'parent' => $this->get('id'),
        ));
        return $this->xpdo->getCount('modResource',$c);
    }
    
    */

}

class StoreCreateProcessor extends modResourceCreateProcessor {
}
class StoreUpdateProcessor extends modResourceUpdateProcessor {
}