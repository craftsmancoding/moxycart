<?php
/*
Here a Store is a Product Container.

It has custom properties (stored as JSON). In other scenarios this data might be stored in 
related tables, but this data is mostly optional, overrideable, and presented as a convenience,
so the data structure is stored locally with the specific record.  

The properites include the following attributes (see getProperties() function below):

Array (
    'product_type'      => regular|subscription|download,
    'default_template'  => template id,
    'sort_order'        => name
    'qty_alert'         =>
    'track_inventory'   => 1|0 boolean
    'specs' => Array (
        1 => true
        2 => true
        ... etc...
    ),
    'variations' => Array(
        1 => true
        2 => true
        ... etc...    
    ),
    'taxonomies' => Array(
        1 => true
        2 => true
        ... etc...    
    )
 
)
*/
require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class Store extends modResource {

    public $showInContextMenu = true;

    /**
     *
     * @return string
     */ 
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','Store');
        $this->set('hide_children_in_tree',true);
    }

    /**
     *
     * @return string
     */     
    public static function getControllerPath(xPDO &$modx) {
        $x = $modx->getOption('moxycart.core_path',null,$modx->getOption('core_path').'components/moxycart/').'controllers/store/';
        return $x;
    }
    
    /**
     *
     * @return array
     */     
    public function getContextMenuText() {
        $this->xpdo->lexicon->load('moxycart:default');
        return array(
            'text_create' => $this->xpdo->lexicon('container'),
            'text_create_here' => $this->xpdo->lexicon('container_create_here'),
        );
    }

    /**
     *
     * @return string
     */ 
    public function getResourceTypeName() {
        $this->xpdo->lexicon->load('moxycart:default');
        return $this->xpdo->lexicon('container');
    } 

    /**
     * @return array
     */
    public function getDefaultProperties() {

        $properties = array (
            'type'      => 'regular',
            'template_id'  => $this->xpdo->getOption(
                'moxycart.default_product_template','',
                $this->xpdo->getOption('default_template')
            ),
            'category' => '',
            'track_inventory'   => 0,
            'fields' => array (),
            'options' =>   array(),
        );
        
        return $properties;
    }

    /**
     *
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
	                'a='+MODx.action['moxycart:index']
	                + '&f=product_create'
	                + '&store_id='+p
	                + '&type=regular'
                );
        	}",
        );
/*
        // FUTURE TODO
        $menu[] = array(
            'text' => $this->xpdo->lexicon('download_create_here'),
            'handler' => "function(itm,e) { 
				var at = this.cm.activeNode.attributes;
		        var p = itm.usePk ? itm.usePk : at.pk;
	            Ext.getCmp('modx-resource-tree').loadAction(
	                'a='+MODx.action['moxycart:index']
	                + '&f=product_create'
	                + '&store_id='+p
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
	                'a='+MODx.action['moxycart:index']
	                + '&f=product_create'
	                + '&store_id='+p
	                + '&type=subscription'
                );
        	}",
        );        
        $menu[] = '-'; // equiv. to <hr/>

        $menu[] = array(
            'text' => $this->xpdo->lexicon('manage_inventory'),
            'handler' => "function(itm,e) { 
				var at = this.cm.activeNode.attributes;
		        var p = itm.usePk ? itm.usePk : at.pk;
	            Ext.getCmp('modx-resource-tree').loadAction(
	                'a='+MODx.action['moxycart:index']
	                + '&store_id='+p
	                + '&f=manage_inventory'
                );
        	}",
        );
*/
        
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
    
    /**
     * If the parent updates the URI, we need to push this down to all the children.
     * By saving each child product, we are triggering their own lookup of the parent's URI.
     * TODO: ONLY trigger the trickle down if the URI changed.
     *      and cache the URI so the children products don't have to look it up n times.
     *      see product.class.php :: save() 
     */
    public function save($cacheFlag= null) {
        $result = parent::save($cacheFlag);
        if (!$this->isNew()) {    
            if($Products = $this->xpdo->getCollection('Product', array('store_id'=>$this->get('id')))) {
                foreach ($Products as $P) {
                    $P->save();
                }
            }
        }
        return $result;
    }
}

//------------------------------------------------------------------------------
//! CreateProcessor
//------------------------------------------------------------------------------
class StoreCreateProcessor extends modResourceCreateProcessor {
    /** 
     * @var Store $object 
     */
    public $object;
    
    /**
     * Override modResourceCreateProcessor::afterSave to provide custom functionality, saving the container settings to a
     * custom field in the manager
     * {@inheritDoc}
     * @return boolean
     */
    public function afterSave() {
        $this->object->set('class_key','Store');
        $this->object->set('hide_children_in_tree', true);
        $this->object->set('cacheable',true);
        $this->object->set('isfolder',false);
        
        
        
        return parent::afterSave();
    }

    /**
     * Override modResourceUpdateProcessor::beforeSave to provide custom functionality, saving settings for the container
     * to a custom field in the DB
     * {@inheritDoc}
     * @return boolean
     */
    public function beforeSave() {
        $this->object->set('class_key','Store');
        $this->object->set('hide_children_in_tree', true);
        $this->object->set('cacheable',true);
        $this->object->set('isfolder',false);
        
        return parent::beforeSave();

    }

}

class StoreUpdateProcessor extends modResourceUpdateProcessor {
    /** 
     * @var Store $object 
     */
    public $object;
    
    /**
     * Override modResourceCreateProcessor::afterSave to provide custom functionality, saving the container settings to a
     * custom field in the manager
     * {@inheritDoc}
     * @return boolean
     */
    public function afterSave() {
        $this->object->set('class_key','Store');
        $this->object->set('cacheable',true);
        $this->object->set('isfolder',true); // ensure we get a clean uri
        return parent::afterSave();
    }

    /**
     * Override modResourceUpdateProcessor::beforeSave to provide custom functionality, saving settings for the container
     * to a custom field in the DB.
     *
     * {@inheritDoc}
     * @return boolean
     */
    public function beforeSave() {
        $this->object->set('class_key','Store');
        $this->object->set('hide_children_in_tree', true);
        $this->object->set('cacheable',true);
        $this->object->set('isfolder',false);

        return parent::beforeSave();
    }
}