<div class="moxycart_canvas_inner">
    <h2 class="moxycart_cmp_heading" id="moxycart_pagetitle">Migrate xCart to Moxycart</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder"><p>Custom Field fields allow you to enter extra data about your product.  The values stored can be displayed to visitors via your product templates.</p></div>

<div class="moxycart_canvas_inner">

<?php
/**
 * Import an xCart install into Moxycart
 *
 * TODO:
 * memberships
 * features
 * offers
 * orders (?? foxycart??)
 * pages (to regular MODX page?)
 * partner_*  (ads?)
 * pricing / variants 
 * reviews
 * taxes (foxycart?)
 * votes (reviews?)
 * translations (???)
 * referrers
 */
?>

<style>
.textlabel {
    display: block;
}
</style>
<?php
\Formbuilder\Form::setValues($data);
\Formbuilder\Form::setTpl('description','<p class="description-txt">[+description+]</p>');
print \Formbuilder\Form::open(self::page('xcart'))
    ->html('<h4>Database Credentials</h4>')
    ->text('host','localhost',array('label'=>'Host Name','class'=>'input input-half'))
    ->text('database','',array('label'=>'Database Name','class'=>'input input-half','placeholder'=>'db_name'))
    ->text('user','',array('label'=>'Database User','class'=>'input input-half','placeholder'=>'db_user'))
    ->text('password','',array('label'=>'Database Password','class'=>'input input-half','placeholder'=>'password'))
    ->text('port',3306,array('label'=>'Port','class'=>'input input-half'))
    ->html('<h4>Product Defaults</h4>')
    ->dropdown('store_id',$data['stores'],'',array('label'=>'Parent Store'))
    ->dropdown('template_id',$data['templates'],'',array('label'=>'Product Template'))    
//    ->dropdown('user_group_id',array())    
    ->html('<h4>Assets</h4>')
    ->text('image_path',MODX_BASE_PATH,array('label'=>'Image Path',
        'description'=>'Full path to where you have copied your xCart image folder. This folder should contain sub-directories C, D, G, etc. This directory must be readable by PHP.',
        'style'=>'width:300px;',
        'class'=>'input input-half'
    ),'[+label+]
        [+error+]
        <input type="text" name="[+name+]" id="[+id+]" value="[+value+]" class="[+class+]" style="[+style+]" placeholder="[+placeholder+]" size="250" [+extra+]/>
        [+description+]')
    ->submit('', 'Migrate',array('class'=>'btn'))
    ->close();
    
if (empty($_POST)) {
    return;
}

$image_path = rtrim($data['image_path'],'/').'/';
$store_id = $data['store_id'];
$template_id = $data['template_id'];

// Criteria for foreign Database
$host = $data['host'];
$username = $data['user'];
$password = $data['password'];
$dbname = $data['database'];
$port = $data['port'];
$charset = 'utf8';
 
$dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=$charset";
$xpdo = new \xPDO($dsn, $username, $password);

$this->modx->setLogLevel(3);
$this->modx->setLogTarget('HTML');

// Test your connection
$o = ($xpdo->connect()) ? true : false;

if (!$o) {
    $this->modx->log(\modX::LOG_LEVEL_ERROR,'Could not connect to database.','','xcart',__LINE__);  
    $this->modx->log(\modX::LOG_LEVEL_ERROR,"credentials\n"
        .'host: '.$host."\n"
        .'username: '.$username."\n"
        .'database: '.$password."\n"
        .'port: '.$port
    ,'','xcart',__LINE__);      
    return;
}

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
//! Manufacturers (taxonomy)
$this->modx->log(\modX::LOG_LEVEL_INFO,'=========== Beginning Import: Manufacturer Taxonomy =============','','xcart',__LINE__);  
if (!$M = $this->modx->getObject('Taxonomy', array('pagetitle'=>'Manufacturer','class_key'=>'Taxonomy'))) {
    $M = $this->modx->newObject('Taxonomy', array(
        'pagetitle'=>'Manufacturer',
        'class_key'=>'Taxonomy',
        'alias' => 'manufacturer',
        'published' => true
    ));
    if(!$M->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving Manufacturer taxonomy','','xcart',__LINE__);  
    }
}
else {
    $this->modx->log(\modX::LOG_LEVEL_INFO,'Existing "Manufacturer" Taxonomy detected: '.$M->get('id'),'','xcart',__LINE__);  
}

//! Manufacturers (Terms)
$this->modx->log(\modX::LOG_LEVEL_INFO,'=========== Beginning Import: Manufacturer Terms =============','','xcart',__LINE__);  
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
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving manufacturer Term:'.$m['manufacturer'],'','xcart',__LINE__);  
    }
    else {
        $this->modx->log(\modX::LOG_LEVEL_INFO,'Taxonomy Term Created/Updated for "'.$m['manufacturer'].'": '.$Term->get('id'),'','xcart',__LINE__);  
    }
    
    $map['xcart_manufacturers'][ $m['manufacturerid'] ] = $Term->get('id');    
}

