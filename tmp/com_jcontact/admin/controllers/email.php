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

class JContactControllerEmail extends JContactController {
	function __construct() {
		parent::__construct();

		$this->registerTask('save', 'save');
		$this->registerTask('cancel', 'cancel');
	}
	
	function display() {
		JRequest::setVar('view', 'email');
		
		parent::display();
	}
	
	function save() {
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model = $this->getModel('email');
				
		if(!$model->store()) {
			JError::raiseError(500, $model->getError() );
		}

		$msg	= JText::_( 'JContact email content saved' );
		$link	= 'index.php?option=com_jcontact&controller=email';

		$this->setRedirect($link, $msg);
	}
	
	function cancel() {
		JRequest::checkToken() or die( 'Invalid Token' );
		$model = $this->getModel('email');
		$model->cancel();
		$this->setRedirect('index.php');
	}
}
?>