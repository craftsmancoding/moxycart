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
    public static $VType;
    public static $VTerm;
        
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
        
        //! VariationTypes
        if (!self::$VType['color'] = self::$modx->getObject('VariationType', array('slug'=>'color'))) {
            self::$VType['color'] = self::$modx->newObject('VariationType');
            self::$VType['color']->fromArray(array(
                'slug' => 'color',
                'name' => 'Color',
                'description' => 'Testing Variation Type',
                'seq' => 0,
            ));
            self::$VType['size']->save();
        }
        if (!self::$VType['size'] = self::$modx->getObject('VariationType', array('slug'=>'size'))) {
            self::$VType['size'] = self::$modx->newObject('VariationType');
            self::$VType['size']->fromArray(array(
                'slug' => 'size',
                'name' => 'Size',
                'description' => 'Testing Variation Type',
                'seq' => 0,
            ));
            self::$VType['size']->save();
        }        
        if (!self::$VType['material'] = self::$modx->getObject('VariationType', array('slug'=>'material'))) {
            self::$VType['material'] = self::$modx->newObject('VariationType');
            self::$VType['material']->fromArray(array(
                'slug' => 'material',
                'name' => 'Material',
                'description' => 'Testing Variation Type',
                'seq' => 0,
            ));
            self::$VType['material']->save();
        }
        if (!self::$VType['printing'] = self::$modx->getObject('VariationType', array('slug'=>'printing'))) {
            self::$VType['printing'] = self::$modx->newObject('VariationType');
            self::$VType['printing']->fromArray(array(
                'slug' => 'printing',
                'name' => 'Printing',
                'description' => 'Testing Variation Type',
                'seq' => 0,
            ));
            self::$VType['printing']->save();
        }        
        //!VariationTerm : Colors
        if (!self::$VTerm['white'] = self::$modx->getObject('VariationTerm', array('slug'=>'white'))) {
            self::$VTerm['white'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['white']->fromArray(array(
                'vtype_id' => self::$VType['color']->get('vtype_id'),
                'slug' => 'white',
                'name' => 'White',
                'sku_prefix' => '',
                'sku_suffix' => '-WHI',
                'seq' => 0,
            ));
            self::$VTerm['white']->save();
        }
        if (!self::$VTerm['black'] = self::$modx->getObject('VariationTerm', array('slug'=>'black'))) {
            self::$VTerm['black'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['black']->fromArray(array(
                'vtype_id' => self::$VType['color']->get('vtype_id'),
                'slug' => 'black',
                'name' => 'Black',
                'sku_prefix' => '',
                'sku_suffix' => '-BLA',
                'seq' => 0,
            ));
            self::$VTerm['black']->save();
        }
        if (!self::$VTerm['red'] = self::$modx->getObject('VariationTerm', array('slug'=>'large'))) {
            self::$VTerm['red'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['red']->fromArray(array(
                'vtype_id' => self::$VType['color']->get('vtype_id'),
                'slug' => 'red',
                'name' => 'Red',
                'sku_prefix' => '',
                'sku_suffix' => '-RED',
                'seq' => 0,
            ));
            self::$VTerm['red']->save();
        }
        
        //!VariationTerm : Sizes
        if (!self::$VTerm['small'] = self::$modx->getObject('VariationTerm', array('slug'=>'small'))) {
            self::$VTerm['small'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['small']->fromArray(array(
                'vtype_id' => self::$VType['size']->get('vtype_id'),
                'slug' => 'small',
                'name' => 'Small',
                'sku_prefix' => '',
                'sku_suffix' => '-S',
                'seq' => 0,
            ));
            self::$VTerm['small']->save();
        }
        if (!self::$VTerm['med'] = self::$modx->getObject('VariationTerm', array('slug'=>'med'))) {
            self::$VTerm['med'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['med']->fromArray(array(
                'vtype_id' => self::$VType['size']->get('vtype_id'),
                'slug' => 'med',
                'name' => 'Medium',
                'sku_prefix' => '',
                'sku_suffix' => '-M',
                'seq' => 0,
            ));
            self::$VTerm['med']->save();
        }
        if (!self::$VTerm['large'] = self::$modx->getObject('VariationTerm', array('slug'=>'large'))) {
            self::$VTerm['large'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['large']->fromArray(array(
                'vtype_id' => self::$VType['size']->get('vtype_id'),
                'slug' => 'large',
                'name' => 'Large',
                'sku_prefix' => '',
                'sku_suffix' => '-L',
                'seq' => 0,
            ));
            self::$VTerm['large']->save();
        }    

        //!VariationTerm : Materials
        if (!self::$VTerm['cotton'] = self::$modx->getObject('VariationTerm', array('slug'=>'cotton'))) {
            self::$VTerm['cotton'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['cotton']->fromArray(array(
                'vtype_id' => self::$VType['material']->get('vtype_id'),
                'slug' => 'cotton',
                'name' => 'Cotton',
                'sku_prefix' => '',
                'sku_suffix' => '-CTN',
                'seq' => 0,
            ));
            self::$VTerm['cotton']->save();
        }
        if (!self::$VTerm['silk'] = self::$modx->getObject('VariationTerm', array('slug'=>'silk'))) {
            self::$VTerm['silk'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['silk']->fromArray(array(
                'vtype_id' => self::$VType['material']->get('vtype_id'),
                'slug' => 'silk',
                'name' => 'Silk',
                'sku_prefix' => '',
                'sku_suffix' => '-SLK',
                'seq' => 0,
            ));
            self::$VTerm['silk']->save();
        }
        if (!self::$VTerm['wool'] = self::$modx->getObject('VariationTerm', array('slug'=>'wool'))) {
            self::$VTerm['wool'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['wool']->fromArray(array(
                'vtype_id' => self::$VType['material']->get('vtype_id'),
                'slug' => 'wool',
                'name' => 'Wool',
                'sku_prefix' => '',
                'sku_suffix' => '-WOOL',
                'seq' => 0,
            ));
            self::$VTerm['wool']->save();
        }
        
        //!VariationTerm : Printing
        if (!self::$VTerm['silkscreen'] = self::$modx->getObject('VariationTerm', array('slug'=>'silkscreen'))) {
            self::$VTerm['silkscreen'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['silkscreen']->fromArray(array(
                'vtype_id' => self::$VType['printing']->get('vtype_id'),
                'slug' => 'silkscreen',
                'name' => 'Silk Screen',
                'sku_prefix' => '',
                'sku_suffix' => '-SCR',
                'seq' => 0,
            ));
            self::$VTerm['silkscreen']->save();
        }
        if (!self::$VTerm['embossed'] = self::$modx->getObject('VariationTerm', array('slug'=>'embossed'))) {
            self::$VTerm['embossed'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['embossed']->fromArray(array(
                'vtype_id' => self::$VType['printing']->get('vtype_id'),
                'slug' => 'embossed',
                'name' => 'Embossed',
                'sku_prefix' => '',
                'sku_suffix' => '-EMB',
                'seq' => 0,
            ));
            self::$VTerm['embossed']->save();
        }
        if (!self::$VTerm['gold'] = self::$modx->getObject('VariationTerm', array('slug'=>'gold'))) {
            self::$VTerm['gold'] = self::$modx->newObject('VariationTerm');
            self::$VTerm['gold']->fromArray(array(
                'vtype_id' => self::$VType['printing']->get('vtype_id'),
                'slug' => 'gold',
                'name' => 'gold',
                'sku_prefix' => '',
                'sku_suffix' => '-GLD',
                'seq' => 0,
            ));
            self::$VTerm['gold']->save();
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
        
        self::$VType['color']->remove();
        self::$VType['size']->remove();
        self::$VType['material']->remove();
        self::$VType['printing']->remove();

        self::$VTerm['white']->remove();
        self::$VTerm['black']->remove();
        self::$VTerm['red']->remove();
        
        self::$VTerm['small']->remove();
        self::$VTerm['medium']->remove();
        self::$VTerm['large']->remove();
        self::$VTerm['cotton']->remove();
        self::$VTerm['silk']->remove();
        self::$VTerm['wool']->remove();
        self::$VTerm['embossed']->remove();
        self::$VTerm['silkscreen']->remove();
        self::$VTerm['gold']->remove();
        
*/

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

    /**
     * 
     *
     */
    public function testTaxonomies() {
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
        
        $One->addFields($fields);
        
        // Verify they all exist:
        $Collection = self::$modx->getCollection('ProductField', array('product_id'=>$product_id));
        $this->assertFalse(empty($Collection),'Product Fields were not added!');
        $cnt = self::$modx->getCount('ProductField', array('product_id'=>$product_id));
        $this->assertEquals(count($fields), $cnt);
        foreach ($fields as $id) {
            $PT = self::$modx->getObject('ProductField', array('product_id'=>$product_id,'field_id'=>$id));
            $this->assertFalse(empty($PT));
        }
        
        // Add duplicates, verify that nothing new was created.
        $One->addFields($fields);
        $cnt2 = self::$modx->getCount('ProductField', array('product_id'=>$product_id));
        $this->assertEquals($cnt, $cnt2);
        
        // Remove all but one
        $odd_man_out = array_pop($fields);
        $One->removeFields($fields);
        $cnt3 = self::$modx->getCount('ProductField', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt3, 1); // should be only one left
        
        // Now, dictate the fields: this should add and remove
        $One->dictateFields($fields);
        $cnt4 = self::$modx->getCount('ProductField', array('product_id'=>$One->get('product_id')));
        $this->assertEquals($cnt4, count($fields)); 
    }    


}