//! Category (taxonomy)
$this->modx->log(\modX::LOG_LEVEL_INFO,'=========== Beginning Import: Categories Taxonomy =============','','xcart',__LINE__);  
if (!$Category = $this->modx->getObject('Taxonomy', array('pagetitle'=>'Categories','class_key'=>'Taxonomy'))) {
    $Category = $this->modx->newObject('Taxonomy', array(
        'pagetitle'=>'Categories',
        'class_key'=>'Taxonomy',
        'alias' => 'categories',
        'published' => true
    ));
    if(!$Category->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving Categories taxonomy','','xcart',__LINE__);  
    }
}
else {
    $this->modx->log(\modX::LOG_LEVEL_INFO,'Existing "Categoreis" Taxonomy detected: '.$Category->get('id'),'','xcart',__LINE__);  
}


//! Categories (Terms)
// xcart_categories
//$map['xcart_categories'] = array();
$this->modx->log(\modX::LOG_LEVEL_INFO,'=========== Beginning Import: Categories Terms =============','','xcart',__LINE__);  
$cats = $xpdo->query("SELECT * FROM xcart_categories ORDER BY order_by"); 
$map['xcart_categories'] = array();
foreach($cats as $c) {
    $key = strtolower(str_replace(' ', '_', $c['category']));    
    if (!$Term = $this->modx->getObject('Term', array('pagetitle'=>$c['category'],'class_key'=>'Term','parent'=>$Category->get('id')))) {
        $Term = $this->modx->newObject('Term');
    }
    $Term->fromArray(array(
        'parent'=> $Category->get('id'),
        'pagetitle'=>$c['category'],
        'description' => $c['meta_description'],
        'introtext' => $c['description'],
        'class_key'=>'Term',
        'alias' => $key,
        'published' => true
    ));
    if (!$Term->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving category Term:'.$c['category'],'','xcart',__LINE__);  
    }
    else {
        $this->modx->log(\modX::LOG_LEVEL_INFO,'Taxonomy Term Created/Updated for "'.$c['category'].'": '.$Term->get('id'),'','xcart',__LINE__);  
    }
    
    $map['xcart_categories'][ $c['categoryid'] ] = $Term->get('id');    
}


//! Extra Fields
// xcart_extra_fields
// xcart-id --> modx id
$this->modx->log(\modX::LOG_LEVEL_INFO,'=========== Beginning Import: Extra Fields =============','','xcart',__LINE__);  
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
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving Field for :'.$x['service_name'],'','xcart',__LINE__);  
    }
    else {
        $this->modx->log(\modX::LOG_LEVEL_INFO,'Custom Field Created/Updated for "'.$x['service_name'].'" --> Moxycart field_id: '.$EF->get('field_id'),'','xcart',__LINE__);      
    }
    $seq++;

    $map['xcart_extra_fields'][ $x['fieldid'] ] = $EF->get('field_id');
}




