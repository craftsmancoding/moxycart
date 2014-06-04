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
    public static $Tax; // Taxonomies
    public static $Term;
    public static $Field;
    public static $OType;
    public static $OTerm;
    public static $Asset;
        
    /**
     * Load up MODX for our tests.
     *
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
                
            ));
            $Product->save();
        }


        //! Taxonomies
        // Create a few Taxonomies, we stick 'em into slots A, B, and C for simplicity
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
        //! Terms
        // Create a few Terms, we stick 'em into slots 1, 2, and 3 for simplicity
        if (!self::$Term['1'] = self::$modx->getObject('Term', array('alias'=>'test-term-1'))) {
            self::$Term['1'] = self::$modx->newObject('Term');
            self::$Term['1']->fromArray(array(
                'parent' => self::$Tax['A']->get('id'),
                'pagetitle' => 'Term 1',
                'alias' => 'test-term-1',
                'uri' => 'test-term-1/',
                'class_key' => 'Term',
                'isfolder' => 1,
                'published' => 1,
                 'properties' => '',
            ));
            self::$Term['1']->save();        
        }
        if (!self::$Term['2'] = self::$modx->getObject('Term', array('alias'=>'test-term-2'))) {
            self::$Term['2'] = self::$modx->newObject('Term');
            self::$Term['2']->fromArray(array(
                'parent' => self::$Tax['A']->get('id'),
                'pagetitle' => 'Term 2',
                'alias' => 'test-term-2',
                'uri' => 'test-term-2/',
                'class_key' => 'Term',
                'isfolder' => 1,
                'published' => 1,
                 'properties' => '',
            ));
            self::$Term['2']->save();        
        }
        if (!self::$Term['3'] = self::$modx->getObject('Term', array('alias'=>'test-term-3'))) {
            self::$Term['3'] = self::$modx->newObject('Term');
            self::$Term['3']->fromArray(array(
                'parent' => self::$Tax['A']->get('id'),            
                'pagetitle' => 'Term 3',
                'alias' => 'test-term-3',
                'uri' => 'test-term-3/',
                'class_key' => 'Term',
                'isfolder' => 1,
                'published' => 1,
                 'properties' => '',
            ));
            self::$Term['3']->save();        
        }

        // !Fields
        if (!self::$Field['one'] = self::$modx->getObject('Field', array('slug'=>'one'))) {
            self::$Field['one'] = self::$modx->newObject('Field');
            self::$Field['one']->fromArray(array(
                'slug' => 'one',
                'label' => 'Test One',
                'description' => 'Testing Field',
                'seq' => 0,
                'group' => 'GroupA',
                'type' => 'text'
            ));
            if(!self::$Field['one']->save()) {
                print 'Could not save field!'; 
            }
        }
        if (!self::$Field['two'] = self::$modx->getObject('Field', array('slug'=>'two'))) {
            self::$Field['two'] = self::$modx->newObject('Field');
            self::$Field['two']->fromArray(array(
                'slug' => 'two',
                'label' => 'Test Two',
                'description' => 'Testing Field',
                'seq' => 0,
                'group' => 'GroupA',
                'type' => 'textarea'
            ));
            self::$Field['two']->save();
        }
        if (!self::$Field['three'] = self::$modx->getObject('Field', array('slug'=>'three'))) {
            self::$Field['three'] = self::$modx->newObject('Field');
            self::$Field['three']->fromArray(array(
                'slug' => 'three',
                'label' => 'Test Three',
                'description' => 'Testing Field',
                'seq' => 0,
                'group' => 'GroupA',
                'type' => 'dropdown'
            ));
            self::$Field['three']->save();
        }
        
        //! OptionTypes
        if (!self::$OType['color'] = self::$modx->getObject('OptionType', array('slug'=>'color'))) {
            self::$OType['color'] = self::$modx->newObject('OptionType');
            self::$OType['color']->fromArray(array(
                'slug' => 'color',
                'name' => 'Color',
                'description' => 'Testing Variation Type',
                'seq' => 0,
            ));
            self::$OType['color']->save();
        }
        if (!self::$OType['size'] = self::$modx->getObject('OptionType', array('slug'=>'size'))) {
            self::$OType['size'] = self::$modx->newObject('OptionType');
            self::$OType['size']->fromArray(array(
                'slug' => 'size',
                'name' => 'Size',
                'description' => 'Testing Variation Type',
                'seq' => 0,
            ));
            self::$OType['size']->save();
        }        
        if (!self::$OType['material'] = self::$modx->getObject('OptionType', array('slug'=>'material'))) {
            self::$OType['material'] = self::$modx->newObject('OptionType');
            self::$OType['material']->fromArray(array(
                'slug' => 'material',
                'name' => 'Material',
                'description' => 'Testing Variation Type',
                'seq' => 0,
            ));
            self::$OType['material']->save();
        }
        if (!self::$OType['printing'] = self::$modx->getObject('OptionType', array('slug'=>'printing'))) {
            self::$OType['printing'] = self::$modx->newObject('OptionType');
            self::$OType['printing']->fromArray(array(
                'slug' => 'printing',
                'name' => 'Printing',
                'description' => 'Testing Variation Type',
                'seq' => 0,
            ));
            self::$OType['printing']->save();
        }        
        //!OptionTerm : Colors
        if (!self::$OTerm['white'] = self::$modx->getObject('OptionTerm', array('slug'=>'white'))) {
            self::$OTerm['white'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['white']->fromArray(array(
                'otype_id' => self::$OType['color']->get('otype_id'),
                'slug' => 'white',
                'name' => 'White',
                'sku_prefix' => '',
                'sku_suffix' => '-WHI',
                'seq' => 0,
            ));
            self::$OTerm['white']->save();
        }
        if (!self::$OTerm['black'] = self::$modx->getObject('OptionTerm', array('slug'=>'black'))) {
            self::$OTerm['black'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['black']->fromArray(array(
                'otype_id' => self::$OType['color']->get('otype_id'),
                'slug' => 'black',
                'name' => 'Black',
                'sku_prefix' => '',
                'sku_suffix' => '-BLA',
                'seq' => 0,
            ));
            self::$OTerm['black']->save();
        }
        if (!self::$OTerm['red'] = self::$modx->getObject('OptionTerm', array('slug'=>'large'))) {
            self::$OTerm['red'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['red']->fromArray(array(
                'otype_id' => self::$OType['color']->get('otype_id'),
                'slug' => 'red',
                'name' => 'Red',
                'sku_prefix' => '',
                'sku_suffix' => '-RED',
                'seq' => 0,
            ));
            self::$OTerm['red']->save();
        }
        
        //!OptionTerm : Sizes
        if (!self::$OTerm['small'] = self::$modx->getObject('OptionTerm', array('slug'=>'small'))) {
            self::$OTerm['small'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['small']->fromArray(array(
                'otype_id' => self::$OType['size']->get('otype_id'),
                'slug' => 'small',
                'name' => 'Small',
                'sku_prefix' => '',
                'sku_suffix' => '-S',
                'seq' => 0,
            ));
            self::$OTerm['small']->save();
        }
        if (!self::$OTerm['med'] = self::$modx->getObject('OptionTerm', array('slug'=>'med'))) {
            self::$OTerm['med'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['med']->fromArray(array(
                'otype_id' => self::$OType['size']->get('otype_id'),
                'slug' => 'med',
                'name' => 'Medium',
                'sku_prefix' => '',
                'sku_suffix' => '-M',
                'seq' => 0,
            ));
            self::$OTerm['med']->save();
        }
        if (!self::$OTerm['large'] = self::$modx->getObject('OptionTerm', array('slug'=>'large'))) {
            self::$OTerm['large'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['large']->fromArray(array(
                'otype_id' => self::$OType['size']->get('otype_id'),
                'slug' => 'large',
                'name' => 'Large',
                'sku_prefix' => '',
                'sku_suffix' => '-L',
                'seq' => 0,
            ));
            self::$OTerm['large']->save();
        }    

        //!OptionTerm : Materials
        if (!self::$OTerm['cotton'] = self::$modx->getObject('OptionTerm', array('slug'=>'cotton'))) {
            self::$OTerm['cotton'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['cotton']->fromArray(array(
                'otype_id' => self::$OType['material']->get('otype_id'),
                'slug' => 'cotton',
                'name' => 'Cotton',
                'sku_prefix' => '',
                'sku_suffix' => '-CTN',
                'seq' => 0,
            ));
            self::$OTerm['cotton']->save();
        }
        if (!self::$OTerm['silk'] = self::$modx->getObject('OptionTerm', array('slug'=>'silk'))) {
            self::$OTerm['silk'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['silk']->fromArray(array(
                'otype_id' => self::$OType['material']->get('otype_id'),
                'slug' => 'silk',
                'name' => 'Silk',
                'sku_prefix' => '',
                'sku_suffix' => '-SLK',
                'seq' => 0,
            ));
            self::$OTerm['silk']->save();
        }
        if (!self::$OTerm['wool'] = self::$modx->getObject('OptionTerm', array('slug'=>'wool'))) {
            self::$OTerm['wool'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['wool']->fromArray(array(
                'otype_id' => self::$OType['material']->get('otype_id'),
                'slug' => 'wool',
                'name' => 'Wool',
                'sku_prefix' => '',
                'sku_suffix' => '-WOOL',
                'seq' => 0,
            ));
            self::$OTerm['wool']->save();
        }
        
        //!OptionTerm : Printing
        if (!self::$OTerm['silkscreen'] = self::$modx->getObject('OptionTerm', array('slug'=>'silkscreen'))) {
            self::$OTerm['silkscreen'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['silkscreen']->fromArray(array(
                'otype_id' => self::$OType['printing']->get('otype_id'),
                'slug' => 'silkscreen',
                'name' => 'Silk Screen',
                'sku_prefix' => '',
                'sku_suffix' => '-SCR',
                'seq' => 0,
            ));
            self::$OTerm['silkscreen']->save();
        }
        if (!self::$OTerm['embossed'] = self::$modx->getObject('OptionTerm', array('slug'=>'embossed'))) {
            self::$OTerm['embossed'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['embossed']->fromArray(array(
                'otype_id' => self::$OType['printing']->get('otype_id'),
                'slug' => 'embossed',
                'name' => 'Embossed',
                'sku_prefix' => '',
                'sku_suffix' => '-EMB',
                'seq' => 0,
            ));
            self::$OTerm['embossed']->save();
        }
        if (!self::$OTerm['gold'] = self::$modx->getObject('OptionTerm', array('slug'=>'gold'))) {
            self::$OTerm['gold'] = self::$modx->newObject('OptionTerm');
            self::$OTerm['gold']->fromArray(array(
                'otype_id' => self::$OType['printing']->get('otype_id'),
                'slug' => 'gold',
                'name' => 'gold',
                'sku_prefix' => '',
                'sku_suffix' => '-GLD',
                'seq' => 0,
            ));
            self::$OTerm['gold']->save();
        }

        // !Assets (data only: no images)
        if (!self::$Asset['a'] = self::$modx->getObject('Asset', array('path'=>'testing/only/a.jpg'))) {
            self::$Asset['a'] = self::$modx->newObject('Asset');
            self::$Asset['a']->fromArray(array(
                'content_type_id' => 9,
                'path' => 'testing/only/a.jpg',
                'url' => 'testing/only/a.jpg',
                'width' => '200',
                'height' => '180',
                'title' => 'Test Image',
                'sig' => 'test',
            ));
            if(!self::$Asset['a']->save()) {
                print 'Could not save asset!'; 
            }
        }
        if (!self::$Asset['b'] = self::$modx->getObject('Asset', array('path'=>'testing/only/b.jpg'))) {
            self::$Asset['b'] = self::$modx->newObject('Asset');
            self::$Asset['b']->fromArray(array(
                'content_type_id' => 9,
                'path' => 'testing/only/b.jpg',
                'url' => 'testing/only/b.jpg',
                'width' => '200',
                'height' => '180',
                'title' => 'Test Image',
                'sig' => 'test',                
            ));
            if(!self::$Asset['b']->save()) {
                print 'Could not save asset!'; 
            }
        }
        if (!self::$Asset['c'] = self::$modx->getObject('Asset', array('path'=>'testing/only/c.jpg'))) {
            self::$Asset['c'] = self::$modx->newObject('Asset');
            self::$Asset['c']->fromArray(array(
                'content_type_id' => 9,
                'path' => 'testing/only/c.jpg',
                'url' => 'testing/only/c.jpg',
                'width' => '200',
                'height' => '180',
                'title' => 'Test Image',
                'sig' => 'test',                
            ));
            if(!self::$Asset['c']->save()) {
                print 'Could not save asset!'; 
            }
        }

        
    }
    
    /**
     *
     */
    public static function tearDownAfterClass() {

/*
        self::$Store->remove();
        self::$Tax['A']->remove();
        self::$Tax['B']->remove();
        self::$Tax['C']->remove();        
        self::$Term['1']->remove();
        self::$Term['2']->remove();
        self::$Term['3']->remove();
        self::$Field['one']->remove();
        self::$Field['two']->remove();
        self::$Field['three']->remove();
        
        self::$OType['color']->remove();
        self::$OType['size']->remove();
        self::$OType['material']->remove();
        self::$OType['printing']->remove();

        self::$OTerm['white']->remove();
        self::$OTerm['black']->remove();
        self::$OTerm['red']->remove();
        
        self::$OTerm['small']->remove();
        self::$OTerm['med']->remove();
        self::$OTerm['large']->remove();
        self::$OTerm['cotton']->remove();
        self::$OTerm['silk']->remove();
        self::$OTerm['wool']->remove();
        self::$OTerm['embossed']->remove();
        self::$OTerm['silkscreen']->remove();
        self::$OTerm['gold']->remove();
        
*/

    }
    
    
    /**
     * The calculated URI of a new Product should read the parent's URI.
     */
    public function testAutoUriGeneration() {
            $Product = new Product(self::$modx);
            $Product->fromArray(array(
                'store_id' => self::$Store->get('id'),
                'name' => 'Horkheimer',
                'content' => '<p>Hello fellow stargazers!</p>',
                'sku' => 'HORKHEIMER-TSHRT',
                'alias' => 'horkheimer-tshirt',
//                'uri' => // Omitted Intentionally for the test
            ));

            $Product->save();
            
            $expected = self::$Store->get('uri').$Product->get('alias');
            $actual = $Product->get('uri');
            $this->assertEquals($expected,$actual);
            
            // Cleanup
            $Product->remove();
    
    }
    
    /**
     * If we change the parent's Alias/URI, does this update the 
     * URI's of all the children products?
     */
    public function testParentUriChange() {
        $slug = substr(md5(uniqid()), 0, 8);
        $old = self::$Store->get('alias');
        self::$Store->set('alias', $slug);
        self::$Store->save();

        // Verify that the products updated.
        $Products = self::$modx->getCollection('Product', array('store_id'=>self::$Store->get('id')));
        $this->assertFalse(empty($Products));
        foreach ($Products as $P) {
            $this->assertTrue(startsWith($P->get('uri'),$slug));
        }

        // Return to normal
        self::$Store->set('alias', $old);
        self::$Store->save();
    }
    
    /**
     * Default values should be inherited from the parent Store
     *
     */
    public function testDefaultValues() {
        $Product = self::$modx->newObject('Product');
    }


    /**
     * When the RELATED products don't exist.
     * @expectedException        \Exception
     * @expectedExceptionMessage Product ID not defined
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
        //self::$modx->setLogTarget('ECHO');
        //self::$modx->setLogLevel(4);    
        $P = new Product(self::$modx);
        
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));
            
        $Others = $P->all(array(
            'store_id' => self::$Store->get('id'),
            'sku:!=' => 'SOUTHPARK-TSHIRT')
        );
        
        $product_id = $One->get('product_id');
        $this->assertFalse(empty($product_id));
        
        // Prep: Remove all relations
        if($Collection = self::$modx->getCollection('ProductRelation', array('product_id'=>$One->get('product_id')))) {
            foreach ($Collection as $C) {
                $C->remove();
            }
        }

        
/*
        $related = array();
        $related_ids = array();
        foreach ($Others as $o) {
            $related[] = array('related_id'=>$o->get('product_id'),'type'=>'related');
            $related_ids[] = $o->get('product_id');
        }

        $One->addRelations($related);
        $result = $One->save();
        $this->assertTrue($result);

        // Verify they all exist:
        $Collection = self::$modx->getCollection('ProductRelation', array('product_id'=>$product_id));
        $this->assertFalse(empty($Collection),'Product Relations '.implode(',',$related_ids).' were not added to product '.$product_id.'!');
        $cnt = self::$modx->getCount('ProductRelation', array('product_id'=>$product_id));
        $this->assertEquals(count($related), $cnt);
        foreach ($related_ids as $related_id) {
            $PR = self::$modx->getObject('ProductRelation', array('product_id'=>$product_id,'related_id'=>$related_id));
            $this->assertFalse(empty($PR));
        }
        
        // Add duplicates, verify that nothing new was created.
        $One->addRelations($related);
        $cnt2 = self::$modx->getCount('ProductRelation', array('product_id'=>$product_id));
        $this->assertEquals(count($related), $cnt2);
        
        // Remove all but one
        $odd_man_out = array_pop($related_ids);
        $One->removeRelations($related_ids);
        $cnt3 = self::$modx->getCount('ProductRelation', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt3, 1); // should be only one left

        // Remove all
        array_push($related_ids, $odd_man_out);
        $One->removeRelations($related_ids);
        $cnt3 = self::$modx->getCount('ProductRelation', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt3, 0); // Remove all

        // Now, dictate the relations: this should add and remove
        $One->dictateRelations($related);
        $cnt4 = self::$modx->getCount('ProductRelation', array('product_id'=>$One->get('product_id'),'type'=>'related'));
        $this->assertEquals($cnt4, count($related)); 
        
        // Verify the order
        $i = 0;
        foreach ($related as $r) {
            $r['product_id'] = $One->get('product_id');
            $PR = self::$modx->getObject('ProductRelation',$r);
            $this->assertFalse(empty($PR));
            $this->assertEquals($i, $PR->get('seq'));
            $i++;
        }
*/
    }    

    /**
     * 
     *
     */    public function testTaxonomies() {
        $P = new Product(self::$modx);
        
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));
            
        // Prep: Remove all Taxonomy Associations for this product
        if($Collection = self::$modx->getCollection('ProductTaxonomy', array('product_id'=>$One->get('product_id')))) {
            foreach ($Collection as $C) {
                $C->remove();
            }
        }
        $product_id = $One->get('product_id');
        $this->assertFalse(empty($product_id));
        
        $taxonomies = array();
        $taxonomies[] = self::$Tax['A']->get('id');
        $taxonomies[] = self::$Tax['B']->get('id');
        $taxonomies[] = self::$Tax['C']->get('id');
        
        $One->addTaxonomies($taxonomies);
        
        // Verify they all exist:
        $Collection = self::$modx->getCollection('ProductTaxonomy', array('product_id'=>$product_id));
        $this->assertFalse(empty($Collection),'Product Taxonomies were not added!');
        $cnt = self::$modx->getCount('ProductTaxonomy', array('product_id'=>$product_id));
        $this->assertEquals(count($taxonomies), $cnt);
        foreach ($taxonomies as $id) {
            $PT = self::$modx->getObject('ProductTaxonomy', array('product_id'=>$product_id,'taxonomy_id'=>$id));
            $this->assertFalse(empty($PT));
        }
        
        // Add duplicates, verify that nothing new was created.
        $One->addTaxonomies($taxonomies);
        $cnt2 = self::$modx->getCount('ProductTaxonomy', array('product_id'=>$product_id));
        $this->assertEquals($cnt, $cnt2);
        
        // Remove all but one
        $odd_man_out = array_pop($taxonomies);
        $One->removeTaxonomies($taxonomies);
        $cnt3 = self::$modx->getCount('ProductTaxonomy', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt3, 1); // should be only one left
        
        // Now, dictate the taxonomies: this should add and remove
        $One->dictateTaxonomies($taxonomies);
        $cnt4 = self::$modx->getCount('ProductTaxonomy', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt4, count($taxonomies)); 
        
        // Verify the order
        $c = self::$modx->newQuery('ProductTaxonomy');
        $c->where(array('product_id'=>$product_id));
        $c->sortby('seq','ASC');
        $PT = self::$modx->getCollection('ProductTaxonomy',$c);
        $i = 0;
        foreach ($PT as $p) {
            $this->assertEquals($i, $p->get('seq'));
            $this->assertEquals($taxonomies[$i], $p->get('taxonomy_id'));
            $i++;
        }
    }    
        
    /**
     * 
     *
     */
    public function testTerms() {
        $P = new Product(self::$modx);
        
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));
            
        // Prep: Remove all Term Associations for this product
        if($Collection = self::$modx->getCollection('ProductTerm', array('product_id'=>$One->get('product_id')))) {
            foreach ($Collection as $C) {
                $C->remove();
            }
        }
        $product_id = $One->get('product_id');
        $this->assertFalse(empty($product_id));
        
        $terms = array();
        $terms[] = self::$Term['1']->get('id');
        $terms[] = self::$Term['2']->get('id');
        $terms[] = self::$Term['3']->get('id');
        
        $One->addTerms($terms);
        
        // Verify they all exist:
        $Collection = self::$modx->getCollection('ProductTerm', array('product_id'=>$product_id));
        $this->assertFalse(empty($Collection),'Product Terms were not added!');
        $cnt = self::$modx->getCount('ProductTerm', array('product_id'=>$product_id));
        $this->assertEquals(count($terms), $cnt);
        foreach ($terms as $id) {
            $PT = self::$modx->getObject('ProductTerm', array('product_id'=>$product_id,'term_id'=>$id));
            $this->assertFalse(empty($PT));
        }
        
        // Add duplicates, verify that nothing new was created.
        $One->addTerms($terms);
        $cnt2 = self::$modx->getCount('ProductTerm', array('product_id'=>$product_id));
        $this->assertEquals($cnt, $cnt2);
        
        // Remove all but one
        $odd_man_out = array_pop($terms);
        $One->removeTerms($terms);
        $cnt3 = self::$modx->getCount('ProductTerm', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt3, 1); // should be only one left
        
        // Now, dictate the terms: this should add and remove
        $One->dictateTerms($terms);
        $cnt4 = self::$modx->getCount('ProductTerm', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt4, count($terms)); 
    }    

    /**
     * 
     *
     */
    public function testFields() {
        $P = new Product(self::$modx);
        
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));
            
        // Prep: Remove all Field Associations for this product
        if($Collection = self::$modx->getCollection('ProductField', array('product_id'=>$One->get('product_id')))) {
            foreach ($Collection as $C) {
                $C->remove();
            }
        }
        $product_id = $One->get('product_id');
        $this->assertFalse(empty($product_id));
        
        $fields = array();
        $fields[] = self::$Field['one']->get('field_id');
        $fields[] = self::$Field['two']->get('field_id');
        $fields[] = self::$Field['three']->get('field_id');
        
        $data = array();
        $data[] = array('field_id' => self::$Field['one']->get('field_id'), 'value'=>'Uno');
        $data[] = array('field_id' => self::$Field['two']->get('field_id'), 'value'=>'Dos');
        $data[] = array('field_id' => self::$Field['three']->get('field_id'), 'value'=>'Tres');
        
        $One->addFields($data);

        // Verify they all exist:
        $Collection = self::$modx->getCollection('ProductField', array('product_id'=>$product_id));
        $this->assertFalse(empty($Collection),'Product Fields were not added!');
        $cnt = self::$modx->getCount('ProductField', array('product_id'=>$product_id));
        $this->assertEquals(count($fields), $cnt);

        // Check Values
        $PT = self::$modx->getObject('ProductField', array('product_id'=>$product_id,'field_id'=>self::$Field['one']->get('field_id')));
        $this->assertFalse(empty($PT));
        $this->assertEquals('Uno', $PT->get('value'));
        $PT = self::$modx->getObject('ProductField', array('product_id'=>$product_id,'field_id'=>self::$Field['two']->get('field_id')));
        $this->assertFalse(empty($PT));
        $this->assertEquals('Dos', $PT->get('value'));
        $PT = self::$modx->getObject('ProductField', array('product_id'=>$product_id,'field_id'=>self::$Field['three']->get('field_id')));
        $this->assertFalse(empty($PT));
        $this->assertEquals('Tres', $PT->get('value'));

        
        // Add duplicates, verify that nothing new was created.
        $cnt1 = self::$modx->getCount('ProductField', array('product_id'=>$product_id));
        $One->addFields($data);
        $cnt2 = self::$modx->getCount('ProductField', array('product_id'=>$product_id));
        $this->assertEquals($cnt1, $cnt2);
        
        // Remove 2
        $cnt_before = self::$modx->getCount('ProductField', array('product_id'=>$One->get('product_id')));
        $One->removeFields(array(self::$Field['two']->get('field_id'), self::$Field['three']->get('field_id')));
        $cnt_after = self::$modx->getCount('ProductField', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt_before-2, $cnt_after, 'Two fields should have been deleted'); // should be 2 fewer
        
        // Now, dictate the fields: this should remove one and add back two and three
        array_shift($data);

        $One->dictateFields($data);

        $x = self::$modx->getObject('ProductField', array('product_id'=>$One->get('product_id'),'field_id'=>self::$Field['one']->get('field_id')));
        $this->assertTrue(empty($x));
        
        $y = self::$modx->getObject('ProductField', array('product_id'=>$One->get('product_id'),'field_id'=>self::$Field['two']->get('field_id')));
        $this->assertFalse(empty($y));
        $this->assertEquals('Dos',$y->get('value'));
        
        $z = self::$modx->getObject('ProductField', array('product_id'=>$One->get('product_id'),'field_id'=>self::$Field['three']->get('field_id')));
        $this->assertFalse(empty($z));
        $this->assertEquals('Tres',$z->get('value')); 
    }    


    /**
     * 
     *
     */
    public function testOptionTypes() {
        $P = new Product(self::$modx);
        
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));
            
        // Prep: Remove all OptionType Associations for this product
        if($Collection = self::$modx->getCollection('ProductOptionType', array('product_id'=>$One->get('product_id')))) {
            foreach ($Collection as $C) {
                $C->remove();
            }
        }
        $product_id = $One->get('product_id');
        $this->assertFalse(empty($product_id));
        
        $otypes = array();
        $otypes[] = self::$OType['color']->get('otype_id');
        $otypes[] = self::$OType['size']->get('otype_id');
        $otypes[] = self::$OType['material']->get('otype_id');
        $otypes[] = self::$OType['printing']->get('otype_id');
        
        $One->addOptionTypes($otypes);
        
        // Verify they all exist:
        $Collection = self::$modx->getCollection('ProductOptionType', array('product_id'=>$product_id));
        $this->assertFalse(empty($Collection),'Product OptionTypes were not added!');
        $cnt = self::$modx->getCount('ProductOptionType', array('product_id'=>$product_id));
        $this->assertEquals(count($otypes), $cnt);
        foreach ($otypes as $id) {
            $PT = self::$modx->getObject('ProductOptionType', array('product_id'=>$product_id,'otype_id'=>$id));
            $this->assertFalse(empty($PT));
        }
        
        // Add duplicates, verify that nothing new was created.
        $One->addOptionTypes($otypes);
        $cnt2 = self::$modx->getCount('ProductOptionType', array('product_id'=>$product_id));
        $this->assertEquals($cnt, $cnt2);
        
        // Remove all but one
        $odd_man_out = array_pop($otypes);
        $One->removeOptionTypes($otypes);
        $cnt3 = self::$modx->getCount('ProductOptionType', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt3, 1); // should be only one left
        
        // Now, dictate the fields: this should add and remove
        $One->dictateOptionTypes($otypes);
        $cnt4 = self::$modx->getCount('ProductOptionType', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt4, count($otypes)); 
    } 


    /** 
     * Compare 2 hashes
     */
    public function testHashDeltas() {
        $P = new Product(self::$modx);
        $h1 = array('x' => 'xray', 'y'=>'yellow');
        $h2 = array('y'=>'yellow','x' =>'xray','z'=>'zebra');
        $x = $P->hashDelta($h1,$h2);
        $this->assertEquals(array('z'=>'zebra'), $x); 
    }

    /**
     *
     *
     */
    public function testProductWithoutStore() {
        $P = new Product(self::$modx);
        $P->fromArray(array(
            'store_id' => 'invalid',
            'name' => 'Orphan Product',
            'title' => 'I got no store',
            'alias' => 'orphan-product',
        ));
   
        $result = $P->save();
        
        $this->assertFalse($result);
        // Todo: set a real xpdo error
        // $this->assertEquals('Invalid Store ID', $P->errors['store_id']);
        
    }
    
    /**
     *
     *
     */
    public function testRequest() {
        // Load up a product with some fields.
        $P = new Product(self::$modx);
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));
        $this->assertFalse(empty($One));          
        $fields = array(
            array('field_id' => self::$Field['one']->get('field_id'), 'value' => 'Uno'),
            array('field_id' => self::$Field['two']->get('field_id'), 'value' => 'Dos'),
            array('field_id' => self::$Field['three']->get('field_id'), 'value' => 'Tres'),            
        );
        
        $One->dictateFields($fields);

        $uri = $One->get('uri');

        $this->assertEquals('test-store/south-park-tshirt',$uri);

        $data = $P->request($uri);

        $raw = $One->toArray();
        $raw['one'] = 'Uno';
        $raw['two'] = 'Dos';
        $raw['three'] = 'Tres';

        $this->assertEquals(ksort($raw),ksort($data));
    }
    
