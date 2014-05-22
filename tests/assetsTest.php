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
namespace Moxycart;
class assetTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    
    /**
     * Load up MODX for our tests.
     *
     */
    public static function setUpBeforeClass() {        
        self::$modx = new \modX();
        self::$modx->initialize('mgr');
        $core_path = self::$modx->getOption('moxycart.core_path','',MODX_CORE_PATH.'components/moxycart/');
        self::$modx->addExtensionPackage($object['namespace'],"{$core_path}model/orm/", array('tablePrefix'=>'moxy_'));
        self::$modx->addPackage('moxycart',"{$core_path}model/orm/",'moxy_');
        self::$modx->addPackage('foxycart',"{$core_path}model/orm/",'foxy_');
    }

    /**
     *
     */
    public function testMODX() {
        $this->assertTrue(defined('MODX_CORE_PATH'), 'MODX_CORE_PATH not defined.');
        $this->assertTrue(defined('MODX_ASSETS_PATH'), 'MODX_ASSETS_PATH not defined.');
        $this->assertTrue(is_a(self::$modx, 'modX'), 'Invalid modX instance.');
    
    }
    
    /**
     * 
     *
     */
    public function testAssetCreation() {

        $A = new Asset(self::$modx);
        $this->assertTrue($A instanceof Asset);
        
        // Verify mass-assignment
        $A->fromArray(array(
            'code' => 'YYY',
            'name' => 'Yellow Yuan',
            'symbol' => '1123',
            'is_active' => 1,
            'seq' => 0
        ));
        $this->assertEquals('YYY', $Currency->code);
        
        // Verify single-assignment
        $Currency->symbol = '3345';
        $this->assertEquals('3345', $Currency->symbol);
        $Currency->set('symbol','3346');
        $this->assertEquals('3346', $Currency->get('symbol'));
        
        $result = $Currency->save();
        $this->assertTrue($result);
        
        $id = $Currency->getPrimaryKey();
        
        $Currency = $Currency->find($id);

        $this->assertEquals('YYY', $Currency->code);
        $Currency->remove();
        
        // Retrieving the deleted row should raise an exception
        try {
            $Currency = new Currency(self::$modx, $id);
        }
        catch (\Exception $expected) {
            return; // the exception class must be properly namespaced with backslash
        }
        $this->fail('An expected exception has not been raised. Currency object was retrieved when it should not exist.');
    
    }
    
    public function testFilters() {
        $Currency = new Currency(self::$modx);
        $args = array('limit'=>10,'offset'=>20);
        $filters = $Currency->getFilters($args);
        $this->assertTrue(empty($filters));
        
        $args = array('limit'=>10,'offset'=>20,'something'=>'else');
        $filters = $Currency->getFilters($args);
        $this->assertEquals($filters['something'],'else');

        $args = array('sort'=>'x','dir'=>'ASC','select'=>'yyy','mycolumn:like'=>'myvalue');
        $filters = $Currency->getFilters($args);        
        $this->assertEquals($filters['mycolumn:like'],'%myvalue%');

        $args = array('mycolumn:starts with'=>'myvalue');
        $filters = $Currency->getFilters($args);        
        $this->assertEquals($filters['mycolumn:LIKE'],'myvalue%');
        $this->assertEquals(count($filters),1);
        
        $args = array('mycolumn:ends with'=>'myvalue');
        $filters = $Currency->getFilters($args);        
        $this->assertEquals($filters['mycolumn:LIKE'],'%myvalue');


        $args = array('one','two','three');
        $filters = $Currency->getFilters($args);
        $this->assertTrue(empty($filters));
    }
    
    public function testCount() {
        $C = new Currency(self::$modx);
        $cnt = $C->count(array('code:STARTS WITH'=>'s'));
        $this->assertEquals($cnt,10);

        $cnt = $C->count(array('name:>'=>'D'));
        $this->assertEquals($cnt,88);
    }
    
    public function testDebug() {
        $C = new Currency(self::$modx);
        $actual = $C->all(array('name:>'=>'D'),true);
        $expected = "SELECT `Currency`.`currency_id` AS `Currency_currency_id`, `Currency`.`code` AS `Currency_code`, `Currency`.`name` AS `Currency_name`, `Currency`.`symbol` AS `Currency_symbol`, `Currency`.`is_active` AS `Currency_is_active`, `Currency`.`seq` AS `Currency_seq` FROM `moxy_currencies` AS `Currency` WHERE `Currency`.`name` > 'D' ORDER BY name LIMIT 20";
        $this->assertEquals(normalize_string($actual),normalize_string($expected));

        $actual = $C->all(array('code:LIKE'=>'VC'),true);
        $expected = "SELECT `Currency`.`currency_id` AS `Currency_currency_id`, `Currency`.`code` AS `Currency_code`, `Currency`.`name` AS `Currency_name`, `Currency`.`symbol` AS `Currency_symbol`, `Currency`.`is_active` AS `Currency_is_active`, `Currency`.`seq` AS `Currency_seq` FROM `moxy_currencies` AS `Currency` WHERE `Currency`.`code` LIKE '%VC%' ORDER BY name LIMIT 20";
        $this->assertEquals(normalize_string($actual),normalize_string($expected));

        $actual = $C->all(array('code:LIKE'=>'VC','limit'=>30),true);
        $expected = "SELECT `Currency`.`currency_id` AS `Currency_currency_id`, `Currency`.`code` AS `Currency_code`, `Currency`.`name` AS `Currency_name`, `Currency`.`symbol` AS `Currency_symbol`, `Currency`.`is_active` AS `Currency_is_active`, `Currency`.`seq` AS `Currency_seq` FROM `moxy_currencies` AS `Currency` WHERE `Currency`.`code` LIKE '%VC%' ORDER BY name LIMIT 30";
        $this->assertEquals(normalize_string($actual),normalize_string($expected));
    }
    
    public function testGetCollection() {
        $Currency = new Currency(self::$modx);
        $collection = $Currency->all(array('code'=>'XXXX'));
        $this->assertTrue(empty($collection));
        $collection = $Currency->all(array('code:STARTS WITH'=>'TR'));
        $values = array();
        foreach ($collection as $c) {
            $values[] = $c->get('code');
        }
        $this->assertEquals($values[0],'TRY');
        $this->assertEquals($values[1],'TRL');
    }

    
    /**
     *
     * @expectedException Currency not found with id
     */
/*
    public function testNotFound() {
        $Currency = new Currency(self::$modx, 123124);    
    }
*/
    
    public function testProducts() {
/*
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
*/     
    }


    /**
     * We have some logic that determines default product attributes based on values set in the 
     * the parent Store.  This ensures the defaults are set correctly.
     */
/*
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
*/

/*
    public function testSpecs() {
        // The basic test:   
        $Specs = self::$moxycart->json_specs(array(), true);
        $this->assertTrue(!empty($Specs), 'Unable to retrieve collection "Spec"');
    }
*/
    
}