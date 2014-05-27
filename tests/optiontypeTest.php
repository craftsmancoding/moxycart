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
class optiontypeTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    public static $OType;
    public static $OTerm;
    
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

        // !OptionType
        if (!self::$OType['color_test'] = self::$modx->getObject('ObjectType', array('slug'=>'color_test'))) {
            self::$OType['color_test'] = self::$modx->newObject('ObjectType');
            self::$OType['color_test']->fromArray(array(
                'slug' => 'color_test',
                'name' => 'Test One',
                'description' => 'Testing Field'
            ));
            if(!self::$OType['color_test']->save()) {
                print 'Could not save option type!'; 
            }
        }
        if (!self::$OTerm['red_test'] = self::$modx->getObject('ObjectTerm', array('slug'=>'red_test'))) {
            self::$OType['red_test'] = self::$modx->newObject('ObjectTerm');
            self::$OType['red_test']->fromArray(array(
                'otype_id' => self::$OType['color_test']->get('otype_id'),
                'slug' => 'red_test',
                'name' => 'Test Red'
            ));
            if(!self::$OTerm['red_test']->save()) {
                print 'Could not save option type!'; 
            }
        }

    }

    /**
     *
     */
    public static function tearDownAfterClass() {
//        self::$OType['test-sample']->remove();
    }

    
    public function testRestrictedWords() {
        $O = new OptionType(self::$modx);
        $O->fromArray(array(
            'slug' => 'code',
            'name' => 'A Reserved Word',
        ));
        $result = $O->save();
        $this->assertFalse($result);
        $this->assertEquals($O->errors['slug'], 'The slug cannot be a reserved word.');
    }
    
}