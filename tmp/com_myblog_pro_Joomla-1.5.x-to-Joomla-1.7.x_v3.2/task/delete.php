<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

class MyblogDeleteTask extends MyblogBaseController
{
	function display()
	{
		$mainframe	=& JFactory::getApplication();
		$my			=& JFactory::getUser();		
		$id			= JRequest::getVar( 'id' , 0 , 'GET' );
		
		$blog		=& JTable::getInstance( 'BlogContent' , 'Myblog' );
		$blog->load( $id );
				
		if( $blog->created_by != $my->id )
		{
			$url		= JRoute::_('index.php?option=com_myblog&task=adminhome&Itemid=' . myGetItemId() , false );
			$mainframe->redirect( $url , 'Do not have permissions to remove this entry.' );
			return;	
		}
		
		$blog->delete();

		myxDeleteBlogDraft($id, $blog->created_by);
		
		// Need to include paging info if necessary, the paging info will come from
		$url		= JRoute::_('index.php?option=com_myblog&task=adminhome&Itemid=' . myGetItemId() , false );
		$mainframe->redirect( $url , 'Blog entry deleted' );
	}
}
