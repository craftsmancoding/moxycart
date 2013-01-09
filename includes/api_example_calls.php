<?php
require_once('CurlClient.php');

function getHeaders($content_type = 'application/vnd.foxycart.com+xml',$token = '') {
	//$headers = array('Accept: ' . $content_type, 'X-API-VERSION: 1');
//	$headers = array('Accept: ' . $content_type, 'FOXYCART-API-VERSION: 1');
	$headers = array('Accept: ' . $content_type); // , 'FOXYCART-API-VERSION: 1');

	$headers[] = 'FOXYCART-API-VERSION: 1';
	
	if ($token) {
		$headers[] = 'Authorization: Bearer '. $token;
	}
	return $headers;
}
function displayResult($client, $forwiki = false) {
	$display_uri = $client->last_request_uri;
	
	$curl_command = 'curl';
	$curl_command .= ' -X ' . $client->last_request_method;
	$data = '';
	if (count($client->last_request_data)) {
	    $data = is_array($client->last_request_data) ? http_build_query($client->last_request_data) : $client->last_request_data;
	    if ($client->last_request_method != 'GET') {
		    $curl_command .= ' -d "' . $data . '"';
	    } else {
	        $display_uri .= '?' . $data;
	    }
	}
	foreach($client->last_request_headers as $header) {
		$curl_command .= ' -H "' . $header . '"';
	}
	$curl_command .= ' ' . $display_uri;
	if ($forwiki) {
		print "\n\n ===== " . $client->last_request_method . ": $display_uri ===== \n<code>";
	} else {
		print $client->last_request_method . ': ' . $display_uri . "\n";
	}
	if (!$forwiki) {
		print '<br />';
	}
	if ($data != '') {
		print 'DATA: ' . $data . "\n";
	}
	if (!$forwiki) {
		print '<br />';
	}
	print 'HEADERS: ' . print_r($client->last_request_headers,true) .  "\n";
	if (!$forwiki) {
		print '<br />';
		print '<pre>';
	}
	print $curl_command;
	if (!$forwiki) {
		print '</pre>' . "\n";
		print '::: Result :::' . "\n";
	} else {
		print "</code>\n == Result Header == \n<code>";
	}
	if (!$forwiki) {
		print '<br />';
	}
	$meta = htmlspecialchars($client->last_header);
	if (!$forwiki) {
		print "<pre>\n";
	}
	if (!$forwiki) {
	    print nl2br($meta) . "\n<br />";
	} else {
	    print $meta . "\n";
	}
	if (!$forwiki) {
		print '</pre>' . "\n";
	}
	if ($forwiki) {
		print "</code>\n == Result Body == \n<code javascript>";
	}
	if (!$forwiki) {
		print '<pre>' . htmlspecialchars($client->last_body) . "</pre>\n";
	} else {
		print $client->last_body;
	}
	if ($forwiki) {
		print '</code>';
	}
}

class MyClient extends CurlClient
{
    public function getRel($rel) {
        $registered_link_relations_in_use = array('self','first','prev','next','last');
        if ($rel == 'rels') {
//            return 'https://api.foxycart.com/' . $rel;
            return 'https://api-sandbox.foxycart.com/' . $rel;
        }
        if (!in_array($rel, $registered_link_relations_in_use)) {
            $rel = 'https://api.foxycart.com/rels/'.$rel;
        }
        return $rel;
    }
    
    public function getLink($rel)
    {
        $rel = $this->getRel($rel);
        $linkObj = parent::getLink($rel);
        return $linkObj;
    }
    
    public function getData()
    {
        // since all of our XML responses have a container node, we ditch it
        $obj = parent::getData();
        if ($this->isXMLContentType()) {
            return $obj->children()->children();
        }
        return $obj;
    }
    
}

$api_home_page = 'https://api-sandbox.foxycart.com';
$client = new \MyClient();
$content_type = 'application/vnd.foxycart.com+json';
$forwiki = false;
if (isset($_GET['type']) && in_array($_GET['type'],array('json','xml'))) {
	$content_type = 'application/vnd.foxycart.com+' . $_GET['type'];
}
if (isset($_GET['forwiki'])) {
	$forwiki = true;
	print '<textarea>';
}