/*
    public function testLoadFromCache() {
        $P = new Product(self::$modx);
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));
        $this->assertFalse(empty($One));
        $uri = $One->get('uri');
        
        $cache_dir = 'moxycart';
        $core_path = self::$modx->getOption('core_path');
        $file = $core_path.'cache/'.$cache_dir.'/'.$uri.'.cache.php';
        if (file_exists($file)) {
            unlink($file);
        }
        $data = $P->request($uri);
    }
*/

    /**
     * Makin' sure our calculated fields are legit
     *
     */
    public function testCalculatedPrice() {
        // Sale Price
        $P = new Product(self::$modx);
        $P->sale_start = date('Y-m-d H:i:s',strtotime('-1 days')); // yesterday
        $P->sale_end = date('Y-m-d H:i:s',strtotime('+1 days')); // tomorrow
        $P->price = 149.00;
        $P->price_sale = 99.00;
        $this->assertEquals(99.00, $P->calculated_price);

        // Regular Price
        $P = new Product(self::$modx);
        $P->sale_start = date('Y-m-d H:i:s',strtotime('-7 days')); // last week
        $P->sale_end = date('Y-m-d H:i:s',strtotime('-1 days')); // yesterday
        $P->price = 149.00;
        $P->price_sale = 99.00;
        $this->assertEquals(149.00, $P->calculated_price);

        // Regular Price (no sale)
        $P = new Product(self::$modx);
        $P->price = 149.00;
        $P->price_sale = 99.00;
        $this->assertEquals(149.00, $P->calculated_price);
        
    }
    
    /**
     * We can only cache until the end of the sale
     */
    public function testCacheLifetime() {
        // With a sale date
        $P = new Product(self::$modx);
        $tmw = strtotime('+1 days');
        $P->sale_end = date('Y-m-d H:i:s',$tmw); // tomorrow
        $lifetime = $tmw - time();
        $this->assertEquals($lifetime, $P->cache_lifetime);
        // Without
        $P = new Product(self::$modx);
        $this->assertEquals(0, $P->cache_lifetime);
    }


    /**
     * 
     *
     */
    public function testAssets() {
        //self::$modx->setLogTarget('ECHO');
        //self::$modx->setLogLevel(4);    
        $P = new Product(self::$modx);
        
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));

        $product_id = $One->get('product_id');
        $this->assertFalse(empty($product_id));
            
        // Prep: Remove all assets
        if($Collection = self::$modx->getCollection('ProductAsset', array('product_id'=>$One->get('product_id')))) {
            foreach ($Collection as $C) {
                $C->remove();
            }
        }
        $Assets = self::$modx->getCollection('Asset', array('sig'=>'test'));
        $this->assertTrue(!empty($Assets));
        
        $assets = array();
        $asset_ids = array();
        foreach ($Assets as $a) {
            $assets[] = array('asset_id'=>$a->get('asset_id'));
            $asset_ids[] = $a->get('asset_id');
        }

        $One->addAssets($assets);
        $result = $One->save();
        $this->assertTrue($result);

        // Verify they all exist:
        foreach ($asset_ids as $id) {
            $PA = self::$modx->getObject('ProductAsset', array('product_id'=>$product_id,'asset_id'=>$id));
            $this->assertTrue(!empty($PA));
        }
        
        // Add duplicates, verify that nothing new was created.
        $before_cnt = self::$modx->getCount('ProductAsset', array('product_id'=>$product_id));
        $One->addAssets($assets);
        $after_cnt = self::$modx->getCount('ProductAsset', array('product_id'=>$product_id));
        $this->assertEquals($before_cnt, $after_cnt);
        
        // Remove all but one
        $odd_man_out = array_pop($asset_ids);
        $One->removeAssets($asset_ids);
        $cnt3 = self::$modx->getCount('ProductAsset', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt3, 1); // should be only one left

        // Remove all
        array_push($asset_ids, $odd_man_out);
        $One->removeAssets($asset_ids);
        $cnt3 = self::$modx->getCount('ProductAsset', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt3, 0); // Remove all

        // Now, dictate the relations: this should add and remove
        rsort($assets);
        $One->dictateAssets($assets);
        $cnt4 = self::$modx->getCount('ProductAsset', array('product_id'=>$One->get('product_id')));
        $this->assertEquals(3, $cnt4); 
        
        // Verify the order
        $i = 0;
        foreach ($assets as $r) {
            $r['product_id'] = $One->get('product_id');
            $PA = self::$modx->getObject('ProductAsset',$r);
            $this->assertFalse(empty($PA));
            $this->assertEquals($i, $PA->get('seq'));
            $i++;
        }
    }    

    
    /**
     *
     *
     */
/*
    public function testVariantWithoutProduct() {
        $P = new Product(self::$modx);
        $One = $P->one(array(
            'store_id' => self::$Store->get('id'),
            'sku' => 'SOUTHPARK-TSHIRT'));        
        $P->addRelations(array(-1,-2,-3));
   
    }
*/

    /**
     * 
     */
/*
    public function testVariantAdd() {
        $P = new Product(self::$modx);
        
    }

    public function testVariantMatrixJSON() {
    
    }
*/
}