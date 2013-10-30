<?php
/**
 * Here a Term is a product container.
 *
 */
require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class Term extends modResource {
   public $showInContextMenu = true;

    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','Term');
        $this->set('hide_children_in_tree',false);
    }
    
    public static function getControllerPath(xPDO &$modx) {
        $x = $modx->getOption('moxycart.core_path',null,$modx->getOption('core_path')).'components/moxycart/controllers/term/';
        return $x;
    }
    
    public function getContextMenuText() {
        $this->xpdo->lexicon->load('moxycart:default');
        return array(
            'text_create' => $this->xpdo->lexicon('term'),
            'text_create_here' => $this->xpdo->lexicon('term_create_here'),
        );
    }
 
    public function getResourceTypeName() {
        $this->xpdo->lexicon->load('moxycart:default');
        return $this->xpdo->lexicon('term');
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

//------------------------------------------------------------------------------
//! CreateProcessor
//------------------------------------------------------------------------------
class TermCreateProcessor extends modResourceCreateProcessor {
    /** @var ArticlesContainer $object */
    public $object;
    /**
     * Override modResourceCreateProcessor::afterSave to provide custom functionality, saving the container settings to a
     * custom field in the manager
     * {@inheritDoc}
     * @return boolean
     */
    public function afterSave() {
        // $this->modx->log(1, __FILE__ . print_r($this->object->toArray(), true));
        $this->object->set('class_key','Term');
        $this->object->set('cacheable',true);
        $this->object->set('isfolder',true);
        return parent::afterSave();
    }


}
class TermUpdateProcessor extends modResourceUpdateProcessor {
}