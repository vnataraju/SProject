<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

require_once( MY_COM_PATH . DS . 'task' . DS . 'show.base.php' );
require_once( MY_COM_PATH . DS . 'libraries' . DS . 'plugins.php' );

class MyblogPrintblogTask extends MyblogShowBase
{
	var $_plugins	= null;
	var $row = null;
	var $uid = null;
	
	function MyblogPrintblogTask()
	{
		$this->_plugins	= new MYPlugins();
		$this->toolbar = MY_TOOLBAR_HOME;
		
		$db			=& JFactory::getDBO();
		$show		= JRequest::getVar( 'show' , '' , 'GET' );
		$id			= JRequest::getVar( 'id' , '' , 'GET' );
		$this->uid	= (!empty( $show ) ) ? $show : $id;

		$uid		= $this->uid;
		
		// Get blog entry
		if (is_numeric($uid))
		{
			$date	=& JFactory::getDate();
			
			$query	= "SELECT c.*,p.".$db->nameQuote('permalink').", '". 
						$date->toMySQL() ."' as ".$db->nameQuote('curr_time').
						", r.".$db->nameQuote('rating_sum')."/r.".$db->nameQuote('rating_count')." as ".$db->nameQuote('rating').
						", r.".$db->nameQuote('rating_count').
						" FROM (".$db->nameQuote('#__content')." as c,".$db->nameQuote('#__myblog_permalinks')." as p) ".
						"LEFT OUTER JOIN ".$db->nameQuote('#__content_rating')." as r ON (r.".$db->nameQuote('content_id')."=c.".$db->nameQuote('id').
						") WHERE c.".$db->nameQuote('id')."=p.".$db->nameQuote('contentid')." and c.".$db->nameQuote('id')."=".$db->Quote($uid);
			$db->setQuery( $query );
			$row	= $db->loadObject();

			if( !$row )
			{
				$row	=& JTable::getInstance( 'BlogContent' , 'Myblog' );
				$row->load( $uid );
			}
		}
		else
		{	
			$uid = stripslashes($uid);
			$uid = urldecode($uid);
			$uid = $db->getEscaped($uid);

			$row	=& JTable::getInstance( 'BlogContent' , 'Myblog' );
			$row->load($uid);
		}
		$this->row = &$row;
	}
	
	function _header()
	{
		return;
	}
	
	// return true if the entry is logged in user entry
	function isMyEntry()
	{
		$my		=& JFactory::getUser();
		
		return ($this->row->created_by == $my->id );
	}

