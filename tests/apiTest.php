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
class apiTest extends \PHPUnit_Framework_TestCase {

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
        self::$modx->addExtensionPackage('moxycart',"{$core_path}model/orm/", array('tablePrefix'=>'moxy_'));
        self::$modx->addPackage('moxycart',"{$core_path}model/orm/",'moxy_');
        self::$modx->addPackage('foxycart',"{$core_path}model/orm/",'foxy_');
                
        // We have to do this here because we get conflicts if we try to do a class map autoload of the 
        // base "" directory.
        // The modmanagercontroller must be included first!
        require_once MODX_CORE_PATH .'model/modx/modmanagercontroller.class.php';    
        require_once $core_path . 'index.class.php';

        // First thing is to pass the modx dependency to the parent controller
        $tmp = new \IndexManagerController(self::$modx);
        
    }


    /** 
     *
     */
    public function testSearch() {
        $API = new ProductController(self::$modx);
        $results = $API->postSearch(array('name:LIKE'=>'shirt'));
        
        $this->assertTrue(!empty($results));
        $results = json_decode($results,true);
        $this->assertTrue(is_array($results));
        $this->assertEquals('success', $results['status']);
        //print_r($results);
    }

}