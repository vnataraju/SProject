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

class JContactViewJContact extends JView {
	function display($tpl = null) {
		global $mainframe;
		
		$config_data =& $this->get('Data');
		
		$base_path = dirname($_SERVER['PHP_SELF']);
	
		$document =& JFactory::getDocument();
		$document->addScript($base_path . '/components/com_jcontact/js/jquery.js');
		$document->addScriptDeclaration('var $jq = jQuery.noConflict();');
		$document->addScript($base_path . '/components/com_jcontact/js/nyroModal/js/jquery.nyroModal.js');
		$document->addStyleSheet($base_path . '/components/com_jcontact/js/nyroModal/styles/nyroModal.css', 'text/css', null, array('id' => 'css'));
		
		@$config_data[0]->id = is_null($config_data[0]->id) ? '' : $config_data[0]->id;
		@$config_data[0]->regon = is_null($config_data[0]->regon) ? '' : $config_data[0]->regon;
		@$config_data[0]->maillist = is_null($config_data[0]->maillist) ? '' : $config_data[0]->maillist;
		@$config_data[0]->maillist_text = is_null($config_data[0]->maillist_text) ? JText::_( 'Select a list' ) : $config_data[0]->maillist_text;
		@$config_data[0]->username = is_null($config_data[0]->username) ? '' : $config_data[0]->username;
		@$config_data[0]->password = is_null($config_data[0]->password) ? '' : $config_data[0]->password;
		@$config_data[0]->apikey = is_null($config_data[0]->apikey) ? '' : $config_data[0]->apikey;
		@$config_data[0]->apiUrl = is_null($config_data[0]->apiUrl) ? 'https://app.icontact.com/icp' : $config_data[0]->apiUrl;
		@$config_data[0]->secret = is_null($config_data[0]->secret) ? '' : $config_data[0]->secret;
		@$config_data[0]->wrapperurl = is_null($config_data[0]->wrapperurl) ? '' : $config_data[0]->wrapperurl;
		@$config_data[0]->show_optional = is_null($config_data[0]->show_optional) ? '' : $config_data[0]->show_optional;
		@$config_data[0]->show_for_all = is_null($config_data[0]->show_for_all) ? '' : $config_data[0]->show_for_all;
		@$config_data[0]->signup_message = is_null($config_data[0]->signup_message) ? '' : $config_data[0]->signup_message;

		$this->assignRef('config_data', $config_data[0]);
		$this->assignRef('base_path', $base_path);
		
		$lists = array();
		
		$lists['regon'] = JHTML::_('select.booleanlist',  'regon', '', $config_data[0]->regon );
		$lists['show_optional'] = JHTML::_('select.booleanlist',  'show_optional', '', $config_data[0]->show_optional );
		$lists['show_for_all'] = JHTML::_('select.booleanlist',  'show_for_all', '', $config_data[0]->show_for_all );
		
		$this->assignRef('lists', $lists);
		parent::display($tpl);
	}
}
?>