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
 * php repoman schema:parse . --model=moxycart --table_prefix=moxy_ --overwrite --restore=store,taxonomy,term,review,product.class
 */
namespace Moxycart;
class validationTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    public static $moxycart;
    
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
    
    
    public function testCurrency() {    
        $Currency = new Currency(self::$modx);
        $Currency->fromArray(array(
            'code' => 'ZZ',
            'name' => 'XX',
            'symbol' => '1123',
            'is_active' => 1,
            'seq' => 0
        ));
        $this->assertTrue(is_object($Currency));        
        $result = $Currency->save();
        $this->assertFalse($result);

        $errors = $Currency->getErrors();
        
        $this->assertEquals($errors['code'], 'ISO 4217 Currency Codes are 3 characters.');
        $this->assertEquals($errors['name'], 'Your currency name must be at least 3 characters.');         
    }

    public function testProduct() {    

        $P = new Product(self::$modx);
        $P->fromArray(array(
            'name' => '',
            'title' => 'No Name',
            'description' => 'Our handsome hoodie for hirsute homeys',
            'content' => '<p>Just imagine this awesome product.</p>',
            'type' => 'regular',
            'sku' => 'MOUSTACHE-HOODIE',
            'sku_vendor' => '',
            'alias' => 'moustache-hoodie',
            'uri' => 'sample-store/moustache-hoodie',
            'track_inventory' => 0,
            'qty_inventory' => 10,
            'qty_alert' => 3,
            'qty_min' => 1,
            'qty_max' => 0,
            'qty_backorder_max' => 5,
            'price' => '19.00',
            'price_strike_thru' => '25.00',
            'price_sale' => '',
            'sale_start' => '',
            'sale_end' => '',
            'category' => 'Default',
            'is_active' => 1,
            'in_menu' => 1,
            'timestamp_created' => date('Y-m-d H:i:s'),
            'currency_id' => 109,
        ));

        $this->assertTrue(is_object($P));        
        $result = $P->save();
        $this->assertFalse($result);

        $errors = $P->getErrors();
        $this->assertEquals($errors['name'], 'Product name must be at least 1 character.');
    }
    
    public function testReview() {    
        $R = new Review(self::$modx);
        $R->fromArray(array(
            'product_id' => 'does not exist',
            'author_id' => null,
            'name' => 'Tops',
            'email' => '',
            'rating' => 88,
            'content' => 'This this is massive',
            'state' => 'pending',
        ));
        $this->assertTrue(is_object($R));        
        $result = $R->save();
        $this->assertFalse($result);

        //$errors = $R->getErrors();
        
        //$this->assertEquals($errors['code'], 'ISO 4217 Currency Codes are 3 characters.');
        //$this->assertEquals($errors['name'], 'Your currency name must be at least 3 characters.');         
        
        // addReview??
    }

    public function testField() {    
        $F = new Field(self::$modx);
        $F->fromArray(array(
            'slug' => 'does not exist',
            'name' => null,
            'description' => 'Sample',
            'group' => 'Test',
            'type' => '!@#$% Invalid Func name',
        ));
        $this->assertTrue(is_object($F));        
        $result = $F->save();
        $this->assertFalse($result);

        $errors = $F->getErrors();

        $this->assertEquals($errors['slug'], 'Contains invalid characters.');
        $this->assertEquals($errors['name'], 'Field name must be at least 1 character.'); 
        $this->assertEquals($errors['type'], 'Contains invalid characters.');

    }


    public function testVariationType() {    
        $V = new VariationType(self::$modx);
        $V->fromArray(array(
            'slug' => '@!#%!invalid characters',
            'name' => 'does not exist',
            'description' => 'Sample',
            'seq' => '',
        ));
        $this->assertTrue(is_object($V));        
        $result = $V->save();
        $this->assertFalse($result);

        $errors = $V->getErrors();
        $this->assertEquals($errors['slug'], 'Contains invalid characters.');
    }

    public function testVariationTerm() {    
        $V = new VariationTerm(self::$modx);
        $V->fromArray(array(
            'slug' => '@!#%!invalid characters',
            'name' => 'does not exist',
            'description' => 'Sample',
            'sku_prefix' => '!@% ',
            'sku_suffix' => '^!#$^!',
            'seq' => '',
        ));
        $this->assertTrue(is_object($V));        
        $result = $V->save();
        $this->assertFalse($result);
        $errors = $V->getErrors();
        
        $this->assertEquals($errors['slug'], 'Contains invalid characters.');
        $this->assertEquals($errors['sku_prefix'], 'Contains invalid characters.');
        $this->assertEquals($errors['sku_suffix'], 'Contains invalid characters.');        
        //$this->assertEquals($errors['vtype_id'], 'The variation type does not exist.');
        
        // This should be valid
/*
        $V = new VariationTerm(self::$modx);
        $V->fromArray(array(
            'slug' => 'somenewslug',
            'name' => 'does not exist',
            'description' => 'Sample',
            'sku_prefix' => '!@% ',
            'sku_suffix' => '^!#$^!',
            'seq' => '',
        ));
        $this->assertTrue(is_object($V));        
        $result = $V->save();
        $this->assertFalse($result);
        $errors = $V->getErrors();
        print_r($errors);
*/
//        $this->assertEquals($errors['slug'], 'Contains invalid characters.');
    }


    
    
}