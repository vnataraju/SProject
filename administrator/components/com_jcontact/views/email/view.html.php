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

class JContactViewEmail extends JView {
	function display($tpl = null) {
		$config_data =& $this->get('Data');
	
		@$config_data[0]->mailsubj1 = is_null($config_data[0]->mailsubj1) ? '' : $config_data[0]->mailsubj1;
		@$config_data[0]->mailcont1 = is_null($config_data[0]->mailcont1) ? '' : $config_data[0]->mailcont1;
		
		@$config_data[0]->mailsubj2 = is_null($config_data[0]->mailsubj2) ? '' : $config_data[0]->mailsubj2;
		@$config_data[0]->mailcont2 = is_null($config_data[0]->mailcont2) ? '' : $config_data[0]->mailcont2;
		
		$this->assignRef('config_data', $config_data[0]);
		
		parent::display($tpl);
	}
}
?>