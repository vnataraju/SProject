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

class JContactViewCategories extends JView {
	function display($tpl = null) {
		global $mainframe;

		$config_data =& $this->get('Data');

		$document =& JFactory::getDocument();

		require_once JPATH_COMPONENT_SITE.DS.'includes'.DS.'jcontact_api.php';
		require_once JPATH_COMPONENT_SITE.DS.'includes'.DS.'domxml-php4-to-php5.php';

		$lists = array();
		$icontact_lists[]  = JHTML::_('select.option',  '0', JText::_( 'Select a Category' )  );
		$icontact_lists  = array_merge( $icontact_lists, get_icontact_lists() );

		$lists['icontact_lists'] = JHTML::_('select.genericlist', $icontact_lists, 'icontact_lists', 'class="inputbox" size="1"', 'value', 'text' );
		$lists['regon'] = JHTML::_('select.booleanlist',  'regon', '', $config_data[0]->regon );

		$this->assignRef('lists', $lists);
		parent::display($tpl);
	}
}
?>