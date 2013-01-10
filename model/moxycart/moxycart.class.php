<?php
class MoxyCart {

	/**
	 * Put the API home page here. Omit trailing slash.
	 * @var string
	 */
	public static $api_home_page = 'https://api-sandbox.foxycart.com';
	
	/**
	 * The full base URI for your Link Relations. Includes trailing slash.
	 * @var string
	 */
	public static $rel_base_uri = 'https://api.foxycart.com/rels/';

	public $required_headers = array('FOXYCART-API-VERSION: 1');

	public $modx;
	public $media_type = 'hal'; // hal or vnd.siren
	private $format = 'json';
	public $request; // used inside the manager: handle mgr pages
	
	/**
	 *
	 */
	function __construct(modX &$modx) {
        $this->modx =& $modx;

        /* allows you to set paths in different environments
         * this allows for easier SVN management of files
         */
        $corePath = $this->modx->getOption('moxycart.core_path',null,$modx->getOption('core_path').'components/moxycart/');
        $assetsPath = $this->modx->getOption('moxycart.assets_path',null,$modx->getOption('assets_path').'components/moxycart/');
        $assetsUrl = $this->modx->getOption('moxycart.assets_url',null,$modx->getOption('assets_url').'components/moxycart/');

        $this->config = array(
            'corePath' => $corePath,
            'assetsUrl' => $assetsUrl,
            'assetsPath' => $assetsPath,
        );

        $this->modx->addPackage('moxycart',$corePath.'model/');
        if ($this->modx->lexicon) {
            $this->modx->lexicon->load('moxycart:default');
        }

	}

	//------------------------------------------------------------------------------
	//! Public functions
	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 *
	 */
    function formatRequestData($data) {
		return json_encode($data);
    }

	/**
	 *
	 * @param string $token
	 * @return array
	 */
    function getHeaders($token = '') {
        $headers = array_merge(
                $this->required_headers,
                array(
                        'Accept: application/hal+json',
                        'Content-Type: application/json'
                )
        );
        if ($token != '') {
            $headers[] = 'Authorization: Bearer '. $token;
        }
        return $headers;
    }

    /**
     * @param array/object
     * @return
     */
	function getToken($resp) {
		return $tokens['client'] = $resp['data']->token->access_token;
    }
    
    /**
     * Initializes MoxyCart based on a specific context.
     *
     * @access public
     * @param string $ctx The context to initialize in.
     * @return string The processed content.
     */
    public function initialize($ctx = 'mgr') {
        switch ($ctx) {
            case 'mgr':
                if (!$this->modx->loadClass('moxycart.request.moxycartrequest',$this->config['corePath'].'model/',true,true)) {
                    return 'Could not load controller request handler.';
                }
                $this->request = new MoxyCartRequest($this);
                return $this->request->handleRequest();
                break;                
        }
    }
    
}
/*EOF*/