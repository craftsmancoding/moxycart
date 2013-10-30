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
        $x = $modx->getOption('moxycart.core_path',null,$modx->getOption('core_path')).'components/moxycart/controllers/store/';
        return $x;
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
    public function prepareTreeNode(array $node = array()) {
        $this->xpdo->lexicon->load('moxycart:default');
        $menu = array();
        $idNote = $this->xpdo->hasPermission('tree_show_resource_ids') ? ' <span dir="ltr">('.$this->id.')</span>' : '';

        $menu[] = array(
            'text' => '<b>'.$this->get('pagetitle').'</b>'.$idNote,
            'handler' => 'Ext.emptyFn',
        );
        $menu[] = '-'; // equiv. to <hr/>
        $menu[] = array(
            'text' => $this->xpdo->lexicon('product_create_here'),
            'handler' => "function(itm,e) { 
				var at = this.cm.activeNode.attributes;
		        var p = itm.usePk ? itm.usePk : at.pk;
	            Ext.getCmp('modx-resource-tree').loadAction(
	                'a='+MODx.action['moxycart:product_create']
	                + '&parent='+p
	                + '&type=regular'
                );
        	}",
        );
        $menu[] = array(
            'text' => $this->xpdo->lexicon('download_create_here'),
            'handler' => "function(itm,e) { 
				var at = this.cm.activeNode.attributes;
		        var p = itm.usePk ? itm.usePk : at.pk;
	            Ext.getCmp('modx-resource-tree').loadAction(
	                'a='+MODx.action['moxycart:product_create']
	                + '&parent='+p
	                + '&type=download'
                );
        	}",
        );
        $menu[] = array(
            'text' => $this->xpdo->lexicon('subscription_create_here'),
            'handler' => "function(itm,e) { 
				var at = this.cm.activeNode.attributes;
		        var p = itm.usePk ? itm.usePk : at.pk;
	            Ext.getCmp('modx-resource-tree').loadAction(
	                'a='+MODx.action['moxycart:product_create']
	                + '&parent='+p
	                + '&type=subscription'
                );
        	}",
        );        
        $menu[] = '-'; // equiv. to <hr/>
        $menu[] = array(
            'text' => $this->xpdo->lexicon('container_duplicate'),
            'handler' => 'function(itm,e) { itm.classKey = "Term"; this.duplicateResource(itm,e); }',
        );
        
        if ($this->get('published')) {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('container_unpublish'),
                'handler' => 'this.unpublishDocument',
            );
        } else {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('container_publish'),
                'handler' => 'this.publishDocument',
            );
        }
        if ($this->get('deleted')) {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('container_undelete'),
                'handler' => 'this.undeleteDocument',
            );
        } else {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('container_delete'),
                'handler' => 'this.deleteDocument',
            );
        }
        $menu[] = '-';
        $menu[] = array(
            'text' => $this->xpdo->lexicon('container_view'),
            'handler' => 'this.preview',
        );

        $node['menu'] = array('items' => $menu);
        $node['hasChildren'] = false;
        return $node;
    }
}

//------------------------------------------------------------------------------
//! CreateProcessor
//------------------------------------------------------------------------------
class StoreCreateProcessor extends modResourceCreateProcessor {
    /** @var ArticlesContainer $object */
    public $object;
    /**
     * Override modResourceCreateProcessor::afterSave to provide custom functionality, saving the container settings to a
     * custom field in the manager
     * {@inheritDoc}
     * @return boolean
     */
    public function afterSave() {
        $this->modx->log(1, __FILE__ . print_r($this->object->toArray(), true));
        $this->object->set('class_key','Store');
        $this->object->set('cacheable',true);
        $this->object->set('isfolder',false);
        return parent::afterSave();
    }


}
class StoreUpdateProcessor extends modResourceUpdateProcessor {
}