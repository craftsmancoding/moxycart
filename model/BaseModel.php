<?php
/**
 * Model
 * This is a bit tricky because we already have an ORM layer: that Product class already
 * handles newObject, save, etc.  The getCollection 
 *
 * This model class should define a unified interface that define the graphs that represent
 * an object data in its entirety.
 *
 * Beware late static bindings!
 * See http://stackoverflow.com/questions/10504129/when-using-self-parent-static-and-how
 */
namespace Moxycart\Model;
class BaseModel {

    public static $modx;
    
    // Used for new/save ops
    public $modelObj; 
    public $attributes = array();
    
    public static $xclass; // The classname for xPDO when referencing objects of this class
    public static $default_sort_col;
    public static $default_sort_dir = 'ASC';

    // Any array keys that define a control parameter and not a filter parameter
    public static $control_params = array('limit','offset','sort','dir','select');
    
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
        self::$modx =& $modx;
        if ($primary_key) {
            if ($this->modelObj = $modx->getObject(static::$xclass, $primary_key)) {            
                $this->attributes = $this->modelObj->fromArray();
            }
            else {
                throw new \Exception(static::$xclass.' not found with id '.$primary_key);
            }
        }
        else {
            $this->modelObj = $modx->newObject(static::$xclass);
            $this->attributes = $modx->getFields(static::$xclass);
        }
    }

    /**
     * 
     */
    public function __get($key) {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key]; 
        }
        else {
            throw new \Exception('Invalid object attribute: '.$key);
        }
    }
    
    /**
     *
     *
     */
    public function __set($key, $value) {
        if (array_key_exists($key, $this->attributes)) {
            $this->attributes[$key] = $value;
        }
        else {
            throw new \Exception('Invalid object attribute: '.$key);
        }

    }

    /**
     * 
     */
    public function __isset($key) {
        return isset($this->attributes[$key]);
    }

    /**
     * 
     */
    public function __unset($key) {
        unset($this->attributes[$key]);
    }
    
    /**
     * @param array $array
     */
    public function fromArray(array $array) {
        $this->modelObj->fromArray($array);
        foreach ($array as $k => $v) {
            $this->$k = $v;
        }
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
        foreach (self::$control_params as $p) {
            unset($array[$p]);
        }
        foreach ($array as $k => $v) {
            if (strtoupper(substr($k,-5)) == ':LIKE') $array[$k] = '%'.$v.'%';
            if (strtoupper(substr($k,-9)) == ':NOT LIKE') $array[$k] = '%'.$v.'%';
        }
        return $array;
    }


    /**
     *
     * @return xPDO iterator (i.e. a collection, but memory efficient)
     */
    public static function all($args,$debug=false) {

        $limit = (int) self::$modx->getOption('limit',$args,self::$modx->getOption('moxycart.default_per_page','',self::$modx->getOption('default_per_page')));
        $offset = (int) self::$modx->getOption('offset',$args,0);
        $sort = self::$modx->getOption('sort',$args,static::$default_sort_col);
        $dir = self::$modx->getOption('dir',$args,static::$default_sort_dir);
        $select_cols = self::$modx->getOption('select',$args);
        
        // Clear out non-filter criteria
        $args = self::getFilters($args); 

        $criteria = self::$modx->newQuery(static::$xclass);

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
        
        return self::$modx->getIterator(static::$xclass,$criteria);
    }
    
    /**
     * 
     * @param array $args
     * @return integer
     */
    public static function count($args) {
        // Clear out non-filter criteria
        $args = self::getFilters($args); 

        $criteria = self::$modx->newQuery(static::$xclass);
        if ($args) {
            $criteria->where($args);
        }
        return self::$modx->getCount(static::$xclass,$criteria);
    }
    
    /**
     * Pass all the attributes for the new object
     *
     */
    public static function create($args=array()) {
        if (empty(static::$xclass)) {
            self::$modx->log(\modX::LOG_LEVEL_ERROR, 'Create object failed: missing object classname.','',__CLASS__,__FILE__,__LINE__);
            return false;
        }
        return self::$modx->newObject(static::$xclass, $args);
    }

    /**
     *
     *
     */    
    public static function delete(int $id) {
        if ($Obj = static::find($id)) {
            return $Obj->remove();
        }
    }
    
    /**
     * Retrieve a single object by its primary key id
     * @param integer $id
     * @return mixed
     */    
    public static function find($id) {
        if ($Obj = self::$modx->getObject(static::$xclass, $id)) {
            return $Obj;
        }
        return false;
    }
    
    public static function update($id,$args) {
        if ($Obj = static::find($id)) {
            $Obj->fromArray($args);
            return $Obj->save();
        }
        return false;
    }
    
    /**
     * Save the update
     * @return mixed integer false on fail
     */
    public function save() {
        return $this->modelObj->save();
/*
        if ($this->modelObj->save()) {
            return $this->modelObj->getPrimaryKey();
        }
        return false;
*/
    }
    
}
/*EOF*/