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
class Base {

    public static $modx;
    

    public static $xclass; // xPDO classname
    public static $default_sort_col;
    public static $default_sort_dir = 'ASC';

    // Any array keys that define a control parameter and not a filter parameter
    public static $control_params = array('limit','offset','sort','dir','select');
    
    /** 
     *
     *
     */
    public function __construct(\modX &$modx) {
        self::$modx =& $modx;
    }

    /**
     * Remove any control argument and return only filters
     * @param array
     * @return array
     */
    public function getFilters($array) {
        foreach (self::$control_params as $p) {
            unset($array[$p]);
        }
        return $array;
    }


    /**
     *
     * @return xPDO collection
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
     *
     *
     */
    public static function create($args) {
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
     *
     *
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
    
}
/*EOF*/