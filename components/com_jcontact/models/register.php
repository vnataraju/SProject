<?php
/************************************************************/
/* Title��..: J!Contact
/* Description..: An integration of iContact and Joomla
/* Author��.: Joomlashack LLC
/* Version��: For Joomla! 1.5.x Stable ONLY
/* Created��: 04/13/07
/* Contact��: support@joomlashack.com
/* Copyright�.: Copyright� 2007 Joomlashack LLC. All rights reserved.
/* License��: Commercial
/************************************************************/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class JContactModelRegister extends JModel {

	function __construct() {
		parent::__construct();
	}

	function _sendMail(&$user, $password, $show_optional = 0)
	{
		global $mainframe;

		$db		=& JFactory::getDBO();

		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$usersConfig 	= &JComponentHelper::getParams( 'com_users' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$useractivation = $usersConfig->get( 'useractivation' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();

		$subject 	= sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);
		
		$ictr = ($email."--name=".$name);
		$activation = $siteURL."index.php?option=com_jcontact&task=activate&activation=".$user->get('activation')."&ictr=".$this->_encrypt($ictr)."&show_optional={$show_optional}";
		

		
		if ( $useractivation == 1 ){
			$message = sprintf ( JText::_( 'SEND_MSG_ACTIVATE' ), $name, $sitename, $activation, $siteURL, $username, $password);
		} else {
			$message = sprintf ( JText::_( 'SEND_MSG' ), $name, $sitename, $siteURL);
		}
		
		$db->setQuery( "SELECT * FROM #__jcontact_config LIMIT 1" );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
	
		$row =& $rows[0];
	
		$mailsubj2 = $row->mailsubj2;
		$mailcont2 = $row->mailcont2 . "<p><a href='" . $activation . "'>" . $activation . "</a></p> ";
	
		$subject = $mailsubj2;
		$message = $mailcont2;

		//$subject 	= html_entity_decode($mailsubj, ENT_QUOTES);
		//$message = html_entity_decode($mailcont, ENT_QUOTES);

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		JUtility::sendMail($mailfrom, $fromname, $email, $subject, $message, 1);

		// Send notification to all administrators
		$subject2 = sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject2 = html_entity_decode($subject2, ENT_QUOTES);

		// get superadministrators id
		foreach ( $rows as $row )
		{
			if ($row->sendEmail)
			{
				$message2 = sprintf ( JText::_( 'SEND_MSG_ADMIN' ), $row->name, $sitename, $name, $email, $username);
				$message2 = html_entity_decode($message2, ENT_QUOTES);
				JUtility::sendMail($mailfrom, $fromname, $row->email, $subject2, $message2);
			}
		}
	}
		
	function _sendMail2($name, $email, $regstring) {
		global $mainframe;

		$db		=& JFactory::getDBO();

		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();
		$sitename 		= $mainframe->getCfg( 'sitename' );

		$activation = $siteURL."index.php?option=com_jcontact&task=register_modicontact2&activation=".$regstring;

		$db->setQuery( "SELECT * FROM #__jcontact_config LIMIT 1" );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
	
		$row =& $rows[0];

		$mailsubj1 = $row->mailsubj1;
		$mailcont1 = $row->mailcont1 . "<p><a href='" . $activation . "'>" . $activation . "</a></p>";
	
		$subject 	= $mailsubj1;
		$message = $mailcont1;

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		JUtility::sendMail($mailfrom, $fromname, $email, $subject, $message, 1);
	}
		
	function _encrypt($string) {
	  $enc = array(
	   'a' => 'Q',
	   'b' => 'W',
	   'c' => 'E',
	   'd' => 'R',
	   'e' => 'T',
	   'f' => 'Y',
	   'g' => 'U',
	   'h' => 'I',
	   'i' => 'O',
	   'j' => 'P',
	   'k' => 'A',
	   'l' => 'S',
	   'm' => 'D',
	   'n' => 'F',
	   'o' => 'G',
	   'p' => 'H',
	   'q' => 'J',
	   'r' => 'K',
	   's' => 'L',
	   't' => 'Z',
	   'u' => 'X',
	   'v' => 'C',
	   'w' => 'V',
	   'x' => 'B',
	   'y' => 'N',
	   'z' => 'M',
	   'A' => 'q',
	   'B' => 'w',
	   'C' => 'e',
	   'D' => 'r',
	   'E' => 't',
	   'F' => 'y',
	   'G' => 'u',
	   'H' => 'i',
	   'I' => 'o',
	   'J' => 'p',
	   'K' => 'a',
	   'L' => 's',
	   'M' => 'd',
	   'N' => 'f',
	   'O' => 'g',
	   'P' => 'h',
	   'Q' => 'j',
	   'R' => 'k',
	   'S' => 'l',
	   'T' => 'z',
	   'U' => 'x',
	   'V' => 'c',
	   'W' => 'v',
	   'X' => 'b',
	   'Y' => 'n',
	   'Z' => 'm',
	   '@' => '%24',//$
	   '.' => '%3C',//<
	   ':' => '%3E',//>
	   '-' => '%5B',//[
	   '_' => '%5D',//]
	   '0' => '0',
	   '1' => '9',
	   '2' => '8',
	   '3' => '7',
	   '4' => '6',
	   '5' => '5',
	   '6' => '4',
	   '7' => '3',
	   '8' => '2',
	   '9' => '1', 
	   ' ' => '%20'
	 );
	 $nr = 0;
	 while (isset($string{$nr})) { 
	  $new .= $enc[$string{$nr}];
	     $nr++;
	 }
	 return $new;
	}
	
		
	function &getData() {
		if(empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}
	
	function _buildQuery() {
		$query = 'SELECT *'
		. ' FROM #__jcontact_config'
		;

		return $query;
	}
}