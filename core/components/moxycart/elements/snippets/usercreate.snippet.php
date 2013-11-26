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
 * Related system settings:
 *
 *  moxycart.user.usergroup id of usergroup where new users should be added.
 *  moxycart.user.role id of role for new users.
 *  moxycart.user.blocked whether or not new users should be blocked (1) or active (0) 
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


$fields = array('customer_first_name','customer_last_name','customer_company','customer_address1',
'customer_address2', 'customer_city','customer_state','customer_postal_code','customer_country','customer_phone',
'customer_email','customer_ip');
$data = array();
foreach ($fields as $f) {
    $data[$f] = $modx->getOption($f, $scriptProperties);
}

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