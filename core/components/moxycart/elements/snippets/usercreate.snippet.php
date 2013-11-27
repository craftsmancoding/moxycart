<?php
/**
 * UserCreate
 *
 * Called as a hook from the parseFoxycartDatafeed Snippet, this hook (snippet) will
 * create a local MODX user (if none exists), keyed off the user's email.
 * If the email address already exists in the system, then no action is taken.
 *
 * We can only set the password and other info when we *create* a user to avoid 
 * a user's data being overwritten by a malicious user.
 *
 * As much as possible, customer data is stored in the default MODX columns, but 
 * some of this needs to be stored in a user's extended fields.
 *
 * Control parameters
 *
 *  moxycart.user.usergroup id of usergroup where new users should be added.
 *  moxycart.user.role id of role for new users.
 *  
    user.active whether or not new users should be activated immediately
    user.primary_group
    
 *  moxycart.user.update_profile whether or not to update user data. 
 *
 * @params everything from a <transaction> node in the Foxycart XML
 * @return string message indicating completion or false on fail.
 */
 
$log = array(
    'target'=>'FILE',
    'options' => array(
        'filename'=>'foxycart.log'
    )
);

$modx->log(xPDO::LOG_LEVEL_DEBUG,'UserCreate',$log,'UserCreate Snippet',__FILE__,__LINE__);
$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH);
$modx->addPackage('moxycart',$core_path.'components/moxycart/model/','moxy_');

/*
      <paypal_payer_id><![CDATA[]]></paypal_payer_id>
      <third_party_id><![CDATA[]]></third_party_id>
      <customer_id><![CDATA[12940181]]></customer_id>
      <is_anonymous><![CDATA[0]]></is_anonymous>
      <customer_first_name><![CDATA[Testing]]></customer_first_name>
      <customer_last_name><![CDATA[Testerson]]></customer_last_name>
      <customer_company><![CDATA[]]></customer_company>
      <customer_address1><![CDATA[833 20th St.]]></customer_address1>
      <customer_address2><![CDATA[Apt #101]]></customer_address2>
      <customer_city><![CDATA[Santa Monica]]></customer_city>
      <customer_state><![CDATA[CA]]></customer_state>
      <customer_postal_code><![CDATA[90403]]></customer_postal_code>
      <customer_country><![CDATA[US]]></customer_country>
      <customer_phone><![CDATA[]]></customer_phone>
      <customer_email><![CDATA[test11@fireproofsocks.com]]></customer_email>
      <customer_ip><![CDATA[75.68.99.192]]></customer_ip>

      <customer_password><![CDATA[X6Mc0MxBq5mnDYZGz5A0qUcEQRI555aHVIKYOU5s1NY=]]></customer_password>
      <customer_password_salt><![CDATA[b778896af4acfba66f20be655c9a0f9e]]></customer_password_salt>
      <customer_password_hash_type><![CDATA[pbkdf2]]></customer_password_hash_type>
      <customer_password_hash_config><![CDATA[1000, 32, sha256]]></customer_password_hash_config>
      
      <shipping_first_name><![CDATA[Testing]]></shipping_first_name>
      <shipping_last_name><![CDATA[Testerson]]></shipping_last_name>
      <shipping_company><![CDATA[]]></shipping_company>
      <shipping_address1><![CDATA[833 20th St.]]></shipping_address1>
      <shipping_address2><![CDATA[Apt #101]]></shipping_address2>
      <shipping_city><![CDATA[Santa Monica]]></shipping_city>
      <shipping_state><![CDATA[CA]]></shipping_state>
      <shipping_postal_code><![CDATA[90403]]></shipping_postal_code>
      <shipping_country><![CDATA[US]]></shipping_country>
      <shipping_phone><![CDATA[]]></shipping_phone>
      
*/

$email = $modx->getOption('customer_email', $scriptProperties);

$query = $modx->newQuery('modUser');
$query->where(array('Profile.email' => $email));
$User = $modx->getObjectGraph('modUser', '{"Profile":{}}',$query);

