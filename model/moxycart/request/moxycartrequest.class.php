<?php
/**
 * MoxyCartRequest
 *
 * Copyright 2013 by Everett Griffiths <everett@craftsmancoding.com>
 *
 * This handles requests in the MODx Manager.
 *
 * This file is part of MoxyCart, an eCommerce platform for MODX revolution
 *
 * MoxyCart is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * MoxyCart is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * MoxyCart; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package MoxyCart
 */

require_once MODX_CORE_PATH . 'model/modx/modrequest.class.php';
/**
 * Encapsulates the interaction of MODx manager with an HTTP request.
 *
 * @package MoxyCart
 * @subpackage request
 * @extends modRequest
 */
class MoxyCartRequest extends modRequest {
    /**
     * Instance refs
     */
    public $modx = null;
    private $MoxyCart = null;
    private $Client = null;
    
    /**
     * The action key to use
     * @var string $actionVar
     */
    public $actionVar = 'action';
    /**
     * The default controller to load if none is specified
     * @var string $defaultAction
     */
    public $defaultAction = 'home';
    /**
     * The currently loaded action
     * @var string $action
     */
    public $action = '';

	/**
	 * This dispatches the requests to actions
	 * @param string $name
	 * @param mixed $args
	 * @return string HTML
	 */
	function __call($name, $args) {
		$modx =& $this->modx;
		$MoxyCart =& $this->MoxyCart;		
		$f = $this->MoxyCart->config['corePath'].'views/mgr/'.$name.'.php';
		return $this->_load_file($f);
	}

    /**
     * @param MoxyCart $MoxyCart A reference to the MoxyCart instance
     */
    function __construct(MoxyCart &$MoxyCart) {
        parent :: __construct($MoxyCart->modx);
        $this->MoxyCart =& $MoxyCart;
    }

	//------------------------------------------------------------------------------
	//! Private Functions
	//------------------------------------------------------------------------------
	/**
	 * Load up and parse a PHP file and return it as a string
	 *
	 * @return string
	 */
	private function _load_file($file, $data=array()) {
		if (is_file($file)) {
			ob_start();
			include $file;
			return ob_get_clean();
		}
		else {
			return 'File not found: '.$file;
		}	
	}
	
	//------------------------------------------------------------------------------
	//! Public Functions
	//------------------------------------------------------------------------------
	public function create_client() {
		$modx =& $this->modx;
		$MoxyCart =& $this->MoxyCart;		
		$f = $this->MoxyCart->config['corePath'].'views/mgr/'.__FUNCTION__.'.php';

		$Profile = $this->modx->user->getOne('Profile');
		return 'asdf'.$this->Client->getLink('create_client');
		// Defaults
		$data = array();
		$data['msg'] = '';
		$data['redirect_uri'] = substr($this->modx->getOption('site_url'),0,-1) . MODX_MANAGER_URL .'?a='.$_GET['a'];
		$data['project_name'] = $this->modx->getOption('site_name');
		$data['contact_name'] = $Profile->get('fullname');
		$data['contact_email'] = $Profile->get('email');
		$data['company_phone'] = $Profile->get('phone');

		// Submitted
		if (!empty($_POST)) {
			// Store
			// ???
			$resp = $this->Client->post($this->Client->getLink('create_client'),$_POST,
				$this->MoxyCart->getHeaders());
			$data['msg'] = print_r($resp,true);				
//			$data['msg'] = $this->_load_file($this->MoxyCart->config['corePath'].'views/error_msg.php',
//				array('content'=>'There was a problem'));
			//return 'Saving...';
		}
		
		return $this->_load_file($f,$data);	
	}
	
    /**
     * Extends modRequest::handleRequest and loads the proper error handler and
     * actionVar value.
     *
     * @return string
     */
    public function handleRequest() {
		    
        $this->loadErrorHandler();
		$this->sanitizeRequest();
		
        // save page to manager object. allow custom actionVar choice for extending classes.
        $action = isset($_REQUEST[$this->actionVar]) ? $_REQUEST[$this->actionVar] : $this->defaultAction;

		// Avoid directory traversals etc.
		if (preg_match('/[^a-zA-Z0-9\_]/',$action)) {
			return 'Invalid Action.';
		}

		$token = $this->modx->getOption('moxycart.token');
		if (empty($token)) {
			$action = 'create_client'; // gotta get the 
		}

		// setup caching
		$Cache = new MODx_Cache($this->modx);
		//return MoxyCart::$rel_base_uri; 
		// setup client
		$this->Client = new Client($Cache,MoxyCart::$rel_base_uri);
//		return __LINE__;
//		return $this->Client->getLink('create_client');
//		return 'asdfadfasd';
		
		$resp = $this->Client->get(MoxyCart::$api_home_page,null,$this->MoxyCart->getHeaders());
		// return print_r($resp,true);
		// Add the header and footer to the action
		$output = $this->_load_file($this->MoxyCart->config['corePath'].'views/header.php');
        $output .= $this->$action();
        $output .= $this->_load_file($this->MoxyCart->config['corePath'].'views/footer.php');
        return $output;
    }
}