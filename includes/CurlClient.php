<?php
class CurlClient
{
	public static $forwiki = false;
	
	public $ch;
	public $base_url = ''; 
	public $last_header = '';
	public $last_body = '';
	public $last_content_type = '';
	
	public $last_request_method = '';
	public $last_request_headers = array();
	public $last_request_data = '';
	public $last_request_uri = '';
	public $last_response = null;

	public function  __construct($base_url = '') {
		$this->base_url = $base_url;
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->ch, CURLOPT_HEADER, TRUE);
		curl_setopt($this->ch, CURLOPT_FRESH_CONNECT, TRUE);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	}
	public function  __destruct()
	{
	    curl_close($this->ch);
	}
	function setBaseURL($base_url)
	{
	    $this->base_url = $base_url;
	}
	function post($uri,$fields = null,$headers = null)
	{
	    return $this->go('POST',$headers,$fields,$uri);
	}
	function get($uri,$fields = null,$headers = null)
	{
	    return $this->go('GET',$headers,$fields,$uri);
	}
	function delete($uri,$fields = null,$headers = null)
	{
	    return $this->go('DELETE',$headers,$fields,$uri);
	}
	function put($uri,$fields = null,$headers = null)
	{
	    return $this->go('PUT',$headers,$fields,$uri);
	}
	function patch($uri,$fields = null,$headers = null)
	{
	    return $this->go('PATCH',$headers,$fields,$uri);
	}
	function go($method,$headers,$fields,$uri)
	{
	    if (is_null($headers)) {
	        $headers = array();
	    }
	    if ($method == 'GET') {
	        if ($fields && $fields != '') {	        
    	        if(strpos($uri, '?') !== false) {
    	            $uri .= '&' . $fields;
    	        } else {
    	            $uri .= '?' . $fields;
    	        }
	        }
	        $fields = null;
	    }
	    $this->last_request_method = $method;
		$this->last_request_headers = $headers;
		$this->last_request_data = $fields;
		$this->last_request_uri = $uri;
		if ($method == 'PATCH') {
			$headers[] = 'X-HTTP-Method-Override: ' . $method; 
			$method = 'POST';
		}
		
		curl_setopt($this->ch, CURLOPT_URL, $this->base_url . $uri);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
   		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($this->ch,CURLOPT_HTTPHEADER,$headers);
		$response = curl_exec($this->ch);
		list($this->last_header, $this->last_body) = explode("\r\n\r\n", $response, 2);
		if (preg_match('/(Content-Type: )(.*$)/m',$this->last_header,$matches)) {
		    $this->last_content_type = $matches[2];
		}
		$resp = array();
		$info = curl_getinfo($this->ch);
        $resp['meta'] = $this->last_header;
		$resp['body'] = $this->getData();
		$resp['error'] = curl_errno($this->ch);
		$resp['error_msg'] = curl_error($this->ch);
		$resp['status'] = $info['http_code'];
		$resp['headers'] = $this->getHeadersArray();
		
		$this->last_response = $resp;
		
		return $resp;
	}
	
	/**
	 * Borrowed from Resty
	 * @link https://github.com/fictivekin/resty.php
	 */
	function getHeadersArray()
	{
	    $headers = array();
	    preg_match("|^([^:]+):\s?(.+)$|", $this->last_header, $matches);
	    if (is_array($matches) && isset($matches[2])) {
	        $header_key = trim($matches[1]);
	        $header_value = trim($matches[2], " \t\n\r\0\x0B\"");
	        if (isset($headers[$header_key])) {
	            if (is_array($headers[$header_key])) {
	                $headers[$header_key][] = $header_value;
	            } else {
	                $previous_entry = $headers[$header_key];
	                $headers[$header_key] = array($previous_entry, $header_value);
	            }
	        } else {
	            $headers[$header_key] = $header_value;
	        }
	    }
	    return $headers;
	}

	function getLink($rel)
	{
	    $linkObj = new stdClass();
	    $linkObj->link = '';
	    $linkObj->type = '';
		if (preg_match('|(link: <)(.*)(>;rel="' . $rel . '";)(.*)(;type=")(.*)(")|', $this->last_header, $matches)) {
		    $linkObj->link = $matches[2];
		    $linkObj->type = $matches[6];
		}
		return $linkObj;
	}
	function getLocation()
	{
		if (preg_match('/(location: )(.*)/', $this->last_header, $matches)) {
			return trim($matches[2]);
		}
		return '';
	}
	function isXMLContentType()
	{
		return (strpos($this->last_content_type,'xml') !== false);
	}
	function isJSONContentType()
	{
		return (strpos($this->last_content_type,'json') !== false);
	}
	function getData()
	{
	    if ($this->last_content_type == '') {
	        return $this->last_body;
	    }
	    if ($this->last_body != '') {
	        if ($this->isXMLContentType()) {
    			$xmlObj = new \SimpleXMLElement($this->last_body);
    			return $xmlObj;
    		}
    		if ($this->isJSONContentType()) {
    			return json_decode($this->last_body);
    		}
		} else {
		    return null;
		}
	}
}