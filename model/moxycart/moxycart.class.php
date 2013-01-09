<?php
class MoxyCart {

	/**
	 * Put the API home page here. Omit trailing slash.
	 * @var string
	 */
	public $api_home_page = 'https://api-sandbox.foxycart.com';
	
	/**
	 * The full base URI for your Link Relations. Includes trailing slash.
	 * @var string
	 */
	public $rel_base_uri = 'https://api.foxycart.com/rels/';

	public $modx;
	
	public $request; // used inside the manager
	
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
     * Initializes MoxyCart based on a specific context.
     *
     * @access public
     * @param string $ctx The context to initialize in.
     * @return string The processed content.
     */
    public function initialize($ctx = 'mgr') {
        $output = '';
        //print $this->config['corePath'].'model/'; exit;
        switch ($ctx) {
            case 'mgr':
                if (!$this->modx->loadClass('moxycart.request.moxycartrequest',$this->config['corePath'].'model/',true,true)) {
                    return 'Could not load controller request handler.';
                }
                $this->request = new MoxyCartRequest($this);
                $output = $this->request->handleRequest();
                break;
        }
        return $output;
    }
	//------------------------------------------------------------------------------
	/**
	 * Load up a PHP file into a string via an include statement. MVC type usage here.
	 *
	 * @param string  $filename (relative to the views/ directory)
	 * @param array   $data (optional) associative array of data
	 * @param string  $path (optional) pathname. Can be overridden for 3rd party fields
	 * @return string the parsed contents of that file
	 */
/*
	public function load_view($filename, $data=array(), $path=null) {

		if (is_file('views/'.$filename)) {
			ob_start();
			include 'views/'.$filename;
			return ob_get_clean();
		}
		die('View file does not exist: ' .$filename);

		//$this->modx->log(xPDO::LOG_LEVEL_DEBUG, "[Gmaps] query URL: $url");
	}
*/

}
/*EOF*/