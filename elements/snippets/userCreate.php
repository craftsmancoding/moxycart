<?php
/**
 * @name userCreate
 * @description Called as a hook from the parseFoxycartDatafeed Snippet, if no matching user exists, this hook will create a local MODX user keyed off the user's email.
 * 
 * If the email address already exists in the system, then no action is taken.
 *
 * We can only set the password and other info when we *create* a user to avoid 
 * a user's data being overwritten by a malicious user.
 *
 * As much as possible, customer data is stored in the default MODX columns, but 
 * some of this needs to be stored in a user's extended fields.
 *
 *
 * INPUT
 *  $scriptProperties : mostly this is data pulled from a Foxycart XML <transaction> node, 
 *  with keys/values matching the tags in that node, e.g.
 *

        SAMPLE CUSTOMER XML SECTION

          <paypal_payer_id><![CDATA[]]></paypal_payer_id>
          <third_party_id><![CDATA[]]></third_party_id>
          <customer_id><![CDATA[12940181]]></customer_id>
          <is_anonymous><![CDATA[0]]></is_anonymous>
          <customer_first_name><![CDATA[John]]></customer_first_name>
          <customer_last_name><![CDATA[Doe]]></customer_last_name>
          <customer_company><![CDATA[]]></customer_company>
          <customer_address1><![CDATA[123 Main St.]]></customer_address1>
          <customer_address2><![CDATA[Apt #456]]></customer_address2>
          <customer_city><![CDATA[Anywhere]]></customer_city>
          <customer_state><![CDATA[CA]]></customer_state>
          <customer_postal_code><![CDATA[98765]]></customer_postal_code>
          <customer_country><![CDATA[US]]></customer_country>
          <customer_phone><![CDATA[]]></customer_phone>
          <customer_email><![CDATA[someone@somewhere.com]]></customer_email>
          <customer_ip><![CDATA[123.23.12.123]]></customer_ip>
    
          <customer_password><![CDATA[XxxxyyyyHHHH111122223333=]]></customer_password>
          <customer_password_salt><![CDATA[bbbb77779999ddddeeeefffff]]></customer_password_salt>
          <customer_password_hash_type><![CDATA[pbkdf2]]></customer_password_hash_type>
          <customer_password_hash_config><![CDATA[1000, 32, sha256]]></customer_password_hash_config>
          
          <shipping_first_name><![CDATA[Testing]]></shipping_first_name>
          <shipping_last_name><![CDATA[Testerson]]></shipping_last_name>
          <shipping_company><![CDATA[]]></shipping_company>
          <shipping_address1><![CDATA[876 Other St.]]></shipping_address1>
          <shipping_address2><![CDATA[Suite B]]></shipping_address2>
          <shipping_city><![CDATA[Elsewhere]]></shipping_city>
          <shipping_state><![CDATA[GA]]></shipping_state>
          <shipping_postal_code><![CDATA[78998]]></shipping_postal_code>
          <shipping_country><![CDATA[US]]></shipping_country>
          <shipping_phone><![CDATA[]]></shipping_phone>
      
 
 * Control parameters
 *
 * Control the behavior of how your users are added to the site via these System Settings:
 *
 *  moxycart.user_group:  id of usergroup where new users should be added 
 *  moxycart.user_role: id of role for new users.
 *  moxycart.user_activate: whether or not new users should be activated immediately
 *  moxycart.user_update:  whether or not to update user data when with new data received via postback
 *
 * TODO:   user.primary_group ???
 *
 * @params everything from a <transaction> node in the Foxycart XML
 * @return string message indicating completion or false on fail.
 */

$core_path = $modx->getOption('moxycart.core_path', null, MODX_CORE_PATH.'components/moxycart/');
require_once $core_path .'vendor/autoload.php';
$Snippet = new \Moxycart\Snippet($modx);
$Snippet->log('userCreate',$scriptProperties);
 
$log = array(
    'target'=>'FILE',
    'options' => array(
        'filename'=>'foxycart.log'
    )
);

// Get System Settings
$user_group = $modx->getOption('moxycart.user_group');
$user_role = $modx->getOption('moxycart.user_role');
$user_activate = $modx->getOption('moxycart.user_activate');
$user_update = $modx->getOption('moxycart.user_update');

// Validate settings
$UserGroup = $modx->getObject('modUserGroup', $user_group);
if ($UserGroup->get('id') != $user_group) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Invalid moxycart.user_group. modUserGroup '.$user_group.' not found.',$log,'userCreate Snippet',__FILE__,__LINE__);
    return false;
}
$Role = $modx->getObject('modUserGroupRole', $user_role);
if ($Role->get('id') != $user_role) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Invalid moxycart.user_role. modUserGroupRole '.$user_role.' not found.',$log,'userCreate Snippet',__FILE__,__LINE__);
    return false;
}


$email = $modx->getOption('customer_email', $scriptProperties);

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Invalid customer_email. Could not create user.',$log,'userCreate Snippet',__FILE__,__LINE__);
    return false;
}

$query = $modx->newQuery('modUser');
$query->where(array('username' => $email));
$User = $modx->getObject('modUser', $query);

if ($User) {
    if ($user_update) {
        $modx->log(xPDO::LOG_LEVEL_DEBUG,'Updating existing user profile for user '.$User->get('id'),$log,'userCreate Snippet',__FILE__,__LINE__); 
        $Profile = $modx->getObject('modUserProfile',array('internalKey' => $User->get('id')));
    }
    else {
        $modx->log(xPDO::LOG_LEVEL_DEBUG,'User already exists with email '.$email. ' (User id '.$User->get('id').')',$log,'userCreate Snippet',__FILE__,__LINE__);
        return true; // Done!
    }
}
else {
    $User = $modx->newObject('modUser');
    $Profile = $modx->newObject('modUserProfile');
    $User->set('active', $user_activate);
    $User->set('primary_group', 1); // todo
}


// Primary User Fields
$User->set('username', $modx->getOption('customer_email',$scriptProperties));


// Make sure we have the correct hash type...
if ($modx->getOption('customer_password_hash_type',$scriptProperties) == 'pbkdf2') {
    // We gotta use fromArray to tie into the rawValues
    $User->fromArray(array(
        'password' => $modx->getOption('customer_password',$scriptProperties),
        'salt' => $modx->getOption('customer_password_salt',$scriptProperties)
    ),'',false,true);
    // fromArray($fldarray, $keyPrefix= '', $setPrimaryKeys= false, $rawValues= false, $adhocValues= false) {

    //$User->set('password', $modx->getOption('customer_password',$scriptProperties));
    //$User->set('salt', $modx->getOption('customer_password_salt',$scriptProperties));    
    // customer_password_hash_config ??
}
// Todo: roll with this?  We need mappings between Foxycart hash types and MODX classnames
else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Invalid password hash type detected: '
        .$modx->getOption('customer_password_hash_type',$scriptProperties)
        . ' Please update your Foxycart advanced settings to use the pbkdf2'
        . ' hash type for MODX Revolution.'
        ,$log,'userCreate Snippet',__FILE__,__LINE__);
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

$User->addOne($Profile);

if (!$User->save()) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Error saving User '.$User->get('username'),$log,'userCreate Snippet',__FILE__,__LINE__);
    return false;
}

return true;

/*EOF*/