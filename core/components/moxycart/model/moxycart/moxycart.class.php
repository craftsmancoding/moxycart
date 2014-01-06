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
    
    public $data = array(); // passed to views.
    
    private $core_path;
    private $assets_url;
    private $mgr_url;
    private $default_limit;
    private $connector_url; 
    private $mgr_connector_url; 
    private $jquery_url;
    public $max_image_width = 250;
    public $thumb_width = 100;

    private $cache; // for iterative ops
    private $depth = 0; //
    
    const MOXYID = 'm42Ccf';
    
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
        $this->connector_url = $this->assets_url.'components/moxycart/connector.php?f=';
        $this->modx->addPackage('moxycart',$this->core_path.'components/moxycart/model/','moxy_');
        // relative to the MODX_ASSETS_PATH or MODX_ASSETS_URL
        $this->upload_dir = $this->modx->getOption('moxycart.upload_dir',null,'images/products/');
        $this->default_limit = $this->modx->getOption('default_per_page'); // TODO: read from a MC setting?
        $this->jquery_url = $this->assets_url.'components/moxycart/js/jquery-2.0.3.min.js';
        
        // Like controller_url, but in the mgr
        // MODx.action['moxycart:index'] + '?f=';
        if ($Action = $this->modx->getObject('modAction', array('namespace'=>'moxycart','controller'=>'index'))) {
            $this->action = $Action->get('id');
        }
        else {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'[moxycart] could not determine mgr action.');
        }
        
        $this->mgr_connector_url = MODX_MANAGER_URL .'?a='.$this->action.'&f=';
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
     * Generates HTML for select <options> (NOT the wrapping <select>)
     * @param $data array 
     * @param $selected $string
     * @param $column_id $string
     * @param $label $string
     * @return string 
     */
    private function _get_options($data = array(),$selected=null, $column_id='id',$label='name') {
        $output = '';
        foreach ($data['results'] as $row) {
            $selected_str = '';
            if ($row[$column_id] == $selected) {
                $selected_str = ' selected="selected"';
            } 
            $output .= sprintf('<option value="%s"%s>%s</option>', $row[$column_id], $selected_str, $row[$label]);
        } 
        return $output;
    }
    
    /**
     * Load a view file. We put in some commonly used variables here for convenience
     *
     * @param string $file: name of a file inside of the "views" folder
     * @param array $data: an associative array containing key => value pairs, passed to the view
     * @return string
     */
    private function _load_view($file, $data=array(),$return=false) {
        $file = basename($file);
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
    
    /** 
     * For iterative parsing of the Taxonomy/Terms properties
     * 
     {
     "children":{
        "44":{"alias":"popular","pagetitle":"Popular","published":true,"menuindex":0,"children":{
            "48":{"alias":"geek","pagetitle":"geek","published":true,"menuindex":0,"children":[]}}},
        "45":{"alias":"special","pagetitle":"Special","published":true,"menuindex":1,"children":[]}},
        "children_ids":{"44":true,"45":true}
     }
        
     convert this to a flat structure
     
     */
    private function _get_subterms($props) {
        $data = array();
        $data['terms'] = '';
        unset($props['children_ids']);
        if (!empty($props['children'])) {
            foreach($props['children'] as $term_id => $tdata) {
                $tdata['class'] = 'taxonomy_term_item';
                $tdata['terms'] = '';
                $tdata['depth'] = str_repeat('&nbsp;', $this->depth * 2);
                if (!empty($tdata['children'])) {
                    $this->depth++;
                    $tdata['terms'] = $this->_get_subterms($tdata);
                    $tdata['class'] = 'taxonomy_parent_item';                    
                }
                $tdata['term_id'] = $term_id;
                $tdata['is_checked'] = '';
                if (isset($this->cache[$term_id])) {
                    $tdata['is_checked'] = ' checked="checked"';
                }
                $data['terms'] .= $this->_load_view('product_term_item.php', $tdata);
            }
        }
        
        return $this->_load_view('product_term_list.php',$data);
    }

    /**
    * Load TinyMCE
    * Add modx-richtext class on textarea
    * @param
    * @return
    **/
    private function _load_tinyMCE() 
    {
        $_REQUEST['a'] = '';  /* fixes E_NOTICE bug in TinyMCE */

        $plugin= $this->modx->getObject('modPlugin',array('name'=>'TinyMCE'));

        // Plugin not present.
        if (!$plugin) {
            return false;
        }

        $tinyPath =  $this->modx->getOption('core_path').'components/tinymce/';
        $tinyUrl =  $this->modx->getOption('assets_url').'components/tinymce/';
        
        $tinyproperties = $plugin->getProperties();
        require_once $tinyPath.'tinymce.class.php';
        $tiny = new TinyMCE( $this->modx, $tinyproperties);

        //$tinyproperties['language'] =  $modx->getOption('fe_editor_lang',array(),$language);
        $tinyproperties['frontend'] = true;
        $tinyproperties['cleanup'] = true; /* prevents "bogus" bug */
        $tinyproperties['width'] = empty ( $props['tinywidth'] )? '95%' :  $props['tinywidth'];
        $tinyproperties['height'] = empty ( $props['tinyheight'])? '400px' :  $props['tinyheight'];
       //$tinyproperties['resource'] =  $resource;
        $tiny->setProperties($tinyproperties);
        $tiny->initialize();

         $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            delete Tiny.config.setup; // remove manager specific initialization code (depending on ModExt)
            Ext.onReady(function() {
                MODx.loadRTE();
            });
        </script>');
    }
    
    //------------------------------------------------------------------------------
    //! Public
    //------------------------------------------------------------------------------
    
    /**
     *
     *
     */
    public function dashboard() {
        $data = array();
        $data['mgr_connector_url'] = $this->mgr_connector_url;
        $out = $this->_load_view('dashboard_header.php',$data);
        $out .= $this->_load_view('dashboard.php',$data);
        $out .= $this->_load_view('dashboard_footer.php',$data);        
        return $out;
    }
    
    /**
     *
     */
    public function receipts() {
        $this->modx->addPackage('foxycart',$this->core_path.'components/moxycart/model/','foxy_');
        
        $data = array();
        $data['mgr_connector_url'] = $this->mgr_connector_url;
        $out = $this->_load_view('dashboard_header.php',$data);
//        $out .= $this->_load_view('dashboard.php',$data);
  
        
        
        $Data = $this->modx->getCollection('Foxydata');
        
        if (!$Data) {
            $out .= 'No transactions.';
            $out .= $this->_load_view('dashboard_footer.php',$data);        
            return $out;
        }
        
        $out = '<table>';
        foreach ($Data as $t) {
            $out .= '<tr>';
            $out .= '<td>'.$t->get('customer_first_name').' '.$t->get('customer_last_name').'</td>';
            $out .= '<td>'.$t->get('order_total').'</td>';
            $out .= '<td><a href="'.$t->get('receipt_url').'">View Receipt</a></td>';
        }
        $out .= '</table>';
        $out .= $this->_load_view('dashboard_footer.php',$data);        
    
        return $out;
    
    }
    
    
    /**
     * Generate a string to be used as the API key
     *
     * @return string
     */
    public function generate_api_key() {
        $length = 54;
        $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count-1)];
        }
        return self::MOXYID . $str;
    }


    /**
     * Used via Ajax to get a raw HTML page component so we don't have to 
     * duplicate formatting templates in PHP *and* javascript.  This ajax
     * controller lets us keep everything in PHP.
     *
     * object: classname of the object
     * view: name of the view
     *
     * @return string
     */
    public function ajax_template($args) {
        $id = (int) $this->modx->getOption('id',$args);    
        $object = $this->modx->getOption('object',$args);
        $template = $this->modx->getOption('template',$args);
        
        $Obj = $this->modx->getObject($object, $id);
        
        if (!$Obj) {
            $this->modx->log(1,'Object not found.');
            return 'object not found: '.$object . ':'.$id;
        }
        $data = $Obj->toArray();
        $data['value'] = '';
        $data['spec'] = $data['name'];
        
        return $this->_load_view($template,$data);
    }
    
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
     * Get a single image for Ajax update.
     */
    public function get_image($args) {
        $id = (int) $this->modx->getOption('image_id', $args);
        
        $Image = $this->modx->getObject('Image',$id);

        
        if (!$Image) {
            return 'Error loading image. '.print_r($args,true);
        }
        
        $data = $Image->toArray();
        $data['action'] = $this->action;
        return $this->_load_view('product_image.php',$data);
    }
 
    /**
     * Post data here to save it
     */
    public function currency_save($args) {
        if (!is_object($this->modx->user)) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'currency_save 401 '.print_r($args,true));
            return $this->_send401();
        }
        $out = array(
            'success' => true,
            'msg' => '',
        );
        
        $token = $this->modx->getOption('HTTP_MODAUTH', $args);   
        if ($token != $this->modx->user->getUserToken($this->modx->context->get('key'))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'currency_save FAILED. Invalid token: '.print_r($args,true));
            $out['success'] = false;
            $out['msg'] = 'Invalid token';
        }
        
        $action = $this->modx->getOption('action', $args);
        
        
        switch ($action) {
            case 'update':
                $Spec = $this->modx->getObject('Currency',$this->modx->getOption('currency_id', $args));
                $Spec->fromArray($args);
                if (!$Spec->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to update Currency.';    
                }
                $out['msg'] = 'Currency updated successfully.';    
                break;
            case 'delete':
                $Spec = $this->modx->getObject('Currency',$this->modx->getOption('currency_id', $args));
                if (!$Spec->remove()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to delete Currency.';    
                }
                $out['msg'] = 'Currency deleted successfully.';    
                break;
            case 'create':
            default:
                $Spec = $this->modx->newObject('Currency');    
                $Spec->fromArray($args);
                if (!$Spec->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Currency.';    
                }
                $out['msg'] = 'Currency created successfully.';    
        }
                
        return json_encode($out);
    }
    
    /**
    * create_thumbnail
    * @param string $filename
    **/
    public function create_thumbnail($filename,$target_path,$target_path_thumb) {   
         if(file_exists(MODX_ASSETS_PATH.$target_path_thumb.$filename)) {
            unlink(MODX_ASSETS_PATH.$target_path_thumb.$filename);
         }

        $ext = strtolower(substr($filename, -4));
        switch ($ext) {
            case '.jpg':
            case 'jpeg':
                $im = imagecreatefromjpeg($target_path . $filename);
                break;
            case '.gif':
                $im = imagecreatefromgif($target_path . $filename);
                break;
            case '.png':
                $im = imagecreatefrompng($target_path . $filename);
                break;
        }
        list($width, $height) = getimagesize($target_path.$filename);
        $ox = imagesx($im);
        $oy = imagesy($im);
        
        $nx = ( $width >= $this->thumb_width ) ? $this->thumb_width : $width;
        $ny = floor($oy * ($nx / $ox));
        
        $nm = imagecreatetruecolor($nx, $ny);

        if (preg_match('/[.](png)$/', $filename)) {
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($nm, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($nm, $background);

                // turning off alpha blending (to ensure alpha channel information 
                // is preserved, rather than removed (blending with the rest of the 
                // image in the form of black))
                imagealphablending($nm, false);

                // turning on alpha channel information saving (to ensure the full range 
                // of transparency is preserved)
                imagesavealpha($nm, true);
        } 

        if (preg_match('/[.](png)$/', $filename)) {
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($nm, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($nm, $background);
        }

        
        imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
        
        if(!file_exists(MODX_ASSETS_PATH.$target_path_thumb)) {
          if (!mkdir(MODX_ASSETS_PATH.$target_path_thumb,0777,true)) {
                $out['success'] = false;
                $out['msg'] = 'Failed to create directory at '.MODX_ASSETS_PATH.$target_path_thumb;    
                $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Failed to create directory at '.MODX_ASSETS_PATH.$target_path_thumb);
                return json_encode($out);
          } 
        }

        if(!imagejpeg($nm, MODX_ASSETS_PATH.$target_path_thumb . $filename)) {
                $out['success'] = false;
                $out['msg'] = 'Failed to create thumb at '.MODX_ASSETS_PATH.$target_path_thumb;    
                $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Failed to create thumb at '.MODX_ASSETS_PATH.$target_path_thumb);
                return json_encode($out);
        }
        return $target_path_thumb . $filename;
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
     * Hosts the "Update Image" form.
     *
     * @param int image_id (from $_GET). Defines the id of the product
     */
    public function image_update($args) {
       
        $image_id = (int) $this->modx->getOption('image_id', $args);

        if (!$Image = $this->modx->getObject('Image', $image_id)) {        
            return 'Image not found : '.$image_id;
        }
        $data = $Image->toArray(); 
        $this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/mgr.css');
        $this->modx->regClientStartupScript($this->jquery_url);
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery-ui.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/bootstrap.js');
        $data['wide_load'] = '';
        $data['visible_height'] = $data['height'];
        $data['visible_width'] = $data['width'];        
        if ($data['width'] > $this->max_image_width) {
            $data['wide_load'] = 'Warning! This image is larger than it appears.';
            $ratio = $this->max_image_width / $data['width'];
            $data['visible_height'] = (int) ($data['height'] * $ratio);
            $data['visible_width'] = $this->max_image_width;
        }
        $data['jcrop_js'] = $this->assets_url.'components/moxycart/js/jcrop.js';
        $data['loader_path'] = $this->assets_url.'components/moxycart/images/gif-load.gif';

        return $this->_load_view('image_update.php',$data);
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
    * upload_image
    * @param product_id int
    * @return $out json
    **/
     public function upload_image($product_id) {
        $out = array(
            'success' => true,
            'msg' => '',
        );
        if (isset($_FILES['file']['name']) ) {
            // Relative to either MODX_ASSETS_URL or MODX_ASSETS_PATH
            $rel_file =  $this->upload_dir.$product_id.'/'.basename($_FILES['file']['name']);
            $target_path = MODX_ASSETS_PATH.$this->upload_dir.$product_id.'/';
            $target_path_thumb = $this->upload_dir.$product_id.'/thumbs' . '/';
            if (!file_exists($target_path)) {
                if (!mkdir($target_path,0777,true)) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to create directory at '.$target_path;    
                    $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Failed to create directory at '.$target_path);
                    return json_encode($out);
                }
            }
            // Image already exists?
            if (file_exists(MODX_ASSETS_PATH.$rel_file)) {
                $out['success'] = false;
                $out['msg'] = 'Upload Failed. File of same name exists';
                $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Upload Cannot Continue. File of same name exists '.MODX_ASSETS_PATH.$rel_file);
                return json_encode($out);
            }
            if(move_uploaded_file($_FILES['file']['tmp_name'],MODX_ASSETS_PATH.$rel_file)) {
                $this->modx->log(MODX_LOG_LEVEL_DEBUG, 'SUCCESS UPLOAD: '.MODX_ASSETS_PATH.$rel_file);
            } 
            else {
                $out['success'] = false;
                $out['msg'] = 'FAILED UPLOAD: '.MODX_ASSETS_PATH.$rel_file;
                $this->modx->log(MODX_LOG_LEVEL_ERROR, 'FAILED UPLOAD: '.MODX_ASSETS_PATH.$rel_file);
                return json_encode($out);
            }

            $out['thumbnail_url'] = $this->create_thumbnail(basename($_FILES['file']['name']),$target_path,$target_path_thumb);
            $out['rel_file'] = $rel_file;
            $out['file_size'] = $_FILES['file']['size'];
            
        } 
        return json_encode($out);
    }

    /**
     * Crop an image in place (original file is destructively edited).
     * In this AddOn, an image MUST be rep'd by a Image, so the id must be set.
     * We return an <img> tag as well to save the hastle of another ajax post.
     * 
     * @params array $args including key for id
     * @return string JSON array
     */
    public function image_crop($args) { 

        
        $out = array(
            'success' => true,
            'msg' => '',
            'img' => ''
        );
        
        $id = (int) $this->modx->getOption('image_id', $args);
        $Image = $this->modx->getObject('Image', $id);
     
        $thumbnail_url = $Image->get('thumbnail_url');
        $filename = basename($thumbnail_url); 
        $target_path_thumb = dirname($thumbnail_url). '/';
        $target_path = MODX_ASSETS_PATH.dirname($target_path_thumb). '/';


        if (!$Image) {
            $out['success'] = false;
            $out['msg'] = 'Image and Image not found.';
            return json_encode($out);
        }
        // http://www.php.net/manual/en/function.imagecopy.php
        $src = $Image->get('path');
       // $src = $Image->get('url');
        if (!file_exists($src)) {
            $out['success'] = false;
            $out['msg'] = 'Image ('.$id.') Image not found: '.$src;
            return json_encode($out);            
        }
        
        $srcImg = '';
        $ext = strtolower(substr($src, -4));
        $image_func = '';
        $quality = null; // different vals for different funcs
        switch ($ext) {
            case '.gif':
                $srcImg = @imagecreatefromgif($src);
                $image_func = 'imagegif';
                break;
            case '.jpg':
            case 'jpeg':
                $srcImg = @imagecreatefromjpeg($src);
                $image_func = 'imagejpeg';
                $quality = 100;
                break;
            case '.png':
                $srcImg = @imagecreatefrompng($src);
                $image_func = 'imagepng';
                $quality = 0;
                break;
            default:
                $out['success'] = false;
                $out['msg'] = 'Image ('.$id.') Unrecognized extension: '.$ext;
                return json_encode($out);                            
        }
        
        if (!$srcImg) {
            $out['success'] = false;
            $out['msg'] = 'Image ('.$id.') could not create image: '.$src;
            return json_encode($out);                        
        }
        
        // Cleared for launch.
        $ratio = 1;
        if ($Image->get('width') > $this->max_image_width) {
            $ratio = $Image->get('width') / $this->max_image_width;
        }
        // Remember: order of ops for type-casting. (int) filters ONLY the variable to its right!!
        $src_x = (int) ($ratio * $this->modx->getOption('x',$args));
        $src_y = (int) ($ratio * $this->modx->getOption('y',$args));
        $src_w = (int) ($ratio * $this->modx->getOption('w',$args));
        $src_h = (int) ($ratio * $this->modx->getOption('h',$args));

        // Remember: at this point, if the user selects the full width of the *displayed*
        // image, it is not necessarily equal to the dimensions of the original image.
        $new_w = (int) ($ratio * $this->modx->getOption('w',$args));
        $new_h = (int) ($ratio * $this->modx->getOption('h',$args));
        $destImg = imagecreatetruecolor($src_w, $src_h);

        if (!imagecopy($destImg, $srcImg, 0, 0, $src_x, $src_y, $src_w, $src_h)) {
            $out['success'] = false;
            $out['msg'] = 'Image ('.$id.') could not crop image: '.$src;
            imagedestroy($srcImg);
            imagedestroy($destImg);
            return json_encode($out);                                    
        }
        
        if (!$image_func($destImg,$Image->get('path'),$quality)) {
            $out['success'] = false;
            $out['msg'] = 'Image ('.$id.') could not save cropped image: '.$src;
            imagedestroy($srcImg);
            imagedestroy($destImg);
            return json_encode($out);                                    
        }
        
        imagedestroy($srcImg);
        imagedestroy($destImg);

        $Image->set('height', $new_h);
        $Image->set('width', $new_w);
        $Image->set('size', filesize($Image->get('path')));

        if (!$Image->save()) {
            $out['success'] = false;
            $out['msg'] = 'Could not update Image: '.$id;            
            return json_encode($out);                                            
        }

         // start create new thumb
        $thumbnail_url = $Image->get('thumbnail_url');
        $filename = basename($thumbnail_url); 
        $target_path_thumb = substr(dirname($thumbnail_url),8). '/';
        $target_path = MODX_ASSETS_PATH.dirname($target_path_thumb). '/';
        $this->create_thumbnail($filename,$target_path,$target_path_thumb);
        // start create new thumb

        $out['msg'] = 'Image cropped successfully.';
        $out['img'] = $this->get_image_tag(array('image_id'=>$id));

        return json_encode($out);
    }

    /**
     * Get a single image tag for Ajax update
     *
     */
    public function get_image_tag($args) {
               
        $id = (int) $this->modx->getOption('image_id', $args);
        
        $Image = $this->modx->getObject('Image',$id);
        
        if (!$Image) {
            return 'Error loading image.';
        }
        
        $data = $Image->toArray();
         
        $data['wide_load'] = '';
        $data['visible_height'] = $data['height'];
        $data['visible_width'] = $data['width'];        
        if ($data['width'] > $this->max_image_width) {
            $data['wide_load'] = 'Warning! This image is larger than it appears.';
            $ratio = $this->max_image_width / $data['width'];
            $data['visible_height'] = (int) ($data['height'] * $ratio);
            $data['visible_width'] = $this->max_image_width;
        }

        $img = $this->_load_view('image.php',$data);

        return $img;
    }

    /**
     * Get a single Spec for Ajax updates
     *
     */
    public function get_spec($args) {
        $spec_id = (int) $this->modx->getOption('spec_id', $args);
        $Spec = $this->modx->getObject('Spec',$spec_id);
        if (!$Spec) {
            return 'Invalid Spec';
        }
        // template name mapping
        $s = $Spec->toArray();
        $s['spec'] = $s['name'];
        return $this->_load_view('product_spec.php',$s); // TODO: react to the spec "type"
    }

    /**
     * Post data here to save it
     */
    public function image_save($args) {
        $action = $this->modx->getOption('action', $args);     
        unset($args['action']);
        $out = array(
            'success' => true,
            'msg' => '',
        );

        switch ($action) {
            case 'update' :
                $product_id = (int) $this->modx->getOption('product_id',$args);

         
                if (isset($_FILES['file']['name']) ) {
                    $uploaded_img = json_decode($this->upload_image($product_id),true);
                    $rel_file = $uploaded_img['rel_file'];
                    list($width, $height) = getimagesize(MODX_ASSETS_PATH.$rel_file);
                    $args['url'] = MODX_ASSETS_URL.$rel_file;
                    $args['path'] = MODX_ASSETS_PATH.$rel_file;
                    $args['width'] = $width;
                    $args['height'] = $height;
                }
                $Image = $this->modx->getObject('Image',$this->modx->getOption('image_id', $args));
                
                $Image->fromArray($args);
                if (!$Image->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to update Image.';    
                }
                $out['msg'] = 'Image updated successfully.';  

                break;
            case 'delete':
                $Image = $this->modx->getObject('Image',$this->modx->getOption('image_id', $args));
                if (!$Image->remove()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to delete Image.';    
                }
                unlink(MODX_BASE_PATH . $Image->get('url'));
                unlink(MODX_BASE_PATH . $Image->get('thumbnail_url'));
                $out['msg'] = 'Image deleted successfully.';    
                break;
            case 'create':
            default:
                $product_id = (int) $this->modx->getOption('product_id',$args);

                if (isset($_FILES['file']['name']) ) {
                    $uploaded_img = json_decode($this->upload_image($product_id),true);

                    if ($uploaded_img['success'] == false) {
                        $out['success'] = false;
                        $out['msg'] = $uploaded_img['msg'];
                        return json_encode($out);
                    }

                    $rel_file = $uploaded_img['rel_file'];
                    // Create db record
                    list($width, $height) = getimagesize(MODX_ASSETS_PATH.$rel_file);
                    $Image = $this->modx->newObject('Image');
                    $Image->set('product_id',$product_id);
                    $Image->set('url',MODX_ASSETS_URL.$rel_file);
                    $Image->set('path',MODX_ASSETS_PATH.$rel_file);
                    $Image->set('thumbnail_url',MODX_ASSETS_URL.$uploaded_img['thumbnail_url']);
                    $Image->set('width',$width);
                    $Image->set('height',$height);
                    $Image->set('size',$uploaded_img['file_size']);
                    $Image->set('is_active',1);
                }
                
                if (!$Image->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Image object for product';
                    $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Failed to save Image object for product '.$product_id .' '.MODX_ASSETS_PATH.$rel_file);
                    return json_encode($out);
                }
                $out['msg'] = 'Successfully saved image';
                $out['image_id'] = $this->modx->lastInsertId();
                $this->modx->log(MODX_LOG_LEVEL_DEBUG, 'Successfully saved image '.$Image->getPrimaryKey() .' '.MODX_ASSETS_PATH.$rel_file);
                
        }

        return json_encode($out);
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

        $data = array();
        $store_id = (int) $this->modx->getOption('store_id',$_GET);
        $data['manager_url'] = $this->mgr_url.'?a=30&id='.$store_id;
        $data['product_form_action'] = 'product_create';
        $data['product_specs'] ='';
        $data['currencies'] = '';
         $specs = $this->json_specs(array('limit'=>0),true);
        $data['specs'] = $this->_get_options($specs,'','spec_id');  

        $currencies = $this->json_currencies(array('limit'=>0,'is_active'=>1),true);
        $currency_id = $this->modx->getOption('moxycart.currency_id','',109); // TODO
        $data['currencies'] = $this->_get_options($currencies,$currency_id,'currency_id'); 
                
        $templates = $this->json_templates(array('limit'=>0),true);
        $data['templates'] = $this->_get_options($templates); 

        $categories = $this->json_categories(array('limit'=>0),true);
        $data['categories'] = $this->_get_options($categories); 


        $stores = $this->json_stores(array('limit'=>0),true);
        $data['stores'] = $this->_get_options($stores,$store_id); 

        $types = $this->json_types(array('limit'=>0),true);
        $data['types'] = $this->_get_options($types);       

        // Taxonomies (yowza!)
        $data['product_taxonomies'] = '';       

        // All avail. taxonomies
        $data['taxonomies'] = '';
        $taxonomies = $this->json_taxonomies(array('limit'=>0),true);
        foreach ($taxonomies['results'] as $t) {
            $t['is_checked'] = '';
            if (isset($active[ $t['id'] ])) {
                $t['is_checked'] = ' checked="checked"';
            }
            $data['taxonomies'] .= $this->_load_view('product_taxonomy.php',$t); // TODO: react to the spec "type"
        }

        $this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/mgr.css');
        $this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/dropzone.css');
        $this->modx->regClientCSS($this->assets_url.'components/moxycart/css/datepicker.css');
        $this->modx->regClientStartupScript($this->jquery_url);
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery-ui.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery.tabify.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/bootstrap-datepicker.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/dropzone.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/script.js');
        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">          
            var connector_url = "'.$this->connector_url.'";
            var use_editor = "'.$this->modx->getOption('use_editor').'";
            var assets_url = "'.MODX_ASSETS_URL.'";
            var redirect_url = "'.$this->mgr_url .'?a='.$this->action . '&f=product_update&product_id='.'";
            // use Ext JS?
            Ext.onReady(function() {
              // populate the form
            });
            </script>
        ');

        if ($this->modx->getOption('use_editor')) {
            $this->_load_tinyMCE();
        }

        return $this->_load_view('product_template.php',$data);
    }


     /**
     * Hosts the "Update Product" form.
     *
     * @param int product_id (from $_GET). Defines the id of the product
     */
    public function product_update($args) {
        $product_id = (int) $this->modx->getOption('product_id', $args);

        if (!$Product = $this->modx->getObject('Product', $product_id)) {        
            return 'Product not found : '.$product_id;
        }
        
        $data = $Product->toArray();
        $data['manager_url'] = $this->mgr_url.'?a=30&id='.$Product->get('store_id');
        $data['connector_url'] = $this->connector_url;
        $data['product_form_action'] = 'product_update';
        
        // Get the dropdowns
        $data['images'] = '';
        $product_images = $this->json_images(array('product_id'=>$product_id,'limit'=>0),true);
        foreach ($product_images['results'] as $img) {
            $img['action'] = $this->action;
            $img['thumb_width'] = $this->thumb_width;
            $data['images'] .= $this->_load_view('product_image.php',$img);
        }
        

        $currencies = $this->json_currencies(array('limit'=>0,'is_active'=>1),true);
        $data['currencies'] = $this->_get_options($currencies,$data['currency_id'],'currency_id');
       
        $templates = $this->json_templates(array('limit'=>0),true);
        $data['templates'] = $this->_get_options($templates,$data['template_id']);


        $categories = $this->json_categories(array('limit'=>0),true);
        $data['categories'] = $this->_get_options($categories,$data['category']);

        $stores = $this->json_stores(array('limit'=>0),true);
        $data['stores'] = $this->_get_options($stores,$data['store_id']);

        $types = $this->json_types(array('limit'=>0),true);
        $data['types'] = $this->_get_options($types,$data['type']);      

        $specs = $this->json_specs(array('limit'=>0),true);
        $data['specs'] = $this->_get_options($specs,'','spec_id');  
        
        $data['product_specs'] = '';
        $specs = $this->json_product_specs(array('limit'=>0,'product_id'=>$product_id),true);

        foreach ($specs['results'] as $s) {
            $data['product_specs'] .= $this->_load_view('product_spec.php',$s); // TODO: react to the spec "type"
        }        

        $product_terms = $this->json_product_terms(array('limit'=>0,'product_id'=>$product_id),true);
        foreach ($product_terms['results'] as $t) {
            $this->cache[ $t['term_id'] ] = true;
        }
        
        // Taxonomies (yowza!)
        $data['product_taxonomies'] = '';
        $product_taxonomies = $this->json_product_taxonomies(array('limit'=>0,'product_id'=>$product_id),true);
        $active = array();
        foreach ($product_taxonomies['results'] as $t) {
            $active[ $t['taxonomy_id'] ] = true;
            $data['product_taxonomies'] .= $this->_load_view('product_taxonomy_heading.php',$t);
            $data['product_taxonomies'] .= $this->_get_subterms($t['properties']);
        }        

        // All avail. taxonomies
        $data['taxonomies'] = '';
        $taxonomies = $this->json_taxonomies(array('limit'=>0),true);
        foreach ($taxonomies['results'] as $t) {
            $t['is_checked'] = '';
            if (isset($active[ $t['id'] ])) {
                $t['is_checked'] = ' checked="checked"';
            }
            $data['taxonomies'] .= $this->_load_view('product_taxonomy.php',$t); // TODO: react to the spec "type"
        }
              
        
                
        $this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/mgr.css');
        $this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/dropzone.css');
        $this->modx->regClientCSS($this->assets_url.'components/moxycart/css/datepicker.css');
        $this->modx->regClientStartupScript($this->jquery_url);
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery-ui.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery.tabify.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/dropzone.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/bootstrap.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/script.js');

    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
    		var product = '.$Product->toJson().';            
    		var connector_url = "'.$this->connector_url.'";
            var use_editor = "'.$this->modx->getOption('use_editor').'";
            var assets_url = "'.MODX_ASSETS_URL.'";    		
            var variation_url = "'.$this->connector_url.'&parent_id='.$product_id.'";
  
            jQuery(document).ready(function() {
                    var myDropzone = new Dropzone("div#image_upload", {url: connector_url+"image_save&product_id='.$product_id.'"});
                    
                    // Refresh the list on success (append new tile to end)
                    myDropzone.on("success", function(file,response) {

                         console.log(response);
                        response = jQuery.parseJSON(response);
                        console.log(response);
                        if (response.success) {
                           
                            var url = connector_url + "get_image&image_id=" + response.image_id;
                            jQuery.post( url, function(data){
                                jQuery("#product_images").append(data);
                                jQuery(".dz-preview").remove();
                            });
                       } 
                       // TODO: better formatting
                       else {
                           
                            $(".dz-success-mark").hide();
                           $(".dz-error-mark").show();
                           $(".moxy-msg").show();
                           $("#moxy-result").html("Failed");
                            $("#moxy-result-msg").html(response.msg);
                            $(".moxy-msg").delay(3200).fadeOut(400);
                       }
                    });
            });

			Ext.onReady(function() {   		
    			renderProductVariationProductsGrid();
    		});
    		</script>
    	');
 	
        $this->modx->regClientStartupScript($this->assets_url . 'components/moxycart/js/productcontainer.js');
        
        if ($this->modx->getOption('use_editor')) {
            $this->_load_tinyMCE();
        }

        $data['mgr_connector_url'] = $this->mgr_connector_url;
        return $this->_load_view('product_template.php',$data);
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
        $store_url = $this->connector_url.'json_products';
        if ($store_id) {
            $back_url = '?a=30&id='.$store_id;
            $store_url .= '&store_id='.$store_id;
        }
        else {
            $back_url = '?a='.$this->action.'&f=product_update&product_id='.$product_id;
            $store_url .= '&parent_id='.$product_id;
        }

    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
    		var back_url = "'.$back_url.'";
    		var store_url = "'.$store_url.'";
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
        $args['limit'] = 0; // get 'em all
        $args['sort'] = 'seq';

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
    		</script>
    	');

        $this->modx->regClientStartupScript($this->jquery_url);
        $this->modx->regClientStartupScript($this->assets_url.'components/moxycart/js/jquery-ui.js');
		$this->modx->regClientCSS($this->assets_url . 'components/moxycart/css/mgr.css');		
		
        $products = $this->json_products($args,true);

        $products['back_url'] = $back_url;        

        return $this->_load_view('product_list.php',$products);
        
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
     * Post data here to save it.  Data should be in the following format:
     *
     * keys in $_POST should match *exactly* column names in products table, e.g.
     *  name
     *  qty_inventory
     *  sale_end 
     *  ... etc..
     *
     * Related data should be stored in the following arrays:
     *
     *  taxonomies  = array(1,2,3)  a simple array of taxonomy_id's
     *  terms       = array(4,5,6)  a simple array of term_id's
     *  specs       = array(            An array of key/value pairs: keys=spec_ids, values=values for that spec
     *                  array(7 => "Value1"), 
     *                  array(8 => "Value2")
     *                )
     *  images      = array(2,4,8)  a simple array of image_ids
     *
     * Finally, an "action" parameter should be passed to indicate whether this function should
     * create, update, or delete a product record.
     *
     */
    public function product_save($args) {
       

        $out = array(
            'success' => true,
            'msg' => '',
        );
        
        
        $action = $this->modx->getOption('action', $args);        
        unset($args['action']);
        
        // Ensure a clean/usable alias
        $resource = $this->modx->newObject('modResource');
        $alias = $this->modx->getOption('alias',$args);
        if (empty($alias)) {
            $args['alias'] = $resource->cleanAlias($args['name']);
        }
        else {
            $args['alias'] = $resource->cleanAlias($args['alias']);
        }
        
         
        $Store = $this->modx->getObject('modResource', $this->modx->getOption('store_id',$args));
        if (!$Store) {
            $out['success'] = false;
            $out['msg'] = 'Invalid store_id '.$this->modx->getOption('store_id',$args); 
            return json_encode($out);
        }
        $args['uri'] = $Store->get('uri') .$args['alias'];
        
        switch ($action) {
            case 'update':
                $product_id = (int) $this->modx->getOption('product_id', $args);
                $this->modx->log(MODX_LOG_LEVEL_ERROR, 'product update args: '. print_r($args,true));        
                
                $Product = $this->modx->getObject('Product',$product_id);
                
                //taxonomies
                $many = array();
                $existing = array();
                $related = $this->modx->getOption('taxonomies',$args,array());
                if ($Taxonomies = $this->modx->getCollection('ProductTaxonomy', array('product_id'=>$product_id))) {
                    // Remove any unchecked items
                    foreach ($Taxonomies as $T) {
                        if (!in_array($T->get('taxonomy_id'), $related)) {
                            if($T->remove() === false) {
                                $this->modx->log(MODX_LOG_LEVEL_ERROR,'product_save failed to remove ProductTaxonomy '.$T->get('id'));
                            }
                        }
                        else {
                            $existing[] = $T->get('taxonomy_id');
                        }
                    }
                } 
                // Add any new ones
                foreach ($related as $r) {
                    if (!in_array($r, $existing)) {
                        $obj = $this->modx->newObject('ProductTaxonomy');
                        $obj->set('taxonomy_id', $r);
                        $many[] = $obj;
                    }
                }
                $Product->addMany($many);


                //terms
                $many = array();
                $existing = array();
                $related = $this->modx->getOption('terms',$args,array());
                if ($Terms = $this->modx->getCollection('ProductTerm', array('product_id'=>$product_id))) {
                    // Remove any unchecked items
                    foreach ($Terms as $T) {
                        if (!in_array($T->get('taxonomy_id'), $related)) {
                            if($T->remove() === false) {
                                $this->modx->log(MODX_LOG_LEVEL_ERROR,'product_save failed to remove ProductTerm '.$T->get('id'));
                            }
                        }
                        else {
                            $existing[] = $T->get('term_id');
                        }
                    }
                } 
                // Add any new ones
                foreach ($related as $r) {
                    if (!in_array($r, $existing)) {
                        $obj = $this->modx->newObject('ProductTerm');
                        $obj->set('term_id', $r);
                        $many[] = $obj;
                    }
                }
                $Product->addMany($many);
                
                
                                
                //specs
                $many = array();
                $existing = array();
                $related = $this->modx->getOption('specs',$args,array());
                if ($Specs = $this->modx->getCollection('ProductSpec', array('product_id'=>$product_id))) {
                    // Remove any unchecked items
                    foreach ($Specs as $S) {
                        if (!in_array($S->get('spec_id'), array_keys($related))) {
                            if($S->remove() === false) {
                                $this->modx->log(MODX_LOG_LEVEL_ERROR,'product_save failed to remove ProductSpec '.$S->get('id'));
                            }
                        }
                        else {
                            $existing[] = $S; // Store the entire object
                        }
                    }
                }
                
                // Update existing
                foreach ($existing as $S) {
                    $S->set('value', $related[ $S->get('spec_id') ]);
                    if(!$S->save()){
                        $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Error saving ProductSpec: '. $S->get('id'));
                    }
                    unset($related[ $S->get('spec_id') ]);
                }
                
                // Add any new ones
                foreach ($related as $k => $v) {
                    $S = $this->modx->newObject('ProductSpec');
                    $S->set('product_id',$product_id);
                    $S->set('spec_id', $k);
                    $S->set('value', $v);
                    if (!$S->save()) {
                        $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Error creating ProductSpec product '. $product_id.' spec_id '.$k .' with value '.$v);
                    }
                }
                
                                
                //images (handled by image_save)
                // Here we just reorder them
                $seq = 0;
                $related = $this->modx->getOption('images',$args,array());
                // Reorder the ones we know about already
                $seq = 0;
                foreach ($related as $image_id) {
                    if ($I = $this->modx->getObject('Image', $image_id)) {
                        $I->set('seq',$seq);
                        $I->save();
                        $seq++;
                    }
                    else {
                        $this->modx->log(MODX_LOG_LEVEL_ERROR,'failed to load image '.$image_id);
                    }
                }
                // Order any ones we didn't know about (i.e. newly uploaded ones)
                $query = $this->modx->newQuery('Image');
                $query->where(array('product_id' => $product_id));
                $query->where(array('image_id:NOT IN' => $related));
                if ($Images = $this->modx->getCollection('Image', $query)) {
                    foreach ($Images as $I) {
                        $I->set('seq',$seq);
                        $I->save();
                        $seq++;
                    }
                }
                
                                
                $Product->fromArray($args);
                if (!$Product->save()) {
                    $this->modx->log(MODX_LOG_LEVEL_ERROR,'problem saving product_id '.$product_id);
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
                //taxonomies
                $related = $this->modx->getOption('taxonomies',$args,array());
                $many = array();
                foreach ($related as $r) {
                    $obj = $this->modx->newObject('ProductTaxonomy');
                    $obj->set('taxonomy_id', $r);
                    $many[] = $obj;
                }
                $Product->addMany($many);
                
                //terms
                $related = $this->modx->getOption('terms',$args,array());
                $many = array();
                foreach ($related as $r) {
                    $obj = $this->modx->newObject('ProductTerm');
                    $obj->set('term_id', $r);
                    $many[] = $obj;
                }
                $Product->addMany($many);
                
                //specs
                $related = $this->modx->getOption('taxonomies',$args,array());
                $many = array();
                foreach ($related as $k => $v) {
                    $obj = $this->modx->newObject('ProductSpec');
                    $obj->set('spec_id', $k);
                    $obj->set('value', $v);
                    $many[] = $obj;
                }
                $Product->addMany($many);
                                
                //images
                $related = $this->modx->getOption('images',$args,array());
                $many = array();
                foreach ($related as $r) {
                    $obj = $this->modx->newObject('ProductImage');
                    $obj->set('image_id', $r);
                    $many[] = $obj;
                }
                $Product->addMany($many);

                if (!$Product->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Product.';    
                }
                //$this->image_save($args);
                $out['product_id']    = $this->modx->lastInsertId();
                $out['msg'] = 'Product created successfully.';
                break; 
        }

        return json_encode($out);        

    }

    /**
     * Post data here to save product sort order.  Data should be in the following format:
     *
     * $_POST['seq'] = array( 11,22,33) where 11,22,33 are product ids.
     *
     */
    public function product_save_seq($args) {
        $out = array(
            'success' => true,
            'msg' => '',
        );
        
        $product_ids = $this->modx->getOption('seq',$args,array());
        
        $seq = 0;
        foreach ($product_ids as $id) {
            $id = (int) $id;
            $Prod = $this->modx->getObject('Product', $id);
            if (!$Prod) {
                $out['success'] = false;
                $out['msg'] = 'Invalid product id: '.$id;
                $this->modx->log(MODX_LOG_LEVEL_ERROR,$out['msg']);
                return json_encode($out);
            }
            $Prod->set('seq', $seq);
            if (!$Prod->save()) {
                $out['success'] = false;
                $out['msg'] = 'Error saving product: '.$id;
                $this->modx->log(MODX_LOG_LEVEL_ERROR,$out['msg']);
                return json_encode($out);
            }
            $seq++;
        }
        $out['msg'] = 'Sort order updated.';
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
    public function spec_save($args) {
        if (!is_object($this->modx->user)) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'spec_save 401 '.print_r($args,true));
            return $this->_send401();
        }
        $out = array(
            'success' => true,
            'msg' => '',
        );
        
        $token = $this->modx->getOption('HTTP_MODAUTH', $args);   
        if ($token != $this->modx->user->getUserToken($this->modx->context->get('key'))) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'spec_save FAILED. Invalid token: '.print_r($args,true));
            $out['success'] = false;
            $out['msg'] = 'Invalid token';
        }
        
        $action = $this->modx->getOption('action', $args);
        
        
        switch ($action) {
            case 'update':
                $Spec = $this->modx->getObject('Spec',$this->modx->getOption('spec_id', $args));
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
        $sort = $this->modx->getOption('sort',$args,'seq');
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
//        print $criteria->toSQL(); //<-- useful for debugging
//        exit;
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        
       // set date and time (unix)
        $now = strtotime(date('Y-m-d H:i:s'));
        
        foreach ($pages as $p) {
            $calculated_price = $p->get('price');
            // if on sale use price sale
            if(strtotime($p->get('sale_start')) <= $now && strtotime($p->get('sale_end')) >= $now) {
                $calculated_price = $p->get('price_sale');
            }

            $row = array(
                'product_id' => $p->get('product_id'),
                'alias' => $p->get('alias'),
                'content' => stripslashes($p->get('content')),
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
                'calculated_price'=> $calculated_price,
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
               $data['results'][] = array(
                'product_id' => $p->get('product_id'), 
                'spec_id' => $p->get('spec_id'), 
                'product' => $p->Product->get('name'),
                'spec' => $p->Spec->get('name'),
                'value' => $p->get('value'),
                'description' => $p->Spec->get('description'),
                'type' => $p->Spec->get('type')
               );
             }
         }

        if($raw) {
            return $data;
        }
        return json_encode($data);
       

    }

    /**
     * Get the taxonomies associated with a given product.
     *
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_product_taxonomies($args,$raw=false) {
        $product_id = (int) $this->modx->getOption('product_id',$args);
        
        $limit = (int) $this->modx->getOption('limit',$args,$this->default_limit);
        $start = (int) $this->modx->getOption('start',$args,0);
        $sort = $this->modx->getOption('sort',$args,'ProductTaxonomy.id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('ProductTaxonomy');
        
        if ($product_id) {
            $criteria->where(array('ProductTaxonomy.product_id'=>$product_id));
        }
                
        $total_pages = $this->modx->getCount('ProductTaxonomy',$criteria);
        
        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollectionGraph('ProductTaxonomy','{"Product":{},"Taxonomy":{}}',$criteria);
        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = array(
                'id' => $p->get('id'),
                'product_id' => $p->get('product_id'),
                'taxonomy_id' => $p->get('taxonomy_id'),
                'name' => $p->Taxonomy->get('pagetitle'),
                'product' => $p->Product->get('name'),
                'properties' => $p->Taxonomy->get('properties')
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
        $sort = $this->modx->getOption('sort',$args,'Term.id');
        $dir = $this->modx->getOption('dir',$args,'ASC');

        $product_id = (int) $this->modx->getOption('product_id',$args);

        $criteria = $this->modx->newQuery('ProductTerm');
        
        if ($product_id) {
            $criteria->where(array('ProductTerm.product_id'=>$product_id));
        }
        
        $total_pages = $this->modx->getCount('ProductTerm',$criteria);

        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);

        $pages = $this->modx->getCollectionGraph('ProductTerm','{"Product":{},"Term":{}}',$criteria);

//        return $criteria->toSQL(); // <-- useful for debugging

        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );

        foreach ($pages as $p) {
            $data['results'][] = array(
                'id' => $p->get('id'),
                'product_id' => $p->get('product_id'),
                'term_id' => $p->get('term_id'),
                'term' => $p->Term->get('pagetitle'),
                'properties' => $p->Term->get('properties')
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