// home page
$resp = $client->get($api_home_page,null,getHeaders($content_type));
displayResult($client,$forwiki);


/*
$create_client_link = $client->getLink('create_client');
$resources_link = $client->getLink('resources');
$rel_link = $client->getLink('rels');

// resources
$resp = $client->get($resources_link->link,null,getHeaders($resources_link->type));
displayResult($client,$forwiki);
$states_link = $client->getLink('store_states');

// resources, states
$resp = $client->get($states_link->link,'country_code=CA',getHeaders($states_link->type));
displayResult($client,$forwiki);

// rels
$resp = $client->get($rel_link->link,null,getHeaders($rel_link->type));
displayResult($client,$forwiki);

// rels, client
$resp = $client->get($rel_link->link . '/client',null,getHeaders($rel_link->type));
displayResult($client,$forwiki);

// create client, no data sent
$resp = $client->post($create_client_link->link,null,getHeaders($create_client_link->type));
displayResult($client,$forwiki);

// create client
$resp = $client->post($create_client_link->link,'redirect_uri=http://example.com&project_name=my_project_' . rand() . '&project_description=some_awesome_project&company_name=foobar&contact_name=me&contact_email=test@example.com&contact_phone=123456789',getHeaders($create_client_link->type));
displayResult($client,$forwiki);
$client_token = $resp['body']->token->access_token;

// home page with client auth
$resp = $client->get($api_home_page,null,getHeaders($content_type,$client_token));
displayResult($client,$forwiki);
$next_link = $client->getLink('client');

// client
$resp = $client->get($next_link->link,null,getHeaders($next_link->type,$client_token));
displayResult($client,$forwiki);
$next_link = $client->getLink('create_user');

// create user, no data sent
$resp = $client->post($next_link->link,null,getHeaders($next_link->type,$client_token));
displayResult($client,$forwiki);

// create user
$resp = $client->post($next_link->link,'first_name=foo&last_name=bar&email=example_' . rand() . '@example.com',getHeaders($next_link->type,$client_token));
displayResult($client,$forwiki);
$user_token = $resp['body']->token->access_token;
$user_uri = $client->getLocation();
$next_link = $client->getLink('self');

// user
$resp = $client->get($user_uri,null,getHeaders($next_link->type,$client_token));
displayResult($client,$forwiki);
$next_link = $client->getLink('create_store');
$user_attributes_link = $client->getLink('attributes');

// create store, no data sent
$resp = $client->post($next_link->link,null,getHeaders($next_link->type,$user_token));
displayResult($client,$forwiki);

// create store, no state
$store_domain = 'foo' . rand() . 'bar';
$resp = $client->post($next_link->link,'store_name=foo&store_domain=' . $store_domain . '&store_url=http://example.com&store_email=example@example.com&store_postal_code=92646&store_country=US',getHeaders($next_link->type,$user_token));
displayResult($client,$forwiki);

// create store
$resp = $client->post($next_link->link,'store_name=foo&store_domain=' . $store_domain . '&store_url=http://example.com&store_email=example@example.com&store_postal_code=92646&store_country=US&store_state=CA',getHeaders($next_link->type,$user_token));
displayResult($client,$forwiki);
$store_token = $resp['body']->token->access_token;
$store_uri = $client->getLocation();
$next_link = $client->getLink('self');

// store
$resp = $client->get($store_uri,null,getHeaders($next_link->type,$store_token));
displayResult($client,$forwiki);

// create a user attribute, no data sent
$resp = $client->post($user_attributes_link->link,null,getHeaders($user_attributes_link->type,$user_token));
displayResult($client,$forwiki);

// create a user attribute
$resp = $client->post($user_attributes_link->link,'name=test_attribute_' . rand() . '&value=awesome',getHeaders($user_attributes_link->type,$user_token));
displayResult($client,$forwiki);
$attribute_uri = $client->getLocation();
$next_link = $client->getLink('self');

// user attribute
$resp = $client->get($attribute_uri,null,getHeaders($next_link->type,$user_token));
displayResult($client,$forwiki);

// PATCH user attribute
$resp = $client->patch($next_link->link,'value=awesomer',getHeaders($next_link->type,$user_token));
displayResult($client,$forwiki);

if ($forwiki) {
	print '</textarea>';
}
*/