if ($User) {
    if ($modx->getOption('moxycart.user.update_profile')) {
        $modx->log(xPDO::LOG_LEVEL_DEBUG,'Updating existing user profile for user '.$User->get('id'),$log,'UserCreate Snippet',__FILE__,__LINE__); 
        $Profile = $modx->getObject('modUserProfile',array('internalKey' => $User->get('id')));
    }
    else {
        $modx->log(xPDO::LOG_LEVEL_DEBUG,'User already exists with email '.$email. ' (User id '.$User->get('id').')',$log,'UserCreate Snippet',__FILE__,__LINE__);
        return true; // fail gracefully
    }
}
else {
    $User = $modx->newObject('modUser');
    $Profile = $modx->newObject('modUserProfile');
}

// Primary User Fields
$User->set('username', $modx->getOption('customer_email',$scriptProperties));
$User->set('active', $modx->getOption('user.active',$scriptProperties));
$User->set('primary_group', $modx->getOption('user.primary_group',$scriptProperties));

// Make sure we have the correct hash type...
if ($modx->getOption('customer_password_hash_type',$scriptProperties) == 'pbkdf2') {
    $User->set('password', $modx->getOption('customer_password',$scriptProperties));
    $User->set('salt', $modx->getOption('customer_password_salt',$scriptProperties));    
    // customer_password_hash_config ??
}
// Todo: roll with this?  We need mappings between Foxycart hash types and MODX classnames
else {
    $modx->log(xPDO::LOG_LEVEL_DEBUG,'Invalid password hash type detected: '
        .$modx->getOption('customer_password_hash_type',$scriptProperties)
        . ' Please update your Foxycart advanced settings to use the pbkdf2'
        . ' hash type for MODX.'
        ,$log,'UserCreate Snippet',__FILE__,__LINE__);
}




// Secondary Fields (Profile)
$fullname = trim($modx->getOption('customer_first_name',$scriptProperties) . ' '
    . $modx->getOption('customer_last_name',$scriptProperties));
$Profile->set('fullname', $fullname);
$Profile->set('email', $modx->getOption('customer_email',$scriptProperties));
$Profile->set('phone', $modx->getOption('customer_phone',$scriptProperties));
$address = trim($modx->getOption('customer_address1',$scriptProperties)."\n"
    .$modx->getOption('customer_address2',$scriptProperties));
$Profile->set('address', $address);
$Profile->set('city', $modx->getOption('customer_city',$scriptProperties));
$Profile->set('state', $modx->getOption('customer_state',$scriptProperties));
$Profile->set('zip', $modx->getOption('customer_postal_code',$scriptProperties));
$Profile->set('country', $modx->getOption('customer_country',$scriptProperties));

// Extended fields
$extended = array();

$extended['shipping_first_name'] = $modx->getOption('shipping_first_name',$scriptProperties);
$extended['shipping_last_name'] = $modx->getOption('shipping_last_name',$scriptProperties);
$extended['shipping_company'] = $modx->getOption('shipping_company',$scriptProperties);
$extended['shipping_address1'] = $modx->getOption('shipping_address1',$scriptProperties);
$extended['shipping_address2'] = $modx->getOption('shipping_address2',$scriptProperties);
$extended['shipping_city'] = $modx->getOption('shipping_city',$scriptProperties);
$extended['shipping_state'] = $modx->getOption('shipping_state',$scriptProperties);
$extended['shipping_postal_code'] = $modx->getOption('shipping_postal_code',$scriptProperties);
$extended['shipping_country'] = $modx->getOption('shipping_country',$scriptProperties);
$extended['shipping_phone'] = $modx->getOption('shipping_phone',$scriptProperties);

$Profile->set('extended',$extended);


/*
$data = array();
foreach ($fields as $f) {
    $data[$f] = $modx->getOption($f, $scriptProperties);
}
*/

//$User->set('fullname', "{$data['']");


/*
$User = $modx->newObject('modUser');
$Profile = $modx->newObject('modUserProfile');

$User->set('username',$username);
$User->set('active',1);
$User->set('password', $password);

$Profile->set('email', $email);
$Profile->set('internalKey',0);
$User->addOne($Profile,'Profile');
*/

/*EOF*/