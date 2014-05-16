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
 
class datafeedTest extends \PHPUnit_Framework_TestCase {

    public static $modx;
    public static $cnt = 0;
    public static $args = array();
    public static $snippetname;
    
    public static function sample_callback($args) {
        self::$cnt++;
        self::$args = $args;
    }

    public static function pretend_snippet($snippetname,$args) {
        self::$cnt++;
        self::$args = $args;
        self::$snippetname = $snippetname;
    }

    
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

        $core_path = self::$modx->getOption('moxycart.core_path','',MODX_CORE_PATH.'components/moxycart/');
        self::$modx->addPackage('foxycart',$core_path.'model/orm/','foxy_');

        include_once dirname(dirname(__FILE__)).'/vendor/autoload.php';
    }


    /**
     *
     */
    public function testRequiredClasses() {
        $this->assertTrue(class_exists('DOMDocument'));
        $this->assertTrue(function_exists('simplexml_load_string'));
        $this->assertTrue(class_exists('SimpleXMLElement'));
        $str = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><foxydata></foxydata>';
		$X = new SimpleXMLElement($str);
		$D = new DOMDocument('1.0');
        $this->assertTrue(is_object($X), 'SimpleXMLElement class not instantiated.');
        $this->assertTrue(is_object($D), 'DOMDocument class not instantiated.');
    }
    
    public function testDecodeToXML() {
        $pwd = 'myrandompassword';
        $data = file_get_contents( dirname(__FILE__).'/foxycart/sample1.xml');
        $encrypted = rc4crypt::encrypt($pwd, $data);
        $payload = urlencode($encrypted);
        
        $Datafeed = new \Foxycart\Datafeed(self::$modx, new \rc4crypt());
        $xml_str = $Datafeed->post2xml($payload,$pwd);

        $this->assertEquals(normalize_string($data),normalize_string($xml_str));
    }
    
    /**
     * @expectedException Exception 
     * @expectedExceptionMessage Invalid Foxycart XML body
     */
    public function testInvalidFoxycartXMLBody()
    {
        $Datafeed = new \Foxycart\Datafeed(self::$modx, new \rc4crypt());
        $xml = 'invalid';
        $transactions = $Datafeed->saveFoxyData($xml);
    }
    
    public function testParseFoxycartXML() {
        $api_key = 'test';
        $xml = file_get_contents( dirname(__FILE__).'/foxycart/sample1.xml');
        
        // Delete from database if present
        if ($Foxydata = self::$modx->getObject('Foxydata', array('api_key'=>$api_key))) {
            $Foxydata->remove();
        }

        $Datafeed = new \Foxycart\Datafeed(self::$modx, new \rc4crypt());
        $result = $Datafeed->saveFoxyData($xml);
        $this->assertEquals($result,'foxy'); 
        $Transaction = self::$modx->getObject('Transaction', array('id'=>'1234567890'));
        $x = ($Transaction) ? true : false;
        $this->assertTrue($x);
        $this->assertEquals($Transaction->get('customer_id'),'12345678');
        
        $transaction_id = $Transaction->get('transaction_id');
        
        $TDs = self::$modx->getCollection('TransactionDetail', array('transaction_id'=>$transaction_id));
        
        $names = array('Ham Steak','5 Knives Sausage Sampler','Refrigerated Box');
        foreach ($TDs as $p) {
            $this->assertTrue(in_array($p->get('product_name'),$names));
        }
        // $Datafeed->saveFoxyData($bogus_xml);
        
    }
    
    
    /**
     * @expectedException Exception 
     * @expectedExceptionMessage Invalid callback event
     */
    public function testInvalidCallbackEvent()
    {
        $Datafeed = new \Foxycart\Datafeed(self::$modx, new \rc4crypt());
        $Datafeed->registerCallback('dork',function($args){});
    }

    /**
     * @expectedException Exception 
     * @expectedExceptionMessage Invalid callback event
     */
    public function testInvalidCallbackEvent2()
    {
        $Datafeed = new \Foxycart\Datafeed(self::$modx, new \rc4crypt());
        $Datafeed->registerCallback(array('dork'),function($args){});
    }

    /**
     * @expectedException Exception 
     * @expectedExceptionMessage Invalid callback
     */
    public function testInvalidCallback()
    {
        $Datafeed = new \Foxycart\Datafeed(self::$modx, new \rc4crypt());
        $Datafeed->registerCallback('dork','not a callable function');
    }
    
    public function testRegisterCallback() {
        $Datafeed = new \Foxycart\Datafeed(self::$modx, new \rc4crypt());
        $Datafeed->registerCallback('postback','datafeedTest::sample_callback');
        $this->assertTrue(!empty($Datafeed->callbacks_raw['postback']));
    }
    
    public function testExecuteCallback() {
        self::$cnt = 0;
        $Datafeed = new \Foxycart\Datafeed(self::$modx, new \rc4crypt());
        $Datafeed->registerCallback('postback','datafeedTest::sample_callback');
        $Datafeed->executeCallbacks('postback',array('x'=>'xray','y'=>'yellow'));
        $this->assertEquals(self::$cnt,1);
        $this->assertEquals(self::$args['x'],'xray');

        $Datafeed->registerCallback('product','datafeedTest::pretend_snippet',array('MySnippet'));
        $Datafeed->executeCallbacks('product',array('a'=>'apple','b'=>'bear','c'=>'cat'));

        $this->assertEquals(self::$cnt,2);
        $this->assertEquals(self::$args['a'],'apple');
        $this->assertEquals(self::$snippetname,'MySnippet');        
        
    }

}