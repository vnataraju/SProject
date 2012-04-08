<?php

/**
 * MyBlog
 * @package MyBlog
 * @copyright (C) 2006 - 2008 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 * */
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

require_once('twitter/tmhOAuth.php');

class MYSocialMedia_Twitter {

    protected $message;
    protected $link;
    protected $tweetConsumerKey;
    protected $tweetConsumerSecret;
    protected $tweetUserToken;
    protected $tweetUserSecret;
    protected $OAuth;

    public function __construct() {
        global $_MY_CONFIG;
        $this->tweetConsumerKey = $_MY_CONFIG->get('tweetConsumerKey');
        $this->tweetConsumerSecret = $_MY_CONFIG->get('tweetConsumerSecret');
        $this->tweetUserToken = $_MY_CONFIG->get('tweetUserToken');
        $this->tweetUserSecret = $_MY_CONFIG->get('tweetUserSecret');

        $this->OAuth = new tmhOAuth(array(
                    'consumer_key' => $this->tweetConsumerKey,
                    'consumer_secret' => $this->tweetConsumerSecret,
                ));
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setLink($link){
        $this->link = $link;
    }

    public function post() {
        $oauth_token = myGetOauth();
        $tweetUserToken = $oauth_token[0]->user_token;
        $tweetUserSecret = $oauth_token[0]->user_token_secret;

        $connection = new tmhOAuth(array(
                    'consumer_key' => $this->tweetConsumerKey,
                    'consumer_secret' => $this->tweetConsumerSecret,
                    'user_token' => $tweetUserToken,
                    'user_secret' => $tweetUserSecret,
                ));

        // Make the API call
        $connection->request('POST',
                $connection->url('1/statuses/update'),
                array('status' => $this->message.' '.$this->link));;
        return $connection->response['code'];
    }

    public function getConfiguration() {
        global $_MY_CONFIG;

        $callback = JURI::root() . 'index.php?option=com_myblog&task=oauth';
        $configuration = array(
            'version' => '1.0',
            'requestScheme' => '',
            'signatureMethod' => 'HMAC-SHA1',
            'oauth_callback' => $callback,
            'requestTokenUrl' => 'https://api.twitter.com/oauth/request_token',
            'authorizeUrl' => 'https://api.twitter.com/oauth/authorize',
            'accessTokenUrl' => 'https://api.twitter.com/oauth/access_token',
            'consumerKey' => $_MY_CONFIG->get('tweetConsumerKey'),
            'consumerSecret' => $_MY_CONFIG->get('tweetConsumerSecret')
        );

        return $configuration;
    }

    public function auth() {
        global $_MY_CONFIG;

        $callback = JURI::root() . 'index.php?option=com_myblog&task=bloggerintegration&app=twitter&action=access_token';
        //$callback = isset($_REQUEST['oob']) ? 'oob' : $here;

        $params = array(
            'oauth_callback' => $callback
        );

        if (isset($_REQUEST['force_write'])) :
            $params['x_auth_access_type'] = 'write';
        elseif (isset($_REQUEST['force_read'])) :
            $params['x_auth_access_type'] = 'read';
        endif;

        $code = $this->OAuth->request('POST', $this->OAuth->url('oauth/request_token', ''), $params);

        // check auth ok or not
        if ($code == 200) {
            $oauth_reponse = $this->OAuth->extract_params($this->OAuth->response['response']);
            $_SESSION['oauth'] = $oauth_reponse;
            $method = isset($_REQUEST['authenticate']) ? 'authenticate' : 'authorize';
            $force = isset($_REQUEST['force']) ? '&force_login=1' : '';
            $authurl = $this->OAuth->url("oauth/{$method}", '') . "?oauth_token={$oauth_reponse['oauth_token']}{$force}";
            $return = $authurl;
        } else {
            $return = false;
        }

        return $return;
    }

    public function request_token() {
        $callback = JURI::root() . 'index.php?option=com_myblog&task=bloggerintegration&app=twitter&action=access_token';

        $code = $this->OAuth->request('POST', $this->OAuth->url('oauth/request_token', ''), array(
                    'oauth_callback' => $callback,
                ));

        $oauth_creds = array();
        if ($code == '200') {
            $oauth_creds = $this->OAuth->extract_params($this->OAuth->response['response']);
            $_SESSION['oauth'] = $oauth_creds;
            $this->request_auth();
        }

        return $oauth_creds;
    }

    public function request_auth($oauth_token) {
        $authurl = $this->OAuth->url("oauth/authorize", '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}";
        header("Location: {$authurl}");
    }

    public function request_access_token($oauth_verifier) {
        $callback = JURI::root() . 'index.php?option=com_myblog&task=bloggerintegration&app=twitter&action=verify_twitter&Itemid='.myGetItemId();
        $this->OAuth->config['user_token'] = $_SESSION['oauth']['oauth_token'];
        $this->OAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];
        $code = $this->OAuth->request(
                        'POST',
                        $this->OAuth->url('oauth/access_token', ''),
                        array(
                            'oauth_verifier' => $oauth_verifier
                        )
        );

        //print_r($this->OAuth->config);
        if ($code == 200) {
            // save configuration for twitter app
            $tempSession = & JFactory::getSession();
            $access_token = $this->OAuth->extract_params($this->OAuth->response['response']);
            $_SESSION['access_token'] = $access_token;
            $_SESSION['oauth'] = null;
            header('Location: ' . $callback);
        } else {
            return false;
        }
    }

    public function verify_access_token() {
        $this->OAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
        $this->OAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

        $code = $this->OAuth->request(
                        'GET',
                        $this->OAuth->url('1/account/verify_credentials')
        );
        if ($code == 200) {
            $resp = json_decode($this->OAuth->response['response']);
            return $_SESSION['access_token'];
        } else {
            return false;
        }
    }

}

?>
