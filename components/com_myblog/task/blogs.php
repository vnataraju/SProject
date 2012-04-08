<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

require_once( MY_COM_PATH . DS . 'task' . DS . 'base.php' );
require_once( MY_LIBRARY_PATH . DS . 'avatar.php' );

jimport( 'joomla.html.pagination' );

class MyblogBlogsTask extends MyblogBaseController
{

	function display($styleid = '', $wrapTag = 'div')
	{
		global $MYBLOG_LANG, $_MY_CONFIG, $Itemid;

		$mainframe	=& JFactory::getApplication();
		
		myAddPageTitle( JText::_('COM_MY_ALL_BLOGS_TITLE') );

		$total = 0;
		
		$limit		= JRequest::getVar( 'limit' , 10 , 'GET' );
		$limitstart	= JRequest::getVar( 'limitstart' , 0 , 'GET' );
		
		// make sure it really is an int
		$limit		= intval($limit);
		$limitstart = intval($limitstart);

		$sections	= $_MY_CONFIG->get( 'managedSections' );
		
		$db			=& JFactory::getDBO();
		
		// In case previous authors have not used myblog before, create a 'blog' for them
		$query		= "SELECT DISTINCT c.".$db->nameQuote('created_by').
					" FROM ".$db->nameQuote('#__content')." c LEFT OUTER JOIN ".
					$db->nameQuote('#__myblog_user')." m on (m.".$db->nameQuote('user_id')."=c.".$db->nameQuote('created_by').
					") WHERE m.".$db->nameQuote('user_id')." IS NULL and ".$db->nameQuote('sectionid')." in ($sections)";
					
		$db->setQuery( $query );

		if( $db->loadObjectlist() )
		{
			foreach($not_in_myblog as $to_insert)
			{
				$db->setQuery("INSERT INTO ".$db->nameQuote('#__myblog_user')." SET ".$db->nameQuote('user_id')."=" . $db->Quote($to_insert->created_by) . ",description=''");
				$db->query();
			}
		}

		$db->setQuery("SELECT distinct(u.".$db->nameQuote('id').") as ".$db->nameQuote('user_id').
					", mu.".$db->nameQuote('description').",u.".$db->nameQuote('username').", u.".$db->nameQuote('name')
			." FROM ".$db->nameQuote('#__users')." u,".$db->nameQuote('#__myblog_user')." mu, ".$db->nameQuote('#__content')." c "
			." WHERE mu.".$db->nameQuote('user_id')."=u.".$db->nameQuote('id')." and c.".$db->nameQuote('created_by')."=mu.".$db->nameQuote('user_id').
			" AND c.".$db->nameQuote('sectionid')." IN ($sections) LIMIT $limitstart,$limit");
		$blogs = $db->loadObjectList();

		$db->setQuery("SELECT count(distinct(u.".$db->nameQuote('id').")) "
			." FROM ".$db->nameQuote('#__users')." u, ".$db->nameQuote('#__myblog_user')." mu, ".$db->nameQuote('#__content')." c "
			." WHERE mu.".$db->nameQuote('user_id')."=u.".$db->nameQuote('id')." and c.".$db->nameQuote('created_by')." = mu.".$db->nameQuote('user_id')." AND c.".$db->nameQuote('sectionid')." in ($sections)");
			
		$total = $db->loadResult();
		$bloggerHTML	= '';
		
		if ($blogs)
		{
			foreach ($blogs as $blog)
			{				
				$blog->avatar		= '';
				
// 				if ($_MY_CONFIG->get('linkAvatar') && $_MY_CONFIG->get('linkAvatar') == "1" )	
// 				{
					$avatar			= 'My' . ucfirst($_MY_CONFIG->get('avatar')) . 'Avatar';
					$avatar			= new $avatar($blog->user_id);
					$blog->avatar	= $avatar->get();
// 				}

				$blog->numEntries	= myCountUserEntry($blog->user_id, "1");
				$blog->numHits		= myCountUserHits($blog->user_id);
				$blog->blogLink		= JRoute::_("index.php?option=com_myblog&blogger=" . $blog->username . "&Itemid=$Itemid");
				$blog->description	= strip_tags(my_strict_nl2br($blog->description, " "), '<u> <i> <b>');
				
				$db->setQuery("SELECT datediff(curdate(),MAX(".$db->nameQuote('created').")) FROM ".$db->nameQuote('#__content')." WHERE ".$db->nameQuote('sectionid')." IN ($sections) AND ".$db->nameQuote('created_by')."=" . $db->Quote($blog->user_id) . " and ".$db->nameQuote('state')."=1 AND ".$db->nameQuote('publish_up')." < now()");
				$lastUpdated		= $db->loadResult();

				if(!empty( $lastUpdated ) )
				{
					if( $lastUpdated > 0 )
					{
						$lastUpdated	= ( $lastUpdated == 1 ? JText::_('COM_MY_BLOG_UPDATED_YESTERDAY') : JText::sprintf( 'COM_MY_BLOG_UPDATED_DAYS_AGO' , $lastUpdated ) );
					}
					else
					{
						$lastUpdated	= JText::_('COM_MY_BLOG_UPDATED_TODAY');
					}

					$blog->last_updated = $lastUpdated;
				}
				else
				{
					$blog->last_updated	= JText::_('COM_MY_BLOG_WAS_NEVER_UPDATED');
				}

				$categories		= myGetUserTags($blog->user_id);
				
				$tmpArray		= array();
				$blogs[0]		= $blog;
				
				$template		= new AzrulJXTemplate();
				$template->set( 'avatarWidth' , $_MY_CONFIG->get('avatarWidth' ) );
				$template->set( 'avatarLeftPadding' , ($_MY_CONFIG->get('avatarWidth' ) + 30 ) );
				$template->set( 'useFullName' , $_MY_CONFIG->get('useFullName') );
				$template->set( 'blogs' , $blogs );
				$template->set( 'categories' , $categories );
				$template->set( 'Itemid' , $Itemid );
				
				$bloggerHTML	.= $template->fetch( $this->_getTemplateName('blogs_blogger') );
				
				unset( $template );
			}
		}

		$template	= new AzrulJXTemplate();
		$template->set( 'bloggerHTML' , $bloggerHTML );
		$content	= $template->fetch($this->_getTemplateName('blogs'));
		
		$queryString = $_SERVER['QUERY_STRING'];
		$queryString = preg_replace("/\&limit=[0-9]*/i", "", $queryString);
		$queryString = preg_replace("/\&limitstart=[0-9]*/i", "", $queryString);
		$pageNavLink = $_SERVER['REQUEST_URI'];
		$pageNavLink = preg_replace("/\&limit=[0-9]*/i", "", $pageNavLink);
		$pageNavLink = preg_replace("/\&limitstart=[0-9]*/i", "", $pageNavLink);

		$pageNav		= new JPagination($total, $limitstart, $limit);		
		$content .= '<div class="my-pagenav">' . $pageNav->getPagesLinks('index.php?' . $queryString) . '</div>';

		return $content;
	}
}