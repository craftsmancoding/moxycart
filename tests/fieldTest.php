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
class fieldTest extends \PHPUnit_Framework_TestCase {

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
        self::$modx->addExtensionPackage('moxycart',"{$core_path}model/orm/", array('tablePrefix'=>'moxy_'));
        self::$modx->addPackage('moxycart',"{$core_path}model/orm/",'moxy_');
        self::$modx->addPackage('foxycart',"{$core_path}model/orm/",'foxy_');

        // !Field
/*
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
*/
    }

    /**
     *
     */
    public static function tearDownAfterClass() {
//        self::$OType['test-sample']->remove();
    }

    
    public function testRestrictedWords() {
        $F = new Field(self::$modx);
        $F->fromArray(array(
            'slug' => 'code',
            'label' => 'A Reserved Word',
        ));
        $result = $F->save();
        $this->assertFalse($result);
        $this->assertEquals($F->errors['slug'], 'The slug cannot be a reserved word.');
    }
    
}