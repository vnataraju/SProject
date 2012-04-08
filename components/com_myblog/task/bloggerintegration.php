<?php

/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

class MyblogBloggerIntegrationTask extends MyblogBaseController {

    function MyblogBloggerstatsTask() {
        $this->toolbar = MY_INTEGRATION;
    }

	function display() {
            
        global $_MY_CONFIG;

        $my            =& JFactory::getUser();
        $user        =& JTable::getInstance( 'Blogger' , 'Myblog' );
        $user->load( $my->id );
        
        $libraries_available = true;
        $user_fb = '';
        $fb_login_url = '';
        $fb_logout_url = '';
        $oauth_access_token = '';
        $enablePostFacebook = $_MY_CONFIG->get('enablePostFacebook');
        $tpl = new AzrulJXTemplate();

        
        if (!extension_loaded('curl') || !extension_loaded('json')) {
            $libraries_available = false;
        }else{
            require_once(MY_LIBRARY_PATH . DS . 'socialmedia' . DS . 'twitter.php');

            require_once(MY_LIBRARY_PATH . DS . 'socialmedia' . DS . 'facebook.php');
            $app = JRequest::getVar('app', '', 'GET');
            $state = JRequest::getVar('state', '', 'GET');
            $oauth_verifier = JRequest::getVar('oauth_verifier', '', 'GET');
            $action = JRequest::getVar('action', '', 'GET');
            $mainframe    =& JFactory::getApplication();
    
            if ($action == 'delete_twitter') {
                // redirect
                if(myDeleteOauth()==true){
                    $mainframe->enqueueMessage( JText::_('COM_MY_DELETE_AUTH_TWITTER_SUCCESS') );
                }else{
                    $mainframe->enqueueMessage( JText::_('COM_MY_DELETE_AUTH_TWITTER_FAILED') );
                }
    
                $mainframe->redirect(JRoute::_('index.php?option=com_myblog&task=bloggerintegration',false));
            }
        
            // SOcial media for twitter
            $twitter = new MYSocialMedia_Twitter;
            $tempSession = & JFactory::getSession();
    
            // save configuration for twitter app
            if ($app == 'twitter') {
                // oauth flow here
                if ($action == 'request_token') {
                    $req_token = $twitter->request_token();
                    if($req_token!=200){
                        $mainframe->enqueueMessage( JText::_('COM_MY_AUTH_FAILED') );
                    }
                } elseif ($action == 'access_token') {
                    $twitter->request_access_token($oauth_verifier);
                } elseif ($action == 'verify_twitter') {
                    $oauth_token = $tempSession->get('oauth_token');
                    $oauth_token_secret = $tempSession->get('oauth_token_secret');
    
                    $auth = $twitter->verify_access_token();
                    if ($auth) {
                        $mainframe->enqueueMessage( JText::_('COM_MY_AUTH_SUCCESS') );
                        //$mainframe->redirect(JRoute::_('index.php?option=com_myblog&task=bloggerintegration'));
                        mySaveOauth($auth['oauth_token'], $auth['oauth_token_secret']);
                    }else{
                        $mainframe->enqueueMessage( JText::_('COM_MY_AUTH_FAILED') );
                        //$mainframe->redirect(JRoute::_('index.php?option=com_myblog&task=bloggerintegration'));
                    }
                }
            }
    
            // check if this user already verified with twitter app

            $oauth = myGetOauth();
            $oauth_access_token = isset($oauth[0]->user_token) ? $oauth[0]->user_token : '';
    
            // Social Media for FB            
            if ($enablePostFacebook) {
                $facebook = new MYSocialMedia_Facebook();
                $fb_login_url = $facebook->getLoginUrl();

    
                $user_fb = $facebook->getUser();
                $user_fb_profile = '';
                if ($user_fb) {
                    try {
                        // Proceed knowing you have a logged in user who's authenticated.
                        $user_fb_profile = $facebook->api('/me');
                        $fb_logout_url = $facebook->getLogoutUrl();
                    } catch (FacebookApiException $e) {
                        error_log($e);
                        $user_fb = null;
                    }
                }
            }
            if($state){
                $mainframe->enqueueMessage( JText::_('COM_MY_AUTH_SUCCESS') );
                $mainframe->redirect(JRoute::_('index.php?option=com_myblog&task=bloggerintegration'));
            }
        }
    
        $html = $tpl->set('libraries_available', $libraries_available);
        $html = $tpl->set('user_fb', $user_fb);
        $html = $tpl->set('enablePostFacebook', $enablePostFacebook);
        $html = $tpl->set('fb_logout_url', $fb_logout_url);
        $html = $tpl->set('fb_login_url', $fb_login_url);
        $html = $tpl->set('oauth_access_token', $oauth_access_token);
        $html = $tpl->fetch(MY_TEMPLATE_PATH . "/admin/blogger_integration.html");
        return $html;
    }
}