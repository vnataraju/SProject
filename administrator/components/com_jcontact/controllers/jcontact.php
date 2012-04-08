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

class JContactControllerJContact extends JContactController {
	function __construct() {
		parent::__construct();

		$this->registerTask('save', 'save');
		$this->registerTask('cancel', 'cancel');
	}
	
	function save() {
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model = $this->getModel('jcontact');
				
		if(!$model->store()) {
			JError::raiseError(500, $model->getError() );
		}

		$msg	= JText::_( 'JContact config saved' );
		$link	= 'index.php?option=com_jcontact';

		$this->setRedirect($link, $msg);
	}
	
	function cancel() {
		JRequest::checkToken() or die( 'Invalid Token' );
		$model = $this->getModel('jcontact');
		$model->cancel();
		$this->setRedirect('index.php');
	}
}
?>