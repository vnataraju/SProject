<?php
/************************************************************/
/* Title: 		J!Contact
/* Description: An integration of iContact and Joomla
/* Author: 		Joomlashack LLC
/* Version: 	For Joomla! 1.5.x Stable ONLY
/* Created: 	04/13/07
/* Contact: 	support@joomlashack.com
/* Copyright: 	Copyright 2007 Joomlashack LLC. All rights reserved.
/* License: 	Commercial
/************************************************************/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.controller' );

class JContactController extends JController {
	
	function display() {
		$document =& JFactory::getDocument();

		$viewName	= JRequest::getVar('view', 'register', 'default', 'cmd');
		$viewType	= $document->getType();

		$view = &$this->getView($viewName, $viewType);

		$model	= &$this->getModel( $viewName );
	
		if (!JError::isError( $model )) {
			$view->setModel( $model, true );
		}

		$view->assign('error', $this->getError());
		$view->display();
	}
	
	function activate()
	{
		global $mainframe;
		
		// Do we even have an activation string?
		$activation = JRequest::getVar('activation', '', '', 'alnum' );
		
		$model =& $this->getModel('register');
		$component_config = $model->getData();
		
		$show_optional = JRequest::getVar('show_optional', null, 'get', 'int');
		
		if ($component_config[0]->show_for_all == 1 || $show_optional == 1) {
			$post1 = $this->decrypt($_REQUEST['ictr']);
			$post2 = explode("--name", $post1);
			$post['modjctreg_email'] = $post2[0];
			$post['modjctreg_name'] = $post2[1];
			$add_to_icontact = jcontact_user_regi($post['modjctreg_name'], $post['modjctreg_email']);
			
			if ($add_to_icontact) {
				$mainframe->redirect("index.php?option=com_user&task=activate&activation=".$activation);
			} else {
				$mainframe->redirect("index.php?option=com_user&task=activate&activation=".$activation);
			}
		}else{
			$post1 = $this->decrypt($_REQUEST['ictr']);
			$post2 = explode("--name", $post1);
			$post['modjctreg_email'] = $post2[0];
			$post['modjctreg_name'] = $post2[1];
			$mainframe->redirect("index.php?option=com_user&task=activate&activation=".$activation);
		}
	}

