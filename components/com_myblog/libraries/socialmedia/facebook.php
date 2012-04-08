<?php
/**
 * MyBlog
 * @package MyBlog
 * @copyright (C) 2006 - 2008 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

require_once('facebook/facebook.php');

class MYSocialMedia_Facebook{
    protected $message;
    protected $link;
    protected $fbAppID;
    protected $fbSecretID;
    protected $facebook;

    public function __construct() {
        global $_MY_CONFIG;
        $this->fbAppID = $_MY_CONFIG->get('fbAppID2');
        $this->fbSecretID = $_MY_CONFIG->get('fbSecretID2');

        $this->facebook = $facebook = new Facebook(array(
              'appId'  => $this->fbAppID,
              'secret' => $this->fbSecretID,
            ));
        
    }

    public function getLoginUrl(){
        return $this->facebook->getLoginUrl( array(
                       'scope' => 'publish_stream'
                       )); 
    }

    public function getLogoutUrl(){
        return $this->facebook->getLogoutUrl();
    }

    public function getUser(){
        return $this->facebook->getUser();
    }

    public function setLink($link){
        $this->link = $link;
    }

    public function setMessage($message){
        $this->message = $message;
    }

    public function post(){
        $status = $this->facebook->api('/me/feed', 'POST',
                                    array(
                                      'link' => $this->link,
                                      'message' => $this->message
                                 ));
        return $status;
    }

    public function api($param){
        $this->facebook->api($param);
    }

}

?>
