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

defined('_JEXEC') or die('Restricted Access');

class JContactControllerCategories extends JContactController {
	function __construct() {
		parent::__construct();
	}
	
	function display() {
		JRequest::setVar('view', 'categories');
		
		parent::display();
	}
}
?>