	function register_save() {
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Get required system objects
		$user 		= clone(JFactory::getUser());
		$pathway 	=& $mainframe->getPathway();
		$config		=& JFactory::getConfig();
		$authorize	=& JFactory::getACL();
		$document   =& JFactory::getDocument();

		// If user registration is not allowed, show 403 not authorized.
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration') == '0') {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		// Initialize new usertype setting
		$newUsertype = $usersConfig->get( 'new_usertype' );
		if (!$newUsertype) {
			$newUsertype = 'Registered';
		}

		// Bind the post array to the user object
		if (!$user->bind( JRequest::get('post'), 'usertype' )) {
			JError::raiseError( 500, $user->getError());
		}

		// Set some initial user values
		$user->set('name', $user->name . " " . $user->lastname);
		$user->set('id', 0);
		$user->set('usertype', '');
		$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));

		$date =& JFactory::getDate();
		$user->set('registerDate', $date->toMySQL());

		// If user activation is turned on, we need to set the activation information
		$useractivation = $usersConfig->get( 'useractivation' );
		if ($useractivation == '1')
		{
			jimport('joomla.user.helper');
			$user->set('activation', md5( JUserHelper::genRandomPassword()) );
			$user->set('block', '1');
		}

		// If there was an error with registration, set the message and display form
		if ( !$user->save() )
		{
			JError::raiseWarning('', JText::_( $user->getError()));
			$this->display();
			return false;
		}


		$model =& $this->getModel('register');
		// Send registration confirmation mail
		$password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
				
		// Everything went fine, set relevant message depending upon user activation state and display message
		if ( $useractivation == 1 ) {
			$message  = JText::_( 'REG_COMPLETE_ACTIVATE' );
		} else {
			$message = JText::_( 'REG_COMPLETE' );
		}
		
		$component_config = $model->getData();
		
		$show_optional = JRequest::getVar('show_optional', null, 'post', 'string');
		
		// 08.06.2008 changes - to send only email with activation code
		//if ($component_config[0]->show_for_all =! 1 && $show_optional != 1) {
		if ($component_config[0]->show_for_all == 1 || $show_optional == 1) {
			$model->_sendMail($user, $password, $show_optional);
			//jcontact_user_reg($user->name, $user->email);
		} else {
			$model->_sendMail($user, $password, $show_optional);
		}
		
		//TODO :: this needs to be replace by raiseMessage
		JError::raiseNotice('', $message);
		$this->display();
	}
	/**
	* @return string
	* @param string $string
	* @desc Decrypt a given URI parameter (which has to encrypted first!), so zOOm can use the original parameters again.
	* @access public
	*/
	function decrypt($string) {
	$string = urldecode($string);
	  $dec = array(
		'Q' => 'a',
		'W' => 'b',
		'E' => 'c',
		'R' => 'd',
		'T' => 'e',
		'Y' => 'f',
		'U' => 'g',
		'I' => 'h',
		'O' => 'i',
		'P' => 'j',
		'A' => 'k',
		'S' => 'l',
		'D' => 'm',
		'F' => 'n',
		'G' => 'o',
		'H' => 'p',
		'J' => 'q',
		'K' => 'r',
		'L' => 's',
		'Z' => 't',
		'X' => 'u',
		'C' => 'v',
		'V' => 'w',
		'B' => 'x',
		'N' => 'y',
		'M' => 'z',
		'q' => 'A',
		'w' => 'B',
		'e' => 'C',
		'r' => 'D',
		't' => 'E',
		'y' => 'F',
		'u' => 'G',
		'i' => 'H',
		'o' => 'I',
		'p' => 'J',
		'a' => 'K',
		's' => 'L',
		'd' => 'M',
		'f' => 'N',
		'g' => 'O',
		'h' => 'P',
		'j' => 'Q',
		'k' => 'R',
		'l' => 'S',
		'z' => 'T',
		'x' => 'U',
		'c' => 'V',
		'v' => 'W',
		'b' => 'X',
		'n' => 'Y',
		'm' => 'Z',
		'$' => '@',
		'<' => '.',
		'>' => ':',
		'[' => '-',
		']' => '_',
		'0' => '0',
		'9' => '1',
		'8' => '2',
		'7' => '3',
		'6' => '4',
		'5' => '5',
		'4' => '6',
		'3' => '7',
		'2' => '8',
		'1' => '9',
		' ' => ' '
	);
	 $new = "";
	 
	 $nr = 0;
	 while (isset($string{$nr})) { 
	  $new .= $dec[$string{$nr}];
	     $nr++;
	 }
	 return $new;
	} 
	
	
	function register_modicontact() {
		global $mainframe;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$post = JRequest::get('post');
		$model =& $this->getModel('register');
		
		$regstring = $model->_encrypt($post[modjctreg_email]."--name=".$post['modjctreg_name']);
		$model->_sendMail2($post['modjctreg_name'], $post['modjctreg_email'], $regstring);
		
		if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
			$return = base64_decode($return);
		}
		
		// Redirect if the return url is not registration or login
		if ( !$return ) {
			$return	= 'index.php';
		}
		
		$msg = JText::_( 'REG_COMPLETE_ACTIVATE2' );
		//$link = JRoute::_('index.php?option=frontpage');
		//$this->setRedirect($link, $msg);
		$mainframe->redirect($return, $msg);
	}
	function register_modicontact2() {
		global $mainframe;
		
		require_once (JPATH_COMPONENT.DS.'views'.DS.'register'.DS.'view.html.php');
		$view = new JContactViewRegister();
		
		//$req = JRequest::get('request');
	
		$post1 = $this->decrypt($_REQUEST['activation']);
		$post2 = explode("--name", $post1);
		$post['modjctreg_email'] = $post2[0];
		$post['modjctreg_name'] = $post2[1];
		
		if($post['modjctreg_name'] == '' || $post['modjctreg_email'] == '') {
			JError::raiseNotice('201', "Please fill out all fields!");
			$this->display();
		} else {
			$message = new stdClass();
			$add = jcontact_user_reg($post['modjctreg_name'], $post['modjctreg_email']);
			if(!$add) {
				$message->title = JText::_( 'Subscription Complete!' );
				$message->text = JText::_( 'Thank you for your subscribing');
			} else {
				$message->title = JText::_( 'Subscription Unsuccessful!' );
				$message->text = JText::_( $add );
			}
			$view->assign('message', $message);
			$view->display('message');
		}
	}



	function submit()	{
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model =& $this->getModel('contact');
		
		if($model->mailTo()) {
			$msg = JText::_( 'Thank you for your e-mail');
			$contact = $model->getContact();
			$link = JRoute::_('index.php?option=com_qcontacts&view=contact&id='.$contact->slug.'&catid='.$contact->catslug, false);
			$this->setRedirect($link, $msg);
		} else {
			$this->setError($model->getError());
			$this->display();
		}
	}
}