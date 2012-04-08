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

jimport('joomla.application.component.view');

class JContactViewRegister extends JView {
	function display($tpl = null)
	{
		global $mainframe;
		
		$pathway  =& $mainframe->getPathway();
		$document =& JFactory::getDocument();

	 	// Page Title
	 	$document->setTitle( JText::_( 'Registration' ) );
		$pathway->addItem( JText::_( 'New' ));

		// Load the form validation behavior
		JHTML::_('behavior.formvalidation');

		$user =& JFactory::getUser();
		$this->assignRef('user', $user);

		$config_data =& $this->get('Data');
		
		$lists = array();
		
		$lists['show_optional'] = JHTML::_('select.booleanlist',  'show_optional', '', '1' );
		
		$this->assignRef('lists', $lists);
		$this->assignRef('config_data', $config_data[0]);

		parent::display($tpl);
	}
}