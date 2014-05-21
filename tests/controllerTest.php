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
class controllerTest extends \PHPUnit_Framework_TestCase {

    // Must be static because we set it up inside a static function
    public static $modx;
    public static $moxycart;
    
    /**
     * Load up MODX for our tests.
     *
     */
    public static function setUpBeforeClass() {        
         
        self::$modx = new \modX();
        self::$modx->initialize('mgr');  
        
        $core_path = self::$modx->getOption('moxycart.core_path', '', MODX_CORE_PATH);
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
    public function testMODX() {
        $this->assertTrue(defined('MODX_CORE_PATH'), 'MODX_CORE_PATH not defined.');
        $this->assertTrue(defined('MODX_ASSETS_PATH'), 'MODX_ASSETS_PATH not defined.');
        $this->assertTrue(is_a(self::$modx, 'modX'), 'Invalid modX instance.');
    
    }


    /** 
     * Load up on guns, bring your friends - Nirvana
     */
    public function testLoadControllers() {
        unset($_REQUEST['class']);
        $result = \IndexManagerController::getInstance(self::$modx);
        $this->assertTrue(is_a($result, '\\Moxycart\\MainController'), 'Invalid Main controller instance.');

        $_REQUEST['class'] = 'Product';
        $result = \IndexManagerController::getInstance(self::$modx);
        $this->assertTrue(is_a($result, '\\Moxycart\\ProductController'), 'Invalid Product controller instance.');

        $_REQUEST['class'] = 'Currency';
        $result = \IndexManagerController::getInstance(self::$modx);
        $this->assertTrue(is_a($result, '\\Moxycart\\CurrencyController'), 'Invalid Currency controller instance.');

        $_REQUEST['class'] = 'Asset';
        $result = \IndexManagerController::getInstance(self::$modx);
        $this->assertTrue(is_a($result, '\\Moxycart\\AssetController'), 'Invalid Asset controller instance.');

        $_REQUEST['class'] = 'Field';
        $result = \IndexManagerController::getInstance(self::$modx);
        $this->assertTrue(is_a($result, '\\Moxycart\\FieldController'), 'Invalid Field controller instance.');

        $_REQUEST['class'] = 'Review';
        $result = \IndexManagerController::getInstance(self::$modx);
        $this->assertTrue(is_a($result, '\\Moxycart\\ReviewController'), 'Invalid Review controller instance.');


    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Invalid data type for class
     */
    public function testBogusClassname()
    {
        $_REQUEST['class'] = array('InvalidDataType');
        $result = \IndexManagerController::getInstance(self::$modx);
    }

    /** 
     *
     */
    public function testLoadBogusController() {
        $_REQUEST['class'] = 'DoesNotExist';
        $result = \IndexManagerController::getInstance(self::$modx);
        $this->assertTrue(is_a($result, '\\Moxycart\\ErrorController'), 'Invalid Error controller instance.');
    
    }

    /**
     *
     *
     */
    public function testUtilityFunctions() {
        //$url = BaseController::url($class='',$method='index',$args=array())
        $tmp = new BaseController(self::$modx);
        // /manager/?a=94&class=Xyz&method=derp
        $url = BaseController::url('Xyz','derp');
//        print $url;        
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