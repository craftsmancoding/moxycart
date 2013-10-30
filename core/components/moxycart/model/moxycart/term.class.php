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

    /**
     * This runs each time the tree is drawn.
     * @param array $node
     * @return array
     */
    public function prepareTreeNode(array $node = array()) {
        $this->xpdo->lexicon->load('moxycart:default');
        $menu = array();
        $idNote = $this->xpdo->hasPermission('tree_show_resource_ids') ? ' <span dir="ltr">('.$this->id.')</span>' : '';
		
		// System Default
		$template_id = $this->getOption('moxycart.default_taxonomy_template'); 
		// Or, see if this Taxonomy node sets its own default...
		$container = $this->xpdo->getObject('modResource', $this->id); 
		if ($container) {
			$props = $container->get('properties');
			if ($props) {
				if (isset($props['taxonomy']['default_template']) && !empty($props['taxonomy']['default_template'])) {
					$template_id = $props['taxonomy']['default_template'];
				}
			}
		}
        $menu[] = array(
            'text' => '<b>'.$this->get('pagetitle').'</b>'.$idNote,
            'handler' => 'Ext.emptyFn',
        );
        $menu[] = '-'; // equiv. to <hr/>
        $menu[] = array(
            'text' => $this->xpdo->lexicon('term_create_here'),
            'handler' => "function(itm,e) { 
				var at = this.cm.activeNode.attributes;
		        var p = itm.usePk ? itm.usePk : at.pk;
	
	            Ext.getCmp('modx-resource-tree').loadAction(
	                'a='+MODx.action['resource/create']
	                + '&class_key=Term'
	                + '&parent='+p
	                + '&template=".$template_id."'
	                + (at.ctx ? '&context_key='+at.ctx : '')
                );
        	}",
        );
        $menu[] = array(
            'text' => $this->xpdo->lexicon('term_duplicate'),
            'handler' => 'function(itm,e) { itm.classKey = "Term"; this.duplicateResource(itm,e); }',
        );
        $menu[] = '-';
        if ($this->get('published')) {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('term_unpublish'),
                'handler' => 'this.unpublishDocument',
            );
        } else {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('term_publish'),
                'handler' => 'this.publishDocument',
            );
        }
        if ($this->get('deleted')) {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('term_undelete'),
                'handler' => 'this.undeleteDocument',
            );
        } else {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('term_delete'),
                'handler' => 'this.deleteDocument',
            );
        }
        $menu[] = '-';
        $menu[] = array(
            'text' => $this->xpdo->lexicon('term_view'),
            'handler' => 'this.preview',
        );

        $node['menu'] = array('items' => $menu);
        $node['hasChildren'] = true;
        return $node;
    }


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