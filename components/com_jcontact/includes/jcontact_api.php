<?php
defined('_JEXEC') or die( 'Restricted access' );
error_reporting(E_ALL ^ E_NOTICE);
define('STATUS_CODE_SUCCESS', 200);
class ICJApi
{
    public $username;
    public $password;
    public $api_key;
    public $token;
    public $seq;
    public $base;
    public $authenicated;
    public $method;
    public $api_url;
    public $data;
    public $appId;

    public function __construct ()
    {
        //$this->set_api_url('https://app.sandbox.icontact.com/icp');
    }
    // get functions
    public function get_api_url ()
    {
        return $this->api_url;
    }
    public function get_data ()
    {
        return $this->data;
    }
    public function get_username ()
    {
        return $this->username;
    }
    public function get_password ()
    {
        return $this->password;
    }
    public function get_api_key ()
    {
        return $this->api_key;
    }
    public function get_token ()
    {
        return $this->token;
    }
    public function get_seq ()
    {
        return $this->seq;
    }
    public function get_base ()
    {
        return $this->base;
    }
    public function get_method ()
    {
        return $this->method;
    }
    public function get_authenticated ()
    {
        return $this->authenticated;
    }
    // Set functions
    public function set_api_url ($a_api_url)
    {
        $this->api_url = $a_api_url;
    }
    public function set_appId ($appId)
    {
        $this->appId = $appId;
    }
    public function set_username ($a_username)
    {
        $this->username = $a_username;
    }
    public function set_password ($a_password)
    {
        $this->password = $a_password;
    }
    public function set_api_key ($a_api_key)
    {
        $this->api_key = $a_api_key;
    }
    public function set_token ($a_token)
    {
        $this->token = $a_token;
    }
    public function set_seq ($a_seq)
    {
        $this->seq = $a_seq;
    }
    public function set_base ($a_base)
    {
        $this->base = $a_base;
    }
    public function set_method ($a_method)
    {
        $this->method = $a_method;
    }
    public function set_data ($a_data)
    {
        $this->data = $a_data;
    }
    public function set_authenticated ($a_authenticated)
    {
        $this->authenticated = $a_authenticated;
    }
    // increment seq number
    public function increment_seq ()
    {
        $this->seq ++;
    }
    //Debug
    public function dump ($item, $type = 'print_r')
    {
        echo '<pre>';
        $type($item);
        echo '</pre>';
    }
    // try to authenticate in IntelliContact
    // returns true in success, else - false
    public function callResource ()
    {
        $url = $this->api_url . $this->base;
        $handle = curl_init();
		
        $headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
			'Api-Version: 2.0',
			'Api-AppId: ' . $this->api_key,
			'Api-Username: ' . $this->username,
			'Api-Password: ' . $this->password,
		);
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        if ($this->method == 'POST') {
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($this->data));
        }
        if ($this->method == 'PUT') {
            curl_setopt($handle, CURLOPT_PUT, true);
            $file_handle = fopen($data, 'r');
            curl_setopt($handle, CURLOPT_INFILE, $file_handle);
        }
        if ($this->method == 'DELETE') {
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        $response = curl_exec($handle);
        $response = json_decode($response, true);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        return (count($response['errors']) === 0) ? array('code' => $code , 'data' => $response) : false;
    }
}

