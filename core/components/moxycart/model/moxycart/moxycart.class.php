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
    }
    
    /**
     * Catch all for bad function requests.
     *
     */
    public function __call($name,$args) {
        $this->modx->log(MODX_LOG_LEVEL_ERROR,'[moxycart] Invalid function name '.__FUNCTION__);
        return false;
    }
    //------------------------------------------------------------------------------
    //! Private
    //------------------------------------------------------------------------------
    /**
     * Generate a panel (e.g. containing instructions) for pages in the manager.
     * @param array: content1, content2, content3, help_link
     * @return string
     */
    private function _get_panel($props) {
        $tpl = file_get_contents($this->core_path.'components/moxycart/layouts/panel.html');
        $uniqid = uniqid();
        $chunk = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
        $chunk->setCacheable(false);
         
        return $chunk->process($props, $tpl);    
    }
    
    //------------------------------------------------------------------------------
    //! Public
    //------------------------------------------------------------------------------
    /**
     * Generates a panel for instructional purposes. 
     * @return html string
     */
    public function create_product_container() {
        if (!$this->modx->hasPermission($this->modx->getOption(__FUNCTION__, $this->perms, $this->default_perm))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'[moxycart::'.__FUNCTION__.'] User does not have sufficient privileges.');
            return false;
        }
        $this->modx->lexicon->load('moxycart:default');
        $props = array();
        $props['content1'] = $this->modx->lexicon('creating_product_container_video');
        $props['content2'] = $this->modx->lexicon('creating_product_container_content2');
        $props['content3'] = $this->modx->lexicon('creating_product_container_content3');
        $props['help_link'] = 'https://github.com/craftsmancoding/moxycart/wiki/Creating-a-Product-Container';
        
        return $this->_get_panel($props);
    }
    
    /**
     * Get a list of products.
     *
     * @param array arguents including limit, start, sort, dir
     *
     * @return mixed JSON-encoded string or PHP array (depends on $json flag). False on permissions error.
     */
    public function list_products($args,$json=true) {
    
        if (!$this->modx->hasPermission($this->modx->getOption(__FUNCTION__, $this->perms, $this->default_perm))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'[moxycart::'.__FUNCTION__.'] User does not have sufficient privileges.');
            return false;
        }
        
        $limit = (int) $this->modx->getOption('limit',$args,10);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('modResource');
        //$criteria->where();
        $total_pages = $this->modx->getCount('modResource',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('modResource',$criteria);
        
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }

        if ($json) {
            return json_encode($data);
        }
        else {
            return $data;
        }
    
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
        
        $out = '<p>These are the following methods available to the Moxycart class.</p>
        <ul>';
        foreach ($methods as $m) {
            if(substr($m, 1, 1) != '_') {
                $out .= '<li>'.$m.'</li>';
            }
        }
        $out .= '</ul>';
        return $out;
    }

}