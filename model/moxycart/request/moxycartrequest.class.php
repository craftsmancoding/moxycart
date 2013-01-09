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
     * A reference to the MoxyCart instance
     * @var MoxyCart $MoxyCart
     */
    public $MoxyCart = null;
    public $modx = null;
    
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
		$moxycart =& $this->MoxyCart;
		
		$f = $this->MoxyCart->config['corePath'].'views/mgr/'.$name.'.php';
		
		if (is_file($f)) {
			ob_start();
			include $f;
			return ob_get_clean();
		}
		else {
			return 'Action not found: '.$f;
		}
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
	private function _load_file($file) {
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
    /**
     * Extends modRequest::handleRequest and loads the proper error handler and
     * actionVar value.
     *
     * @return string
     */
    public function handleRequest() {
        $this->loadErrorHandler();

        /* save page to manager object. allow custom actionVar choice for extending classes. */
        $action = isset($_REQUEST[$this->actionVar]) ? $_REQUEST[$this->actionVar] : $this->defaultAction;

		// Avoid directory traversals etc.
		if (preg_match('/[^a-zA-Z0-9\_]/',$action)) {
			return 'Invalid Action.';
		}

		$output = $this->_load_file($this->MoxyCart->config['corePath'].'views/header.php');
        $output .= $this->$action();
        $output .= $this->_load_file($this->MoxyCart->config['corePath'].'views/footer.php');
        return $output;
    }

    /**
     * Prepares the MODx response to a mgr request that is being handled.
     *
     * @access public
     * @return boolean True if the response is properly prepared.
     */
    private function _respond() {
        $modx =& $this->modx;
        $MoxyCart =& $this->MoxyCart;

        // $viewHeader = include $this->MoxyCart->config['corePath'].'controllers/mgr/header.php';

        $f = $this->MoxyCart->config['corePath'].'controllers/mgr/'.$this->action.'.php';
        if (file_exists($f)) {
            $viewOutput = include $f;
        } else {
            $viewOutput = 'Action not found: '.$f;
        }

        return $viewOutput;
    }
}