<?php
/**
 * Before running these tests, you must install the package using Repoman
 * and seed the database with the test data!
 *
 *  php repoman.php install /path/to/repos/moxycart '--seed=base,test'
 * 
 * That will ensure that the database tables contain the correct test data. 
 * If you need to create more test data, make sure you add the appropriate 
 * arrays to the model/seeds/test directory (either manually or via repoman's
 * export command).
 *
 * To run these tests, pass the test directory as the 1st argument to phpunit:
 *
 *   phpunit path/to/moxycart/core/components/moxycart/tests
 *
 * or if you're having any trouble running phpunit, download its .phar file, and 
 * then run the tests like this:
 *
 *  php phpunit.phar path/to/moxycart/core/components/moxycart/tests
 *
 *
 */
 
class modelTest extends PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    public static $moxycart;
    
    /**
     * Load up MODX for our tests.
     *
     */
    public static function setUpBeforeClass() {        
        $docroot = dirname(dirname(dirname(dirname(__FILE__))));
        while (!file_exists($docroot.'/config.core.php')) {
            if ($docroot == '/') {
                die('Failed to locate config.core.php');
            }
            $docroot = dirname($docroot);
        }
        if (!file_exists($docroot.'/config.core.php')) {
            die('Failed to locate config.core.php');
        }
        
        include_once $docroot . '/config.core.php';
        
        if (!defined('MODX_API_MODE')) {
            define('MODX_API_MODE', false);
        }
        
        include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
         
        self::$modx = new modX();
        self::$modx->initialize('mgr');  
        
        $core_path = self::$modx->getOption('moxycart.core_path', '', MODX_CORE_PATH);
        include_once $core_path . 'components/moxycart/model/moxycart/moxycart.class.php';
        
        self::$moxycart = new Moxycart(self::$modx);
        
    }

    /**
     *
     */
    public function testMODX() {
        $this->assertTrue(defined('MODX_CORE_PATH'), 'MODX_CORE_PATH not defined.');
        $this->assertTrue(defined('MODX_ASSETS_PATH'), 'MODX_ASSETS_PATH not defined.');
        $this->assertTrue(is_a(self::$modx, 'modX'), 'Invalid modX instance.');
    
    }
    
    public function testProducts() {
        // The basic test:   
        $Products = self::$moxycart->json_products(array(), true);
        $this->assertTrue(!empty($Products), 'Unable to retrieve collection "Product"');

        // Product ID exist test:   
        $product_id = 1;
        $Product = self::$moxycart->json_products(array('product_id'=>$product_id), true);
        $this->assertTrue($Product['total'] == 1, 'No Product Found with an id of ' . $product_id);

        // Get the first product
        $P = array_shift($Products['results']); 
        $this->assertTrue($P['sku'] == 'MOUSTACHE-HOODIE', 'Product sku is '.$P['sku']);        
        
        
        // Test sorting:
        $Products = self::$moxycart->json_products(array('sort'=>'name', 'dir'=>'ASC'),true);
        $P = array_shift($Products['results']); 
        $this->assertTrue($P['sku'] == 'ANOTHER-SWEATER', 'Product sku is '.$P['sku']);    

        // Test filters -- 
        $Products = self::$moxycart->json_products(array('in_menu'=>0),true);        
        $this->assertTrue($Products['total'] == 1, 'Only 1 product is flagged with in_menu 0');     
    }


    /**
     * We have some logic that determines default product attributes based on values set in the 
     * the parent Store.  This ensures the defaults are set correctly.
     */
    public function testProductDefaults() {
        $P = self::$modx->newObject('Product');
        // First, we test it with no store_id passed
        $defaults = $P->get_defaults();
        $this->assertTrue($defaults['template_id'] == self::$modx->getOption('default_template'), 'Default template not correct.');
        // defaults should be inherited from the parent store
        if ($Store = self::$modx->getObject('Store', array('alias'=> 'sample-store'))) {
            $store_id = $Store->get('id');
            $defaults = $P->get_defaults($store_id);
            $this->assertTrue($defaults['template_id'] == 2, 'Default template not inherited from store.');    
            $this->assertTrue($defaults['product_type'] == 'regular', 'Product type not inherited from store.');    
            $this->assertTrue($defaults['sort_order'] == 'SKU', 'Sort order not inherited from store.');    
            $this->assertTrue($defaults['qty_alert'] == 5, 'qty_alert not inherited from store.');
            $this->assertTrue(isset($defaults['specs'][1]), 'Product specs not inherited from store.');    
            $this->assertTrue(isset($defaults['specs'][3]), 'Product specs not inherited from store.');
        }
    }

    public function testSpecs() {
        // The basic test:   
        $Specs = self::$moxycart->json_specs(array(), true);
        $this->assertTrue(!empty($Specs), 'Unable to retrieve collection "Spec"');
    }
    
}