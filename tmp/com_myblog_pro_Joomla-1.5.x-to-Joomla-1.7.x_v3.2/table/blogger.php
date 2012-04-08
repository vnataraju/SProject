<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

class myblogBlogger extends JTable
{ 
	var $user_id		= null;
	var $description	= '';
	var $title			= '';
	var $feedburner		= '';
	var $style 			= null;
	var $params			= null;
	var $userStyle 		= null;
	var $googlegears	= '';
	
	function __construct(&$db)
	{
		parent::__construct('#__myblog_user','user_id', $db);
		
		$this->description	= JText::_('COM_MY_A_SHORT_DESCRIPTION_ABOUT_YOUR_BLOG');
		$this->title		= JText::_('COM_MY_A_TITLE_ABOUT_YOUR_BLOG');
	}
	
	// Return the customized css styling for each user. Stored in
	// $userStyle array
	function getStyle($style)
	{
		if(isset($this->userStyle[$style]))
		{
			return $this->userStyle[$style];
		}
		else 
			return '';
	}
	
	function load($id)
	{
		$db		=& $this->getDBO();

		if($id != 0)
		{
			$query	= "SELECT `user_id` FROM #__myblog_user WHERE `user_id`='{$id}'";
			$db->setQuery( $query );
			
			$userId	= $db->loadResult();

			if( !$userId )
			{
				$data	= new stdClass();
				
				$data->user_id		= $id;
				$data->description	= JText::_('A SHORT DESCRIPTION ABOUT YOUR BLOG');
				
				$db->insertObject( '#__myblog_user' , $data );
			}
		}
		// Check if the user exist, if not, load up with default values
		parent::load($id);
		
		// Load userstyle
		if(!empty($this->style))
		{
			$this->userStyle = unserialize($this->style);
		}		
	}

	function store()
	{
		$db		=& $this->getDBO();
		$data	= new stdClass();
		
		$data->user_id		= $this->user_id;
		$data->description	= $this->description;
		$data->title		= $this->title;
		$data->feedburner	= $this->feedburner;
		$data->style		= $this->style;
		$data->params		= $this->params;
		$data->googlegears	= $this->googlegears;
		
		return $db->updateObject( '#__myblog_user' , $data , 'user_id' );
	}
} 
