<?php

namespace HyperClient\cache;

/**
 * MODx based implementation of iCache
 *
 * @implements \HyperClient\interfaces\iCache
 * @author Everett Griffiths
 * @package
 */


class MODx_Cache implements \HyperClient\interfaces\iCache {

	private $cache_opts = array(xPDO::OPT_CACHE_KEY => 'moxycart');
	private $lifetime = 0;
	private $modx;

	/**
	 *
	 */
	function __construct() {
		global $modx;
		$this->modx = $modx;
	}


	/**
	 * Checks whether or not the key exists.
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function exists($key) {
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