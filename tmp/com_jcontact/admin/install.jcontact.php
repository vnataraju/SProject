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

defined( '_JEXEC' ) or die( 'Restricted access' );

function com_install() {
	
	$db =& JFactory::getDBO();
	
	$sql = "SELECT id FROM #__components " .
	"WHERE `option` = 'com_jcontact' AND parent=0";
	$db->setQuery($sql);
	$component_id = $db->loadResult();
	
	if(!is_null($component_id)) {
		$sql = "UPDATE #__menu SET componentid=" . $component_id .
		" WHERE link LIKE '%option=com_jcontact%' AND type='component'";
		$db->setQuery($sql);
		$db->query();
	}
}
?>
<h1>JContact Installed</h1>