function get_account_id() {
    // temp xml file name
    global $mainframe;
    $mailfrom = $mainframe->getCfg('mailfrom');
    $fromname = $mainframe->getCfg('fromname');
    $sitename = $mainframe->getCfg('live_site');
    $db = & JFactory::getDBO();
    $db->setQuery("SELECT * FROM #__jcontact_config LIMIT 1");
    $rows = $db->loadObjectList();
    if ($db->getErrorNum()) {
        echo $db->stderr();
        return false;
    }
    $row = & $rows[0];
    $regon = $row->regon;
    $maillist = $row->maillist;
    $username = $row->username;
    $password = $row->password;
    $apikey = $row->apikey;
    $apiUrl = $row->apiUrl;
    $wrapperurl = $row->wrapperurl;
    $mailsubj = $row->mailsubj;
    $mailcont = $row->mailcont;
    // register in IntelliContact only if registration is turned on
    if ($regon) {
        // calls to api functions
	    $api = new ICJApi();
	    $api->set_username($username);
	    $api->set_password($password);
	    $api->set_api_key($apikey);
	    $api->set_api_url($apiUrl);
	    $api->set_base("/a");
		$api->set_method('GET');
        $response = $api->callResource();
        if ($response['code'] == STATUS_CODE_SUCCESS) {
            $response = $response['data']['accounts'][0]['accountId'];
        } else {
            echo $api_error = "Authentication error";
        }
    }
    return $response;
}

function get_client_folder_id() {
    // temp xml file name
    global $mainframe;
    $mailfrom = $mainframe->getCfg('mailfrom');
    $fromname = $mainframe->getCfg('fromname');
    $sitename = $mainframe->getCfg('live_site');
    $db = & JFactory::getDBO();
    $db->setQuery("SELECT * FROM #__jcontact_config LIMIT 1");
    $rows = $db->loadObjectList();
    if ($db->getErrorNum()) {
        echo $db->stderr();
        return false;
    }
    $row = & $rows[0];
    $regon = $row->regon;
    $maillist = $row->maillist;
    $username = $row->username;
    $password = $row->password;
    $apikey = $row->apikey;
    $apiUrl = $row->apiUrl;
    $wrapperurl = $row->wrapperurl;
    $mailsubj = $row->mailsubj;
    $mailcont = $row->mailcont;
    // register in IntelliContact only if registration is turned on
    if ($regon) {
        // calls to api functions
	    $api = new ICJApi();
	    $api->set_username($username);
	    $api->set_password($password);
	    $api->set_api_key($apikey);
	    $api->set_api_url($apiUrl);
	    $api->set_base("/a/".get_account_id().'/c');
		$api->set_method('GET');
        $response = $api->callResource();
        if ($response['code'] == STATUS_CODE_SUCCESS) {
            $response = $response['data']['clientfolders'][0]['clientFolderId'];
        } else {
            echo $api_error = "Authentication error";
        }
    }
    return $response;
}

function get_icontact_lists ()
{
    // temp xml file name
    $temp_xml_file = "tmp/ic_temp.xml";
    global $mainframe;
    $mailfrom = $mainframe->getCfg('mailfrom');
    $fromname = $mainframe->getCfg('fromname');
    $sitename = $mainframe->getCfg('live_site');
    $db = & JFactory::getDBO();
    $db->setQuery("SELECT * FROM #__jcontact_config LIMIT 1");
    $rows = $db->loadObjectList();
    if ($db->getErrorNum()) {
        echo $db->stderr();
        return false;
    }
    $row = & $rows[0];
    $regon = $row->regon;
    $maillist = $row->maillist;
    $username = $row->username;
    $password = $row->password;
    $apikey = $row->apikey;
    $apiUrl = $row->apiUrl;
    $wrapperurl = $row->wrapperurl;
    $mailsubj = $row->mailsubj;
    $mailcont = $row->mailcont;
    $list_icontact = array();
    // register in IntelliContact only if registration is turned on
    if ($regon) {
        // calls to api functions
	    $api = new ICJApi();
	    $api->set_username($username);
	    $api->set_password($password);
	    $api->set_api_key($apikey);
	    $api->set_api_url($apiUrl);
	    $api->set_base("/a/".get_account_id()."/c/".get_client_folder_id()."/lists");
		$api->set_method('GET');
        $response = $api->callResource();
        if ($response['code'] == STATUS_CODE_SUCCESS) {
            $counter = 0;
            foreach ($response['data']['lists'] as $list) {
                $list_icontact[$counter]->value = $list['listId'];
                $list_icontact[$counter]->text = $list['name'];
                $counter ++;
            }
        } else {
            echo $api_error = "Authentication error";
        }
    }
    return $list_icontact;
}

