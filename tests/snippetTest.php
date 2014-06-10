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
 GOTCHAS:
 
 1. You need to set the $modx variable before using runSnippet:
        global $modx;
        $modx = self::$modx;
 (using & won't work for some reason)

 2. You must run tests with the same permissions as the webserver, e.g. in MAMP
    you must run tests as the admin user.

 3. runSnippet will not preserve datatypes on return, so you cannot rely on assertTrue 
    or assertFalse to check the outputs.  E.g. returning false will return '', returning 
    true from a Snippet returns a 1.
    
 */
namespace Moxycart;
class snippetTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    public static $Store;
    public static $Tax;
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

       if (!self::$Tax['A'] = self::$modx->getObject('Taxonomy', array('alias'=>'test-taxonomy-a'))) {
            self::$Tax['A'] = self::$modx->newObject('Taxonomy');
            self::$Tax['A']->fromArray(array(
                'pagetitle' => 'Taxonomy A',
                'alias' => 'test-taxonomy-a',
                'uri' => 'test-taxonomy-a/',
                'class_key' => 'Taxonomy',
                'isfolder' => 1,
                'published' => 1,
                 'properties' => '',
            ));
            self::$Tax['A']->save();        
        }
        if (!self::$Tax['B'] = self::$modx->getObject('Taxonomy', array('alias'=>'test-taxonomy-b'))) {
            self::$Tax['B'] = self::$modx->newObject('Taxonomy');
            self::$Tax['B']->fromArray(array(
                'pagetitle' => 'Taxonomy B',
                'alias' => 'test-taxonomy-b',
                'uri' => 'test-taxonomy-b/',
                'class_key' => 'Taxonomy',
                'isfolder' => 1,
                'published' => 1,
                 'properties' => '',
            ));
            self::$Tax['B']->save();        
        }
        if (!self::$Tax['C'] = self::$modx->getObject('Taxonomy', array('alias'=>'test-taxonomy-c'))) {
            self::$Tax['C'] = self::$modx->newObject('Taxonomy');
            self::$Tax['C']->fromArray(array(
                'pagetitle' => 'Taxonomy C',
                'alias' => 'test-taxonomy-c',
                'uri' => 'test-taxonomy-c/',
                'class_key' => 'Taxonomy',
                'isfolder' => 1,
                'published' => 1,
                 'properties' => '',
            ));
            self::$Tax['C']->save();        
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
        self::$Tax['A']->remove();
        self::$Tax['B']->remove();
        self::$Tax['C']->remove(); 
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
        //$modx->setLogTarget('ECHO');
        $props = array();
        $modx->setOption('moxycart.api_key','sample-api-key'); // temporary override
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
        $props['innerTpl'] = '<li>[[+product_id]]: [[+value]]</li>';
        $props['outerTpl'] = '<ul>[[+content]]</ul>'; 
        $actual = self::$modx->runSnippet('getProductFields', $props);  
        $expected = '<ul><li>'.self::$product_id.': Test Value</li></ul>';
        //$this->assertEquals(normalize_string($expected), normalize_string($actual));     
    }

    /**
     * Test getTaxonomies
     * How to test this when we don't know which taxonomies are in the db?
     */
/*
    public function testGetTaxonomies() {
        global $modx;
        $modx = self::$modx;
        $props = array();
        $props['innerTpl'] = '<li>[[+pagetitle]]</li>';
        $props['outerTpl'] = '<ul>[[+content]]</ul>';
        $actual = self::$modx->runSnippet('getTaxonomies', $props);  
        $expected = '<ul><li>Taxonomy A</li><li>Taxonomy B</li><li>Taxonomy C</li></ul>';
        $this->assertEquals(normalize_string($expected), normalize_string($actual));    
    }
*/

    /**
     * Test SortTaxonomies
     */
/*
    public function testSortTaxonomies() {
        global $modx;
        $modx = self::$modx;
        $props = array();
        $props['sort'] = 'id';
        $props['dir'] = 'DESC';
        $props['innerTpl'] = '<li>[[+pagetitle]]</li>';
        $props['outerTpl'] = '<ul>[[+content]]</ul>';
        $actual = self::$modx->runSnippet('getTaxonomies', $props);  
        $expected = '<ul><li>Taxonomy C</li><li>Taxonomy B</li><li>Taxonomy A</li></ul>';
        $this->assertEquals(normalize_string($expected), normalize_string($actual));    
    }
*/
    
    
    public function testCreateUser() {

        global $modx;
        $modx = self::$modx;
        if ($User = $modx->getObject('modUser', array('username'=>'dude@dudeson.com'))) {
            $User->remove();
        }        
        //$modx->setLogLevel(3);
        
        $props = array();        
        $modx->setOption('moxycart.user_group','DOES NOT EXIST');
        $modx->setOption('moxycart.user_role', 1);
        $modx->setOption('moxycart.user_activate',1);
        $modx->setOption('moxycart.user_update',1);        
        
        $actual = $modx->runSnippet('userCreate', $props);  
        $this->assertFalse((bool)$actual);

        $user_group = $modx->getObject('modUserGroup', array('name'=>'Customer'));
        $modx->setOption('moxycart.user_group',$user_group->get('id'));

        $props['customer_email'] = 'Invalid Email';
        $actual = $modx->runSnippet('userCreate', $props);  
        $this->assertFalse((bool)$actual);
        
        $props['customer_first_name'] = 'Dude first_name';
        $props['customer_last_name'] = 'Dude last_name';
        $props['customer_company'] = 'Dude Company';
        $props['customer_address1'] = 'Dude address1';
        $props['customer_address2'] = 'Dude address2';
        $props['customer_city'] = 'Dude City';
        $props['customer_state'] = 'Dude State';
        $props['customer_postal_code'] = '12345';
        $props['customer_country'] = 'Dude Country';
        $props['customer_phone'] = '111-222-3333';
        $props['customer_email'] = 'dude@dudeson.com';
        $props['customer_ip'] = '111.0.0.123';
        
        $props['customer_password'] = 'xxxxx';
        $props['customer_password_salt'] = 'yyyyy';
        $props['customer_password_hash_type'] = 'pbkdf2';
        $props['customer_password_hash_config'] = '1000, 32, sha256';
        
        $props['shipping_first_name'] = 'Ship first_name';
        $props['shipping_last_name'] = 'Ship last_name';
        $props['shipping_company'] = 'Ship company';
        $props['shipping_address1'] = 'Ship address1';
        $props['shipping_address2'] = 'Ship address2';
        $props['shipping_city'] = 'Ship city';
        $props['shipping_state'] = 'Ship state';
        $props['shipping_postal_code'] = '23456';
        $props['shipping_country'] = 'Ship Country';
        $props['shipping_phone'] = '444-555-6666';
        
        
        $actual = $modx->runSnippet('userCreate', $props);  
        
        $this->assertTrue((bool) $actual);
        
        $User = $modx->getObject('modUser', array('username'=>'dude@dudeson.com'));
        $this->assertTrue(!empty($User));
        $this->assertTrue(!empty($User->Profile));
        $this->assertTrue((bool) $User->get('active'));
        $this->assertEquals($props['customer_email'], $User->Profile->get('email'));
        $this->assertEquals('Dude first_name Dude last_name', $User->Profile->get('fullname'));
        $this->assertEquals($props['customer_phone'], $User->Profile->get('phone'));
        $this->assertEquals("Dude address1\nDude address2", $User->Profile->get('address'));
        $this->assertEquals($props['customer_country'], $User->Profile->get('country'));
        $this->assertEquals($props['customer_city'], $User->Profile->get('city'));
        $this->assertEquals($props['customer_state'], $User->Profile->get('state'));
        $this->assertEquals($props['customer_postal_code'], $User->Profile->get('zip'));

        $this->assertEquals($props['customer_password'], $User->get('password'));
        $this->assertEquals($props['customer_password_salt'], $User->get('salt'));
                
        // try changing stuff when it's disallowed
        $props['customer_country'] = 'St. Other Country';
        $props['customer_phone'] = '444-555-7777';
        $modx->setOption('moxycart.user_update',false);
        $actual = $modx->runSnippet('userCreate', $props);  
        $User = $modx->getObject('modUser', array('username'=>'dude@dudeson.com'));
        $this->assertTrue((bool) $actual);        
        $this->assertNotEquals($props['customer_country'], $User->Profile->get('country'));        
        $this->assertNotEquals($props['customer_phone'], $User->Profile->get('phone'));
        // Now allow it 
        $modx->setOption('moxycart.user_update',true);
        $actual = $modx->runSnippet('userCreate', $props);  
        $User = $modx->getObject('modUser', array('username'=>'dude@dudeson.com'));
        $this->assertTrue((bool) $actual);
        $this->assertEquals($props['customer_country'], $User->Profile->get('country'));        
        $this->assertEquals($props['customer_phone'], $User->Profile->get('phone'));
        
        
        
        $User->remove();
    
    }
    
    
    public function parseFoxycartDatafeed() {
    
    }
}