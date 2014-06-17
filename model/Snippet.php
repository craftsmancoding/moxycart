<?php 
/**
 * BaseClass for Moxycart Snippets
 */
namespace Moxycart;
class Snippet {

    public $modx;
    public $old_log_level;
    
    public function __construct(&$modx) {
        $this->modx =& $modx;       
    }

    public function __destruct() {
        $this->modx->setLogLevel($this->old_log_level);
        /*
        // TODO
        $xpdo->setLogTarget(array(
           'target' => 'FILE',
           'options' => array(
               'filename' => 'install.' . strftime('%Y-%m-%dT%H:%M:%S')
            )
        ));
        */
    }
    

    public function get_rate_average($method,$args) {
    	$records = $this->modx->moxycart->$method($args, true);

    	$total = 0;
    	$rating = 0;
		if($records['total'] == 0) {
			return '';
		}

		foreach ($records['results'] as $row) {
	   		$total += $row['rating'];
		}

		$rating = ($total / $records['total']) / $records['total'];
		return round($rating, 2);

    }

    /**
     * Logging Snippet info
     *
     */
    public function log($snippetName, $scriptProperties) {
        $log_level = $this->modx->getOption('log_level',$scriptProperties, $this->modx->getOption('log_level'));
        $this->old_log_level = $this->modx->setLogLevel($log_level);
        
        // TODO
        //$this->old_log_target = $this->modx->getOption('log_level');
        //$log_target = $this->modx->getOption('log_target',$scriptProperties);
 
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, "scriptProperties:\n".print_r($scriptProperties,true),'','Snippet '.$snippetName);
    }


    /**
     * Format a record set:
     *
     * Frequently Snippets need to iterate over a record set. Each record should be formatted 
     * using the $innerTpl, and the final output should be optionally wrapped in the $outerTpl.
     *
     * See http://rtfm.modx.com/revolution/2.x/developing-in-modx/other-development-resources/class-reference/modx/modx.getchunk
     *
     * @param array of arrays (a simple record set), or an array of objects (an xPDO Collection)
     * @param string formatting $innerTpl formatting string OR chunk name
     * @param string formatting $outerTpl formatting string OR chunk name (optional)
     * @return string
     */
    public function format($records,$innerTpl,$outerTpl=null) {
        if (empty($records)) {
            return '';
        }

        
        // A Chunk Name was passed
        $use_tmp_chunk = false;
        if (!$innerChunk = $this->modx->getObject('modChunk', array('name' => $innerTpl))) {
            $use_tmp_chunk = true;
        }
        
        $out = '';

        foreach ($records as $r) {
          
            if (is_object($r)) $r = $r->toArray('',false,false,true); // Handle xPDO objects

            // Use a temporary Chunk when dealing with raw formatting strings
            if ($use_tmp_chunk) {
                $uniqid = uniqid();
                $innerChunk = $this->modx->newObject('modChunk', array('name' => "{tmp-inner}-{$uniqid}"));
                $innerChunk->setCacheable(false);    
                $out .= $innerChunk->process($r, $innerTpl);
            }
            // Use getChunk when a chunk name was passed
            else {
                $out .= $this->modx->getChunk($innerTpl, $r);
            }
        }

        
        if ($outerTpl) {
            $props = array('content'=>$out);
            // Formatting String
            if (!$outerChunk = $this->modx->getObject('modChunk', array('name' => $outerTpl))) {  
                $uniqid = uniqid();
                $outerChunk = $this->modx->newObject('modChunk', array('name' => "{tmp-outer}-{$uniqid}"));
                $outerChunk->setCacheable(false);    
                return $outerChunk->process($props, $outerTpl);        
            }
            // Chunk Name
            else {
                return $this->modx->getChunk($outerTpl, $props);
            }
        }

        return $out;
    }
    
    /**
     * Given a potentially deeply nested array, this demonstrates which placeholders are available
     * for debugging purposes.
     * @param array
     */
    public function revealPlaceholders($array) {
    
    }

}