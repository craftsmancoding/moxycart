<?php
/**
 * moxycart class file for moxycart extra
 *
 * This file retrieves data for various Moxycart functions, e.g. product lists,
 * related products.  It is primarily accessed by the assets/components/moxycart/connector.php
 * file, but really, any 3rd party could use it to retrieve data as well.
 * Copyright 2013 by Everett Griffiths everett@craftsmancoding.com
 * Created on 07-05-2013
 *
 * moxycart is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * moxycart is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * moxycart; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package moxycart
 */


 class moxycart {
    
    public $modx;

    public $action; // &a=xxx for primary Moxycart action
    
    private $core_path;
    private $assets_url;
    private $mgr_url;
    private $default_limit;
    private $connector_url; 
    private $component_id;

    
    /**
     * Map a function name to a MODX permission, e.g. 
     * 'edit_product' => 'edit_document'
     */
    private $perms = array(
        'edit_product' => 'edit_document',
    );
    
    /**
     * This is the permission tested against if nothing is explicitly defined
     * in the $perms array.
     */
    private $default_perm = 'view_document';

    public function __construct(&$modx) {
        $this->modx =& $modx;
        $this->core_path = $this->modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
        $this->assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        $this->mgr_url = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $this->component_id = isset($_GET['a']) ? (int) $_GET['a'] : 'e'; 
        $this->connector_url = $this->assets_url.'components/moxycart/connector.php?f=';
        $this->modx->addPackage('moxycart',$this->core_path.'components/moxycart/model/','moxy_');
        $this->default_limit = $this->modx->getOption('default_per_page'); // TODO: read from a MC setting?
        
        // Like controller_url, but in the mgr
        // MODx.action['moxycart:index'] + '?f=';
        if ($Action = $this->modx->getObject('modAction', array('namespace'=>'moxycart','controller'=>'index'))) {
            $this->action = $Action->get('id');
        }
        else {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'[moxycart] could not determine mgr action.');
        }
        
    }
    
    /**
     * Catch all for bad function requests.
     *
     */
    public function __call($name,$args) {
        $this->modx->log(MODX_LOG_LEVEL_ERROR,'[moxycart] Invalid function name '.__FUNCTION__);
        return $this->help($args);
    }
    //------------------------------------------------------------------------------
    //! Private
    //------------------------------------------------------------------------------
    /**
     * Generate a panel (e.g. containing instructions) for pages in the manager.
     * @param array: content1, content2, content3, help_link
     * @return string
     */