function jcontact_user_regi ($a_name, $a_email)
{
    // temp xml file name
    $temp_xml_file = "tmp/ic_temp.xml";
    global $mainframe;
    $mailfrom = $mainframe->getCfg('mailfrom');
    $fromname = $mainframe->getCfg('fromname');
    $sitename = JURI::base();
    $db = & JFactory::getDBO();
    $db->setQuery("SELECT * FROM #__jcontact_config LIMIT 1");
    $rows = $db->loadObjectList();
    if ($db->getErrorNum()) {
        echo $db->stderr();
        return false;
    }
    $row = & $rows[0];
    $regon = $row->regon;
    $maillist = $row->maillist;
    $username = $row->username;
    $password = $row->password;
    $apikey = $row->apikey;
    $apiUrl = $row->apiUrl;
    $wrapperurl = $row->wrapperurl;
    $mailsubj = $row->mailsubj;	
    $mailcont = $row->mailcont;
    // register in IntelliContact only if registration is turned on
    if ($regon) {
        //igo method					
        $names = explode(' ', $a_name);
		
		$api = new ICJApi();
	    $api->set_username($username);
	    $api->set_password($password);
	    $api->set_api_key($apikey);
	    $api->set_api_url($apiUrl);
	    $api->set_base("/a/".get_account_id()."/c/".get_client_folder_id()."/contacts");
		$api->set_method('POST');
		$api->set_data(array(array('firstName' => $names[0] , 'lastName' => $names[1] , 'email' => $a_email)));
        $response = $api->callResource();
        $contactId = $response['data']['contacts'][0]['contactId'];

		$api->set_base("/a/".get_account_id()."/c/".get_client_folder_id()."/subscriptions");
		$api->set_data(array(array('contactId' => $contactId , 'listId' => $maillist , 'status' => 'normal')));
        $response = $api->callResource();
		return ($response['code'] == STATUS_CODE_SUCCESS)?true:false;
    }
}

function unsubscribe ($e)
{
    if ($email) {
        $email = $e;
    } else {
        $email = JRequest::getVar('email', null, 'get', 'string');
    } // Lets get the email.
    $lid = JRequest::getVar('lid', null, 'get', 'string');
    $check = JRequest::getVar('check', null, 'get', 'string');
    // get settings from DB
    global $db;
    $db = & JFactory::getDBO();
    $db->setQuery("SELECT * FROM #__jcontact_config LIMIT 1");
    $rows = $db->loadObjectList();
    if ($db->getErrorNum()) {
        echo $db->stderr();
        return false;
    }
    $row = & $rows[0];
    $regon = $row->regon;
    $maillist = $row->maillist;
    $username = $row->username;
    $password = $row->password;
    $apikey = $row->apikey;
    $apiUrl = $row->apiUrl;
    $wrapperurl = $row->wrapperurl;
    $mailsubj = $row->mailsubj;
    $mailcont = $row->mailcont;
    // calls to api functions
    $api = new ICJApi();
    $api->set_username($username);
    $api->set_password($password);
    $api->set_api_key($apikey);
    $api->set_api_url($apiUrl);
	$api->set_method('GET');
    $email222 = & JFactory::getUser();
	$mail = $email222->email;
    $api->set_base("/a/".get_account_id()."/c/".get_client_folder_id()."/contacts" . "?email=$mail");
    $response = $api->callResource();
    if ($response['code'] == STATUS_CODE_SUCCESS) {
        $contactId = $response['data']['contacts'][0]['contactId'];
    } else {
        echo "<h1>Error 1.0</h1>\n";
    }

	$api->set_method('POST');
	$api->set_base("/a/".get_account_id()."/c/".get_client_folder_id()."/contacts/{$contactId}");
	$api->set_data(array('status' => 'donotcontact'));
    $response = $api->callResource();
    if ($response['code'] == STATUS_CODE_SUCCESS) {
        echo "<h1>Success</h1>\n";
    } else {
        echo "Error while processing unsubscription.";
    }
}