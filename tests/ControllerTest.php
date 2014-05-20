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

class controllerTest extends PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    public static $moxycart;
    
    /**
     * Load up MODX for our tests.
     *
     */
    public static function setUpBeforeClass() {        
        $docroot = dirname(dirname(dirname(dirname(__FILE__))));
        while (!file_exists($docroot.'/config.core.php')) {
            if ($docroot == '/') {
                die('Failed to locate config.core.php');
            }
            $docroot = dirname($docroot);
        }
        if (!file_exists($docroot.'/config.core.php')) {
            die('Failed to locate config.core.php');
        }
        
        include_once $docroot . '/config.core.php';
        
        if (!defined('MODX_API_MODE')) {
            define('MODX_API_MODE', false);
        }
        
        include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
         
        self::$modx = new modX();
        self::$modx->initialize('mgr');  
        
        $core_path = self::$modx->getOption('moxycart.core_path', '', MODX_CORE_PATH);
        // We have to do this here because we get conflicts if we try to do a class map autoload of the 
        // base "" directory.
        // The modmanagercontroller must be first.
        require_once MODX_CORE_PATH .'model/modx/modmanagercontroller.class.php';    
        require_once $core_path . 'index.class.php';

        // First thing is to pass the modx dependency to the parent controller
        $tmp = new IndexManagerController(self::$modx);
        
    }

    /**
     *
     */
    public function testMODX() {
        $this->assertTrue(defined('MODX_CORE_PATH'), 'MODX_CORE_PATH not defined.');
        $this->assertTrue(defined('MODX_ASSETS_PATH'), 'MODX_ASSETS_PATH not defined.');
        $this->assertTrue(is_a(self::$modx, 'modX'), 'Invalid modX instance.');
    
    }


    public function testLoadController() {
        $result = IndexManagerController::getInstance(self::$modx);
        $this->assertTrue(is_a($result, '\\Moxycart\\Controller\\Main'), 'Invalid Main instance.');
    }


    
/*
    public function testLoadView() {
        $file = 'product_template.php';
        $method = new ReflectionMethod(
          'MoxycartController', '_load_view'
        );
        $method->setAccessible(TRUE);
        $this->assertTrue($method->invokeArgs(self::$moxycart,array($file)) != 'view_not_found','View Not Found');
    }
*/

    
}