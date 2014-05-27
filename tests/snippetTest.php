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
class snippetTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    public static $Store;
    public static $Field;
    public static $product_id;
    
    /**
     * Load up MODX for our tests.
     * Create sample data for testing.
     */
    public static function setUpBeforeClass() {        
        self::$modx = new \modX();
        self::$modx->initialize('mgr');

        $core_path = self::$modx->getOption('moxycart.core_path','',MODX_CORE_PATH.'components/moxycart/');
        self::$modx->addExtensionPackage('moxycart',"{$core_path}model/orm/", array('tablePrefix'=>'moxy_'));
        self::$modx->addPackage('moxycart',"{$core_path}model/orm/",'moxy_');
        self::$modx->addPackage('foxycart',"{$core_path}model/orm/",'foxy_');

        // Prep: create a parent store 
        if (!self::$Store = self::$modx->getObject('Store', array('alias'=>'test-store'))) {
            self::$Store = self::$modx->newObject('Store');
            self::$Store->fromArray(array(
                'pagetitle' => 'Test Store',
                'longtitle' => 'Test Store',
                'menutitle' => 'Test Store',
                'description' => 'A temporary store used for testing',
                'alias' => 'test-store',
                'uri' => 'test-store/',
                'class_key' => 'Store',
                'isfolder' => 1,
                'published' => 1,
                 'properties' => '{"moxycart":{"product_type":"regular","product_template":"2","sort_order":"SKU","qty_alert":"5","track_inventory":0,"fields":{"1":true,"3":true},"variations":[],"taxonomies":[]}}',
                'Template' => array('templatename' => 'Sample Store'),
            ));
            self::$Store->save();        
        }

        // Prep: create a Test Product Field
        if (!self::$Field = self::$modx->getObject('Field', array('slug'=>'test-field'))) {
            self::$Field = self::$modx->newObject('Field');
            self::$Field->fromArray(array(
                'slug' => 'Test-field',
                'label' => 'Test Field',
                'description' => 'Test Field',
                'group' => 'GroupA',
                'seq'   => 0,
                'type'=>'text'
            ));
            self::$Field->save();       
        }
        
        // Rustle up some products
        if (!$Product = self::$modx->getObject('Product', array('alias'=> 'south-park-tshirt', 'store_id'=> self::$Store->get('id')))) {
            $Product = self::$modx->newObject('Product');
            $Product->fromArray(array(
                'store_id' => self::$Store->get('id'),
                'name' => 'Southpark Tshirt',
                'title' => 'Southpark Tshirt',
                'description' => '',
                'content' => '<p>Just imagine this awesome product.</p>',
                'type' => 'regular',
                'sku' => 'SOUTHPARK-TSHIRT',
                'sku_vendor' => '',
                'alias' => 'south-park-tshirt',
                'uri' => self::$Store->get('uri').'south-park-tshirt',
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
                'currency_id' => 109,
            ));
            $Product->save();
        }

        if (!$Product = self::$modx->getObject('Product', array('alias'=> 'family-guy-tshirt', 'store_id'=> self::$Store->get('id')))) {        
            $Product = self::$modx->newObject('Product');
            $Product->fromArray(array(
                'store_id' => self::$Store->get('id'),
                'name' => 'Family Guy Tshirt',
                'title' => 'Family Guy Tshirt',
                'description' => '',
                'content' => '<p>Just imagine this awesome product.</p>',
                'type' => 'regular',
                'sku' => 'FAMILYGUY-TSHIRT',
                'sku_vendor' => '',
                'alias' => 'family-guy-tshirt',
                'uri' => self::$Store->get('uri').'family-guy-tshirt',
                'track_inventory' => 0,
                'qty_inventory' => 10,
                'qty_alert' => 3,
                'qty_min' => 1,
                'qty_max' => 0,
                'qty_backorder_max' => 5,
                'price' => '20.00',
                'price_strike_thru' => '25.00',
                'price_sale' => '',
                'sale_start' => '',
                'sale_end' => '',
                'category' => 'Default',
                'is_active' => 1,
                'in_menu' => 1,
                'currency_id' => 109,
            ));
            $Product->save();
        }
        
        if (!$Product = self::$modx->getObject('Product', array('alias'=> 'simpsons-tshirt', 'store_id'=> self::$Store->get('id')))) {        
            $Product = self::$modx->newObject('Product');
            $Product->fromArray(array(
                'store_id' => self::$Store->get('id'),
                'name' => 'Simpsons Tshirt',
                'title' => 'Simpsons Tshirt',
                'description' => '',
                'content' => '<p>Just imagine this awesome product.</p>',
                'type' => 'regular',
                'sku' => 'SIMPSONS-TSHIRT',
                'sku_vendor' => '',
                'alias' => 'simpsons-tshirt',
                'uri' => self::$Store->get('uri').'simpsons-tshirt',
                'track_inventory' => 0,
                'qty_inventory' => 10,
                'qty_alert' => 3,
                'qty_min' => 1,
                'qty_max' => 0,
                'qty_backorder_max' => 5,
                'price' => '21.00',
                'price_strike_thru' => '25.00',
                'price_sale' => '',
                'sale_start' => '',
                'sale_end' => '',
                'category' => 'Default',
                'is_active' => 1,
                'in_menu' => 1,
                'currency_id' => 109,
            ));
            $Product->save();
        }

        // create test data for Product Field
        self::$product_id = $Product->get('product_id');
        if (!$ProductField = self::$modx->getObject('ProductField', array('product_id'=> self::$product_id, 'field_id'=> self::$Field->get('field_id')))) {        
            $ProductField = self::$modx->newObject('ProductField');
            $ProductField->fromArray(array(
                'product_id' => self::$product_id,
                'field_id' =>  self::$Field->get('field_id'),
                'value' => 'Test Value'
            ));
            $ProductField->save();
        }        

    }

    /**
     *
     */
    public static function tearDownAfterClass() {
        self::$Store->remove();
        self::$Field->remove();
    }


    /**
     *
     *
     */
    public function testFormat() {
        $Snippet = new Snippet(self::$modx);
        
        $records = array();
        $records[] = array('first_name' => 'Bob', 'last_name'=>'Smith');
        $records[] = array('first_name' => 'Jane', 'last_name'=>'Doe');
        $records[] = array('first_name' => 'Sedge', 'last_name'=>'Jones');
        
        $innerTpl = '<li>[[+first_name]] [[+last_name]]</li>';
        $outerTpl = '<ul>[[+content]]</ul>';
        
        $actual = $Snippet->format($records,$innerTpl,$outerTpl);
        $expected = '<ul><li>Bob Smith</li><li>Jane Doe</li><li>Sedge Jones</li></ul>';
        $this->assertEquals(normalize_string($expected), normalize_string($actual));
    }    

    /**
     *
     *
     */
    public function testFormat2() {
        $Snippet = new Snippet(self::$modx);
        
        $c = self::$modx->newQuery('Product');
        $c->where(array('is_active' => 1, 
                    'store_id'=> self::$Store->get('id')));
        $c->sortby('price','ASC');
        
        $Prods = self::$modx->getCollection('Product', $c);
        
        $this->assertFalse(empty($Prods));
                
        $innerTpl = '<li>[[+name]]: [[+price]]</li>';
        $outerTpl = '<ul>[[+content]]</ul>';
        
        $actual = $Snippet->format($Prods,$innerTpl,$outerTpl);
        $expected = '<ul><li>Southpark Tshirt: 19</li><li>Family Guy Tshirt: 20</li><li>Simpsons Tshirt: 21</li></ul>';
        $this->assertEquals(normalize_string($expected), normalize_string($actual));
    }

    public function testFormat3() {
        $Snippet = new Snippet(self::$modx);
        $P = new Product(self::$modx);
        
        $Prods = $P->all(array('is_active' => 1, 'store_id'=> self::$Store->get('id'),'sort'=>'price'));
        
        $this->assertFalse(empty($Prods));
                
        $innerTpl = '<li>[[+name]]: [[+price]]</li>';
        $outerTpl = '<ul>[[+content]]</ul>';
        
        $actual = $Snippet->format($Prods,$innerTpl,$outerTpl);
        $expected = '<ul><li>Southpark Tshirt: 19</li><li>Family Guy Tshirt: 20</li><li>Simpsons Tshirt: 21</li></ul>';
        $this->assertEquals(normalize_string($expected), normalize_string($actual));
    }

    /**
     * Seems that you have to force the Snippet to be cached before this will work.
     */
    public function testSecure() {
        global $modx;
        $modx = self::$modx;
        
        $props = array();
        $props['options'] = 'sample-api-key';
        $props['name'] = 'price';
        $props['input'] = '29.99';
        $actual = $modx->runSnippet('secure', $props);
        $expected = '||9490fad91f94bf559168219c2efdeee5ddda247edd8fb3bc483fb9a36542efb6';
        $this->assertEquals(normalize_string($expected), normalize_string($actual));
        
    }

    /**
     * Test the getProducts Snippet
     *
     */
    public function testGetProducts() {
        // You MUST set $modx as a global variable, or runSnippet will encounter errors!
        // You have to do this for EACH test function when you are testing a Snippet!
        global $modx;
        $modx = self::$modx;

        
        $props = array();
        $props['store_id'] = self::$Store->get('id');
        $props['log_level'] = 4;
        $props['innerTpl'] = '<li>[[+name]]: [[+price]]</li>';
        $props['outerTpl'] = '<ul>[[+content]]</ul>';        
        $props['sort'] = 'price';
        $actual = self::$modx->runSnippet('getProducts', $props);
        $expected = '<ul><li>Southpark Tshirt: 19</li><li>Family Guy Tshirt: 20</li><li>Simpsons Tshirt: 21</li></ul>';
        $this->assertEquals(normalize_string($expected), normalize_string($actual));
        
    }

    /**
     * Test the getProductFields Snippet
     *
     */
    public function testGetProductFields() {
        global $modx;
        $modx = self::$modx;
        $props = array();
        $props['product_id'] = self::$product_id; 
        $props['test_id'] = 'test';      
        self::$modx->runSnippet('getProductFields', $props);        
    }
    
}