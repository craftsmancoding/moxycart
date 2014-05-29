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
                
        // We have to do this here because we get conflicts if we try to do a class map autoload of the 
        // base "" directory.
        // The modmanagercontroller must be included first!
        require_once MODX_CORE_PATH .'model/modx/modmanagercontroller.class.php';    
        require_once $core_path . 'index.class.php';

        // First thing is to pass the modx dependency to the parent controller
        $tmp = new \IndexManagerController(self::$modx);

        // !Field
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
        $this->assertTrue(is_a($result, '\\Moxycart\\PageController'), 'Invalid Page controller instance.');

        $_REQUEST['class'] = 'Product';
        $result = \IndexManagerController::getInstance(self::$modx);
        $this->assertTrue(is_a($result, '\\Moxycart\\ProductController'), 'Invalid Product controller instance.');

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
    public function testURLGeneration() {
        //$url = BaseController::url($class='',$method='index',$args=array())
        $tmp = new BaseController(self::$modx);
        $classname = 'Xyz';
        $methodname = 'derp';
        $url = BaseController::url($classname,$methodname);
        $m = preg_match('#'.MODX_MANAGER_URL.preg_quote('?a=','#').'\d+'.preg_quote('&class='.$classname.'&method='.$methodname).'#i',$url);
        $this->assertFalse(empty($m));        
    }

    
    public function testEditController() {
        $F = new Field(self::$modx);
        $F = $F->one(array('slug'=>'one'));
        
        $data = $F->toArray();
        $data['label'] = 'Test '.date('H:i:s');
        $Controller = new FieldController(self::$modx);
        $response = $Controller->postEdit($data);
        $this->assertEquals('{"status":"success","data":{"msg":"Field updated successfully."}}',$response);
    }

    
}