	function display($styleid = '', $wrapTag = 'div')
	{
		global $MYBLOG_LANG, $_MY_CONFIG;

		if(!myAllowedGuestView('entry'))
		{
			$template		= new AzrulJXTemplate();
			$content		= $template->fetch($this->_getTemplateName('permissions'));
			return $content;
		}
		
		$Itemid		= myGetItemId();
		$row		= null;
		$task		= '';
		$task_url	= "";

		if ($task!="")
		{
			$task_url = "&task=$task";
		}
		
		// Load plugins
		$this->_plugins->load();
		
		$row = &$this->row;

		// Need to fix the permalink with complete path
		$row->permalink = myGetPermalinkUrl($row->id);
		$row->comments = "";
		$mainframe		=& JFactory::getApplication();
		$date			=& JFactory::getDate( $row->created );
		$date->setOffSet( $mainframe->getCfg( 'offset' ) );

		$row->createdFormatted	= $date->toFormat( $_MY_CONFIG->get('dateFormat') );
		$row->title = myUnhtmlspecialchars($row->title);
		$row->title = htmlspecialchars($row->title); 
		$db			=& JFactory::getDBO();
		
		$currentDate	=& JFactory::getDate();
		if ($row->state != 1 || $row->publish_up > $currentDate->toMySQL() )
		{
			echo "Cannot find the entry.The user has either change the permanent link or the content has not been published.";
			return;
		}
		else
		{
			$db->setQuery("UPDATE ".$db->nameQuote('#__content')." SET ".$db->nameQuote('hits')."=hits+1 WHERE ".$db->nameQuote('id')."=".$db->Quote($row->id));
			$db->query();
		}

		// Page title will be added whatever $mosConfig_MetaTitle setting is
		myAddPageTitle(myUnhtmlspecialchars($row->title));
		myAddPathway($row->title);

                $doc =& JFactory::getDocument();


		if ($mainframe->getCfg('MetaAuthor') == '1')
		{
			$doc->setMetaData( 'tag-name' , $row->created_by );
		}

		// Attach meta tags for tags. Get the tags for this article.
		$tags	= myGetTags($row->id);
		
		foreach($tags as $tag){
			$doc->setMetaData('tag-keywords', $tag->name);
		}
		
		$doc->setMetaData( 'tag-description', $row->metadesc );
		$doc->setMetaData( 'tag-keywords', $row->metakey );
		/** end add meta tags **/
		
		$user	=& JFactory::getUser();
		
		$tpl = new AzrulJXCachedTemplate(serialize($row) . $user->usertype . $_MY_CONFIG->get('template') . $task);


		if (!$tpl->is_cached())
		{
			// Process text, combine introtext/fulltext
			$row->text	= '';
			
			if($row->introtext && trim($row->introtext) != '')
			{
				$row->text	.= $row->introtext;
			}

			// Process anchor for #readmore
			if($_MY_CONFIG->get('anchorReadmore'))
			{
				$tmpFull	= $row->text;
				$tmp		= substr($row->text, 0 , strlen($row->text) - 10);
				$row->text	= $tmp;
				$row->text	.= '<a name="readmore"></a>';
				$row->text	.= substr($tmpFull, strlen($tmpFull) - 10, strlen($tmpFull));					
			}
			
			// Add the rest of the fulltext
			if($row->fulltext && trim($row->fulltext) != '')
			{
				$row->text	.= $row->fulltext;
			}
			
			$row->author = myUserGetName($row->created_by, $_MY_CONFIG->get('useFullName'));
			$row->authorLink = JRoute::_("index.php?option=com_myblog$task_url&blogger=" . urlencode(myGetAuthorName($row->created_by)) . "&Itemid=$Itemid");
			$row->categories = myCategoriesURLGet($row->id, true, $task);
			$row->emailLink = JRoute::_("index.php?option=com_content&task=emailform&id={$this->uid}");
			$row->jcategory		= '<a href="' . JRoute::_('index.php?option=com_myblog&task=tag&jcategory=' . $row->catid ) . '">' . myGetJoomlaCategoryName( $row->catid ) . '</a>';
			// Get the avatar
			//$this->_setupAvatarHTML($row);
			$avatar	= 'My' . ucfirst($_MY_CONFIG->get('avatar')) . 'Avatar';
			$avatar	= new $avatar($row->created_by);
			
			$row->avatar	= $avatar->get();
			
			// Social Bookmarking display
			if($_MY_CONFIG->get('showBookmarking')){
				// Not needed for print view
				// myGetBookmarks($row);
			}
			
			$row->afterContent = '';
			$row->beforeContent = '';

			$params	= $this->_buildParams();
			$row->beforeContent		= @$this->_plugins->trigger('onBeforeDisplayContent', $row, $params, 0);
			$row->onPrepareContent	= @$this->_plugins->trigger('onPrepareContent', $row, $params, 0);
			$row->afterContent		= "<br/>". @$this->_plugins->trigger('onAfterDisplayContent', $row, $params, 0);
			
			$date					= new JDate( $row->created );
			
			$date->setOffSet( $mainframe->getCfg('offset') );
			$row->created			= $date->toFormat( "%Y-%m-%d %H:%M:%S" );

			$row->editLink = '<span style="cursor:pointer;" onclick="myAzrulShowWindow(\'index.php?option=com_myblog&task=write&keepThis=true&TB_iframe=true&no_html=1&id='.$row->id.'\');">&nbsp;[Edit]&nbsp;</span>';
			// Check if user enables back link
			if($_MY_CONFIG->get('enableBackLink'))
				$row->afterContent .= myGetBackLink();

			// Remove all member with '_' prefix. Get rid of reference to cms/cmsdb
			unset($row->_table);
			unset($row->_key);
			unset($row->_db);

			$tpl->set('userId', $user->id);
			$tpl->set('entry', $tpl->object_to_array($row));

		}


		$content = $tpl->fetch_cache(MY_TEMPLATE_PATH . "/admin/printview.html");
		return $content;
	}
}
?>
