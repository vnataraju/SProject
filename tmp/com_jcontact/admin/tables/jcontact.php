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

class TableJContact extends JTable {
	/** @var int Primary key */
	var $id = null;
	/** @var tinyint */
	var $regon = null;
	/** @var string */
	var $maillist = null;
	/** @var string */
	var $maillist_text = 'Select a list';
	/** @var string */
	var $username = null;
	/** @var string */
	var $password = null;
	/** @var string */
	var $apikey = null;
	/** @var string **/
	var $apiUrl = null;
	/** @var int */
	var $accountid = null;
	/** @var int */
	var $clientFolderId = null;
	/** @var string */
	var $wrapperurl = null;
	/** @var string */
	var $mailsubj1 = null;
	/** @var tinyblob */
	var $mailcont1 = null;
	/** @var string */
	var $mailsubj2 = null;
	/** @var tinyblob */
	var $mailcont2 = null;
	/** @var tinyint */
	var $show_optional = null;
	/** @var tinyint */
	var $show_for_all = null;
	/** @var string */
	var $signup_message = null;
	/**
	* @param database A database connector object
	*/
	function __construct(&$db) {
		parent::__construct( '#__jcontact_config', 'id', $db );
	}

	function check()
	{
		/*
		$this->default_con = intval( $this->default_con );

		if (JFilterInput::checkAttribute(array ('href', $this->webpage))) {
			$this->setError(JText::_('Please provide a valid URL'));
			return false;
		}

		if (strlen($this->webpage) > 0 && (!(eregi('http://', $this->webpage) || (eregi('https://', $this->webpage)) || (eregi('ftp://', $this->webpage))))) {
			$this->webpage = 'http://'.$this->webpage;
		}

		if(empty($this->alias)) {
			$this->alias = $this->name;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		if(trim(str_replace('-','',$this->alias)) == '') {
			$datenow = new JDate();
			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
		}
		*/
		return true;
	}
}
