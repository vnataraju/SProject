<?php
/************************************************************/
/* Title……..: J!Contact
/* Description..: An integration of iContact and Joomla
/* Author…….: Joomlashack LLC
/* Version……: For Joomla! 1.5.x Stable ONLY
/* Created……: 04/13/07
/* Contact……: support@joomlashack.com
/* Copyright….: Copyright© 2007 Joomlashack LLC. All rights reserved.
/* License……: Commercial
/************************************************************/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

class JContactViewUnsubscribe extends JView {
	function display($tpl = null)
	{
		global $mainframe;
		
		$unsubscribe_message = unsubscribe($e);
	
		$this->assignRef('unsubscribe_message', $unsubscribe_message);
		
		parent::display($tpl);
	}
}