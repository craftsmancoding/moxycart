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
 * See http://forums.modx.com/thread/91009/xpdo-validation-rules-executing-prematurely#dis-post-498398 
 */
namespace Moxycart;
class productTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    public static $Store;
    
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
    
    /**
     *
     */
    public static function tearDownAfterClass() {
        self::$Store->remove();
    }
    
    
    /**
     * The calculated URI of a new Product should read the parent's URI.
     */
    public function testAutoUriGeneration() {
    
    }
    
    /**
     * If we change the parent's Alias/URI, does this update the 
     * URI's of all the children products?
     */
    public function testParentUriChange() {
    
    }
    
    /**
     * Default values should be inherited from the parent Store
     *
     */
    public function testDefaultValues() {
    
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Product ID not defined
     */
    public function testRelationExceptions() {
        $P = new Product(self::$modx);
        $P->addRelations(array(1,2,3));
    }

    /**
     * When THIS product does not exist
     * @expectedException        \Exception
     * @expectedExceptionMessage Product does not exist
     */
    public function testRelationExceptions2() {
        $P = new Product(self::$modx);
        $P->set('product_id', -123); // invalid product_id
        $P->addRelations(array(1,2,3));
    }

    /**
     * When the RELATED products don't exist.
     * @expectedException        \Exception
     * @expectedExceptionMessage Invalid product ID
     */
    public function testRelationExceptions3() {
        $P = new Product(self::$modx);
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));        
        $P->addRelations(array(-1,-2,-3));
    }
    
    /**
     * 
     *
     */
    public function testRelations() {
        $P = new Product(self::$modx);
        
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));
            
        $Others = $P->all(array(
            'store_id' => self::$Store->get('id'),
            'sku:!=' => 'SOUTHPARK-TSHIRT')
        );

        // Prep: Remove all relations
        if($Collection = self::$modx->getCollection('ProductRelation', array('product_id'=>$One->get('product_id')))) {
            foreach ($Collection as $C) {
                $C->remove();
            }
        }
        $product_id = $One->get('product_id');
        $this->assertFalse(empty($product_id));
        
        $related = array();
        foreach ($Others as $o) {
            $related[] = $o->get('product_id');
        }
        
        $One->addRelations($related);
        
        // Verify they all exist:
        $Collection = self::$modx->getCollection('ProductRelation', array('product_id'=>$product_id));
        $this->assertFalse(empty($Collection),'Product Relations were not added!');
        $cnt = self::$modx->getCount('ProductRelation', array('product_id'=>$product_id));
        $this->assertEquals(count($related), $cnt);
        foreach ($related as $related_id) {
            $PR = self::$modx->getObject('ProductRelation', array('product_id'=>$product_id,'related_id'=>$related_id));
            $this->assertFalse(empty($PR));
        }
        
        // Add duplicates, verify that nothing new was created.
        $One->addRelations($related);
        $cnt2 = self::$modx->getCount('ProductRelation', array('product_id'=>$product_id));
        $this->assertEquals($cnt, $cnt2);
        
        // Remove all but one
        $odd_man_out = array_pop($related);
        $One->removeRelations($related);
        $cnt3 = self::$modx->getCount('ProductRelation', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt3, 1); // should be only one left
        
        // Now, dictate the relations: this should add and remove
        $One->dictateRelations($related);
        $cnt4 = self::$modx->getCount('ProductRelation', array('product_id'=>$One->get('product_id'),'type'=>'related'));
        $this->assertEquals($cnt4, count($related)); 
        
        // Verify the order
        $c = self::$modx->newQuery('ProductRelation');
        $c->where(array('product_id'=>$product_id));
        $c->sortby('seq','ASC');
        $PR = self::$modx->getCollection('ProductRelation',$c);
        $i = 0;
        foreach ($PR as $p) {
            $this->assertEquals($i, $p->get('seq'));
            $this->assertEquals($related[$i], $p->get('related_id'));
            $i++;
        }
    }    
}