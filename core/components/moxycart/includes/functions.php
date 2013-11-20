<?php

/**
 * Load a view file. We put in some commonly used variables here for convenience
 *
 * @param string $file: name of a file inside of the "views" folder
 * @param array $data: an associative array containing key => value pairs, passed to the view
 * @return string
 */
function load_view($file, $data=array(),$return=false) {
	global $modx;
	$moxycart_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
	if (file_exists($moxycart_path.'components/moxycart/views/'.$file)) {
	    if (!isset($return) || $return == false) {
	        ob_start();
	        include ($moxycart_path.'components/moxycart/views/'.$file);
	        $output = ob_get_contents();
	        ob_end_clean();
	    }     
	} 
	else {
		global $modx;
		$output = $modx->lexicon('view_not_found', array('file'=> 'views/'.$file));
	}

	return $output;

}