/*
    private function _get_panel($props) {
        $tpl = file_get_contents($this->core_path.'components/moxycart/layouts/panel.html');
        $uniqid = uniqid();
        $chunk = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
        $chunk->setCacheable(false);
        $props['assets_url'] = $this->assets_url;    
        return $chunk->process($props, $tpl);    
    }
*/
    /**
     * Convert a variation matrix code (json encoded)
     *
     * @param string $json formatted text
     * @return string
     */
    private function _get_variant_info($json) {
        if (empty($json)) {
            return '';
        }
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return $data;
        }
        $out = array();
        foreach ($data as $vtype_id => $vterm_id) {
            $variant = '';
            $Type = $this->modx->getObject('VariationType',$vtype_id);
            $Term = $this->modx->getObject('VariationTerm',$vterm_id);
            if ($Type && $Term) {
                $out[] = $Type->get('name') .': '.$Term->get('name');
            }
        }
        
        return implode(',', $out);
    }
    
    private function _send401() {
        header('HTTP/1.0 401 Unauthorized');
        print 'Unauthorized';
        exit;
    }
    
    /**
     * Load a view file. We put in some commonly used variables here for convenience
     *
     * @param string $file: name of a file inside of the "views" folder
     * @param array $data: an associative array containing key => value pairs, passed to the view
     * @return string
     */
    private function _load_view($file, $data=array(),$return=false) {

    	if (file_exists($this->core_path.'components/moxycart/views/'.$file)) {
    	    if (!isset($return) || $return == false) {
    	        ob_start();
    	        include ($this->core_path.'components/moxycart/views/'.$file);
    	        $output = ob_get_contents();
    	        ob_end_clean();
    	    }     
    	} 
    	else {
    		$output = $this->modx->lexicon('view_not_found', array('file'=> 'views/'.$file));
    	}
    
    	return $output;
    
    }    
    //------------------------------------------------------------------------------
    //! Public
    //------------------------------------------------------------------------------
     //------------------------------------------------------------------------------
    //! Currencies
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Manage Currencies" page
     *
     */
    public function currencies_manage($args) {
		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/currencies.js');
		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/RowEditor.js');
		$this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/moxycart.css');

    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
    		Ext.onReady(function() {   		
    			renderManageCurrencies();
    		});
    		</script>
    	');
		
        return '<div id="moxycart_canvas"></div>';
    }
 
    /**
     * Post data here to save it
     */
    public function currency_save() {
        if (!is_object($this->modx->user)) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'currency_save 401 '.print_r($_POST,true));
            return $this->_send401();
        }
        $out = array(
            'success' => true,
            'msg' => '',
        );
        
        $token = $this->modx->getOption('HTTP_MODAUTH', $_POST);   
        if ($token != $this->modx->user->getUserToken($this->modx->context->get('key'))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'currency_save FAILED. Invalid token: '.print_r($_POST,true));
            $out['success'] = false;
            $out['msg'] = 'Invalid token';
        }
        
        $action = $this->modx->getOption('action', $_POST);
        
        
        switch ($action) {
            case 'update':
                $Spec = $this->modx->getObject('Currency',$this->modx->getOption('currency_id', $_POST));
                $Spec->fromArray($_POST);
                if (!$Spec->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to update Currency.';    
                }
                $out['msg'] = 'Currency updated successfully.';    
                break;
            case 'delete':
                $Spec = $this->modx->getObject('Currency',$this->modx->getOption('currency_id', $_POST));
                if (!$Spec->remove()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to delete Currency.';    
                }
                $out['msg'] = 'Currency deleted successfully.';    
                break;
            case 'create':
            default:
                $Spec = $this->modx->newObject('Currency');    
                $Spec->fromArray($_POST);
                if (!$Spec->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Currency.';    
                }
                $out['msg'] = 'Currency created successfully.';    
        }
                
        return json_encode($out);
    }
    
    //------------------------------------------------------------------------------
    //! Images
    //------------------------------------------------------------------------------
    /**
     *
     */
    public function images_manage($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage Images here.</div>';
    }

    /**
     *
     */
    public function image_create($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Upload an image here.</div>';
    }

    /**
     * image_id
     */
    public function image_update($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Update an image here.</div>';
    }


    /**
     * @param int image_id
     */
    public function image_delete($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete Image here</div>';
    } 

    /**
     * Post data here to save it
     */
    public function image_save($args) {
        $this->modx->log(1,'image_save: '.print_r($args,true));
        // $_POST... todo
    }    
       
    //------------------------------------------------------------------------------
    //! Products
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Create Product" form.
     *
     * @param int parent (from $_GET). Defines the id of the parent page.
     */
    public function product_create($args) {
    	
    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
            var site_url = "'.MODX_SITE_URL.'";
    		Ext.onReady(function() {
    
    			MODx.load({
    				xtype: "modx-page-resource-create",
    				canSave:true,
    				mode:"create"
    			});
    		
    			renderProduct();
    		
    		});
    		</script>
    	');	

    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/util/datetime.js');
//    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/element/modx.panel.tv.renders.js');
    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');	
//    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/resource/modx.panel.resource.js');
        $this->modx->regClientStartupScript($this->mgr_url.'assets/modext/sections/resource/create.js');	
    	$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/productcontainer.js');
    	$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/manageimages.js');
    
        return '<div id="modx-panel-resource-div"> </div>';
    
    }

        //------------------------------------------------------------------------------
    //! Products
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Create Product" form.
     *
     * @param int parent (from $_GET). Defines the id of the parent page.
     */
    public function product_create2($args) {
        $this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/mgr.css');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/handlebars-v1.1.2.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery-1.7.2.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/nicedit.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery.tabify.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/script.js');
        $data = array();
        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">          
            var connector_url = "'.$this->connector_url.'";
            var redirect_url = "'.$this->mgr_url .'?a='.$this->component_id . '&f=product_update2&product_id='.'";
            // use Ext JS?
            Ext.onReady(function() {
              // populate the form
            });
            </script>
        ');

        
        return $this->_load_view('product_create.php',$data);
    }

    /**
     * Hosts the "Update Product" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function product_update($args) {
    
        $product_id = (int) $this->modx->getOption('product_id', $args);
        
        if (!$product_id) {
            return 'Invalid product_id';
        }
        
        $Product = $this->modx->getObject('Product', $product_id);
        if (!$Product) {
            return 'Invalid product_id';
        }
        
//        return '<pre>'.$Product->toJson().'</pre>';
    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/util/datetime.js');
//    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/element/modx.panel.tv.renders.js');
    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');	
//    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/resource/modx.panel.resource.js');
        $this->modx->regClientStartupScript($this->mgr_url.'assets/modext/sections/resource/update.js');	
    	$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/productcontainer.js');
        
    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
    		var site_url = "'.MODX_SITE_URL.'";
    		var product = '.$Product->toJson().';
    		Ext.onReady(function() {
    			MODx.load({
    				xtype: "modx-page-resource-update",
    				canSave:true,
    				mode:"update"
    			});
    			renderProduct();
    		});
    		</script>
    	');

        return '<div id="modx-panel-resource-div"> </div>';
    }

     /**
     * Hosts the "Update Product" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function product_update2($args) {
        $product_id = (int) $this->modx->getOption('product_id', $args);
        
        if (!$Product = $this->modx->getObject('Product', $product_id)) {        
            return 'Product not found : '.$product_id;
        }
        
        $data = $Product->toArray();
        $data['connector_url'] = $this->connector_url;
        
        // Get the dropdowns
        $data['images'] = '';
        $product_images = $this->json_images(array('product_id'=>$product_id,'limit'=>0),true);
        foreach ($product_images['results'] as $img) {
            $data['images'] .= $this->_load_view('product_image.php',$img);
        }
        
        $data['currencies'] = '';
        $currencies = $this->json_currencies(array('limit'=>0,'is_active'=>1),true);
        foreach ($currencies['results'] as $c) {
            $c['value'] = $c['currency_id'];
            $c['name'] = $c['name'];
            $c['selected'] = '';
            if ($c['value'] == $data['curency_id']) {
                $c['selected'] = ' selected="selected"';
            }
            $data['currencies'] .= $this->_load_view('option.php',$c);
        }
        
        
        $data['templates'] = '';
        $templates = $this->json_templates(array('limit'=>0),true);
        foreach ($templates['results'] as $t) {
            $t['value'] = $t['id'];
            $t['name'] = $t['name']; // WARNING: we swapped names in json_templates. not templatename!
            $t['selected'] = '';
            if ($t['value'] == $data['template_id']) {
                $t['selected'] = ' selected="selected"';
            }
            $data['templates'] .= $this->_load_view('option.php',$t);
        }
        $data['categories'] = '';
        $categories = $this->json_categories(array('limit'=>0),true);
        foreach ($categories['results'] as $c) {
            $c['value'] = $c['name'];
            $c['name'] = $c['name'];
            $c['selected'] = '';
            if ($c['value'] == $data['store_id']) {
                $c['selected'] = ' selected="selected"';
            }
            $data['categories'] .= $this->_load_view('option.php',$c);
        }

        $data['stores'] = '';
        $stores = $this->json_stores(array('limit'=>0),true);
        foreach ($stores['results'] as $s) {
            $s['value'] = $s['id'];
            $s['name'] = $s['name']; // WARNING: we swapped names in json_stores. not pagetitle!
            $s['selected'] = '';
            if ($s['value'] == $data['store_id']) {
                $s['selected'] = ' selected="selected"';
            }
            $data['stores'] .= $this->_load_view('option.php',$s);
        }
        $data['types'] = '';
        $types = $this->json_types(array('limit'=>0),true);
        foreach ($types['results'] as $t) {
            $t['value'] = $t['id'];
            $t['name'] = $t['name']; 
            $t['selected'] = '';
            if ($t['value'] == $data['type']) {
                $t['selected'] = ' selected="selected"';
            }
            $data['types'] .= $this->_load_view('option.php',$t);
        }        
        
                
        $this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/mgr.css');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/handlebars-v1.1.2.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery-1.7.2.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery-ui.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/nicedit.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery.tabify.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/dropzone.js');

    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
    		var product = '.$Product->toJson().';            
    		var connector_url = "'.$this->connector_url.'";
    		// use Ext JS?
    		Ext.onReady(function() {
    		  // populate the form
    		});
    		</script>
    	');
    	
//        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/script.js');

        return $this->_load_view('product_update.php',$data);
    }

    /**
     * Hosts the "Update Variation" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function product_delete($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete Product Page.</div>';
    }


    /**
     * Hosts the "Manage Inventory" modal window
     *
     * @param int parent (from $_GET). Defines the id of the parent page.
     * @param int product_id (from $_GET) defines the product_id
     */
    public function product_inventory($args) {
        $store_id = (int) $this->modx->getOption('store_id', $args);
        $product_id = (int) $this->modx->getOption('product_id', $args);
        if ($store_id) {
            $back_url = '?a=30&id='.$store_id;
        }
        else {
            $back_url = '?a='.$this->action.'&f=product_update&product_id=2'.$product_id;
        }

    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
    		var back_url = "'.$back_url.'";
    		Ext.onReady(function() {   		
    			renderManageInventoryPanel();
    		});
    		</script>
    	');
		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/manageinventory.js');
		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/RowEditor.js');
		$this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/moxycart.css');	
		
        return '<div id="moxycart_canvas"></div>';
    }

    /**
     * Editing the product inventory will post here.
     *
     */
	public function product_inventory_save($args) {
        $this->modx->log(MODX_LOG_LEVEL_ERROR, 'product_inventory_save: '.print_r($args,true));
        $out = array(
            'success' => true,
            'msg' => '',
        );
        $products = $this->modx->getOption('products',$args);
        
        if (!empty($products) && is_array($products)) {
            foreach ($products as $product_id => $data) {
                $Product = $this->modx->getObject('Product', $product_id);
                if(!$Product) {
                    $this->modx->log(MODX_LOG_LEVEL_ERROR,'product_inventory_save product_id not found '.$product_id);
                    continue;
                }
                
                $qty = $Product->get('qty_inventory');
                $change = (int) $data['qty_change'];
                $alert = (int) $data['qty_alert'];
                $Product->set('qty_inventory',$qty + $change);
                $Product->set('qty_alert',$alert);
                
                if (!$Product->save()) {
                    $this->modx->log(MODX_LOG_LEVEL_ERROR,'product_inventory_save failed to update inventory for product '.$product_id);
                    $out['success'] = false;
                    $out['msg'] = 'Failed to update inventory.';
                }
            }
        }
        
		return json_encode($out);
	}

    /**
     * Handles updates to a product's images, e.g. drag and drop
     *
     * @param int product_id (from $_GET) defines the product_id
     */
    public function product_images($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Handles Product Image updates</div>';
    }

    /**
     * Hosts the "Manual Sort Order" modal window: used when a user wants to specify a manual
     * sort order for the products in a container.
     *
     * @param int parent (from $_GET). Defines the id of the parent page.
     */
    public function product_sort_order($args) {
        // Add Required JS files here:
        // You can get here 2 ways: all products in a store, or all variations in a product.
        $store_id = (int) $this->modx->getOption('store_id', $args);
        $product_id = (int) $this->modx->getOption('product_id', $args);
        if ($store_id) {
            $back_url = '?a=30&id='.$store_id;
        }
        else {
            $back_url = '?a='.$this->action.'&f=product_update&product_id=2'.$product_id;
        }
    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
            var back_url = "'.$back_url.'";
    		Ext.onReady(function() {   		
    			renderProductSortPanel();
    		});
    		</script>
    	');
		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/productsortorder.js');
		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/RowEditor.js');
		$this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/moxycart.css');		
		
        return '<div id="moxycart_canvas"></div>';
    }

    /**
     * Handles editing of a single product spec
     * @param int id (from $_GET).
     */
    public function product_specs_update($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Update Product spec</div>';
    }

    /**
     * Post data here to save it
     */
    public function product_save($args) {
        $this->modx->log(1, 'product_save args: '. print_r($args,true));

        $this->modx->log(1, 'token: '. $this->modx->getOption('HTTP_MODAUTH', $args). ' usertoken: '.$this->modx->user->getUserToken($this->modx->context->get('key')));        
/*
        if (!is_object($this->modx->user)) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'spec_save 401 '.print_r($_POST,true));
            return $this->_send401();
        }
*/
        $out = array(
            'success' => true,
            'msg' => '',
        );
        
/*
        $token = $this->modx->getOption('HTTP_MODAUTH', $_POST);   
        if ($token != $this->modx->user->getUserToken($this->modx->context->get('key'))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'product_save FAILED. Invalid token: '.print_r($_POST,true));
            $out['success'] = false;
            $out['msg'] = 'Invalid token';
            return json_encode($out);        
        }
*/
        
        $action = $this->modx->getOption('action', $args);        
        unset($args['action']);
        
        $alias = $this->modx->getOption('alias',$args);
        if (empty($alias)) {
             $resource = $this->modx->newObject('modResource');
            //$args['name'] = $this->modx->resource->cleanAlias($args['name']);
            $args['alias'] = $resource->cleanAlias($args['name']);
        }
        $Store = $this->modx->getObject('modResource', $this->modx->getOption('store_id',$args));
        if (!$Store) {
            $out['success'] = false;
            $out['msg'] = 'Invalid store_id '.$this->modx->getOption('store_id',$args); 
            return json_encode($out);
        }
        $args['uri'] = $Store->get('uri') . $alias;
        
        switch ($action) {
            case 'update':
                $Product = $this->modx->getObject('Product',$this->modx->getOption('product_id', $args));

                $Product->fromArray($args);
                if (!$Product->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to update product.';    
                }
                $out['msg'] = 'Product updated successfully.';    
                break;
            case 'delete':
                $Product = $this->modx->getObject('Product',$this->modx->getOption('product_id', $args));
                if (!$Product->remove()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to delete Product.';    
                }
                $out['msg'] = 'Product deleted successfully.';    
                break;
            case 'create':
                 
                $Product = $this->modx->newObject('Product');    
                $Product->fromArray($args);
                if (!$Product->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Product.';    
                }
                $out['product_id']    = $this->modx->lastInsertId();;
                $out['msg'] = 'Product created successfully.';
                break; 
        }

        return json_encode($out);        

    }
    
    //------------------------------------------------------------------------------
    //! Specs
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Manage Variation Terms" page
     */
    public function specs_manage($args) {
		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/specs.js');
		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/RowEditor.js');
		$this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/moxycart.css');

    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
    		Ext.onReady(function() {   		
    			renderManageSpecs();
    		});
    		</script>
    	');		
        return '<div id="moxycart_canvas"></div>';
    }

    /**
     * Post data here to save it
     */
    public function spec_save() {
        if (!is_object($this->modx->user)) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'spec_save 401 '.print_r($_POST,true));
            return $this->_send401();
        }
        $out = array(
            'success' => true,
            'msg' => '',
        );
        
        $token = $this->modx->getOption('HTTP_MODAUTH', $_POST);   
        if ($token != $this->modx->user->getUserToken($this->modx->context->get('key'))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'spec_save FAILED. Invalid token: '.print_r($_POST,true));
            $out['success'] = false;
            $out['msg'] = 'Invalid token';
        }
        
        $action = $this->modx->getOption('action', $_POST);
        
        
        switch ($action) {
            case 'update':
                $Spec = $this->modx->getObject('Spec',$this->modx->getOption('spec_id', $_POST));
                $Spec->fromArray($_POST);
                if (!$Spec->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to update Spec.';    
                }
                $out['msg'] = 'Spec updated successfully.';    
                break;
            case 'delete':
                $Spec = $this->modx->getObject('Spec',$this->modx->getOption('spec_id', $_POST));
                if (!$Spec->remove()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to delete Spec.';    
                }
                $out['msg'] = 'Spec deleted successfully.';    
                break;
            case 'create':
            default:
                $Spec = $this->modx->newObject('Spec');    
                $Spec->fromArray($_POST);
                if (!$Spec->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Spec.';    
                }
                $out['msg'] = 'Spec created successfully.';    
        }
                
        return json_encode($out);
    }  
 
    //------------------------------------------------------------------------------`
    //! Variation
    //------------------------------------------------------------------------------
    /**
     *
     * @param int product_id (from $_GET). Defines the id of the parent product.
     */
    public function variation_create($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">This is the Create Variation Page.</div>';
    }

    /**
     * Hosts the "Update Variation" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function variation_update($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">This is the Update Variation Page.</div>';
    }

    /**
     * Hosts the "Delete Variation" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function variation_delete($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete Variation Page.</div>';
    }
    
    /**
     * Post data here to save it
     */
    public function variation_save() {
        // $_POST... todo
    }  
    
    //------------------------------------------------------------------------------
    //! Variation Terms
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Manage Variation Terms" page
     */
    public function variation_terms_manage($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');

		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/variation_terms.js');

		$vtype_id = (int) $this->modx->getOption('vtype_id',$args);
		
    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
    		Ext.onReady(function() {   		
    			renderVariationTerms(' . $vtype_id . ');
    		});
    		</script>
    	');	
		
		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/RowEditor.js');
		$this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/moxycart.css');		
		
        return '<div id="moxycart_canvas"></div>';
    }

    /**
     * Post data here to save it
     */
    public function variation_term_save() {
        if (!is_object($this->modx->user)) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'variation_term_save 401 '.print_r($_POST,true));
            return $this->_send401();
        }
        $out = array(
            'success' => true,
            'msg' => '',
        );
        
        $token = $this->modx->getOption('HTTP_MODAUTH', $_POST);   
        if ($token != $this->modx->user->getUserToken($this->modx->context->get('key'))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'variation_term_save FAILED. Invalid token: '.print_r($_POST,true));
            $out['success'] = false;
            $out['msg'] = 'Invalid token';
        }
        
        $action = $this->modx->getOption('action', $_POST);
        
        switch ($action) {
            case 'update':
                $VariationTerm = $this->modx->getObject('VariationTerm',$this->modx->getOption('vterm_id', $_POST));
                $VariationTerm->fromArray($_POST);
                if (!$VariationTerm->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to update Variation Term.';    
                }
                $out['msg'] = 'Variation Term updated successfully.';    
                break;
            case 'delete':
                $VariationTerm = $this->modx->getObject('VariationTerm',$this->modx->getOption('vterm_id', $_POST));
                if (!$VariationTerm->remove()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to delete Variation Term.';    
                }
                $out['msg'] = 'Variation Term deleted successfully.';    
                break;
            case 'create':
            default:
                $VariationTerm = $this->modx->newObject('VariationTerm');    
                $VariationTerm->fromArray($_POST);
                if (!$VariationTerm->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Variation Term.';    
                }
                $out['msg'] = 'Variation Term created successfully.';    
        }
                
        return json_encode($out);  
    }
    
    //------------------------------------------------------------------------------
    //! Variation Types
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Manage Variation Types" page
     *
     */
    public function variation_types_manage($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');

		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/variation_types.js');

    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
    		Ext.onReady(function() {   		
    			renderVariationTypes();
    		});
    		</script>
    	');	
		
		$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/RowEditor.js');
		$this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/moxycart.css');
		
        return '<div id="moxycart_canvas"></div>';		
		
    }

    /**
     * Post data here to save it
     */
    public function variation_type_save() {
        if (!is_object($this->modx->user)) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'variation_type_save 401 '.print_r($_POST,true));
            return $this->_send401();
        }
        $out = array(
            'success' => true,
            'msg' => '',
        );
        
        $token = $this->modx->getOption('HTTP_MODAUTH', $_POST);   
        if ($token != $this->modx->user->getUserToken($this->modx->context->get('key'))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'variation_type_save FAILED. Invalid token: '.print_r($_POST,true));
            $out['success'] = false;
            $out['msg'] = 'Invalid token';
        }
        
        $action = $this->modx->getOption('action', $_POST);
        
        
        switch ($action) {
            case 'update':
                $VariationType = $this->modx->getObject('VariationType',$this->modx->getOption('vtype_id', $_POST));
                $VariationType->fromArray($_POST);
                if (!$VariationType->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to update Variation Type.';    
                }
                $out['msg'] = 'Variation Type updated successfully.';    
                break;
            case 'delete':
                $VariationType = $this->modx->getObject('VariationType',$this->modx->getOption('vtype_id', $_POST));
                if (!$VariationType->remove()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to delete Variation Type.';    
                }
                $out['msg'] = 'Variation Type deleted successfully.';    
                break;
            case 'create':
            default:
                $VariationType = $this->modx->newObject('VariationType');    
                $VariationType->fromArray($_POST);
                if (!$VariationType->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Variation Type.';    
                }
                $out['msg'] = 'Variation Type created successfully.';    
        }
                
        return json_encode($out);   
    }

    //------------------------------------------------------------------------------
    //!
    //------------------------------------------------------------------------------

    /**
     * Hosts the "Delete Variation Term" page
     * @param int vterm_id
     */
    public function welcome($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Welcome page!</div>';
    }

   
    //------------------------------------------------------------------------------
    //! AJAX Store stuff
    //------------------------------------------------------------------------------
    /**
     * FoxyCart Categories... TODO: query the API!
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_categories($args,$raw=false) {

        $total_pages = 1;
        
        // Init our array
        $data = array(
            'results'=>array(array('id'=>'default','name'=>'Default')),
            'total' => $total_pages,
        );

        if ($raw) {
            return $data;
        }
        return json_encode($data);    
    }

    /**
     *
     *
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_currencies($args,$raw=false) {

        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'currency_id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('Currency');
        if (isset($args['is_active'])) {
            $criteria->where(array('is_active' => (int) $this->modx->getOption('is_active',$args)));
        }
        $total_pages = $this->modx->getCount('Currency',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Currency',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }

        if ($raw) {
            return $data;
        }
        return json_encode($data);

        
    }
    

    /**
     * Get a list of products.
     *
     * @param array arguents including limit, start, sort, dir
     *
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */

    public function json_products($args,$raw=false) {
    
/*
        if (!$this->modx->hasPermission($this->modx->getOption(__FUNCTION__, $this->perms, $this->default_perm))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'[moxycart::'.__FUNCTION__.'] User does not have sufficient privileges.');
            return false;
        }
*/
        
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'product_id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $parent_id = (int) $this->modx->getOption('parent_id',$args);
        $store_id = (int) $this->modx->getOption('store_id',$args);
        
        $criteria = $this->modx->newQuery('Product');
        if ($parent_id) {
            $criteria->where(array('parent_id'=>$parent_id));
        }
        if ($store_id) {
            $criteria->where(array('store_id'=>$store_id));
        } 
        $total_pages = $this->modx->getCount('Product',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Product',$criteria);
        // return $criteria->toSQL(); //<-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $row = array(
                'product_id' => $p->get('product_id'),
                'name' => $p->get('name'),
                'sku' => $p->get('sku'),
                'type' => $p->get('type'),
                'qty_inventory' => $p->get('qty_inventory'),
                'qty_alert' => $p->get('qty_alert'), 
                'price' => $p->get('price'),
                'category' => $p->get('category'),
                'uri' => $p->get('uri'),
                'is_active' => $p->get('is_active'), 
                'seq' => $p->get('seq'), 
            );
            
            $row['variant'] = $this->_get_variant_info($p->get('variant_matrix'));
            $data['results'][] = $row;
        }

        if ($raw) {
            return $data;
        }
        return json_encode($data);

    
    }

    /**
     * product_id
     
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */

    public function json_product_specs($args,$raw=false) {
        
        $product_id = (int) $this->modx->getOption('product_id',$args);
        $spec_id = (int) $this->modx->getOption('spec_id',$args);
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);

        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'id');
        $dir = $this->modx->getOption('dir',$args,'ASC');

        $product_id = (int) $this->modx->getOption('product_id',$args);
        $spec_id = (int) $this->modx->getOption('spec_id',$args);
                
        $criteria = $this->modx->newQuery('ProductSpec');
        
        if ($product_id) {
            $criteria->where(array('product_id'=>$product_id));
        }
        if ($spec_id) {
            $criteria->where(array('spec_id'=>$spec_id));
        }
                
        $total_pages = $this->modx->getCount('ProductSpec',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);

        $pages = $this->modx->getCollectionGraph('ProductSpec','{"Spec":{}}',$criteria);

        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        if($pages) {
             foreach ($pages as $p) {
                $product = $this->modx->getObject('Product',array('product_id'=>$p->get('product_id')));
                $spec = $this->modx->getObject('Spec',array('spec_id'=>$p->get('spec_id')));
               $data['results'][] = array(
                'product' => ($product) ? $product->get('name') : '',
                'spec' => ($spec) ? $spec->get('name') : '',
                'value' => $p->get('value'),
               );
             }
         }
      

        if($raw) {
            return $data;
        }
        return json_encode($data);
       

    }

    /**
     * product_id ?  if not set, then its for a store     
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_product_taxonomies($args,$raw=false) {
        $product_id = (int) $this->modx->getOption('product_id',$_REQUEST);
        
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('ProductTaxonomy');
        
        if ($product_id) {
            $criteria->where(array('product_id'=>$product_id));
        }
                
        $total_pages = $this->modx->getCount('ProductTaxonomy',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('ProductTaxonomy',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = array(
                'id' => $p->get('id'),
                'pagetitle' => $p->get('pagetitle'),
            );
        }

        if ($raw) {
            return $data;
        }
        return json_encode($data);

    }

    /**
     * Shows all the terms for the given product, filtered by product_id.
        * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)     
     */
    public function json_product_terms($args,$raw=false) {
             
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'id');
        $dir = $this->modx->getOption('dir',$args,'ASC');

        $product_id = (int) $this->modx->getOption('product_id',$args);
       
        
        $criteria = $this->modx->newQuery('ProductTerms');
        
        if ($product_id) {
            $criteria->where(array('product_id'=>$product_id));
        }
        
        $total_pages = $this->modx->getCount('ProductTerms',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Term',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );

        foreach ($pages as $p) {
            $term = $this->modx->getObject('modResource',array('id'=>$p->get('term_id')));
            $data['results'][] = array(
                'id' => $p->get('id'),
                'term' => ($term) ? $term->get('pagetitle') : '',
            );
        }

        if ($raw) {
            return $data;
        }
        return json_encode($data);

    }

    /**
     * product_id ?
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_images($args,$raw=false) {
        $product_id = (int) $this->modx->getOption('product_id',$args);
        
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'seq');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('Image');
        
        if ($product_id) {
            $criteria->where(array('product_id'=>$product_id));
        }
                
        $total_pages = $this->modx->getCount('Image',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Image',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }

        if ($raw) {
            return $data;
        }

        return json_encode($data);

    }


    /**
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_specs($args,$raw=false) {

        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'spec_id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('Spec');
        //$criteria->where();
        $total_pages = $this->modx->getCount('Spec',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Spec',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }

        if ($raw) {
            return $data;
        }
        return json_encode($data);

        
    }

    /**
     *
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_stores($args,$raw=false) {
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'menuindex');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('Store');
        $criteria->where(array('class_key'=>'Store'));
        $total_pages = $this->modx->getCount('Store',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Store',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = array(
                'id' => $p->get('id'),
                'name' => $p->get('pagetitle')
            );
        }

        if ($raw) {
            return $data;
        }
        return json_encode($data);

        
    }

    /**
     * PARAMS:
     * store_id (id)
     *
     * Gathers all available specs from the db, then gathers page's list of checked specs
     * and returns a list of all specs and a 1|0 value for each. 
     * Returns a recordset.
     *
     * In the store
     * 
     * Return: as json_specs, but with a 0|1 value set for 'is_checked' added
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_store_specs($args,$raw=false) {
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'spec_id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        $store_id = (int) $this->modx->getOption('store_id',$args);

        $Store = $this->modx->getObject('Store', $store_id);
        if (!$Store) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Store not found: '.$store_id);
            return 'Invalid store. Include valid store_id'; 
        }
        $properties = $Store->get('properties');
        $specs = $this->modx->getOption('specs',$properties);
        
        $criteria = $this->modx->newQuery('Spec');
        //$criteria->where();
        $total_pages = $this->modx->getCount('Spec',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Spec',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $vals = $p->toArray();
            if (isset($specs[$p->get('spec_id')])) {
                $vals['is_checked'] = 1; 
            }
            else {
                $vals['is_checked'] = 0; 
            }
            
            $data['results'][] = $vals;
        }


        if ($raw) {
            return $data;
        }
        return json_encode($data);

    }


    /**
     * store_id (id)
     *
     * Gathers all available taxonomies from db, then gathers page's list of checked taxonomies
     * and returns a list of all taxonomies and a 1|0 value for each. is_checked
     *
     * Return: taxonomy.pagetitle, taxonomy.id, value (1|0)
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)     
     */
    public function json_store_taxonomies($args,$raw=false) {
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        $store_id = (int) $this->modx->getOption('store_id',$args);

        $Store = $this->modx->getObject('Store', $store_id);
        if (!$Store) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Store not found: '.$store_id);
            return 'Invalid store. Include valid store_id'; 
        }
        $properties = $Store->get('properties');
        $taxonomies = $this->modx->getOption('taxonomies',$properties);
                
        $criteria = $this->modx->newQuery('Taxonomy');
        $criteria->where(array('class_key'=>'Taxonomy'));
        $total_pages = $this->modx->getCount('Taxonomy',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Taxonomy',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $is_checked = 0;
            if (isset($taxonomies[$p->get('id')])) {
                $is_checked = 1;    
            }
            $data['results'][] = array(
                'id' => $p->get('id'),
                'pagetitle' => $p->get('pagetitle'),
                'is_checked' => $is_checked
            );
        }

        if ($raw) {
            return $data;
        }
        return json_encode($data);

    }


    /**
     * store_id (id)
     *
     * Gathers all available variations from db, then gathers page's list of checked variations
     * and returns a list of all taxonomies and a 1|0 value for each.
     *
     * Return: variation_type.name, taxonomy.id, value (1|0)
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)     
     */
    public function json_store_variation_types($args,$raw=false) {
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'vtype_id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        $store_id = (int) $this->modx->getOption('store_id',$args);

        $Store = $this->modx->getObject('Store', $store_id);
        if (!$Store) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Store not found: '.$store_id);
            return 'Invalid store. Include valid store_id'; 
        }
        $properties = $Store->get('properties');
        $specs = $this->modx->getOption('variation_types',$properties);
        
        $criteria = $this->modx->newQuery('VariationType');
        //$criteria->where();
        $total_pages = $this->modx->getCount('VariationType',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('VariationType',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $vals = $p->toArray();
            if (isset($specs[$p->get('vtype_id')])) {
                $vals['is_checked'] = 1; 
            }
            else {
                $vals['is_checked'] = 0; 
            }
            
            $data['results'][] = $vals;
        }


        if ($raw) {
            return $data;
        }
        return json_encode($data);

    }


    /**
     *
     *
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */         
    public function json_taxonomies($args,$raw=false) {
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('Taxonomy');
        $criteria->where(array('class_key'=>'Taxonomy'));
        $total_pages = $this->modx->getCount('Taxonomy',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Taxonomy',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = array(
                'id' => $p->get('id'),
                'pagetitle' => $p->get('pagetitle')
            );
        }

        if ($raw) {
            return $data;
        }
        return json_encode($data);

        
    }

    /**
     *
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */     
    public function json_terms($args,$raw=false) {

        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'menuindex');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('Term');
        $criteria->where(array('class_key'=>'Term'));
        $total_pages = $this->modx->getCount('Term',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Term',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = array(
                'id' => $p->get('id'),
                'name' => $p->get('pagetitle')
            );
        }

        if ($raw) {
            return $data;
        }
        return json_encode($data);

        
    }
    
    
    /**
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_templates($args,$raw=false) {

        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('modTemplate');
        //$criteria->where();
        $total_pages = $this->modx->getCount('modTemplate',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('modTemplate',$criteria);
        
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            // $data['results'][] = $p->toArray(); // <-- too much info!
            $data['results'][] = array(
                'id' => $p->get('id'),
                'name' => $p->get('templatename')
            );
        }
        
        if ($raw) {
            return $data;
        }
        return json_encode($data);

    
    }

    /**
     * Putting this in like the rest of the data sources, even though this is hard-coded
     *
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_types($args,$raw=false) {

        // Init our array
        $data = array(
            'results'=>array(
                array('name'=>'Regular','id'=>'regular'),
                array('name'=>'Subscription','id'=>'subscription'),
                array('name'=>'Download','id'=>'download')
            ),
            'total' => 1,
        );

        if ($raw) {
            return $data;
        }
        return json_encode($data);

        
    }

    /**
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */    
    public function json_variation_types($args,$raw=false) {
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = 'VariationType.'.$this->modx->getOption('sort',$args,'vtype_id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('VariationType');
        //$criteria->where();
        $total_pages = $this->modx->getCount('VariationType',$criteria);
      
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollectionGraph('VariationType','{"Terms":{}}',$criteria);

//        return $criteria->toSQL();// <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            
            $row = $p->toArray();
            $row['terms'] = '';
            if ($p->Terms) {
                $terms = array();
                $i = 1;
                foreach ($p->Terms as $t) {
                    $terms[] = $t->get('name');
                    $i++;
                    // Max number of terms to list
                    if ($i > 3) {
                        break;
                    }
                }
                $row['terms'] = implode(', ', $terms) .'...';
            }
            $data['results'][] = $row;
        }
        
        if ($raw) {
            return $data;
        }
        return json_encode($data);

        
    }
    
    /**
     * 
     *
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_variation_terms($args,$raw=false) {
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'vterm_id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $vtype_id = (int) $this->modx->getOption('vtype_id',$args);
        
        $criteria = $this->modx->newQuery('VariationTerm');
        if ($vtype_id) {
            $criteria->where(array('vtype_id'=>$vtype_id));
        }

        $total_pages = $this->modx->getCount('VariationTerm',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollectionGraph('VariationTerm','{"Type":{}}',$criteria);
        //return $criteria->toSQL(); //<-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $val = $p->toArray();
            $val['variation_type'] = $p->Type->get('name');
            $data['results'][] = $val;
        }

        if ($raw) {
            return $data;
        }
        return json_encode($data);
    
    }


    
    /**
     * A little helper function for developers debugging the Ajax requests.
     *
     */
    public function help() {
        if (!$this->modx->hasPermission($this->modx->getOption(__FUNCTION__, $this->perms, $this->default_perm))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'[moxycart::'.__FUNCTION__.'] User does not have sufficient privileges.');
        }    
        $methods = get_class_methods($this);
        
        $out = '<p>These are the following methods available to the Moxycart class. Some of these functions supply JSON 
        data for Ajax data stores, some of these functions are intended to create Ext JS forms in the manager.</p>
        <ul>';
        $this->assets_url = $this->modx->getOption('moxycart.assets_url', null, $this->assets_url);
        $this->mgr_url = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        
        foreach ($methods as $m) {
            if(substr($m, 0, 1) != '_') {
                if (isset($_GET['a'])) {
                    $a = (int) $_GET['a'];
                    
                    if (substr($m, 0, 4) == 'json') {
                        $out .= '<li>'.$m.' <a href="'.$this->assets_url.'components/moxycart/connector.php?f='.$m.'">(Ajax)</a></li>';
                    }
                    else {
                        $out .= '<li>'.$m.' <a href="'.$this->mgr_url.'?a='.$a.'&f='.$m.'">(Manager Page)</a></li>';
                    }                                             
                }
                else {
                    if (substr($m, 0, 4) == 'json') {
                        $out .= '<li>'.$m.' <a href="'.$this->assets_url.'components/moxycart/connector.php?f='.$m.'">(Ajax)</a></li>';
                    }                
                }
            }
        }
        $out .= '</ul>';
        return $out;
    }

}