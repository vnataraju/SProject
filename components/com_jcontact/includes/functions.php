<?php

$temp_xml_file = "tmp/ic_temp.xml";
global $mainframe;
if(!defined('_JEXEC')){
  require_once( $mainframe->getCfg('absolute_path') . '/includes/patTemplate/patTemplate.php');
} else {
	

	
	$db	=& JFactory::getDBO();
echo "Database prefix is : " . $db->getPrefix();

	$db->setQuery( "SELECT * FROM #__jcontact_config LIMIT 1" );
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo $db->stderr();
		return false;
	}

	$row =& $rows[0];

	$regon = $row->regon;
	$maillist = $row->maillist;
	$username = $row->username;
	$password = $row->password;
	$apikey = $row->apikey;
	$secret = $row->secret;
 	$wrapperurl = $row->wrapperurl;
	$mailsubj = $row->mailsubj;
	$mailcont = $row->mailcont;
	}
	echo $maillist;
	
?>