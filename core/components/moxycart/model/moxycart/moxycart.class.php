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
    /** @var $modx modX */
    public $modx;
    /** @var $props array */
    public $props;

    private $core_path;
    private $assets_url;
    private $mgr_url;
    private $default_limit;
    
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
        $this->modx->addPackage('moxycart',$this->core_path.'components/moxycart/model/','moxy_');
        $this->default_limit = $this->modx->getOption('default_per_page'); // TODO: read from a MC setting?
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
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage Currencies here.</div>';
    }

    /**
     *
     */
    public function currency_create($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Create a Currency here.</div>';
    }

    /**
     * currency_id
     */
    public function currency_update($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Update a Currency here.</div>';
    }


    /**
     * @param int currency_id
     */
    public function currency_delete($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete Currency here</div>';
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
    
       
    //------------------------------------------------------------------------------
    //! Products
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Create Product" form.
     *
     * @param int parent (from $_GET). Defines the id of the parent page.
     */
    public function product_create($args) {
    	
    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/util/datetime.js');
    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/element/modx.panel.tv.renders.js');
    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');	
    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
    	$this->modx->regClientStartupScript($this->mgr_url.'assets/modext/widgets/resource/modx.panel.resource.js');
        $this->modx->regClientStartupScript($this->mgr_url.'assets/modext/sections/resource/create.js');	
    	$this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/productcontainer.js');
    	
    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
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
    
        return '<div id="modx-panel-resource-div"> </div>';
    
    }

    /**
     * Hosts the "Update Product" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function product_update($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">This is the Update Product Page.</div>';
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
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage your inventory here.</div>';
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
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Sort your products manually, using drag-and-drop.</div>';
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

    //------------------------------------------------------------------------------
    //! Specs
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Manage Variation Terms" page
     */
    public function specs_manage($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage your Specs.</div>';
    }

    /**
     * Hosts the "Create Variation Term" page
     * @param int vterm_id
     */
    public function spec_create($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Create spec.</div>';
    }

    /**
     * Hosts the "Delete Variation Term" page
     * @param int vterm_id
     */
    public function spec_delete($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete spec</div>';
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
    
  
    
    //------------------------------------------------------------------------------
    //! Variation Terms
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Manage Variation Terms" page
     */
    public function variation_terms_manage($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage your Variation Terms here.</div>';
    }

    /**
     * Hosts the "Create Variation Term" page
     * @param int vterm_id
     */
    public function variation_term_create($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Create variation term.</div>';
    }

    /**
     * Hosts the "Delete Variation Term" page
     * @param int vterm_id
     */
    public function variation_term_delete($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete variation term.</div>';
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
        return '<div id="moxycart_canvas">Manage your Variation Types here.</div>';
    }

    /**
     * Hosts the "Manage Variation Types" page
     *
     */
    public function variation_type_create($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Create a Variation Type here.</div>';
    }

    /**
     * Hosts the "Manage Variation Terms" page
     * @param int vtype_id
     */
    public function variation_type_delete($args) {
        // Add Required JS files here:
        //$this->regClientStartupScript($this->assets_url'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete a Variation Type here</div>';
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

        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'name');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $total_pages = 1;
        
        // Init our array
        $data = array(
            'results'=>array(array('name'=>'Default')),
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
        //$criteria->where();
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
            $data['results'][] = array(
                'id' => $p->get('id'),
                'name' => $p->get('name'),
                'sku' => $p->get('sku'),
                'type' => $p->get('type'),
                'qty_inventory' => $p->get('qty_inventory'),
                'qty_alert' => $p->get('qty_alert'), 
                'price' => $p->get('price'),
                'category' => $p->get('category'),
                'uri' => $p->get('uri'),
                'is_active' => $p->get('is_active'), 
            );
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
                $data['results'][] = $p->toArray();
            }
        }

        if ($raw) {
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
     * Shows all the terms for the given product, filtered by taxonomy_id or by product_id.
     * Taxonomy_id is special: you can use it to retrieve hierarchical terms... taxonomy_id
     * can be a term_id. 
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)     
     */
    public function json_product_terms($args,$raw=false) {
             
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'id');
        $dir = $this->modx->getOption('dir',$args,'ASC');

        $product_id = (int) $this->modx->getOption('product_id',$args);
        $term_id = (int) $this->modx->getOption('term_id',$args);
        $taxonomy_id = (int) $this->modx->getOption('taxonomy_id',$args); // trickier
        
        $criteria = $this->modx->newQuery('ProductTerms');
        
        if ($product_id) {
            $criteria->where(array('product_id'=>$product_id));
        }
        if ($term_id) {
            $criteria->where(array('term_id'=>$term_id));
        }
        if ($taxonomy_id) {
            $Tax = $this->modx->getObject('modResource', array('id'=>$taxonomy_id, 'class_key'=>'Taxonomy'));
            
            if ($Tax) {
                $properties = $Tax->get('properties');
                $children_ids = array_keys($Tax->getOption('children_ids',$properties,array()));
                $criteria->where(array('term_id:IN'=>$children_ids));
            }
            else {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Taxonomy not found: '.$taxonomy_id);
            }
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
     * product_id ?
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_images($args,$raw=false) {
        $product_id = (int) $this->modx->getOption('product_id',$_REQUEST);
        
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'image_id');
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
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */    
    public function json_variation_types($args,$raw=false) {
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'vtype_id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
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
            $data['results'][] = $p->toArray();
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
            $criteria->where(array('vtype_id'=>$parent_id));
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
        $this->assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
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