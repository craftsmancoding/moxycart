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
    
    /**
     * Calculates a signature fingerprint for the Term in its current state. 
     * Used to determine if the term has changed.  The calculation must include
     * parent (most importantly) so that updates on the parents can be triggered
     * if a term is moved.  The fingerprint calculation should also include *all* 
     * data points stored in the "children" array hierarchy. 
     *
     * @return string
     */
    private function _calc_fingerprint() {
        $properties = $this->get('properties');
        $children = $this->xpdo->getOption('children',$properties,array());
        return md5($this->get('parent').$this->get('alias')
            .$this->get('pagetitle').$this->get('menuindex').json_encode($children));
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

    /**
     * We override/enhance the parent save() operation so we can cache the 
     * hierarchical data in there as the terms are manipulated.
     *
     * properties is rendered as JSON, has the following structure:
     *
     * Array(
        'fingerprint' => used to determine if a term was updated
        'prev_parent' => used to determine if a term was moved.
        'children' => Array(        
            $page_id => Array( 
                'alias' => $alias
                'pagetitle' => $pagetitle
                'published' => $published
                'children' => Array(**RECURSION of the $page_id array**)
             ),
        )
        
     * Updating a term triggers a ripple UP the tree.
     * Moving a term up/down in the hierarchy forces an unsetting in the prev_parent
     */
    public function save($cacheFlag=null) {
        $properties = $this->get('properties');
        $fingerprint = $this->xpdo->getOption('fingerprint',$properties); // the old one
        $prev_parent = $this->xpdo->getOption('prev_parent',$properties); 
        $children = $this->xpdo->getOption('children',$properties,array());
        $properties['fingerprint'] = $this->_calc_fingerprint(); // the new one
        $properties['prev_parent'] = $this->get('parent');
        $this->set('properties', $properties);
        $rt = parent::save($cacheFlag); // <-- the normal save

        // old == new ?
        if ($fingerprint == $properties['fingerprint']) {
            $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, $this->get('id').': Fingerprint unchanged. No action taken.','',__CLASS__,basename(__FILE__),__LINE__);
            $rt = parent::save($cacheFlag); 
            return $rt; // nothing to do
        }
        $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, 'New Fingerprint detected.',$this->get('id'),__CLASS__,basename(__FILE__),__LINE__);

        // moved?  Run unset on prev_parent to remove this term as a child
        if ($prev_parent != $this->get('parent')) {
            $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, $this->get('id').': Move in the hierarchy detected from '.$prev_parent .' to '. $this->get('parent'),'',__CLASS__,basename(__FILE__),__LINE__);
            $PrevParent = $this->xpdo->getObject('modResource', $prev_parent);
            if ($PrevParent) {
                $prev_parent_props = $PrevParent->get('properties');
                unset($prev_parent_props['children'][$this->get('id')]);
                unset($prev_parent_props['children_ids'][$this->get('id')]);
                $PrevParent->set('properties',$prev_parent_props);
                if (!$PrevParent->save()) { // <-- this may ripple up
                    $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, $this->get('id').': Error saving previous parent '.$prev_parent,'',__CLASS__,basename(__FILE__),__LINE__); 
                }
            }
        }
       
        $Parent = $this->xpdo->getObject('modResource', $this->get('parent'));
        if (!$Parent) {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Parent not found!',$this->get('id'),__CLASS__,basename(__FILE__),__LINE__);
            return $rt; // nothing we can do
        }
        $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, $this->get('id').': Updating the parent ('.$this->get('parent').')','',__CLASS__,basename(__FILE__),__LINE__);
        
        $parent_props = $Parent->get('properties');
        
        // Children may be out of date by this point        
        $parent_props['children'][$this->get('id')] = array(
            'alias' => $this->get('alias'),
            'pagetitle' => $this->get('pagetitle'),
            'published' => $this->get('published'),
            'menuindex' => $this->get('menuindex'),
            'children' => $children // out of date... arg...
        );
        $parent_props['children_ids'][$this->get('id')] = true;
        $Parent->set('properties', $parent_props);
        if (!$Parent->save()) { // <-- this may ripple up
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, $this->get('id').': Error saving parent '.$this->get('parent'),'',__CLASS__,basename(__FILE__),__LINE__);
        }
        
        // return $this->save($cacheFlag);
        return $rt;
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
        // $this->modx->log(1, basename(__FILE__) . print_r($this->object->toArray(), true));
        $this->object->set('class_key','Term');
        $this->object->set('cacheable',true);
        $this->object->set('isfolder',true);
        return parent::afterSave();
    }


}
class TermUpdateProcessor extends modResourceUpdateProcessor {
}