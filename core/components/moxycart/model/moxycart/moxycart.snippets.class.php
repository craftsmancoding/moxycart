<?php 
/**
 * BaseClass for Moxycart Snippets
 */
class MoxycartSnippet {

	/** @var $modx modX */
    public $modx;
    public $Moxycart;
    
    public function __construct(&$modx) {
        $this->modx =& $modx;       
        $core_path = $this->modx->getOption('moxycart.core_path', '' , MODX_CORE_PATH);
        require_once($core_path.'components/moxycart/model/moxycart/moxycart.class.php');
        $this->Moxycart = new Moxycart($modx);
    }

    /**
	* Pull records and format the returned data
	* @param string $method - Available methods from moxycrt.class.php
	* @param array $args
	* @return string $output
    **/
    public function execute($method,$args) {
    	$outerTpl = $this->modx->getOption('outerTpl',$args,'MoxyOuterTpl');
		$innerTpl = $this->modx->getOption('innerTpl',$args,'MoxyInnerTpl');
		$args['limit'] = 0;
        unset($args['outerTpl']);
        unset($args['innerTpl']);
        //return 'method: '.$method . ' ' .print_r($args,true);
		$records = $this->Moxycart->$method($args, true);

		if($records['total'] == 0) {
			return 'No Record Found.';
		}

		$innerOut = '';
		$output = '';
		if (isset($records['results']) && is_array($records['results'])) {

				foreach ($records['results'] as $row) {
			   		$innerOut .= $this->modx->getChunk($innerTpl,$row);
				}

			
		}
	
		$innerPlaceholder = array('moxy.items' => $innerOut);
		$output = $this->modx->getChunk($outerTpl,$innerPlaceholder); 
		return $output;
    }
}







