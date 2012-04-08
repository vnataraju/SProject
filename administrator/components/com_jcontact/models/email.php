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

jimport('joomla.application.component.model');

class JContactModelEmail extends JModel {
	var $_data = null;

	function __construct()	{
			parent::__construct();
			
			global $mainframe, $option;
	}
	
	function &getData() {
		if(empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}
	
	function _buildQuery() {
		$query = 'SELECT *'
		. ' FROM #__jcontact_config'
		;

		return $query;
	}
	
	function store() {
		$row =& $this->getTable('jcontact');
		
		$data = JRequest::get('post');
		
		$data['mailcont1'] = JRequest::getVar( 'mailcont1', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$data['mailcont2'] = JRequest::getVar( 'mailcont2', '', 'post', 'string', JREQUEST_ALLOWRAW );
		
		if(!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
				
		if(!$row->check()) {
			$this->setError($row->getError());
			return false;
		}
		
		if(!$row->store()) {
			$this->setError($row->getError());
			return false;
		}

		$row->checkin();

		$this->setState('rid', $row->id);
		
		return true;
	}
	
	function cancel() {
		$row =& $this->getTable();
		$row->bind( JRequest::get( 'post' ));
		$row->checkin();
	}
}
?>