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
 
class rc4cryptTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testRC4() {
        $RC4 = new rc4crypt();
        $this->assertTrue(is_object($RC4), 'rc4crypt class not instantiated.');
    }
    
    /** 
     * Just making @#$%!#@$ sure this is working.
     */
    public function testEncrypt() {
        $password = 'test password';
        $data = 'Some random data.';
        
        $encrypted = rc4crypt::encrypt ($password, $data);
        $unencrypted = rc4crypt::decrypt($password, $encrypted);

        $this->assertEquals($data,$unencrypted);

        $unencrypted = rc4crypt::decrypt('notpassword', $encrypted);
        $this->assertNotEquals($data,$unencrypted);
    }

}