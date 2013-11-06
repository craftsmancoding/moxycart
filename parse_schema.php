<?php
// Built to live inside assets/mycomponents/moxycart/ :
require_once '../../../config.core.php';
require_once MODX_CORE_PATH . 'config/config.inc.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modx();

// http://rtfm.modx.com/display/revolution20/Creating+a+Resource+Class
//------------------------------------------------------------------------------
//! CONFIGURATION
//------------------------------------------------------------------------------
// Your package shortname:
$package_name = 'moxycart';
 
// Set this to false if you've started to customize the PHP classes, otherwise
// your changes will be overwritten!
$regenerate_classes = true;
 
$my_table_prefix='moxy_';

//$adjusted_core_path = MODX_CORE_PATH;
$adjusted_core_path = MODX_BASE_PATH.'assets/mycomponents/moxycart/core/';

//------------------------------------------------------------------------------
//  DO NOT TOUCH BELOW THIS LINE
//------------------------------------------------------------------------------
 
if (!defined('MODX_CORE_PATH')) {
    print_msg('<h1>Parsing Error</h1>
        <p>MODX_CORE_PATH not defined! Did you include the correct config file?</p>');
    exit;
}


$xpdo_path = strtr(MODX_CORE_PATH . 'xpdo/xpdo.class.php', '\\', '/');
include_once ( $xpdo_path );
  
// A few definitions of files/folders:
$package_dir = $adjusted_core_path . "components/$package_name/";
$model_dir = $adjusted_core_path . "components/$package_name/model/";
$class_dir = $adjusted_core_path . "components/$package_name/model/$package_name";
$schema_dir = $adjusted_core_path . "components/$package_name/model/schema";
$mysql_class_dir = $adjusted_core_path . "components/$package_name/model/$package_name/mysql";
$xml_schema_file = $adjusted_core_path . "components/$package_name/model/schema/$package_name.mysql.schema.xml";
  
// A few variables used to track execution times.
$mtime= microtime();
$mtime= explode(' ', $mtime);
$mtime= $mtime[1] + $mtime[0];
$tstart= $mtime;
  
// Validations
if ( empty($package_name) ) {
    print_msg('<h1>Parsing Error</h1>
        <p>The $package_name cannot be empty!  Please adjust the configuration and try again.</p>');
    exit;
}
  
// Create directories if necessary
$dirs = array($package_dir, $schema_dir ,$mysql_class_dir, $class_dir);
  
foreach ($dirs as $d) {
    if ( !file_exists($d) ) {
        if ( !mkdir($d, 0777, true) ) {
            print_msg( sprintf('<h1>Parsing Error</h1>
                <p>Error creating <code>%s</code></p>
                <p>Create the directory (and its parents) and try again.</p>'
                , $d
            ));
            exit;
        }
    }
    if ( !is_writable($d) ) {
        print_msg( sprintf('<h1>Parsing Error</h1>
            <p>The <code>%s</code> directory is not writable by PHP.</p>
            <p>Adjust the permissions and try again.</p>'
        , $d));
        exit;
    }
}
  
 
print_msg( sprintf('<br/><strong>Ok:</strong> The necessary directories exist and have the correct permissions inside of <br/>
        <code>%s</code>', $package_dir));
  
if (file_exists($xml_schema_file)) {
    print_msg( sprintf('<br/><strong>Ok:</strong> Using existing XML schema file:<br/><code>%s</code>',$xml_schema_file));
}
 
$xpdo = new xPDO("mysql:host=$database_server;dbname=$dbase", $database_user, $database_password);
$xpdo->setLogLevel(xPDO::LOG_LEVEL_INFO);
$xpdo->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
 
$manager = $xpdo->getManager();
$generator = $manager->getGenerator();
 
// Use this to generate classes and maps from your schema
if ($regenerate_classes) { 
    print_msg('<br/>Attempting to remove/regenerate class files...');
    delete_class_files($class_dir);
    delete_class_files($mysql_class_dir);
}

$generator->parseSchema($xml_schema_file,$model_dir);

if(!$xpdo->addPackage('moxycart',$adjusted_core_path.'components/moxycart/model/',$my_table_prefix)) {
    return 'Package Error.';
}       


//$xpdo->addExtensionPackage('moxycart',"{$adjusted_core_path}components/$package_name/model/");

// Clear out Tables
print '<h3>Dropping Tables...</h3>';
$manager->removeObjectContainer('Currency');
$manager->removeObjectContainer('Product');
$manager->removeObjectContainer('Spec');
$manager->removeObjectContainer('VariationType'); 
$manager->removeObjectContainer('VariationTerm');
$manager->removeObjectContainer('ProductVariationTypes');
$manager->removeObjectContainer('ProductTaxonomy');
$manager->removeObjectContainer('ProductTerms');
$manager->removeObjectContainer('ProductSpecs');
$manager->removeObjectContainer('Cart');
$manager->removeObjectContainer('Image');

// Re-create them
print '<h3>Creating Tables...<h3>';
$manager->createObjectContainer('Currency');
$manager->createObjectContainer('Product');
$manager->createObjectContainer('Spec');
$manager->createObjectContainer('VariationType'); 
$manager->createObjectContainer('VariationTerm');
$manager->createObjectContainer('ProductVariationTypes');
$manager->createObjectContainer('ProductTaxonomy');
$manager->createObjectContainer('ProductTerms');
$manager->createObjectContainer('ProductSpecs');
$manager->createObjectContainer('Cart');
$manager->createObjectContainer('Image');



// Seed Data
$data_src_dir = '_build/data/moxycart/';
print '<h3>Seeding Data...</h3>';



$currencies = include $data_src_dir . 'transport.currencies.php';
if (is_array($currencies)) {
    print '<h4>Table: currencies</h4>';
    foreach($currencies as $c) {
        $Currency = $xpdo->newObject('Currency');
        $Currency->fromArray($c);
        if (!$Currency->save()) {
            print "Error saving currency {$c['code']}!<br/>";
        }
        else {
            print "Currency created {$c['code']}<br/>";
        }
    }
}
else {
    print 'ERROR: $currencies not an array.<br/>';
}

$specs = include $data_src_dir . 'transport.specs.php';
if (is_array($specs)) {
    print '<h4>Table: specs</h4>';
    foreach($specs as $s) {
        $Spec = $xpdo->newObject('Spec');
        $Spec->fromArray($s);
        if (!$Spec->save()) {
            print "Error saving spec {$s['name']}!<br/>";
        }
        else {
            print "Spec created {$s['name']}<br/>";
        }
    }
}
else {
    print 'ERROR: $specs not an array.<br/>';
}

$variation_types = include $data_src_dir . 'transport.variationtypes.php';
if (is_array($variation_types)) {
    print '<h4>Table: variation_types</h4>';
    foreach($variation_types as $v) {
        $VT = $xpdo->newObject('VariationType');
        $VT->fromArray($v);
        if (!$VT->save()) {
            print "Error saving variation_type {$v['name']}!<br/>";
        }
        else {
            print "Variation Type created {$v['name']}<br/>";
        }
    }
}
else {
    print 'ERROR: $variation_types not an array.<br/>';
}
    

$variation_terms = include $data_src_dir . 'transport.variationterms.php';
if (is_array($variation_terms)) {
    print '<h4>Table: variation_terms</h4>';
    foreach($variation_terms as $v) {
        $VT = $xpdo->newObject('VariationTerm');
        $VT->fromArray($v);
        if (!$VT->save()) {
            print "Error saving variation_term {$v['name']}!<br/>";
        }
        else {
            print "Variation Term created {$v['name']}<br/>";
        }
    }
}
else {
    print 'ERROR: $variation_terms not an array.<br/>';
}

$products = include $data_src_dir . 'transport.products.php';
if (is_array($products)) {
    print '<h4>Table: products</h4>';
    foreach($products as $p) {
        $Product = $xpdo->newObject('Product');
        $Product->fromArray($p);
        if (!$Product->save()) {
            print "Error saving product {$p['name']}!<br/>";
        }
        else {
            print "Product created {$p['name']}<br/>";
        }
    }
}
else {
    print 'ERROR: $products not an array.<br/>';
}

$images = include $data_src_dir . 'transport.images.php';
if (is_array($images)) {
    print '<h4>Table: images</h4>';
    foreach($images as $i) {
        $Img = $xpdo->newObject('Image');
        $Img->fromArray($i);
        if (!$Img->save()) {
            print "Error saving image {$i['title']}!<br/>";
        }
        else {
            print "Image created {$i['title']}<br/>";
        }
    }
}
else {
    print 'ERROR: $images not an array.<br/>';
}



$taxonomies = include $data_src_dir . 'transport.taxonomies.php';
if (is_array($taxonomies)) {
    print '<h4>Table: modx_site_content</h4>';
    foreach($taxonomies as $i) {
        $taxonomy = $modx->newObject('modResource');
        $taxonomy->fromArray($i);
        if (!$taxonomy->save()) {
            print "Error saving Taxonomy {$i['pagetitle']}!<br/>";
        }
        else {
            print "Taxonomy created {$i['pagetitle']}<br/>";
        }
    }
}
else {
    print 'ERROR: $taxonomies not an array.<br/>';
}


$terms = include $data_src_dir . 'transport.terms.php';
if (is_array($terms)) {
    print '<h4>Table: modx_site_content</h4>';
    foreach($terms as $i) {
        $term = $modx->newObject('modResource');
        $term->fromArray($i);
        if (!$term->save()) {
            print "Error saving Term {$i['pagetitle']}!<br/>";
        }
        else {
            print "Term created {$i['pagetitle']}<br/>";
        }
    }
}
else {
    print 'ERROR: $terms not an array.<br/>';
}




$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);
 
print_msg("<br/><br/><strong>Finished!</strong> Execution time: {$totalTime}<br/>");
print_msg("<br/>Check <code>$class_dir</code> for your newly generated class files!"); 
exit();
 
//------------------------------------------------------------------------------
//! FUNCTIONS
//------------------------------------------------------------------------------
/**
 * Deletes the MODX class files in a given directory.
 *
 * @param string $dir: full path to directory containing class files you wish to delete.
 * @return void
 */
function delete_class_files($dir) {
    global $verbose;
  
    $all_files = scandir($dir);
    foreach ( $all_files as $f ) {

        if ( preg_match('#\.class\.php$#i', $f) || preg_match('#\.map\.inc\.php$#i', $f)) {
            if (in_array(basename($f),array('moxycart.class.php','taxonomyparents.class.php',
                'termparents.class.php','store.class.php','taxonomy.class.php','term.class.php'))) {
                continue; // skip
            } 

            if ( unlink("$dir/$f") ) {
                if ($verbose) {
                    print_msg( sprintf('<br/>Deleted file: <code>%s/%s</code>',$dir,$f) );
                }
            }
            else {
                print_msg( sprintf('<br/>Failed to delete file: <code>%s/%s</code>',$dir,$f) );
            }
        }
    }
}
 
/**
 * Formats/prints messages. HTML is stripped if this is run via the command line.
 *
 * @param string $msg to be printed
 * @return void this actually prints data to stdout
 */
function print_msg($msg) {
    if ( php_sapi_name() == 'cli' ) {
        $msg = preg_replace('#<br\s*/>#i', "\n", $msg);
        $msg = preg_replace('#<h1>#i', '== ', $msg);
        $msg = preg_replace('#</h1>#i', ' ==', $msg);
        $msg = preg_replace('#<h2>#i', '=== ', $msg);
        $msg = preg_replace('#</h2>#i', ' ===', $msg);
        $msg = strip_tags($msg) . "\n";
    }
    print $msg;
}