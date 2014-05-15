<?php
/**
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
 * To run just the tests in this file, specify the file:
 *
 *  phpunit tests/autoloadTest.php
 *
 */
 
class autoloadTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    
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
        
/*
        $core_path = self::$modx->getOption('moxycart.core_path', '', MODX_CORE_PATH);
        include_once $core_path . 'components/moxycart/model/moxycart/moxycart.class.php';
        
        self::$moxycart = new Moxycart(self::$modx);
*/
        include_once dirname(dirname(__FILE__)).'/vendor/autoload.php';
    }


    /**
     *
     */
    public function testMODX() {
        $this->assertTrue(defined('MODX_CORE_PATH'), 'MODX_CORE_PATH not defined.');
        $this->assertTrue(defined('MODX_ASSETS_PATH'), 'MODX_ASSETS_PATH not defined.');
        $this->assertTrue(is_a(self::$modx, 'modX'), 'Invalid modX instance.');
    }
    
    
    public function testLoad() {

        $M = new Moxycart(self::$modx);
        $this->assertTrue(is_object($M), 'Moxycart class not instantiated.');        

            $class = '\\Moxycart\\Controller\\'.$class;
//print $class .'<br/>'. __FILE__.':'.__LINE__;exit;

//        $this->assertTrue(class_exists($class), 'Moxycart\\Controller\\Product class not found.');

        
        $P = new Moxycart\Controller\Product(self::$modx);
//            $P = new Moxycart\Product();
        
        $this->assertTrue(is_object($P), 'Moxycart\\Controller\\Product class not instantiated.');
    }
    
    public function testFoxycart() {
        $RC4 = new rc4crypt();
        $this->assertTrue(is_object($RC4), 'rc4crypt class not instantiated.');
        
//        $F = new Foxycart\Datafeed();
//	$xml = new SimpleXMLElement($FoxyData_decrypted);
//	$dom = new DOMDocument('1.0');
    }
}