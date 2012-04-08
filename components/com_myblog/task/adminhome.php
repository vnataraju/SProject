<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

require_once( MY_COM_PATH . DS . 'task' . DS . 'base.php' );

class MyblogAdminhomeTask extends MyblogBaseController
{
	function MyblogAdminhomeTask()
	{
		$this->toolbar	= MY_TOOLBAR_ACCOUNT;
	}

	function display()
	{
		global $_MY_CONFIG;

		$document   =& JFactory::getDocument();

		$my			=& JFactory::getUser();
		$db			=& JFactory::getDBO();
		$limitstart	= JRequest::getVar( 'limitstart' , 0 , 'REQUEST' );
 		$limitstart = intval($limitstart);
		$secid		= $_MY_CONFIG->get('postSection');
		$limit		= JRequest::getVar( 'limit' , MY_DEFAULT_LIMIT , 'REQUEST' );

		$secid		= $_MY_CONFIG->get('postSection');
		$limit		= $limitstart ? "LIMIT $limitstart, ".MY_DEFAULT_LIMIT : 'LIMIT '.MY_DEFAULT_LIMIT;
		
                $whereSection = (JVERSION >= 1.6)
			? " AND catid IN (" . $_MY_CONFIG->get('managedSections') . ")"
			: " AND sectionid IN (" . $_MY_CONFIG->get('managedSections') . ")";

		$query		= "SELECT a.*,b.title as catname, COUNT(c.id) as numtag  
					FROM #__content a
					JOIN #__categories b ON a.catid=b.id 
					LEFT JOIN #__myblog_content_categories c ON a.id=c.contentid
					WHERE a.`created_by`=".$db->Quote($my->id)
					. $whereSection
					. "GROUP BY a.id ORDER BY a.created DESC, a.id DESC "
					. $limit;
		
		/*$query		= "SELECT * FROM #__content WHERE `created_by`=".$db->Quote($my->id)
					. $whereSection
					. "ORDER BY created DESC, id DESC "
					. $limit;*/

		$db->setQuery($query);
		$entries	= $db->loadObjectList();

		jimport( 'joomla.filesystem.file' );
		$jomcommentExists	= JFile::exists( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_jomcomment' . DS . 'config.jomcomment.php' );
		
		// Add the actions buttons and modify the date format
		for($i = 0; $i < count($entries); $i++)
		{
		    // Htmlspecialchars the title
			$entries[$i]->title    = htmlspecialchars($entries[$i]->title);
			$entries[$i]->action = '[ edit | delete ]';
			
			
			if( $jomcommentExists && $_MY_CONFIG->get('useComment') )
			{
				$query	= "SELECT COUNT(*) FROM #__jomcomment AS a WHERE a.contentid=" .$db->Quote($entries[$i]->id) . " AND a.option='com_myblog'";
				$db->setQuery( $query );
				$count	= $db->loadResult();
				$entries[$i]->commentCount = $count;
			}
		}

		// Add pagination
		$config = array();
                
		$query	= "SELECT count(*) FROM #__content WHERE `created_by`=".$db->Quote($my->id)." $whereSection ORDER BY created";
		$db->setQuery( $query );
		$total	= $db->loadResult();

		$pagination	= myPagination( $total , $limitstart , MY_DEFAULT_LIMIT );

		myAddEditorHeader();

		if(!class_exists('AzrulJXTemplate'))
			include_once($this->cms->get_path('plugins'). '/system/pc_includes/template.php');

		$tpl = new AzrulJXTemplate();
		$tpl->set('postingRights', myGetUserCanPost());
		$tpl->set('publishRights', myGetUserCanPublish());
		$tpl->set('myitemid', myGetItemId());
		$tpl->set('pagination', $pagination->links );
		$tpl->set('myentries', $entries);
		$tpl->set( 'limit' , $limit);
		$html = $tpl->fetch(MY_TEMPLATE_PATH."/admin/home.html");
		return $html;
	}
}