//! Products
$this->modx->log(\modX::LOG_LEVEL_INFO,'=========== Beginning Import Products =============','','xcart',__LINE__);  
$map['xcart_products'] = array();
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
    $P->set('template_id', $template_id);    
    
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
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving product '.$P->get('name'),'','xcart',__LINE__);   
        continue;
    }
    else {
        $this->modx->log(\modX::LOG_LEVEL_INFO,'Product Created/Updated "'.$P->get('name').'" --> Moxycart product_id: '.$P->get('product_id'),'','xcart',__LINE__);   
    }
    $product_id = $P->getPrimaryKey();
    $map['xcart_products'][ $r['productid'] ] = $product_id;
    
    // Manufacturer
    $this->modx->log(\modX::LOG_LEVEL_INFO,'**** Starting Manufacturer lookup...','','xcart',__LINE__);           
    if ($r['manufacturerid']) {
        $term_id = (isset($map['xcart_manufacturers'][ $r['manufacturerid'] ])) ? $map['xcart_manufacturers'][ $r['manufacturerid'] ] : false;
        if ($term_id) {        
            if (!$PT = $this->modx->getObject('ProductTerm', array('product_id'=>$product_id,'term_id'=>$term_id))) {
                $PT = $this->modx->newObject('ProductTerm', array('product_id'=>$product_id,'term_id'=>$term_id));            
            }
            if (!$PT->save()) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'Error saving manufacturer ProductTerm','','xcart',__LINE__);           
            }
            else {
                $this->modx->log(\modX::LOG_LEVEL_INFO,'ProductTerm (Manufacturer) Created/Updated "'.$P->get('name').'" --> term_id: '.$term_id,'','xcart',__LINE__);  
            }
        }
        else {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'No mapping for xcart_manufacturers: '.$r['manufacturerid'],'','xcart',__LINE__);           
        }
  
    }

    // xcart_extra_field_values 
    $this->modx->log(\modX::LOG_LEVEL_INFO,'****  Starting Extra Fields lookup...','','xcart',__LINE__);               
    $extra_field_values = $xpdo->query("SELECT * FROM xcart_extra_field_values WHERE productid={$r['productid']}");         
    foreach($extra_field_values as $xfv) {
        if (isset($map['xcart_extra_fields'][ $xfv['fieldid'] ])) {
            $field_id = $map['xcart_extra_fields'][ $xfv['fieldid'] ];
            if (!$PF = $this->modx->getObject('ProductField', array('product_id'=>$product_id, 'field_id'=> $field_id))) {
                $PF = $this->modx->newObject('ProductField', array('product_id'=>$product_id, 'field_id'=> $field_id));
            }
            $PF->set('value', $xfv['value']);
            if (!$PF->save()) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving ProductField','','xcart',__LINE__);           
            }
            else {
                $this->modx->log(\modX::LOG_LEVEL_INFO,'ProductField Created/Updated field_id: '.$PF->get('field_id').' value: '.$PF->get('value'),'','xcart',__LINE__);  
            }
        }
        else {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'No mapping for xcart_extra_fields: '. $xfv['fieldid'],'','xcart',__LINE__);           
        }
    }
    
    // Categories
    // xcart_products_categories
    $this->modx->log(\modX::LOG_LEVEL_INFO,'Starting Categories lookup...','','xcart',__LINE__);           
    $products_categories = $xpdo->query("SELECT * FROM xcart_products_categories WHERE productid={$r['productid']}");         
    foreach($products_categories as $pc) {
        if (isset($map['xcart_categories'][ $pc['categoryid'] ])) {
            $term_id = $map['xcart_categories'][ $pc['categoryid'] ];
            if ($term_id) {        
                if (!$PT = $this->modx->getObject('ProductTerm', array('product_id'=>$product_id,'term_id'=>$term_id))) {
                    $PT = $this->modx->newObject('ProductTerm', array('product_id'=>$product_id,'term_id'=>$term_id));            
                }
                if (!$PT->save()) {
                    $this->modx->log(\modX::LOG_LEVEL_ERROR,'Error saving Category ProductTerm','','xcart',__LINE__);           
                }
                else {
                    $this->modx->log(\modX::LOG_LEVEL_INFO,'ProductTerm (Category) Created/Updated "'.$P->get('name').'" --> term_id: '.$term_id,'','xcart',__LINE__);  
                }
            }
            else {
                $this->modx->log(\modX::LOG_LEVEL_ERROR,'No mapping for xcart_manufacturers: '.$r['manufacturerid'],'','xcart',__LINE__);           
            }
        }
        else {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'No mapping for xcart_categories: '.$pc['categoryid'],'','xcart',__LINE__);           
        }
    }

    
}

