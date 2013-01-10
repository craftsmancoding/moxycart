<?php
/**
 * MODx based implementation of iCache
 *
 * @implements \HyperClient\interfaces\iCache
 * @author Everett Griffiths
 * @package
 */
class MODx_Cache implements iCache {

	private $cache_opts = array('cache_key' => 'moxycart');
	private $lifetime = 0;
	public $modx;

	/**
	 *
	 */
	function __construct(modX &$modx) {
        $this->modx = & $modx;
	}

	/**
	 * Checks whether or not the key exists.
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function exists($key) {
//		print 'Here'; exit;
//		print_r($this->modx); exit;
//		print 'Key: '.$key; exit;
//		print __LINE__; 
/*
		try {
		    $x = $this->modx->cacheManager->get($key, $this->cache_opts);
		} 
		catch (Exception $e) {
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
    	}
		print __LINE__; 
*/

		//print '-->'.$x; exit;
		return (bool) $this->modx->cacheManager->get($key, $this->cache_opts);
	}

	/**
	 *
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function fetch($key) {
		return $this->modx->cacheManager->get($key, $this->cache_opts);
	}

	/**
	 *
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return boolean TRUE on success, FALSE on fail
	 */
	public function store($key, $value) {
		return $this->modx->cacheManager->set($key, $value, $this->lifetime, $this->cache_opts);
	}
}
/*EOF*/