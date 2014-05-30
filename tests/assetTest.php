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
        self::$modx->addExtensionPackage('moxycart',"{$core_path}model/orm/", array('tablePrefix'=>'moxy_'));
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
        $this->assertTrue(file_exists($result));
    }

    // Get file extension
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
        $C2 = $A->getContentType($filename);    
        $this->assertEquals($C->get('id'),$C2->get('id'));
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
        
        $info = $A->getImageInfo($filename);
        $this->assertEquals($info['width'],430);
        $this->assertEquals($info['height'],400);
        $this->assertEquals($info['type'],2);
        $this->assertEquals($info['mime'],'image/jpeg');

        $info = $A->getImageInfo('/some/file/that:does/not/exist.jpg');
        $this->assertFalse($info);
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Invalid data type.
     */
    public function testExceptionsfromFile() {
        $A = new Asset(self::$modx);
        $file = 'Dud';
        $A->fromFile($file,'/tmp');
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Invalid data type.
     */
    public function testExceptionsfromFile2() {
        $A = new Asset(self::$modx);
        $file = array('tmp_name'=>'','name'=>'');
        $A->fromFile($file,array('fail'));
    }
    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Missing required keys in FILE array
     */
    public function testExceptionsfromFile3() {
        $A = new Asset(self::$modx);
        $file = array('not_tmp_name'=>'','not_name'=>'');
        $A->fromFile($file,'/somewhere');
    }
    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Invalid data type for path
     */
    public function testExceptionsfromFile4() {
        $A = new Asset(self::$modx);
        $file = array('tmp_name'=> array('boned'),'name'=>'');
        $A->fromFile($file,'/somewhere');
    }

    /**
     * Tests the fromFile method, verifying that it creates a new object record
     */
    public function testFromFile() {
        //self::$modx->setLogLevel(4);
        //self::$modx->setLogTarget('ECHO');
        $A = new Asset(self::$modx);

        $orig_filename = dirname(__FILE__).'/assets/support.jpg'; 
        $result = copy($orig_filename, dirname(__FILE__).'/assets/support2.jpg');
        $this->assertTrue($result, 'Failed to copy test image.');
        // Our disposable testing file:
        $filename = dirname(__FILE__).'/assets/support2.jpg';
        $FILE = array(
            'tmp_name' => $filename,
            'name' =>'support2.jpg'
        );
        
        // In prod: MODX_ASSETS_PATH . self::$modx->getOption('moxycart.upload_dir');
        $storage_basedir = dirname(__FILE__).'/asset_library/';        
                
        $A2 = $A->fromFile($FILE,$storage_basedir);
        
        $this->assertFalse(empty($A2));
        
        
        
        $this->assertTrue(file_exists($storage_basedir.date('Y/m/d/')), 'Directory does not exist: '.$storage_basedir.date('Y/m/d/'));
        $this->assertTrue(file_exists($storage_basedir.date('Y/m/d/').'support2.jpg'), 'File does not exist: '.$storage_basedir.date('Y/m/d/').'support2.jpg');
        $this->assertEquals(md5_file($orig_filename), $A2->get('sig'),'File signature does not match.');
        $this->assertEquals(date('Y/m/d/').'support2.jpg', $A2->get('path'),'Asset path incorrect');
        $this->assertEquals(date('Y/m/d/').'support2.jpg', $A2->get('url'),'Asset path incorrect');
        
/*
        // TODO: test the thumbnail
        $path = $A2->get('path');
        $thumbnail = dirname($path).'/'. $this->modx->getOption('moxycart.thumbnail_dir'). moxycart.thumbnail_width
        $A2->get('thumbnail_url');
*/
        
        $result = $A2->remove($storage_basedir);
        $this->assertTrue($result);
        $this->assertFalse(file_exists($storage_basedir.date('Y/m/d/').'support2.jpg'), 'File does not exist: '.$storage_basedir.date('Y/m/d/').'support2.jpg');
        
        unlink($filename);
        
        Asset::rrmdir($storage_basedir.date('Y/'));
    }
    
    /**
     *
     */
    public function testgetUniqueFilename() {
        $A = new Asset(self::$modx);
        $file = '/tmp/'.uniqid().'.txt';
        $this->assertFalse(file_exists($file));
        $file2 = $A->getUniqueFilename($file);
        $this->assertEquals($file, $file2);    
        $file = dirname(__FILE__).'/asset_library/readme.txt';
        $file2 = $A->getUniqueFilename($file);
        $this->assertEquals(dirname(__FILE__).'/asset_library/readme 3.txt',$file2);
    }
    
    /**
     *
     *
     */
    public function testExisting() {
        $A = new Asset(self::$modx);
        $file = dirname(__FILE__).'/assets/support.jpg';
        $Asset = self::$modx->newObject('Asset');
        $C = $A->getContentType($file);
        $Asset->fromArray(array(
            'content_type_id' => $C->get('id'),
            'title' => 'Delete me',
            'alt' => 'Delete me',
            'url' => 'tmp/path/only/'.basename($file),
            'path' => 'tmp/path/only/'.basename($file),
            'sig' => md5_file($file)
        ));
        $result = $Asset->save();
        $this->assertFalse(empty($result));
        $asset_id = $Asset->getPrimaryKey();
        
        $A2 = $A->getExisting($file);
        $this->assertEquals($A2->get('asset_id'), $asset_id);
        $this->assertEquals($A2->get('title'), 'Delete me');
        $this->assertEquals($A2->get('sig'), $Asset->get('sig'));
        $A2->remove();
        $Asset->remove();
    }
 
     /**
      * This should fail because it's not a REAL uploaded file, and PHP KNOWS!!!
      * @expectedException        \Exception
      * @expectedExceptionMessage Unable to move uploaded file
      */
    public function testUploadTmp()
    {
        $Asset = new Asset(self::$modx);
        $filename = '/tmp/'.uniqid().'.moxycart.dick';
        touch($filename);
        $Asset->uploadTmp($filename, 'moxycart.dick','/tmp/does/not/matter');
    }
    
    public function testGetThumbFilename() {
        $A = new Asset(self::$modx);
        $orig = dirname(__FILE__).'/assets/support.jpg';
        $dir = $A->getThumbFilename($orig,'thumbs/',200,100);
        $this->assertEquals(dirname(__FILE__).'/assets/thumbs/support.200x100.jpg',$dir);
    }

    /**
     *
     */
    public function testThumbnail() {
        $A = new Asset(self::$modx);
        $orig = dirname(__FILE__).'/assets/support.jpg';
        Asset::rrmdir(dirname($orig).'/t2/');
        $A->getThumbnail($orig,'t2','50','45');
        $this->assertTrue(file_exists(dirname($orig).'/t2/support.50x45.jpg'));
        Asset::rrmdir(dirname($orig).'/t2/');
    }    

}