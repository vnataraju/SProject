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

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT.DS.'includes'.DS.'jcontact_api.php';
if (version_compare(PHP_VERSION,'5','>=')) { 
	require_once JPATH_COMPONENT.DS.'includes'.DS.'domxml-php4-to-php5.php';
}

require_once(JPATH_COMPONENT.DS.'controller.php');

if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	
    if(file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}
$classname = 'JContactController'.$controller;
$controller = new $classname();

$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));
$controller->redirect();
?>