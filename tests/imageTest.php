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
class imageTest extends \PHPUnit_Framework_TestCase {

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
    }

    /** 
     * 
     */
/*
    public function testPrepareAssetPath()
    {
        $dir = MODX_ASSETS_PATH . self::$modx->getOption('moxycart.upload_dir');
        $Asset = new Asset(self::$modx);
        $result = $Asset->preparePath($dir);
        $this->assertTrue($result);
    }
*/


    /**
     * Can we make good thumbnails?
     *
     */
    public function testThumbnail() {
        // prep
        $tw = self::$modx->getOption('moxycart.thumbnail_width');
        $th = self::$modx->getOption('moxycart.thumbnail_height');
        $src = dirname(__FILE__).'/assets/support.jpg'; 
        $dst = dirname($src).'/thumbs/'.basename($src);
        if (file_exists($dst)) {
            unlink($dst);        
        }

        $this->assertFalse(file_exists($dst));
              
        $result = Image::scale2w($src,$dst,112);
        
        $this->assertTrue(file_exists($dst));
        $this->assertEquals($result,$dst);
        
        $info = getimagesize($result);
        $this->assertFalse(empty($info));
        $this->assertEquals($info[0],112);
        
        if (file_exists($dst)) {
            unlink($dst);        
        }        
    }

    /**
     * Test with a different dimension
     */
    public function testThumbnail2() {
        // prep
        $src = dirname(__FILE__).'/assets/support.jpg'; 
        $dst = dirname($src).'/thumbs/'.basename($src);
        if (file_exists($dst)) {
            unlink($dst);        
        }

        $this->assertFalse(file_exists($dst));
              
        $result = Image::scale2w($src,$dst,100);
        
        $this->assertTrue(file_exists($dst));
        $this->assertEquals($result,$dst);
        
        $info = getimagesize($result);
        $this->assertFalse(empty($info));
        $this->assertEquals($info[0],100);
                
        if (file_exists($dst)) {
            unlink($dst);        
        } 
    }


    /**
     * Test with yet another dimension and compare signatures
     */
    public function testThumbnail3() {
        // prep
        $src = dirname(__FILE__).'/assets/support.jpg'; 
        $dst = dirname($src).'/thumbs/'.basename($src);
        if (file_exists($dst)) {
            unlink($dst);        
        }

        $this->assertFalse(file_exists($dst));
              
        $result = Image::scale2w($src,$dst,222);
        
        $this->assertTrue(file_exists($dst));
        $this->assertEquals($result,$dst);
        
        $info = getimagesize($result);
        $this->assertFalse(empty($info));
        $this->assertEquals($info[0],222);

        $actual_sig = md5_file($result);
        $expected_sig = md5_file(dirname(__FILE__).'/assets/222.support.jpg');
        $this->assertEquals($actual_sig,$expected_sig);
                
        if (file_exists($dst)) {
            unlink($dst);        
        } 
    }    

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage File not found
     */
    public function testCropExceptions() {
        $result = Image::crop('/does/not/exist','ignore',0,0,100,100);
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage File not found
     */
    public function testCropExceptions2() {
        $result = Image::crop(array('junk'),'ignore',0,0,100,100);
    }

    /**
     * This should fail when you use a php file.
     *
     * @expectedException        \Exception
     * @expectedExceptionMessage Could not read image
     */
    public function testCropExceptions3() {
        $result = Image::crop(__FILE__,'ignore',0,0,100,100);
    }

    /**
     *
     * @expectedException        \Exception
     * @expectedExceptionMessage Failed to create directory
     */
    public function testCropExceptions4() {
        $src = dirname(__FILE__).'/assets/support.jpg'; 
        $result = Image::crop($src,'/can/not/write/here.jpg',0,0,100,100);
    }
    
    /**
     *
     *
     */
    public function testCrop() {
        $src = dirname(__FILE__).'/assets/macbook_pro.jpg'; 
        $dst = dirname(__FILE__).'/assets/cropped.macbook_pro.jpg';
        if (file_exists($dst)) {
            unlink($dst);        
        }        
        $x = 0;
        $y = 0;
        $w = 640;
        $h = 478;
        $result = Image::crop($src,$dst,$x,$y,$w,$h);
        $actual_sig = md5_file($result);
        $expected_sig = md5_file(dirname(__FILE__).'/assets/topleft.macbook_pro.jpg');
        $this->assertEquals($actual_sig,$expected_sig);
        
        if (file_exists($result)) {
            unlink($result);        
        }
    }
    
    public function testRealThumb() {
        $src = dirname(__FILE__).'/assets/macbook_pro.jpg'; 
        $dst = dirname(__FILE__).'/assets/thumb.macbook_pro.jpg';
        $actual_dst = dirname(__FILE__).'/assets/thumb2.macbook_pro.jpg';
        $tmp = 'thumb.macbook_pro.jpg';
        if (file_exists($dst)) {
            unlink($dst);        
        }        
        $result = Image::thumbnail($src,$dst,300,150);

        $actual_sig = md5_file($actual_dst);
        $expected_sig = md5_file($dst);
        $this->assertEquals($actual_sig,$expected_sig);
        if (file_exists($dst)) {
            unlink($dst);        
        }        

    }
    
}