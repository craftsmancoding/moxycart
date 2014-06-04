<h3>xCart Database Credentials</h3>
<style>
.textlabel {
    display: block;
}
</style>
<?php
print \Formbuilder\Form::open()
    ->text('host','localhost',array('label'=>'Host Name'))
    ->text('database','',array('label'=>'Database Name'))
    ->text('user','',array('label'=>'Database User'))
    ->text('password','',array('label'=>'Database Password'))
    ->dropdown('store_id',array())
    ->close();


// Criteria for foreign Database
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'cannonlewis';
$port = 3306;
$charset = 'utf8';
 
$dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=$charset";
$xpdo = new \xPDO($dsn, $username, $password);
//print_r($xpdo); 
// Test your connection
echo $o = ($xpdo->connect()) ? 'Connected' : 'Not Connected';

// Issue queries against the foreign database:

// Pre-requisite Custom Fields
// xcart_manufacturers
if (!$MF = $this->modx->getOption('Field', array('slug'=>'manufacturer'))) {
    $manfs = $xpdo->query("SELECT * FROM xcart_manufacturers ORDER BY orderby"); 
    $vals = array();
    foreach($manfs as $m) {
        $key = strtolower(str_replace(' ', '_', $m['manufacturer']));
        $vals[ $key ] = $m['manufacturer'];
    }
    $MF = $this->modx->newOption('Field');
    $MF->set('slug', 'manufacturer');
    $MF->set('label', 'Manufacturer');
    $MF->set('description', 'Imported from xCart: Manufacturer');
    $MF->set('type', 'dropdown');
    $MF->set('config', json_encode($vals));
    $MF->save();
}

// xcart_extra_fields
$extrafields = $xpdo->query("SELECT * FROM xcart_extra_fields ORDER BY orderby"); 
foreach($extrafields as $x) {
    if (!$EF = $this->modx->getObject('Field', array('slug'=> $x['service_name']))) {
        $EF = $this->modx->newObject('Field');
        $EF->set('slug', $x['service_name']);
        $EF->set('label', $x['field']);
        if ($x['active'] == 'Y') {
            $EF->set('is_active', true);
        }
        else {
            $EF->set('is_active', false);
        }
        $EF->set('description', 'Imported from xCart');
        $EF->save();
    }
}





$products = $xpdo->query("SELECT * FROM xcart_products"); 
//$recordCount = $results->rowCount();
//print $recordCount;
foreach ($products as $r) {
//    print $r['product'] ."<br/>";
    if ($P = $this->modx->getObject('Product', array('sku' => $r['productcode']))) {
        printf('Product with SKU %s already exists in the local database. Skipping...<br/>',$r['productcode']);
        continue;
    }
    $P = $this->modx->newObject('Product');
    $P->set('name', $r['product']);
    $P->set('sku', $r['productcode']);
    $P->set('weight', $r['weight']);
    $P->set('price', $r['list_price']);
   // forsale : Y (yes) N (no) H (yes, but hidden)
    if ($r['forsale'] == 'Y') {
        $P->set('is_active', true);
        $P->set('in_menu', true);
    }
    elseif ($r['forsale'] == 'N') {
        $P->set('is_active', false);
        $P->set('in_menu', true);    
    }
    elseif ($r['forsale'] == 'H') {
        $P->set('is_active', true);
        $P->set('in_menu', false);    
    }
    $P->set('description', $r['descr']);
    $P->set('meta_keywords', $r['meta_keywords']);
    $P->set('content', $r['fulldescr']);
    $P->set('qty_inventory', $r['avail']);
    $P->set('qty_min', $r['min_amount']);
    $P->set('qty_alert', $r['low_avail_limit']);
    $P->set('', $r['']);
    $P->set('title', $r['title_tag']);
    // Manufacturer
    // $r['manufacturerid']
    // xcart_extra_field_values 
    
    // Tags?
    
    // Images
    
}

//------------------------------------------------------------------------------
//! Users
//------------------------------------------------------------------------------
// xcart_customers

?>