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

$store_id = 234;

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
$o = ($xpdo->connect()) ? true : false;

if (!$o) {
    print 'Not connected to database.';
    return;
}
$this->modx->setLogLevel(3);
// Issue queries against the foreign database:

// Pre-requisite Custom Fields
// xcart_manufacturers as a  custom field?
/*
if (!$MF = $this->modx->getObject('Field', array('slug'=>'manufacturer'))) {
    $manfs = $xpdo->query("SELECT * FROM xcart_manufacturers ORDER BY orderby"); 
    $vals = array();
    foreach($manfs as $m) {
        $key = strtolower(str_replace(' ', '_', $m['manufacturer']));
        $vals[ $key ] = $m['manufacturer'];
    }
    $MF = $this->modx->newObject('Field');
    $MF->set('slug', 'manufacturer');
    $MF->set('label', 'Manufacturer');
    $MF->set('description', 'Imported from xCart: Manufacturer');
    $MF->set('type', 'dropdown');
    $MF->set('config', json_encode($vals));
    $MF->save();
}
*/
// as a taxonomy
if (!$M = $this->modx->getObject('Taxonomy', array('pagetitle'=>'Manufacturer','class_key'=>'Taxonomy'))) {
    $M = $this->modx->newObject('Taxonomy', array(
        'pagetitle'=>'Manufacturer',
        'class_key'=>'Taxonomy',
        'alias' => 'manufacturer',
        'published' => true
    ));
    if(!$M->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving Manufacturer taxonomy','','xcart'); 
    }
}
$manfs = $xpdo->query("SELECT * FROM xcart_manufacturers ORDER BY orderby"); 
$vals = array();
$map['xcart_manufacturers'] = array();
foreach($manfs as $m) {
    $key = strtolower(str_replace(' ', '_', $m['manufacturer']));    
    if (!$Term = $this->modx->getObject('Term', array('pagetitle'=>$m['manufacturer'],'class_key'=>'Term','parent'=>$M->get('id')))) {
        $Term = $this->modx->newObject('Term');
    }
    $Term->fromArray(array(
        'parent'=> $M->get('id'),
        'pagetitle'=>$m['manufacturer'],
        'description' => $m['descr'],
        'class_key'=>'Term',
        'alias' => $key,
        'published' => true
    ));
    if (!$Term->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving manufacturer Term:'.$m['manufacturer'],'','xcart'); 
    }
    
    $map['xcart_manufacturers'][ $m['manufacturerid'] ] = $Term->get('id');    
}


// xcart_extra_fields
// xcart-id --> modx id
$map['xcart_extra_fields'] = array();
$extrafields = $xpdo->query("SELECT * FROM xcart_extra_fields ORDER BY orderby"); 
$seq = 0;
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
    }
    $EF->set('seq', $seq);
    if(!$EF->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving Field for :'.$x['service_name'],'','xcart'); 
    }
    $seq++;

    $map['xcart_extra_fields'][ $x['fieldid'] ] = $EF->get('field_id');
}


$products = $xpdo->query("SELECT * FROM xcart_products"); 
//$recordCount = $results->rowCount();
//print $recordCount;
foreach ($products as $r) {
//    print $r['product'] ."<br/>";
    if (!$P = $this->modx->getObject('Product', array('sku' => $r['productcode']))) {
        //printf('Product with SKU %s already exists in the local database. Skipping...<br/>',$r['productcode']);
        $P = $this->modx->newObject('Product');
    }

    $P->set('store_id', $store_id);
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
    $P->set('title', $r['title_tag']);
    
    if (!$P->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving product!','','xcart');  
        continue;
    }
    $product_id = $P->getPrimaryKey();

    // Manufacturer
    if ($r['manufacturerid']) {

        $term_id = (isset($map['xcart_manufacturers'][ $r['manufacturerid'] ])) ? $map['xcart_manufacturers'][ $r['manufacturerid'] ] : false;
        if ($term_id) {        
            if (!$PT = $this->modx->getObject('ProductTerm', array('product_id'=>$product_id,'term_id'=>$term_id))) {
                $PT = $this->modx->newObject('ProductTerm', array('product_id'=>$product_id,'term_id'=>$term_id));
                if (!$PT->save()) {
                    $this->modx->log(\modX::LOG_LEVEL_ERROR,'Error saving manufacturer ProductTerm','','xcart');          
                }
            }
        }
  
    }

    // xcart_extra_field_values 
    $extra_field_values = $xpdo->query("SELECT * FROM xcart_extra_field_values WHERE productid={$r['productid']}");         
    foreach($extra_field_values as $xfv) {
        if (isset($map['xcart_extra_fields'][ $xfv['fieldid'] ])) {
            $field_id = $map['xcart_extra_fields'][ $xfv['fieldid'] ];
            if (!$PF = $this->modx->getObject('ProductField', array('product_id'=>$product_id, 'field_id'=> $field_id))) {
                $PF = $this->modx->newObject('ProductField', array('product_id'=>$product_id, 'field_id'=> $field_id));
            }
            $PF->set('value', $xfv['value']);
            if (!$PF->save()) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving ProductField','','xcart');          
            }
        }
        else {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Could not look up extra field value','','xcart');          
        }
    }
    
    // Tags?
    
    // Assets
    
}


//------------------------------------------------------------------------------
//! Users
//------------------------------------------------------------------------------
// Create a user group just for the imported records
if (!$UG = $this->modx->getObject('modUserGroup', array('name'=>'Customer'))) {
    $UG = $this->modx->newObject('modUserGroup', array('name'=>'Customer'));
    $UG->set('description', 'Imported from xCart');
    if(!$UG->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving modUserGroup "Customer"!','','xcart');  
    }
}
$this->modx->log(\modX::LOG_LEVEL_INFO,'Customer modUserGroup: '.$UG->get('id'),'','xcart');  

// Get a role
if(!$Role = $this->modx->getObject('modUserGroupRole',array('name'=>'Member'))) {
    $Role = $this->modx->createObject('modUserGroupRole',array('name'=>'Member'));
    $Role->set('name', 'Member');
    $Role->set('description', 'Created for xCart import');
    $Role->set('authority',9999);
    if(!$Role->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving role (modUserGroupRole) "Member"!','','xcart'); 
    }
}
$this->modx->log(\modX::LOG_LEVEL_INFO,'Customer modUserGroupRole: '.$Role->get('id'),'','xcart');  

// xcart_customers
$customers = $xpdo->query("SELECT * FROM xcart_customers"); 
//$customers = $xpdo->query("SELECT * FROM xcart_address_book"); 
foreach ($customers as $c) {
    if (!$c['username']) continue;
    if (!$U = $this->modx->getObject('modUser',array('username'=>$c['username']))) {
        $U = $this->modx->newObject('modUser',array('username'=>$c['username']));
        $Profile = $this->modx->newObject('modUserProfile');
        $Profile->set('email', $c['email']);
        $Profile->set('fullname', $c['firstname'].' '.$c['lastname']);
        $U->addOne($Profile);        
        if (!$U->save()) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving User: '.$c['username'],'','xcart'); 
        }
    }

    if (!$UGM = $this->modx->getObject('modUserGroupMember', array('member'=>$U->get('id'), 'user_group'=>$UG->get('id')))) {
        $UGM = $this->modx->newObject('modUserGroupMember');
        $UGM->set('user_group', $UG->get('id'));
        $UGM->set('role', $Role->get('id'));
        $UGM->set('member', $U->get('id'));
        $U->addMany($UGM);
        if(!$UGM->save()) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem adding User '.$c['username'] .' to Group','','xcart'); 
        }
    }

}


?>