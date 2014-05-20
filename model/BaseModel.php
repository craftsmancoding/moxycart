<?php
/**
 * BaseModel - simplifiying the interaction a bit (I hope) with the underlying xPDO objects
 * As much as I like the cleaner interface offered by static functions, it just doesn't work 
 * well here because we need to inject the containing MODX object as a dependency.  So the
 * children of BaseModel let us:
 *      1. access object attributes directly (without the $obj->get() and $obj->set() functions)
 *      2. offer shorthand ways of retrieval/searching
 *
 * This is a bit tricky because we already have an ORM layer: e.g. its Product class already
 * handles newObject, save, etc -- we can't just extend it for the features we want because xpdo
 * changes the exact class used depending on the database driver.  So we end up with this weird
 * hybrid class that implements many of the same functions xpdo (e.g. toArray) and passes them
 * through to the ORM object underneath. 
 *
 * WARNING: confusion can arrise where it's not clear whether you've got a Moxycart\Model\Product
 * or an xPDO \Product class on your hands because they look and act very similarly.  
 *
 *
 * Beware late static bindings!
 * See http://stackoverflow.com/questions/10504129/when-using-self-parent-static-and-how
 */
namespace Moxycart;
class BaseModel {

    public $modx;
    
    // Used for new/save ops
    public $modelObj; 
    
    //public static $xclass; // The classname for xPDO when referencing objects of this class
    //public static $default_sort_col;
    //public static $default_sort_dir = 'ASC';

    // Any array keys that define a control parameter and not a filter parameter
    public $control_params = array('limit','offset','sort','dir','select');
    
    /** 
     * We set $this->modelObj here instead of extending the base xpdoObject class because
     * xpdo abstracts the database at run-time and the exact class instantiated depends on 
     * the type of database used.
     *
     * @param object $modx
     * @param integer primary key (optional) used when retrieving objects only
     *
     */
    public function __construct(\modX &$modx, $primary_key=null) {
        $this->modx =& $modx;
        if ($primary_key) {
            if (!$this->modelObj = $this->modx->getObject($this->xclass, $primary_key)) {
                throw new \Exception($this->xclass.' not found with id '.$primary_key);
            }
        }
        else {
            $this->modelObj = $modx->newObject($this->xclass);
        }
    }

    /**
     * 
     */
    public function __get($key) {
        return $this->modelObj->get($key);
    }

    /**
     * 
     */
    public function get($key) {
        return $this->modelObj->get($key);
    }
        
    /**
     *
     *
     */
    public function __set($key, $value) {
        return $this->modelObj->set($key,$value);
    }

    /**
     *
     *
     */
    public function set($key, $value) {
        return $this->modelObj->set($key,$value);
    }

    /**
     * 
     */
    public function __isset($key) {
        $attributes = $this->modx->getFields($this->xclass);
        return array_key_exists($key, $attributes);
    }

    /**
     * 
     */
    public function __unset($key) {
        return $this->modelObj->set($key,null);
    }
    
    /**
     *
     */
    public function __toString() {
        return print_r($this->modelObj->toArray(),true);
    }
    
    /**
     * @param array $array
     */
    public function fromArray(array $array) {
        return $this->modelObj->fromArray($array);
    }

    public function getPrimaryKey() {
        return $this->modelObj->getPrimaryKey();
    }
    
    public function remove() {
        return $this->modelObj->remove();
    }

    /**
     * Remove any "control" arguments and return only "filter" arguments
     * with some convenience bits for searches. Controls are things like limit, offset,
     * or other things that control HOW the results are returned whereas filters determine
     * WHAT gets returned.
     *
     * @param array
     * @return array
     */
    public function getFilters($array) {
        foreach ($this->control_params as $p) {
            unset($array[$p]);
        }
        
        foreach ($array as $k => $v) {
            // For convenience, we add in the %'s
            if (strtoupper(substr($k,-5)) == ':LIKE') $array[$k] = '%'.$v.'%';
            if (strtoupper(substr($k,-9)) == ':NOT LIKE') $array[$k] = '%'.$v.'%';
            if (strtoupper(substr($k,-12)) == ':STARTS WITH') {
                unset($array[$k]);
                $array[substr($k,0,-12).':LIKE'] = $v.'%';
            }
            if (strtoupper(substr($k,-10)) == ':ENDS WITH') {
                unset($array[$k]);
                $array[substr($k,0,-10).':LIKE'] = '%'.$v;
            }

            // Remove any simple array stuff
            if (is_integer($k)) unset($array[$k]);
        }
        return $array;
    }


    /**
     * We use getIterator, but we have to work around the "feature" (bug?) that 
     * it will not return an empty array if it has no results. See
     * https://github.com/modxcms/revolution/issues/11373
     *
     * @param array $arguments (including filters)
     * @param boolean $debug
     * @return mixed xPDO iterator (i.e. a collection, but memory efficient) or SQL query string
     */
    public function all($args,$debug=false) {

        $limit = (int) $this->modx->getOption('limit',$args,$this->modx->getOption('moxycart.default_per_page','',$this->modx->getOption('default_per_page')));
        $offset = (int) $this->modx->getOption('offset',$args,0);
        $sort = $this->modx->getOption('sort',$args,$this->default_sort_col);
        $dir = $this->modx->getOption('dir',$args,$this->default_sort_dir);
        $select_cols = $this->modx->getOption('select',$args);
        
        // Clear out non-filter criteria
        $args = self::getFilters($args); 

        $criteria = $this->modx->newQuery($this->xclass);

        if ($args) {
            $criteria->where($args);
        }
        
        if ($limit) {
            $criteria->limit($limit, $offset); 
            $criteria->sortby($sort,$dir);
        }
    
        if ($debug) {
            $criteria->prepare();
            return $criteria->toSQL();
        }

        // Both array and string input seem to work
        if (!empty($select_cols)) {
            $criteria->select($select_cols);
        }
        // Workaround for issue https://github.com/modxcms/revolution/issues/11373
        $collection = $this->modx->getIterator($this->xclass,$criteria);
        foreach ($collection as $c) {
            return $collection;
        }
        return array();
    }
    
    /**
     * 
     * @param array $args
     * @return integer
     */
    public function count($args) {
        if(!isset($args['limit'])) $args['limit'] = 0;
        // Clear out non-filter criteria
        $args = $this->getFilters($args); 
        
        $criteria = $this->modx->newQuery($this->xclass);
        if ($args) {
            $criteria->where($args);
        }
        return $this->modx->getCount($this->xclass,$criteria);
    }
    
    /**
     *
     *
     */    
    public static function delete(int $id) {
        if ($Obj = $this->find($id)) {
            return $Obj->remove();
        }
        else {
            throw new \Exception('Object not found.');
        }
    }
    
    /**
     * Retrieve a single object by its primary key id -- we pass this back to the constructor
     * so we can return an instance of this class.
     *
     * @param integer $id
     * @return mixed
     */    
    public function find($id) {
        $classname = '\\Moxycart\\Model\\'.$this->xclass;        
        return new $classname($this->modx,$id);
    }
    

    /**
     * Save the update
     * @return mixed integer false on fail
     */
    public function save() {
        return $this->modelObj->save();
    }
    
}
/*EOF*/