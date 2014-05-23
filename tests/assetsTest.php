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
class assetTest extends \PHPUnit_Framework_TestCase {

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
        self::$modx->addExtensionPackage($object['namespace'],"{$core_path}model/orm/", array('tablePrefix'=>'moxy_'));
        self::$modx->addPackage('moxycart',"{$core_path}model/orm/",'moxy_');
        self::$modx->addPackage('foxycart',"{$core_path}model/orm/",'foxy_');
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Invalid data type for path
     */
    public function testPreparePath()
    {
        $Asset = new Asset(self::$modx);
        $Asset->preparePath(array('fail'));
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Path must be a directory. File found instead.
     */
    public function testPreparePath2()
    {
        $Asset = new Asset(self::$modx);
        $filename = '/tmp/'.uniqid().'.moxycart.text';
        $result = touch($filename);
        $this->assertTrue($result);
        $Asset->preparePath($filename);
        unlink($filename);
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Failed to create directory
     */
    public function testPreparePath3()
    {
        $Asset = new Asset(self::$modx);
        $dir = '/deleteme.txt';
        $Asset->preparePath($dir);
    }

    /** 
     * Actually prepare it this time
     */
    public function testPreparePath4()
    {
        $dir = MODX_ASSETS_PATH . self::$modx->getOption('moxycart.upload_dir');
        $Asset = new Asset(self::$modx);
        $result = $Asset->preparePath($dir);
        $this->assertTrue($result);
    }

    public function testGetExt() {
        $A = new Asset(self::$modx);
        $this->assertEquals($A->getExt('/does/not/exist.php'),'php');
        $this->assertEquals($A->getExt('/does/not/exist.PHP'),'php');
        $this->assertEquals($A->getExt('/some/file.jpg'),'jpg');
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage File not found
     */
    public function testGetContentTypeFail()
    {
        $Asset = new Asset(self::$modx);
        $file = '/does/not/exist';
        $Asset->getContentType($dir);
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Content type not defined.
     */
    public function testGetContentTypeFail2()
    {
        $Asset = new Asset(self::$modx);
        $filename = '/tmp/'.uniqid().'.moxycart.dick';
        $result = touch($filename);
        $Asset->getContentType($filename);
        unlink($result);
    }


    public function testGetContentType() {
        $A = new Asset(self::$modx);
        $filename = dirname(__FILE__).'/assets/macbook_pro.jpg';
        $C = self::$modx->getObject('modContentType', array('name'=>'JPG'));
        $this->assertFalse(empty($C));
        $id = $A->getContentType($filename);    
        $this->assertEquals($id,$C->get('id'));
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Invalid data type for path
     */
    public function testRelPath() {
        $A = new Asset(self::$modx);
        $A->getRelPath(array('bogus'));
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Prefix not found in path
     */
    public function testRelPath2() {
        $A = new Asset(self::$modx);
        $filename = dirname(__FILE__).'/assets/macbook_pro.jpg';
        $A->getRelPath($filename);
    }

    public function testRelPath3() {
        $A = new Asset(self::$modx);
        $filename = dirname(__FILE__).'/assets/macbook_pro.jpg'; 
        $relpath = $A->getRelPath($filename, dirname(__FILE__));
        $this->assertEquals($relpath, 'assets/macbook_pro.jpg');
    }

    /**
     * 
     */
    public function testImageSize() {
        $filename = dirname(__FILE__).'/assets/support.jpg'; 
    
        $dir = MODX_ASSETS_PATH . self::$modx->getOption('moxycart.upload_dir');
        $A = new Asset(self::$modx);
        
        $info = $A->getImageSize($filename);
        $this->assertEquals($info['width'],430);
        $this->assertEquals($info['height'],400);
        $this->assertEquals($info['type'],2);
        $this->assertEquals($info['mime'],'image/jpeg');

        $info = $A->getImageSize('/some/file/that:does/not/exist.jpg');
        $this->assertFalse($info);
    }

    /**
     * Tests the fromFile method, verifying that it returns an existing
     * object record
     */
    public function testFromFileExisting() {

        $dir = MODX_ASSETS_PATH . self::$modx->getOption('moxycart.upload_dir');
        $A = new Asset(self::$modx);
        $result = $A->preparePath($dir);
        $this->assertTrue($result);
        $this->assertTrue($A instanceof Asset);
        
        $ContentType = self::$modx->getObject('modContentType',array('name'=>'JPG'));
        
        $this->assertFalse(empty($ContentType));
        $this->assertTrue($ContentType instanceof \modContentType);

        if ($Existing = self::$modx->getObject('Asset', array('path'=>'assets/macbook_pro.jpg'))) {
            $Existing->remove();
        }
        
        // Verify that fromFile returns an existing Asset object
        $A->fromArray(array(
            'content_type_id' => $ContentType->get('id'),
            'title' => 'Sample Image',
            'alt' => 'This is only a sample image',
            'url' => 'assets/macbook_pro.jpg',
            'thumbnail_url' => 'assets/.thumb.macbook_pro.jpg',
            'path' => 'assets/macbook_pro.jpg',
            'width' => 1280,
            'height' => 956,
            'size' => 560,
            'duration' => 0,
            'is_active' => true,
            'is_protected' => false,
            'seq' => 0
        ));
        $result = $A->save();
        $this->assertTrue($result);
        $asset_id = $A->getPrimaryKey();
        $this->assertFalse(empty($asset_id));

        $A = new Asset(self::$modx);
        $filename = dirname(__FILE__).'/assets/macbook_pro.jpg'; 
        $A = $A->fromFile($filename,array(),dirname(__FILE__));
        
        $this->assertEquals($asset_id, $A->get('asset_id'));
        
        $A->remove();
    }

    /**
     * Tests the fromFile method, verifying that it creates a new object record
     */
    public function testFromFileNew() {
        $filename = dirname(__FILE__).'/assets/support.jpg'; 
    
        $dir = MODX_ASSETS_PATH . self::$modx->getOption('moxycart.upload_dir');
        $A = new Asset(self::$modx);
                
        $result = $A->preparePath($dir);
        $this->assertTrue($result);
        $this->assertTrue($A instanceof Asset);
        
        if ($Existing = self::$modx->getObject('Asset', array('path'=>'assets/support.jpg'))) {
            $Existing->remove();
        }
        
        // Verify that fromFile creates a Asset object        
        $A->fromFile($filename,array(
                'title' => 'Support',
                'alt' => 'This is a test of the fromFile method'
                ),dirname(__FILE__));
        
        $result = $A->save();
        $this->assertTrue($result);
        
        $asset_id = $A->getPrimaryKey();
        
        $B = $A->find($asset_id);

        $this->assertEquals($B->get('path'), 'assets/support.jpg');
        $this->assertEquals($B->get('width'), 430);
        $this->assertEquals($B->get('height'), 400);
        $this->assertEquals('assets/thumbs/support.jpg',$B->get('thumbnail_url'));
        
//        $B->remove();
    }
}