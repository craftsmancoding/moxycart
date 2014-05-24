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
class modelTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    public static $Field;
    
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
     * Let's test working with a simple model (Currency)
     *
     */
    public function testBasicRetrieval() {

        $F = new Field(self::$modx);
        $this->assertTrue($F instanceof Field);
        
        // Verify mass-assignment
        $F->fromArray(array(
            'slug' => 'four',
            'label' => 'For Testing',
            'type' => 'textarea'
        ));
        $this->assertEquals('textarea', $F->type);
        
        // Verify single-assignment
        $F->label = 'Not for Testing';
        $this->assertEquals('Not for Testing', $F->label);
        $F->set('label','Something Else');
        $this->assertEquals('Something Else', $F->get('label'));
        
        $result = $F->save();
        $this->assertTrue($result);
        
        $id = $F->getPrimaryKey();
        
        $F = $F->find($id);
        $this->assertFalse(empty($F));
        $this->assertEquals('Something Else', $F->get('label'));
        $F->remove();
            
    }
    
    public function testFilters() {
        $F = new Field(self::$modx);
        $args = array('limit'=>10,'offset'=>20);
        $filters = $F->getFilters($args);
        $this->assertTrue(empty($filters));
        
        $args = array('limit'=>10,'offset'=>20,'something'=>'else');
        $filters = $F->getFilters($args);
        $this->assertEquals($filters['something'],'else');

        $args = array('sort'=>'x','dir'=>'ASC','select'=>'yyy','mycolumn:like'=>'myvalue');
        $filters = $F->getFilters($args);        
        $this->assertEquals($filters['mycolumn:like'],'%myvalue%');

        $args = array('mycolumn:starts with'=>'myvalue');
        $filters = $F->getFilters($args);        
        $this->assertEquals($filters['mycolumn:LIKE'],'myvalue%');
        $this->assertEquals(count($filters),1);
        
        $args = array('mycolumn:ends with'=>'myvalue');
        $filters = $F->getFilters($args);        
        $this->assertEquals($filters['mycolumn:LIKE'],'%myvalue');


        $args = array('one','two','three');
        $filters = $F->getFilters($args);
        $this->assertTrue(empty($filters));
    }
    
    public function testCount() {
        $F = new Field(self::$modx);
        $cnt = $F->count(array('slug:STARTS WITH'=>'t'));
        $this->assertEquals(2,$cnt);

        $cnt = $F->count(array('type:>='=>'text'));
        $this->assertEquals(2,$cnt);
    }
    
    public function testDebug() {
        $F = new Field(self::$modx);
        $actual = $F->all(array('label:>'=>'D'),true);
        $expected = "SELECT `Field`.`field_id` AS `Field_field_id`, `Field`.`slug` AS `Field_slug`, `Field`.`label` AS `Field_label`, `Field`.`description` AS `Field_description`, `Field`.`config` AS `Field_config`, `Field`.`seq` AS `Field_seq`, `Field`.`group` AS `Field_group`, `Field`.`type` AS `Field_type`, `Field`.`timestamp_created` AS `Field_timestamp_created`, `Field`.`timestamp_modified` AS `Field_timestamp_modified` FROM `moxy_fields` AS `Field` WHERE `Field`.`label` > 'D' ORDER BY slug LIMIT 20";
        $this->assertEquals(normalize_string($actual),normalize_string($expected));

        $actual = $F->all(array('slug:LIKE'=>'VC'),true);
        $expected = "SELECT `Field`.`field_id` AS `Field_field_id`, `Field`.`slug` AS `Field_slug`, `Field`.`label` AS `Field_label`, `Field`.`description` AS `Field_description`, `Field`.`config` AS `Field_config`, `Field`.`seq` AS `Field_seq`, `Field`.`group` AS `Field_group`, `Field`.`type` AS `Field_type`, `Field`.`timestamp_created` AS `Field_timestamp_created`, `Field`.`timestamp_modified` AS `Field_timestamp_modified` FROM `moxy_fields` AS `Field` WHERE `Field`.`slug` LIKE '%VC%' ORDER BY slug LIMIT 20";
        $this->assertEquals(normalize_string($actual),normalize_string($expected));

        $actual = $F->all(array('slug:LIKE'=>'VC','limit'=>30),true);
        $expected = "SELECT `Field`.`field_id` AS `Field_field_id`, `Field`.`slug` AS `Field_slug`, `Field`.`label` AS `Field_label`, `Field`.`description` AS `Field_description`, `Field`.`config` AS `Field_config`, `Field`.`seq` AS `Field_seq`, `Field`.`group` AS `Field_group`, `Field`.`type` AS `Field_type`, `Field`.`timestamp_created` AS `Field_timestamp_created`, `Field`.`timestamp_modified` AS `Field_timestamp_modified` FROM `moxy_fields` AS `Field` WHERE `Field`.`slug` LIKE '%VC%' ORDER BY slug LIMIT 30";
        $this->assertEquals(normalize_string($actual),normalize_string($expected));
                
        $actual = $F->all(array('slug:STARTS WITH'=>'VC','limit'=>30),true);
        $expected = "SELECT `Field`.`field_id` AS `Field_field_id`, `Field`.`slug` AS `Field_slug`, `Field`.`label` AS `Field_label`, `Field`.`description` AS `Field_description`, `Field`.`config` AS `Field_config`, `Field`.`seq` AS `Field_seq`, `Field`.`group` AS `Field_group`, `Field`.`type` AS `Field_type`, `Field`.`timestamp_created` AS `Field_timestamp_created`, `Field`.`timestamp_modified` AS `Field_timestamp_modified` FROM `moxy_fields` AS `Field` WHERE `Field`.`slug` LIKE 'VC%' ORDER BY slug LIMIT 30";
        $this->assertEquals(normalize_string($actual),normalize_string($expected));
    }
    
    public function testGetCollection() {
        $F = new Field(self::$modx);
        $collection = $F->all(array('group'=>'GroupZ'));
        $this->assertTrue(empty($collection));
        $collection = $F->all(array('group:STARTS WITH'=>'Group'));

        $values = array();
        foreach ($collection as $c) {
            $values[] = $c->get('slug');
        }

        $this->assertEquals($values[0],'one');
        $this->assertEquals($values[1],'three');
        $this->assertEquals($values[2],'two');
    }

    public function testOne() {
        $F = new Field(self::$modx);

        $F2 = $F->one(array('slug'=>'does not exist'));
        $this->assertFalse($F2);        
        
        $F = $F->one(array('slug'=>'two'));
        $this->assertFalse(empty($F));
        
        $this->assertTrue(is_a($F, '\\Moxycart\\Field'));
    }

    
    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Invalid object type.
     */
    public function testInvalidObjectType() {
        $Chunk = self::$modx->newObject('modChunk');
        $F = new Field(self::$modx, $Chunk);    
    }

    /**
     */
    public function testValidObjectType() {
        $F = self::$modx->newObject('Field');
        $F2 = new Field(self::$modx, $F);
    }

    
    
}