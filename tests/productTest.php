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
class productTest extends \PHPUnit_Framework_TestCase {

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
     * See http://forums.modx.com/thread/91009/xpdo-validation-rules-executing-prematurely#dis-post-498398 
     *
     */
    public function testProduct() {
/*
        $Store = self::$modx->newObject('Store');
        $Store->fromArray(array(
            'pagetitle' => 'Another Store',
            'longtitle' => 'Another Store',
            'menutitle' => 'Another Store',
            'description' => 'An example store',
            'alias' => 'another-store',
            'uri' => 'another-store/',
            'class_key' => 'Store',
            'isfolder' => 1,
            'published' => 1,
             'properties' => '{"moxycart":{"product_type":"regular","product_template":"2","sort_order":"SKU","qty_alert":"5","track_inventory":0,"fields":{"1":true,"3":true},"variations":[],"taxonomies":[]}}',
            'Template' => array('templatename' => 'Sample Store'),
        ));
        
        $Product = self::$modx->newObject('Product');
        $Product->fromArray(    array(
            'name' => 'Southpark Tshirt',
            'title' => 'Southpark Tshirt',
            'description' => '',
            'content' => '<p>Just imagine this awesome product.</p>',
            'type' => 'regular',
            'sku' => 'SOUTHPARK-TSHIRT',
            'sku_vendor' => '',
            'alias' => 'south-park-tshirt',
            'uri' => 'another-store/south-park-tshirt',
            'track_inventory' => 0,
            'qty_inventory' => 10,
            'qty_alert' => 3,
            'qty_min' => 1,
            'qty_max' => 0,
            'qty_backorder_max' => 5,
            'price' => '19.00',
            'price_strike_thru' => '25.00',
            'price_sale' => '',
            'sale_start' => '',
            'sale_end' => '',
            'category' => 'Default',
            'is_active' => 1,
            'in_menu' => 1,
            'timestamp_created' => date('Y-m-d H:i:s'),
            'currency_id' => 109,
        ));

        $result = $Store->save();
        if (!$result) {
            print 'FAILED to save!'."\n";
            $validator = $Save->getValidator();
            if ($validator->validate() == false) {
                $messages = $validator->getMessages();
                foreach ($messages as $m) {
//                        $this->errors[] = $m;
                    print_r($m);
                    //$this->errors[$m['field']] = $m['message'];
                }
            }
        }
        else {
            print "Saved Store.\n";
        }

        
        if (!$Store->addOne($Product)) {
            print "AddOne returned false\n";
            $validator = $Product->getValidator();
            if ($validator->validate() == false) {
                $messages = $validator->getMessages();
                foreach ($messages as $m) {
//                        $this->errors[] = $m;
                    print_r($m);
                    //$this->errors[$m['field']] = $m['message'];
                }
            }
        }
        $result = $Store->save();
        if (!$result) {
            print 'FAILED to re-save!'."\n";
        }
        else {
            print 'Re-Saved Store.';
        }
*/
        
/*
        $result = $Store->save();
        if (!$result) {
            print 'FAILED to save!'."\n";
            $validator = $Save->getValidator();
            if ($validator->validate() == false) {
                $messages = $validator->getMessages();
                foreach ($messages as $m) {
//                        $this->errors[] = $m;
                    print_r($m);
                    //$this->errors[$m['field']] = $m['message'];
                }
            }
        }
        else {
            print "Saved.\n";
        }
*/
    

    }    
}