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

    public function __construct(&$modx, &$config = array()) {
        $this->modx =& $modx;
        $this->props =& $config;
        $this->core_path = $this->modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
        $this->assets_url = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        $this->modx->addPackage('moxycart',$this->core_path.'components/moxycart/model/','moxy_');
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
    public function currencies_manage() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage Currencies here.</div>';
    }

    /**
     *
     */
    public function currency_create() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Create a Currency here.</div>';
    }

    /**
     * currency_id
     */
    public function currency_update() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Update a Currency here.</div>';
    }


    /**
     * @param int currency_id
     */
    public function currency_delete() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete Currency here</div>';
    }
 
    //------------------------------------------------------------------------------
    //! Images
    //------------------------------------------------------------------------------
    /**
     *
     */
    public function images_manage() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage Images here.</div>';
    }

    /**
     *
     */
    public function image_create() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Upload an image here.</div>';
    }

    /**
     * image_id
     */
    public function image_update() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Update an image here.</div>';
    }


    /**
     * @param int image_id
     */
    public function image_delete() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
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
    public function product_create() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">This is the Create Product Page.</div>';
    }

    /**
     * Hosts the "Update Product" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function product_update() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">This is the Update Product Page.</div>';
    }

    /**
     * Hosts the "Update Variation" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function product_delete() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete Product Page.</div>';
    }


    /**
     * Hosts the "Manage Inventory" modal window
     *
     * @param int parent (from $_GET). Defines the id of the parent page.
     * @param int product_id (from $_GET) defines the product_id
     */
    public function product_inventory() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage your inventory here.</div>';
    }

    /**
     * Handles updates to a product's images, e.g. drag and drop
     *
     * @param int product_id (from $_GET) defines the product_id
     */
    public function product_images() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Handles Product Image updates</div>';
    }

    /**
     * Hosts the "Manual Sort Order" modal window: used when a user wants to specify a manual
     * sort order for the products in a container.
     *
     * @param int parent (from $_GET). Defines the id of the parent page.
     */
    public function product_sort_order() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Sort your products manually, using drag-and-drop.</div>';
    }

    /**
     * Handles editing of a single product spec
     * @param int id (from $_GET).
     */
    public function product_specs_update() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Update Product spec</div>';
    }

    //------------------------------------------------------------------------------
    //! Specs
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Manage Variation Terms" page
     */
    public function specs_manage() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage your Specs.</div>';
    }

    /**
     * Hosts the "Create Variation Term" page
     * @param int vterm_id
     */
    public function spec_create() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Create spec.</div>';
    }

    /**
     * Hosts the "Delete Variation Term" page
     * @param int vterm_id
     */
    public function spec_delete() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete spec</div>';
    }

  
 
    //------------------------------------------------------------------------------`
    //! Variation
    //------------------------------------------------------------------------------
    /**
     *
     * @param int product_id (from $_GET). Defines the id of the parent product.
     */
    public function variation_create() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">This is the Create Variation Page.</div>';
    }

    /**
     * Hosts the "Update Variation" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function variation_update() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">This is the Update Variation Page.</div>';
    }

    /**
     * Hosts the "Delete Variation" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function variation_delete() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete Variation Page.</div>';
    }
    
  
    
    //------------------------------------------------------------------------------
    //! Variation Terms
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Manage Variation Terms" page
     */
    public function variation_terms_manage() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage your Variation Terms here.</div>';
    }

    /**
     * Hosts the "Create Variation Term" page
     * @param int vterm_id
     */
    public function variation_term_create() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Create variation term.</div>';
    }

    /**
     * Hosts the "Delete Variation Term" page
     * @param int vterm_id
     */
    public function variation_term_delete() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete variation term.</div>';
    }

    //------------------------------------------------------------------------------
    //! Variation Types
    //------------------------------------------------------------------------------
    /**
     * Hosts the "Manage Variation Types" page
     *
     */
    public function variation_types_manage() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Manage your Variation Types here.</div>';
    }

    /**
     * Hosts the "Manage Variation Types" page
     *
     */
    public function variation_type_create() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Create a Variation Type here.</div>';
    }

    /**
     * Hosts the "Manage Variation Terms" page
     * @param int vtype_id
     */
    public function variation_type_delete() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Delete a Variation Type here</div>';
    }


    //------------------------------------------------------------------------------
    //!
    //------------------------------------------------------------------------------

    /**
     * Hosts the "Delete Variation Term" page
     * @param int vterm_id
     */
    public function welcome() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        // Add Required JS files here:
        //$this->addJavascript($assetsUrl'components/moxycart/test.js');
        return '<div id="moxycart_canvas">Welcome page!</div>';
    }
   
    //------------------------------------------------------------------------------
    //! AJAX Store stuff
    //------------------------------------------------------------------------------
    /**
     * FoxyCart Categories... TODO: query the API!
     */
    public function json_categories() {

        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'name');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
        //$criteria = $this->modx->newQuery('modTemplate');
        //$criteria->where();
        //$total_pages = $this->modx->getCount('modTemplate',$criteria);
        $total_pages = 1;
        //$criteria->limit($limit, $start); 
        //$criteria->sortby($sort,$dir);
        //$pages = $this->modx->getCollection('modTemplate',$criteria);
        
        // Init our array
        $data = array(
            'results'=>array(array('name'=>'Default')),
            'total' => $total_pages,
        );
        //foreach ($pages as $p) {
            // $data['results'][] = $p->toArray(); // <-- too much info!
        //    $data['results'][] = array(
        //        'name' => 'Default'
        //    );
        //}
        
        return json_encode($data);
    
    }

    public function json_currencies() {

        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'currency_id');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
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

        return json_encode($data);
        
    }
    
    /**
     *
     */
    public function json_specs() {

        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'spec_id');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
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

        return json_encode($data);
        
    }

    /**
     * Get a list of products.
     *
     * @param array arguents including limit, start, sort, dir
     *
     * @return mixed JSON-encoded string or PHP array (depends on $json flag). False on permissions error.
     */
    public function json_products() {
    
        if (!$this->modx->hasPermission($this->modx->getOption(__FUNCTION__, $this->perms, $this->default_perm))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'[moxycart::'.__FUNCTION__.'] User does not have sufficient privileges.');
            return false;
        }
        
        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'product_id');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
        $criteria = $this->modx->newQuery('Product');
        //$criteria->where();
        $total_pages = $this->modx->getCount('Product',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Product',$criteria);
        //return $criteria->toSQL(); //<-- useful for debugging
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

        return json_encode($data);
    
    }

    /**
     * product_id
     
     * Return: spec.name, value, spec.description
     */
    public function json_product_specs() {
        $product_id = (int) $this->modx->getOption('product_id',$_REQUEST);
        $spec_id = (int) $this->modx->getOption('spec_id',$_REQUEST);
        
        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'id');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
        $criteria = $this->modx->newQuery('ProductSpecs');
        
        if ($product_id) {
            $criteria->where(array('product_id'=>$product_id));
        }
        if ($spec_id) {
            $criteria->where(array('spec_id'=>$spec_id));
        }
                
        $total_pages = $this->modx->getCount('ProductSpecs',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('ProductSpecs',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }

        return json_encode($data);
    }

    /**
     * product_id ?  if not set, then its for a store     
     * Return: taxonomy.pagetitle, value (1|0)
     */
    public function json_product_taxonomies() {
        $product_id = (int) $this->modx->getOption('product_id',$_REQUEST);
        
        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'id');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
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

        return json_encode($data);
    }

    /**
     * product_id ?  if not set, then its for a store     
     * Return: taxonomy.pagetitle, value (1|0)
     */
    public function json_product_terms() {
        return 'TODO';
        $product_id = (int) $this->modx->getOption('product_id');
        
        $criteria = $this->modx->newQuery('ProductTaxonomy');
        
//        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('ProductTaxonomy',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }

        return json_encode($data);
    }

    /**
     * product_id ?
     * Return: ???
     */
    public function json_images() {
        $product_id = (int) $this->modx->getOption('product_id',$_REQUEST);
        
        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'image_id');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
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

        return json_encode($data);
    }

    /**
     * product_id or store_id
     * Return: ???
     */
    public function json_inventory() {
        return 'TODO';
        $product_id = (int) $this->modx->getOption('product_id');
        
        $criteria = $this->modx->newQuery('ProductTaxonomy');
        
//        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('ProductTaxonomy',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }

        return json_encode($data);
    }


    public function json_stores() {

        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'menuindex');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
        $criteria = $this->modx->newQuery('Store');
        //$criteria->where();
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

        return json_encode($data);
        
    }

    /**
     * store_id (id)
     *
     * Gathers all available specs from the db, then gathers page's list of checked specs
     * and returns a list of all specs and a 1|0 value for each.
     *
     * Return: spec.name, value (1|0)
     */
    public function json_store_specs() {
        return 'TODO';
        $product_id = (int) $this->modx->getOption('product_id');
        
        $criteria = $this->modx->newQuery('ProductTaxonomy');
        
//        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('ProductTaxonomy',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }

        return json_encode($data);
    }


    /**
     * store_id (id)
     *
     * Gathers all available taxonomies from db, then gathers page's list of checked taxonomies
     * and returns a list of all taxonomies and a 1|0 value for each.
     *
     * Return: taxonomy.pagetitle, taxonomy.id, value (1|0)
     */
    public function json_store_taxonomies() {
        return 'TODO';
        $product_id = (int) $this->modx->getOption('product_id');
        
        $criteria = $this->modx->newQuery('ProductTaxonomy');
        
//        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('ProductTaxonomy',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
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
     */
    public function json_store_variation_types() {
        return 'TODO';
        $product_id = (int) $this->modx->getOption('product_id');
        
        $criteria = $this->modx->newQuery('ProductTaxonomy');
        
//        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('ProductTaxonomy',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }

        return json_encode($data);
    }

    
    public function json_taxonomies() {
        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'id');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
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

        return json_encode($data);
        
    }

    /**
     *
     */
    public function json_templates() {

        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'id');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
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
        
        return json_encode($data);
    
    }


    
    public function json_variations() {
        $limit = (int) $this->modx->getOption('limit',$_POST,10);
        $start = (int) $this->modx->getOption('start',$_POST,0);
        $sort = $this->modx->getOption('sort',$_POST,'vtype_id');
        $dir = $this->modx->getOption('dir',$_POST,'ASC');
        
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

        return json_encode($data);
        
    }
    
    /**
     * Biggest problem here is caching... it's too slow to retrieve hierarchical data.
     *
     * taxonomy_id 
     */
    public function json_variation_terms() {
        return 'TODO';
        $product_id = (int) $this->modx->getOption('product_id',$_REQUEST);
        
        $criteria = $this->modx->newQuery('ProductTaxonomy');
        
//        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('ProductTaxonomy',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
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
        $assetsUrl = $this->modx->getOption('moxycart.assets_url', null, MODX_ASSETS_URL);
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        
        foreach ($methods as $m) {
            if(substr($m, 0, 1) != '_') {
                if (isset($_GET['a'])) {
                    $a = (int) $_GET['a'];
                    
                    if (substr($m, 0, 4) == 'json') {
                        $out .= '<li>'.$m.' <a href="'.$assetsUrl.'components/moxycart/connector.php?f='.$m.'">(Ajax)</a></li>';
                    }
                    else {
                        $out .= '<li>'.$m.' <a href="'.$mgrUrl.'?a='.$a.'&f='.$m.'">(Manager Page)</a></li>';
                    }                                             
                }
                else {
                    if (substr($m, 0, 4) == 'json') {
                        $out .= '<li>'.$m.' <a href="'.$assetsUrl.'components/moxycart/connector.php?f='.$m.'">(Ajax)</a></li>';
                    }                
                }
            }
        }
        $out .= '</ul>';
        return $out;
    }

}