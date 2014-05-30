<?php
/**
 *
 * See the IndexManagerController class (index.class.php) for routing info.
 *
 * @package moxycart 
 */
namespace Moxycart;
class FieldController extends APIController {
    public $model = 'Field';        

    /**
     * Remember we have to set up the manager container
     *
     */
    public function getRow(array $scriptProperties = array()) {
    
        $field_id = (int) $this->modx->getOption('field_id',$scriptProperties);
        $Obj = new Field($this->modx);    
        if (!$result = $Obj->find($field_id)) {
            return $this->sendError('Page not found.');
        }
        
        
        $data = $result->toArray();
        $path = $this->modx->getOption('moxycart.core_path','', MODX_CORE_PATH.'components/moxycart/').'views/';
        ob_start();
		include $path.'field/instance.php';
		$content .= ob_get_clean();
		return $content;
//        return $this->fetchTemplate('field/instance.php');
    }    
}
/*EOF*/