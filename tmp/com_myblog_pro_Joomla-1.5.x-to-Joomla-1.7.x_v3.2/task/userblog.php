<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

require_once( MY_COM_PATH . DS . 'task' . DS . 'author.php' );

class MyblogUserblogTask extends MyblogAuthorTask
{
	
	function MyblogUserblogTask()
	{
		parent::MyblogBrowseBase();
		
		$this->toolbar = MY_TOOLBAR_BLOGGER;
		
		$my				=& JFactory::getUser();
		$authorId = $author = $my->id; 
		
		$this->authorId = $authorId;
		
		$this->author =& JTable::getInstance( 'Blogger' , 'Myblog' );
		$this->author->load($authorId);
	}
	
	function display()
	{
		$my		=& JFactory::getUser();
		
		if( $my->id == 0 )
		{
			# If user not logged in, cannot view his/her blog
			echo '<div id="fp-content">';
			echo JText::_('COM_MY_LOGIN_TO_VIEW_BLOG');
			echo '</div>';
		}
		else
		{
			$content	= parent::display();
			myAddPageTitle( $my->name . "'s Blog");
			return $content;
		}
	}
}
