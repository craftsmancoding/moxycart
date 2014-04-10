<?php
/**
 * Model
 * This is a bit tricky because we already have an ORM layer: that Product class already
 * handles newObject, save, etc.  The getCollection 
 *
 * This model class should define a unified interface that define the graphs that represent
 * an object data in its entirety.
 */
namespace Moxycart;
class Product {
    public static $modx;
    
    public function __construct(\modX &$modx) {
        self::$modx = $modx;
    }

    /**
     *
     * @return xPDO collection
     */
    public static function all($args,$debug=false) {

//        return $args;
//        $x = self::$modx->newObject('Product');
//        return $x->toArray();


        $limit = (int) self::$modx->getOption('limit',$args,self::$modx->getOption('moxycart.default_per_page','',self::$modx->getOption('default_per_page')));
        $start = (int) self::$modx->getOption('start',$args,0);
        $sort = self::$modx->getOption('sort',$args,'name');
        $dir = self::$modx->getOption('dir',$args,'ASC');

        // Clear out non-filter criteria
        unset($args['limit']);
        unset($args['start']);
        unset($args['sort']);
        unset($args['dir']);
 

        $criteria = self::$modx->newQuery('Product');
//return $criteria;
        if ($args) {
            $criteria->where($args);
        }
//        $P = self::$modx->getObject('Product', 1);
//        return $P->toArray();
        if ($count) {
            return self::$modx->getCount('Product',$criteria);        
        }
//            return $criteria->toSQL();

        $criteria->limit($limit, $start); 
        $criteria->sortby($sort,$dir);
        if ($debug) {
            $criteria->prepare();
            return $criteria->toSQL();
        }

        // Both array and string input seem to work
        // TODO: config to let user define which columns to select
        //$criteria->select(array('product_id','name','description','type','sku'));
        return self::$modx->getIterator('Product',$criteria);

    }
    
    public static function count($args) {
        // Clear out non-filter criteria
        unset($args['limit']);
        unset($args['start']);
        unset($args['sort']);
        unset($args['dir']);
 

        $criteria = self::$modx->newQuery('Product');
        if ($args) {
            $criteria->where($args);
        }
        return self::$modx->getCount('Product',$criteria);
    
    }
    
    public static function create($args) {
        return self::$modx->newObject('Product', $args);
    }
    
    public static function delete(int $id) {
        $P = self::$modx->getObject('Product', $id);
        return $P->remove();
    }
    
    public static function find($id) {
    
    }
    
    public static function update($id,$args) {
    
    }
    
}
/*EOF*/