//------------------------------------------------------------------------------
//! Assets
//------------------------------------------------------------------------------
$this->modx->log(\modX::LOG_LEVEL_INFO,'=========== Beginning Image Migration =============','','xcart',__LINE__);  
$image_tables = array('xcart_images_B','xcart_images_C','xcart_images_D','xcart_images_F','xcart_images_G','xcart_images_L',
    'xcart_images_M','xcart_images_P','xcart_images_S','xcart_images_T','xcart_images_W','xcart_images_Z');
$this->modx->log(\modX::LOG_LEVEL_INFO,'=========== Beginning Import: Images =============','','xcart',__LINE__);  
$prefix = './images/';
foreach ($image_tables as $tbl) {
    $imgs = $xpdo->query("SELECT * FROM {$tbl} ORDER BY id, orderby");         
    foreach ($imgs as $i) {
        $xcart_productid = $i['id'];
        if (!isset($map['xcart_products'][$xcart_productid])) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'No mapping for xcart_products: '.$xcart_productid. ' Skipping image.','','xcart',__LINE__);           
            continue;
        }
        $src = $image_path. substr($i['image_path'], strlen($prefix));
        if (!file_exists($src)) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Image file not found: '.$src . '(table: $tbl imageid: '.$i['imageid'].')','','xcart',__LINE__);           
            continue;        
        }
        
        $FILE = array(
            'tmp_name' => $src,
            'name' => $i['filename'],
            'alt' => $i['alt'],
            'title' => $i['alt'],
            'is_active' => ($i['avail'] == 'Y') ? true : false, 
        );
        
        $Asset = new \Moxycart\Asset($this->modx);
        
        try {        
            $Asset = $Asset->fromFile($FILE);
            $this->modx->log(\modX::LOG_LEVEL_INFO,'Asset Created/Updated: '.$Asset->get('asset_id'),'','xcart',__LINE__);  
        }
        catch (\Exception $e) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Could not create Asset from file: '.$e->getMessage(),'','xcart',__LINE__);             
        }
        
        
    }
}


//------------------------------------------------------------------------------
//! Users
//------------------------------------------------------------------------------

// Create a user group just for the imported records
if (!$UG = $this->modx->getObject('modUserGroup', array('name'=>'Customer'))) {
    $UG = $this->modx->newObject('modUserGroup', array('name'=>'Customer'));
    $UG->set('description', 'Imported from xCart');
    if(!$UG->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving modUserGroup "Customer"!','','xcart',__LINE__);   
    }
}
$this->modx->log(\modX::LOG_LEVEL_INFO,'Customer modUserGroup: '.$UG->get('id'),'','xcart',__LINE__);   

// Get a role
if(!$Role = $this->modx->getObject('modUserGroupRole',array('name'=>'Member'))) {
    $Role = $this->modx->createObject('modUserGroupRole',array('name'=>'Member'));
    $Role->set('name', 'Member');
    $Role->set('description', 'Created for xCart import');
    $Role->set('authority',9999);
    if(!$Role->save()) {
        $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving role (modUserGroupRole) "Member"!','','xcart',__LINE__);  
    }
}
$this->modx->log(\modX::LOG_LEVEL_INFO,'Customer modUserGroupRole: '.$Role->get('id'),'','xcart',__LINE__);   

//! Customers
// xcart_customers
$this->modx->log(\modX::LOG_LEVEL_INFO,'=========== Beginning Import: Customers =============','','xcart',__LINE__);  
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
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem saving User: '.$c['username'],'','xcart',__LINE__);  
        }
    }

    if (!$UGM = $this->modx->getObject('modUserGroupMember', array('member'=>$U->get('id'), 'user_group'=>$UG->get('id')))) {
        $UGM = $this->modx->newObject('modUserGroupMember');
        $UGM->set('user_group', $UG->get('id'));
        $UGM->set('role', $Role->get('id'));
        $UGM->set('member', $U->get('id'));
        $U->addMany($UGM);
        if(!$UGM->save()) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR,'Problem adding User '.$c['username'] .' to Group','','xcart',__LINE__);  
        }
    }

}

$this->modx->log(\modX::LOG_LEVEL_INFO,'=========== XCART IMPORT COMPLETE =============','','xcart',__LINE__);  

?>
</div>