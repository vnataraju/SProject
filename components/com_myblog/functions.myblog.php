<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

global $_MY_CONFIG;

if( !$_MY_CONFIG )
{
	require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_myblog' . DS . 'config.myblog.php' );
	$_MY_CONFIG 	= new MYBLOG_Config();
}

/**
* Get a draft version of the article
*/
function myGetDraft($contentid, $userid){
	$db		=& JFactory::getDBO();
	$strSQL	= "SELECT draft_json_content FROM ".$db->nameQuote('#__myblog_drafts')." WHERE ".$db->nameQuote('content_id').'='.$db->Quote($contentid).' AND '.$db->nameQuote('user_id').'='.$db->Quote($userid);
	$db->setQuery( $strSQL );

	$result	= $db->loadResult();
	if(empty($result)) return false;
	return json_decode($result);
}


/**
 *	Info: To reset the ordering for older entries and set the new ordering for a content
 *	@params: $entry (blogContent) 
 **/
function mySortOrder(&$entry)
{
	$db		=& JFactory::getDBO();
	$strSQL	= "SELECT * FROM ".$db->nameQuote('#__content')." WHERE ".$db->nameQuote('catid').'='.$db->Quote($entry->catid);
	$db->setQuery( $strSQL );

	$result	= $db->loadObjectList();

	// If there is already an ordering, dont perform anything since My Blog doesn't allow user to change the ordering.
	// User may be editing the content, we dont want to update the ordering.
	if(!$entry->ordering)
	{
		// We give ourself a new ordering id for this specific content it.
		if(count($result) == 0)
		{
			$entry->ordering	= 1;
		}
		else
		{
			$entry->ordering	= count($result) + 1;
		}
		//$entry->store();
		$strSQL	= "UPDATE ".$db->nameQuote('#__content').
				" SET ".$db->nameQuote('ordering').'='.$db->Quote($entry->ordering).
				' WHERE '.$db->nameQuote('id').'='.$db->Quote($entry->id);
		$db->setQuery( $strSQL );
		$db->query();
	}
}

/**
 * Retrieves pagination object
 **/ 
function myPagination($total, $limitstart, $limit)
{
	$pagination	= new stdClass();
	
	jimport('joomla.html.pagination');
	$pageNav				= new JPagination($total, $limitstart, $limit);
	
	$pagination->limitstart	= $limitstart;
	$pagination->limit		= $limit;
	$pagination->total		= $total;
	$pagination->footer		= $pageNav->getListFooter();
	$pagination->links		= $pageNav->getPagesLinks();

	return $pagination;
}

function mySendTrackbacks($contentId, $trackbacks)
{
	global $_MY_CONFIG;

	$mainframe	=& JFactory::getApplication();

	$sections	= $_MY_CONFIG->get('managedSections');
	
	if(!class_exists('Trackback'))
	{
		require_once( MY_LIBRARY_PATH . DS . 'trackback' . DS . 'trackback_cls.php' );
	}
	
	$db			=& JFactory::getDBO();
	$strSQL	= "SELECT a.*, b.".$db->nameQuote('permalink').", c.".$db->nameQuote('name')
			. " FROM ".$db->nameQuote('#__content')." AS a, "
			. $db->nameQuote('#__myblog_permalinks')." AS b, "
			. $db->nameQuote('#__users')." AS c "
			. "WHERE a.".$db->nameQuote('id').'=.b.'.$db->nameQuote('contentid')
			. " AND a.".$db->nameQuote('id')."=".$db->Quote($contentId)
			. " AND a.".$db->nameQuote('created_by').'=c.'.$db->nameQuote('id')
			. " AND a.".$db->nameQuote('sectionid')." IN ($sections)";
	
	$db->setQuery( $strSQL );
	$row 	= $db->loadObject();
	$Itemid	= myGetItemId();
        $Itemid = empty($Itemid) ? myGetAdminItemId():$Itemid;
	
	if($trackbacks && $trackbacks != '' && $row)
	{
		$trackbacks	= explode(',', $trackbacks);
		
		foreach($trackbacks as $url)
		{
			// Check if we have already sent a trackback request.
			$strSQL	= "SELECT COUNT(*) FROM ".$db->nameQuote('#__myblog_tb_sent')
					. " WHERE ".$db->nameQuote('url').'='.$db->Quote($url)." AND "
					. $db->nameQuote('contentid').'='.$db->Quote($row->id);
			$db->setQuery( $strSQL );
			
			// Since we never send a request before, send it.
			if( $db->loadResult() <= 0)
			{
				$trackback	= new Trackback($mainframe->getCfg('sitename'), $row->name, 'UTF-8');
				$url		= trim(strip_tags($url));
				
				
				$content			= new stdClass();
				$content->id		= $row->id;
				$permalinkUrl		= myGetPermalinkUrl($row->id);
				$content->url		= myGetExternalLink($permalinkUrl);								
				$content->title		= $trackback->cut_short($row->title);
				$content->excerpt	= $trackback->cut_short($row->introtext . $row->fulltext);
				
				if($trackback->ping($url, $content->url, $content->title, $content->excerpt))
				{
					// Add into the database that we actually sent a trackback
					$strSQL	= "INSERT INTO ".$db->nameQuote('#__myblog_tb_sent')." SET ".$db->nameQuote('url')."=".$db->Quote($url).",".$db->nameQuote('contentid')."=".$db->Quote($content->id);
					$db->setQuery( $strSQL );
					$db->query();
				}
			}
						
		}
	}
	else
	{
		return false;
	}
}

function myAllowedGuestView($context)
{
	global $_MY_CONFIG;
	
	$my			=& JFactory::getUser();
	$allowed	= true;

	if($context == 'intro')
	{
		if($_MY_CONFIG->get('viewIntro') == '2' && $my->id == '0')
			$allowed	= false;
	} 
	else if($context == 'entry')
	{
		if($_MY_CONFIG->get('viewEntry') == '2' && $my->id == '0')
			$allowed	= false;
	}
	return $allowed;
}

// Add a list of new and existing tags based on the array
function myAddTags($contentId, $tags)
{
	global $_MY_CONFIG;

	include_once(MY_LIBRARY_PATH.'/tags.php');
	
	$tagObj			= new MYTags();
	$db 			=& JFactory::getDBO();

	// delete old tags used by this current content id.
	$strSQL			= "DELETE FROM ".$db->nameQuote('#__myblog_content_categories')." WHERE ".$db->nameQuote('contentid')."=".$db->Quote($contentId);
	$db->setQuery( $strSQL );
	$db->query();

        if(!empty($tags)){
           if( is_array( $tags ) )
            {
                    // Add tags
                    foreach($tags as $tag)
                    {
                            $tagObj->add(trim($tag));

                            // Update the blog entry with this tag's id.
                            $strSQL	= "INSERT INTO ".$db->nameQuote('#__myblog_content_categories')
                                            . " (".$db->nameQuote('contentid').",".$db->nameQuote('category')
                                            .") VALUES (".$db->Quote($contentId).", ".$db->Quote($tagObj->insertId).")";
                            $db->setQuery( $strSQL );
                            $db->query();
                    }
            }
            else
            {
                    $tagObj->add(trim($tags));

                    // Update the blog entry with this tag's id.
                    $strSQL	= "INSERT INTO ".$db->nameQuote('#__myblog_content_categories')
                                            . " (".$db->nameQuote('contentid').",".$db->nameQuote('category')
                                            .") VALUES (".$db->Quote($contentId).", ".$db->Quote($tagObj->insertId).")";
                    $db->setQuery( $strSQL );
                    $db->query();
            }
        }
        
}

/**
 * Retrieves the tag id for specific tag.
 *
 * @param $tagName	Tag name. 
 */  
function myGetTagId( $tagName )
{
	$db   =& JFactory::getDBO();
	$db->setQuery("SELECT ".$db->nameQuote('id')." FROM ".$db->nameQuote('#__myblog_categories')." WHERE ".$db->nameQuote('name')."=". $db->Quote($tagName) ); 
	return $db->loadResult();
}

/**
 *	Get the tag name since we have a slug now, we need to map it to the correct tag.
 **/ 
function myGetTagName($tag)
{
	$db  =& JFactory::getDBO(); 
	$strSQL = "SELECT ".$db->nameQuote('name').
			" FROM ".$db->nameQuote('#__myblog_categories').
			" WHERE ".$db->nameQuote('name').
			" LIKE " .$db->Quote($tag).
			" OR ".$db->nameQuote('slug').
			" LIKE " .$db->Quote($tag)."";
	$db->setQuery( $strSQL );
	return $db->loadResult();
}

function myGetUsedTags($userId)
{
	$db		=& JFactory::getDBO();
	
	$strSQL	= "SELECT DISTINCT c.".$db->nameQuote('name').", c.".$db->nameQuote('slug')
			." FROM ".$db->nameQuote('#__content')." AS a, "
			. $db->nameQuote('#__myblog_content_categories')." AS b, "
			. $db->nameQuote('#__myblog_categories')." AS c "
			. "WHERE a.".$db->nameQuote('id').'=b.'.$db->nameQuote('contentid')
			. "AND c.".$db->nameQuote('id')."=b.".$db->nameQuote('category')
			. "AND a.".$db->nameQuote('created_by')."=".$db->Quote($userId);

	$db->setQuery($strSQL);
	return $db->loadObjectList();
}

/**
 *	Get list of tags for a given content
 */ 
function myGetTags($contentid)
{
	$db =& JFactory::getDBO();
	
	$query = "SELECT b.".$db->nameQuote('id').",b.".$db->nameQuote('name').", b.".$db->nameQuote('slug').
			" FROM ".$db->nameQuote('#__myblog_content_categories')." as a,".$db->nameQuote('#__myblog_categories').
			" as b WHERE b.".$db->nameQuote('id')."=a.".$db->nameQuote('category')." AND a.".$db->nameQuote('contentid').'='.$db->Quote($contentid).
			" ORDER BY b.".$db->nameQuote('name')." DESC";
	$db->setQuery( $query );
	$result = $db->loadObjectList();
	
	// Check for slug if it exists, use it as name
	for($i = 0; $i < count($result); $i++)
	{
		$tag	= $result[$i];
		
		if($tag->slug == '')
			$tag->slug	= $tag->name;
	}

	return $result;
}

function myGetSid($length = 12)
{
	$token	= md5(uniqid('a'));
	$sid	= md5(uniqid(rand(), true));
	
	$return	= '';
	for($i = 0; $i < $length; $i++){
		$return .= substr($sid, rand(1, ($length-1)), 1 );
	}
	return $return;
}

function myGetExternalLink( $url , $xhtml = false )
{
	$uri	=& JURI::getInstance();
	$base	= $uri->toString( array('scheme', 'host', 'port'));
		
	return $base . JRoute::_( $url , $xhtml );
}

function myNotifyAdmin($contentId, $author = '', $title = '', $text = '', $isNew)
{
	global $_MY_CONFIG, $MYBLOG_LANG;
	
	$mainframe	=& JFactory::getApplication();
	$status		= false;
	$db 		=& JFactory::getDBO();
	
	// Notify administrators.
	if($_MY_CONFIG->get('allowNotification'))
	{
		$emails 	= $_MY_CONFIG->get('adminEmail');
		
		if(empty($emails)) return false;

		$recipients	= explode(',', $_MY_CONFIG->get('adminEmail'));
		
		if(!class_exists('MYMailer'))
			include_once(MY_LIBRARY_PATH . '/mail.php');
		
		if(!class_exists('AzrulJXTemplate'))
		{
			include_once( JPATH_PLUGINS . DS . 'system' . DS . 'pc_includes' . DS . 'template.php' );
		}
			

		$sid	= myGetSid();
		$date	= strftime("%Y-%m-%d %H:%M:%S", time() + ($mainframe->getCfg('offset') * 60 * 60));

		// Maintenance mode, clear existing 'sid'
		$strSQL	= "DELETE FROM ".$db->nameQuote('#__myblog_admin')." WHERE ".$db->nameQuote('sid')."=".$db->Quote($sid);
		$db->setQuery($strSQL);
		$db->query();
		
		// Re-insert new sid
		$strSQL	= "INSERT INTO ".$db->nameQuote('#__myblog_admin')." SET ".
					$db->nameQuote('sid')."=".$db->Quote($sid).", ".
					$db->nameQuote('cid')."=".$db->Quote($contentId).", ".
					$db->nameQuote('date')."=".$db->Quote($date);
		$db->setQuery($strSQL);
		$db->query();
		
		// Publish link
		$publish	= myGetExternalLink('index.php?option=com_myblog&task=admin&do=publish&sid=' . $sid , false );
		
		// Unpublish link
		$unpublish	= myGetExternalLink('index.php?option=com_myblog&task=admin&do=unpublish&sid=' . $sid , false );
		
		// Delete link
		$delete		= myGetExternalLink('index.php?option=com_myblog&task=admin&do=remove&sid=' . $sid , false );

		$template	= new AzrulJXTemplate();
		
		$text		= strip_tags($text);
		
		if($isNew)
		{
			$content	= $template->fetch(MY_TEMPLATE_PATH . "/_default/new.notify.tmpl.html");
			
			$content	= str_replace('%PUBLISH%', $publish, $content);
			$content	= str_replace('%UNPUBLISH%', $unpublish, $content);
			$content	= str_replace('%DELETE%', $delete, $content);
			$content	= str_replace('%AUTHOR%', $author, $content);
			$content	= str_replace('%TITLE%', $title, $content);
			$content	= str_replace('%ENTRY%', $text, $content);
			
			$title		= JText::_('A new blog entry has been posted');
		}
		else
		{
			// Entry is updated.
			$content	= $template->fetch(MY_TEMPLATE_PATH . "/_default/update.notify.tmpl.html");
			
			$content	= str_replace('%UNPUBLISH%', JRoute::_($unpublish), $content);
			$content	= str_replace('%DELETE%', JRoute::_($delete), $content);
			$content	= str_replace('%AUTHOR%', $author, $content);
			$content	= str_replace('%TITLE%', $title, $content);
			$content	= str_replace('%ENTRY%', $text, $content);
			
			$title		= JText::_('A blog entry has been updated');
		}
		$mail		= new MYMailer();
		
		$status		= $mail->send($mainframe->getCfg('mailfrom'), $recipients, $title, $content);
	}
	return $status;
}

function myClearCache()
{
	$cache		=& JFactory::getCache();
	
	$cache->clean();
}

function myGetUser($session)
{	
	if($session)
	{
		$vars	= explode(':' , $session);
		$db		=& JFactory::getDBO();

		$strSQL	= "SELECT ".$db->nameQuote('userid').", ".$db->nameQuote('username').", ".$db->nameQuote('usertype')
				." FROM ".$db->nameQuote('#__session')." WHERE ".$db->nameQuote('userid')."=" . $db->Quote($vars[0])
				." AND ".$db->nameQuote('session_id')."=" . $db->Quote($vars[1]);
		
		$db->setQuery( $strSQL );
		$data	= $db->loadObject();

		if($data)
		{
			//use clone here because we only want to bypass the front end login for the sake of showing the editor.
			//we do not want to actually log this user in on the front end
			$orig_my =& JFactory::getUser();
			$my = clone $orig_my;
			$my->id		= $data->userid;
			$my->username	= $data->username;
			$my->usertype	= $data->usertype;
			
			//For Joomla 1.6 or later, we want to compare usergroup instead of user types. 
			//So lets get the user group here and compare them with those allowed to post entry as specified in the Myblog settings
			if(JVERSION >= 1.6){
				$groups_sql = "SELECT a.group_id, b.title   
								FROM `#__user_usergroup_map` a 
								JOIN `#__usergroups` b
								ON a.group_id = b.id 
								WHERE a.user_id=".$db->Quote($vars[0]);
				$db->setQuery( $groups_sql );
				$grps = $db->loadObjectList();
				
				$tmp = array();
				foreach($grps as $g){
					$tmp[$g->title] = $g->group_id;
				}
				$my->groups = $tmp;
				unset($tmp);
			}
			
			return $my;
		}
	}
	return false;
}

/**
 * Get the status if user is allowed to post
 * Return: boolean
 **/
function myGetUserCanPost( $id = 0 )
{
	global $_MY_CONFIG;
	
	// Check posting permissions
	$posterIds  		= explode(',', $_MY_CONFIG->get('allowedPosters'));
	$postGroups 		= explode(',', $_MY_CONFIG->get('postGroup'));
	$extraPostGroups    = explode(',', $_MY_CONFIG->get('extraPostGroups'));
	$adminPostGroups    = explode(',', $_MY_CONFIG->get('adminPostGroup'));
	$disallowedUsers	= explode(',', $_MY_CONFIG->get('disallowedPosters'));
	
	if( $id == 0 )
	{
		$my =& JFactory::getUser();
		
		//since editor is using iFrame, we need to simulate a login in order to get the current user data for access permission
		$session= JRequest::getVar( 'session' , '' , 'GET' );
		if($session){
			$my = myGetUser($session);
		}else{
			if(JVERSION < 1.6 && $my->guest==1) return false;
		}
	}
	else
	{
		$my				=& JFactory::getUser( $id );
	}

	$userId			= $my->id;

        // usertype desprecated in Joomla >1.6
        $userType = '';
        $userGroups = '';
        $userGroupName = array();
        $userGroupId = array();

        if(JVERSION <= 1.5){
            $userType		= $my->usertype;
        }
		else{
			$userGroups = $my->groups;
	
			foreach($userGroups as $key=>$value){
				$userGroupName[] = $key;
				$userGroupId[] = $value;
			}
		}
		$userName		= $my->username;

        if(!$userId)
	{
		return false;
	}
	
	array_walk($postGroups, 'myTrim');
	array_walk($extraPostGroups, 'myTrim');
	array_walk($adminPostGroups, 'myTrim');

	# If user is not allowed to post
        if(JVERSION <= 1.5){

            if (!in_array($userType, $adminPostGroups))
            {
                    if (!in_array($userType, $extraPostGroups) && !in_array($userType, $postGroups) && !(in_array($userId, $posterIds) || in_array(myUserGetName($userId),$posterIds)))
                    {
                            return false;
                    }
            }
        }
        else{
            if (!array_intersect($userGroupName, $adminPostGroups))
            {
                if (!array_intersect($userGroupName, $extraPostGroups)  && !array_intersect($userGroupName, $postGroups) && !(in_array($userId, $posterIds) || in_array(myUserGetName($userId),$posterIds)))
                {
                        return false;
                }
            }
            
        }
        
	// Check if users is specifically blocked from posting.
	if(in_array($userId, $disallowedUsers) || in_array($userName, $disallowedUsers))
		return false;

	return true;
}

/**
 * Get the status if user is allowed to publish or unpublish
 * Return: boolean
 **/
function myGetUserCanPublish()
{
	global $_MY_CONFIG;

	$publishRights  = false;
	
	// list of publishing permissions
	$posterIds      	= explode(',', $_MY_CONFIG->get('allowedPublishers'));
	$publishers     	= explode(',', $_MY_CONFIG->get('publishControlGroup'));
	$extraPublishGroups = explode(',', strtolower($_MY_CONFIG->get('extraPublishGroups')));
	$adminPublishGroup	= explode(',', $_MY_CONFIG->get('adminPublishControlGroup'));
	
	array_walk($extraPublishGroups, 'myTrim');
	
	$user 		= & JFactory::getUser();
	
	if( $user->id == 0 )
	{		
		//since editor is using iFrame, we need to simulate a login in order to get the current user data for access permission
		$session= JRequest::getVar( 'session' , '' , 'GET' );
		if($session)
			$user = myGetUser($session);
	}
	$userId	= $user->id;
	
	// usertype desprecated in Joomla >1.6
	$userType = '';
	$userGroups = '';
	
	if(JVERSION >= 1.6){
		$userType = $user->groups;
		foreach($userType as $gname => $gid){
			
			if(in_array($gname, $adminPublishGroup)) return true;
			if(in_array($gname, $publishers)) return true;
			if(in_array($gid, $posterIds)) return true;
			if(in_array(strtolower($gname), $extraPublishGroups)) return true;
			if(in_array(myUserGetName($userId), $posterIds)) return true;
		}
		return false;
	}else{
		$userType = $user->usertype;
		if (in_array($userType, $extraPublishGroups) or (in_array($userId, $posterIds) or in_array(myUserGetName($userId),$posterIds)) or in_array($userType, $publishers) or in_array($userType, $adminPublishGroup))
			return true;
		return false;
	}
}

/**
 * Get the status of the azrul video mambot
 * Return: boolean
 **/
function myGetAzVideoBot()
{
	$azVideoId  = '';

	// Get the azrul video mambot's id
	$strSQL		= "SELECT `id` FROM #__mambots WHERE `element`='azvideobot' AND `folder`='content'";
	$db->setQuery($strSQL);
	$azVideoId	= $db->loadResult();
	
	// Check if azrulvideomambot file even exists
	if(file_exists( JPATH_PLUGINS . DS . 'content' . DS . 'azvideobot.php') && ( $azVideoId ))
	{
		$strSQL = "SELECT `my_published` FROM #__myblog_mambots WHERE `mambot_id`=".$db->Quote($azVideoId);
		$db->setQuery($strSQL);

		if($db->loadResult() == "1")
		{
		    return true;
		}
	}
	return false;
}

/**
 * Get the status of Jom Comment installation and integrated on the site.
 * Return: boolean
 **/
function myGetJomComment()
{
	global $_MY_CONFIG;

	jimport( 'joomla.filesystem.file' );

	$file	= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_jomcomment' . DS . 'config.jomcomment.php';

	if($_MY_CONFIG->get('useComment') && JFile::exists( $file ) )
	    return true;

	return false;
}

/**
 *	Get list of tags for a given user
 */ 
function myGetUserTags($uid)
{
	global $_MY_CONFIG;
	$db			=& JFactory::getDBO();
	
	$sections	= $_MY_CONFIG->get('managedSections');
	$db->setQuery("SELECT distinct(b.name) from #__content as c,#__myblog_content_categories as a,#__myblog_categories as b WHERE c.id=a.contentid and b.id=a.category and c.created_by=".$db->Quote($uid)." and c.state='1' and c.sectionid in ($sections) and c.publish_up < now() ORDER BY b.name DESC");
	return $db->loadObjectList();
}

/**
 *	Replace newlines with <br/> tags
 */ 
function my_strict_nl2br($text, $replac = " <br />")
{
	return preg_replace("/\r\n|\n|\r/", $replac, $text);
}

/**
 *	Retrieve number of entries for a given user
 */ 
function myCountUserEntry($uid, $exclude_drafts = "")
{
	global $_MY_CONFIG;

	$sections   = $_MY_CONFIG->get('managedSections');
	$db			=& JFactory::getDBO();
	
	$extra 		= "";
	
	$date		=& JFactory::getDate();
	
	if ($exclude_drafts == "1")
	{
		$extra = "and state='1' and publish_up < '" . $date->toMySQL() . "'";
	}

        $extra .= (JVERSION >= 1.6)
			? " AND catid IN (" . $_MY_CONFIG->get('managedSections') . ")"
			: " AND sectionid IN (" . $_MY_CONFIG->get('managedSections') . ")";


	$db->setQuery("SELECT COUNT(*) FROM #__content WHERE created_by=".$db->Quote($uid)."  $extra");
	$result = $db->loadResult();
	
	if ($result == "")
		return 0;
	
	return $result;
}

/**
 *	Retrieve number of comments for a given user
 */ 
function myCountUserComment($uid)
{
	global $_MY_CONFIG;
	
	$sections   = $_MY_CONFIG->get('managedSections');
	$db			=& JFactory::getDBO();
	
	$strSQL = "SELECT COUNT(*) FROM #__jomcomment AS a "
			. "INNER JOIN #__content AS b "
			. "WHERE b.id=a.contentid "
			. "AND a.option=".$db->Quote('com_myblog')
			. "AND b.created_by=".$db->Quote($uid);
	$db->setQuery($strSQL);
	
	$result = $db->loadResult();

	if ($result == "")
		return 0;
	
	return $result;
}

/**
 *	Retrieve number of hits for a given user
 */ 
function myCountUserHits($uid)
{
	global $_MY_CONFIG;

	$db			=& JFactory::getDBO();
	$sections   = $_MY_CONFIG->get('managedSections');
	
	$db->setQuery("SELECT SUM(hits) FROM #__content WHERE created_by=".$db->Quote($uid)." and sectionid in ($sections) and state='1' and publish_up < NOW()");
	$result = $db->loadResult();
	
	if ($result == "")
		return 0;
	
	return $result;
}

/**
 *	Retrieves a list of links to the unpublsihed entries for a user
 */ 
function myGetUserDraft($uid)
{
	global $MYBLOG_LANG, $Itemid, $_MY_CONFIG;
	
	$db			=& JFactory::getDBO();
	$sections	= $_MY_CONFIG->get('managedSections');

	$db->setQuery("SELECT title,created,id FROM #__content WHERE created_by=".$db->Quote($uid)." AND state=0 and sectionid in ($sections) ORDER BY created");
	$result = $db->loadObjectList();
	$drafts = "<em>";

	if ($result)
	{
		foreach ($result as $row)
		{
			$draftlink = JRoute::_("index.php?option=com_myblog&no_html=1&task=edit&id=$row->id&Itemid=$Itemid&admin=1&tmpl=component");
			
			if ($drafts != "<em>")
				$drafts .= ", ";
			$drafts .= "<a class=\"draftLink\" href=\"$draftlink\">".$row->title."</a>";
		}
	}
	else
	{
		$drafts = $MYBLOG_LANG['_MB_NO_DRAFTS'];
	}
	
	$drafts .= "</em>";
	
	return $drafts;
}

/**
 *	Retrieves number of Jom comments for an article.
 */ 
function myCountJomcomment($article_Id, $com = "com_myblog")
{
	$db	=& JFactory::getDBO();
	
	$db->setQuery("SELECT COUNT(*) FROM #__jomcomment WHERE contentid=".$db->Quote($article_Id)." AND (`option`=".$db->Quote($com)." OR `option`=".$db->Quote('com_content').") AND published='1'");
	$result = $db->loadResult();
	
	return $result;
}

/**
 * Retrieves number of articles in a selected category.
 **/
function myGetCategoryCount($categoryId)
{
	$db		=& JFactory::getDBO();
	$date	=& JFactory::getDate();
	
	$strSQL = "SELECT COUNT(*) FROM #__content "
			. "WHERE `catid`=".$db->Quote($categoryId)." AND `state`='1' AND created <= " . $db->Quote( $date->toMySQL() );
                        
	$db->setQuery($strSQL);
	return $db->loadResult();
}

/**
 *	Get a list of categories for a content.
 */ 
function myCategoriesURLGet($contentid, $linkCats = true, $task="")
{
	global $MYBLOG_LANG;
	
	$Itemid		= myGetItemId();
        $Itemid = empty($Itemid) ? myGetAdminItemId():$Itemid;
	$result		= myGetTags($contentid);
	$catCount	= count($result);
	$link		= "";
	$task		= empty($task) ? '&task=tag' : "&task=$task" ;
	
	if($result)
	{
		$count = 0;
		
		foreach ($result as $row)
		{
			if ($link != "")
				$link .= ", ";
			
			if ($linkCats)
			{
				$url	= JRoute::_('index.php?option=com_myblog' . $task . '&category=' . urlencode($row->slug) . '&Itemid=' . $Itemid);				
				$link .= "<a href=\"$url\">$row->name</a>";
			}
			else
			{
				$link .= $row->name;
			}
			$count++;
		}
	}
	else
	{
		$link .= "<em>". JText::_('COM_MY_BLOG_UNTAGGED') ."</em>&nbsp;";
	}
	
	return $link;
}

/**
 *	Retrieves the comment link for a content
 */ 
function myCommentsURLGet($contentid, $addCommentCount = true, $task="")
{
	global $Itemid, $MYBLOG_LANG;
	
	$link = "";
	
	if ($task!="")
		$task = "&task=$task";
	
	if ($addCommentCount)
	{
		$numcomment = intval(myCountJomcomment($contentid));
	}
	
	return JText::sprintf('COM_MY_BLOG_COMMENTS' , $numcomment ); //$link;
}


/**
 *	Gets the author username/fullname   
 */ 
function myGetAuthorName($uid, $useFullName="")
{
	$db		=& JFactory::getDBO();
	
	$select = "username";
	
	if ($useFullName == "1")
		$select = "name";
	
	$db->setQuery("SELECT ".$db->nameQuote($select)." FROM #__users WHERE id=".$db->Quote($uid));
	$result = $db->loadResult();
	
	return $result;
}

function myGetAuthorEmail($uid)
{
	$db		=& JFactory::getDBO();
	
	$db->setQuery("SELECT `email` FROM #__users WHERE id=".$db->Quote($uid));
	$result = $db->loadResult();
	
	return $result;
}

/** %UserName% 
 *	Gets the author's id
 */ 
function myGetAuthorId($name, $useFullName="")
{
	$db		=& JFactory::getDBO();
	
	$name	= urldecode($name);

	// If name is empty, just return 0 since some site somehow has username's ''
	if(!$name)
		return '0';

	// If display full name instead of username, need to check both fullname nad username,
	// just in case the blogger url param contains the username.
	if ($useFullName=="1")
	{
		$db->setQuery("SELECT id FROM #__users WHERE name RLIKE '[[:<:]]$name"."[[:>:]]' or username RLIKE '[[:<:]]$name"."[[:>:]]' ");
		$result = $db->loadResult();
		return ($result ? $result : "0");
	}
	
	// get exact username match
	$name	= $db->getEscaped( $name );

	$db->setQuery("SELECT id FROM #__users WHERE username=".$db->Quote($name));

	$result = $db->loadResult();

	// if not found, try get similar match
	if (!$result)
	{
		$db->setQuery("SELECT id FROM #__users WHERE username RLIKE '[[:<:]]$name"."[[:>:]]'");
		$result = $db->loadResult();
	}
	
	// if still not found, check name and see if contains username.
	if (!$result)
	{
		$db->setQuery("SELECT id FROM #__users WHERE name RLIKE '[[:<:]]$name"."[[:>:]]'");
		$result = $db->loadResult();
	}
	
	return ($result ? $result : "0");
}

/**
 *	Gets the author username/fullname 
 */ 
function myUserGetName($uid, $useFullName="")
{
	$db		=& JFactory::getDBO();
	
	$uid	= intval($uid);
	$select = "username";
	
	if ($useFullName == "1")
		$select = "name";
	
	$db->setQuery("SELECT $select FROM #__users WHERE id=".$db->Quote($uid));
	$username = $db->loadResult();

	return $username;
}

// Return the complete & valid permalink-URL for the given content 
function myGetPermalinkUrl($uid, $task="", $blogger = '')
{	
	$db			=& JFactory::getDBO();
	$Itemid 	= myGetItemId();
	$Itemid = empty($Itemid) ? myGetAdminItemId():$Itemid;

	$db->setQuery("SELECT permalink from #__myblog_permalinks WHERE contentid=".$db->Quote($uid));
	$link = $db->loadResult();
	
	if (!$link OR empty($link))
	{
		// The permalink might be empty. We need to delete it
		$db->setQuery("DELETE FROM #__myblog_permalinks WHERE contentid=".$db->Quote($uid));
		$db->query();
		
		$db->setQuery("SELECT title from #__content WHERE id=".$db->Quote($uid));
		$title = $db->loadResult();
		
		// remove unwatned chars from permalink
		$link = myTitleToLink(trim($title));
		$link	.= '.html';
		
		$db->setQuery("SELECT count(*) from #__myblog_permalinks WHERE permalink=".$db->Quote($link)." and contentid !=".$db->Quote($uid));
		$linkExists = $db->loadResult();
		
		if ($linkExists)
		{
			$link = myTitleToLink(trim($title));
			
			$plink = "$link-$uid.html";
			$db->setQuery("SELECT count(*) from #__myblog_permalinks WHERE permalink=".$db->Quote($plink)." and contentid !=".$db->Quote($uid));
			$count = 0;
			
			while ($db->loadResult())
			{
				$count++;
				$plink = "$link-$uid-$count.html";
				$db->setQuery("SELECT contentid from #__myblog_permalinks WHERE permalink=".$db->Quote($plink)." and contentid != ".$db->Quote($uid) );
				$db->query();
			}
			$db->setQuery("INSERT INTO #__myblog_permalinks SET permalink=".$db->Quote($plink).",contentid=".$db->Quote($uid));
			$db->query();
		}
		else
		{
			$db->setQuery("INSERT INTO #__myblog_permalinks SET permalink=".$db->Quote($link).",contentid=".$db->Quote($uid));
			$db->query();
		}		
	}
	
	$link = urlencode($link);
	
	if ($task!="")
	{
		$task = "&task=$task";
	}

	if ($blogger!="")
	{
		$blogger = "&blogger=$blogger";
	}
		
	$url 	= "index.php?option=com_myblog&show={$link}{$task}{$blogger}&Itemid={$Itemid}";
	$sefUrl = JRoute::_( $url );
	
	return $sefUrl;
}


function myIsValidPermalink( $permalink , $contentid )
{
	$db		=& JFactory::getDBO();
	
	$db->setQuery("SELECT count(*) from #__myblog_permalinks WHERE permalink=".$db->Quote($permalink)." AND contentid !=".$db->Quote($contentid));
	$result	= $db->loadResult() > 0 ? false : true;
	
	return $result;
}

// Return the complete & valid permalink-URL for the given content
function myGetPermalink($uid, $task="")
{
	$db 	=& JFactory::getDBO();
	$Itemid = myGetItemId();
        $Itemid = empty($Itemid) ? myGetAdminItemId():$Itemid;

	$db->setQuery("SELECT permalink from #__myblog_permalinks WHERE contentid=".$db->Quote($uid));
	$link = $db->loadResult();

	if (!$link OR empty($link))
	{
		// The permalink might be empty. We need to delete it
		$db->setQuery("DELETE FROM #__myblog_permalinks WHERE contentid=".$db->Quote($uid));
		$db->query();
		
		$db->setQuery("SELECT title from #__content WHERE id=".$db->Quote($uid));
		$title = $db->loadResult();

		// remove unwatned chars from permalink
		$link = myTitleToLink(trim($title));
		$link	.= '.html';
		$db->setQuery("SELECT count(*) from #__myblog_permalinks WHERE permalink=".$db->Quote($link)." and contentid !=".$db->Quote($uid));
		$linkExists = $db->loadResult();

		if ($linkExists)
		{
			$link = myTitleToLink(trim($title));

			$plink = "$link-$uid.html";
			$db->setQuery("SELECT contentid from #__myblog_permalinks WHERE permalink=".$db->Quote($plink)." and contentid !=".$db->Quote($uid));
			$count = 0;

			while ($db->loadResult())
			{
				$count++;
				$plink = "$link-$uid-$count.html";
				$db->setQuery("SELECT contentid from #__myblog_permalinks WHERE permalink=".$db->Quote($plink)." and contentid !=".$db->Quote($uid));
			}

			//$plink = urlencode($plink);
			$db->setQuery("INSERT INTO #__myblog_permalinks SET permalink=".$db->Quote($plink).",contentid=".$db->Quote($uid) );
			$db->query();
   			return $plink;
		}
		else
		{
			$db->setQuery("INSERT INTO #__myblog_permalinks SET permalink=".$db->Quote($link).",contentid=".$db->Quote($uid) );
			$db->query();
            return $link;
		}
	}
	return $link;
}

/**
 *	Displays images on image browser, resized while maintaining aspect ratio
 */ 
function displayAndResizeImages($img_array)
{
	$uploadedImages = "";
	
	if ($img_array) {
		foreach ($img_array as $img) {
			if (!empty ($img)) {
				$img['filename'] = trim($img['filename']);
				$imageMaxSize = 80;
				$imageWidth = $img['width'];
				$imageHeight = $img['height'];
				
				if ($imageHeight > $imageMaxSize or $imageWidth > $imageMaxSize) {
					if ($imageHeight > $imageWidth) {
						$imageWidth = ($imageMaxSize / $imageHeight) * $imageWidth;
						$imageHeight = $imageMaxSize;
					} else {
						$imageHeight = ($imageMaxSize / $imageWidth) * $imageHeight;
						$imageWidth = $imageMaxSize;
					}
				}
				
				$basename = basename($img['filename']);
				
				
				$imgtag = "<img src='{$img['filename']}' hspace='4' vspace='4' align='left' alt='$basename' border='0' />";
				$imgtag = addslashes($imgtag);
				$onDblCick	 ="tinyMCE.execCommand('mceFocus',false, 'mce_editor_0');";
				$onDblCick	.="tinyMCE.execCommand('mceInsertContent',false, '$imgtag');";
				
				
				$uploadedImages .= "<div title=\"$basename\" class=\"imgContainerOut\" ondblclick=\"$onDblCick\">
					<span class=\"imgContainer\" onmouseover=\"this.className='imgContainerHover';\" onmouseout=\"this.className='imgContainer';\">
						<center>
							<img src=\"" . $img['filename'] . "\" width=\"$imageWidth\" height=\"$imageHeight\" />
						</center>
					</span>
					</div>";
			}
		}
	} else {
		$uploadedImages = " ";
	}
	
	return $uploadedImages;
}

/**
 *	Retrieves AJAX page navigation links
 */ 
function writeAjaxPageNav($comp, $func, $limit, $limitstart, $total, $loadingDivName, $loadingImgURL = "")
{
	$txt = '';
	$displayed_pages = 10;
	$total_pages = $limit ? ceil($total / $limit) : 0;
	$this_page = $limit ? ceil(($limitstart +1) / $limit) : 1;
	$start_loop = (floor(($this_page -1) / $displayed_pages)) * $displayed_pages +1;
	
	if ($start_loop + $displayed_pages -1 < $total_pages) {
		$stop_loop = $start_loop + $displayed_pages -1;
	} else {
		$stop_loop = $total_pages;
	}
	
	$link = "javascript:loading('$loadingDivName','$loadingImgURL');jax.icall('$comp','" . $func . "','" . $limit . "',";
	
			
	$pnSpace = '';
	
	if (_PN_LT || _PN_RT)
		$pnSpace = "&nbsp;";
	
	if ($this_page > 1) {
		$page = ($this_page -2) * $limit;
		$txt .= '<a href="' . "$link '0');" . '" class="pagenav" ><img border=0 src="components/com_myblog/images/Backward_16x16.png" alt=""/>' . $pnSpace . _PN_START . '</a> ';
		$txt .= '<a href="' . "$link '$page');" . '" class="pagenav" ><img border=0 src="components/com_myblog/images/Play2_16x16.png" alt=""/>' . $pnSpace . _PN_PREVIOUS . '</a> ';
	} else {
	}
	
	for ($i = $start_loop; $i <= $stop_loop; $i++) {
		$page = ($i -1) * $limit;
		
		if ($i == $this_page) {
			$txt .= '<span class="pagenav">' . $i . '</span> ';
		} else {
			$txt .= '<a href="' . "$link '$page');" . '" class="pagenav"><strong>' . $i . '</strong></a> ';
		}
	}

	if ($this_page < $total_pages) {
		$page = $this_page * $limit;
		$end_page = ($total_pages -1) * $limit;
		$txt .= '<a href="' . "$link '$page');" . '" class="pagenav">' . _PN_NEXT . $pnSpace . '<img border=0 src="components/com_myblog/images/Play_16x16.png" alt=""/></a> ';
		$txt .= '<a href="' . "$link '$end_page');" . '" class="pagenav">' . _PN_END . $pnSpace . '<img border=0 src="components/com_myblog/images/Forward_16x16.png" alt=""/></a>';
	} else {
	}
	
	return $txt;
}

/**
 *	Gets the tag clouds given a query and number of cloud variations
 */ 
function myGetTagClouds($query, $clouds = 8)
{
	$db	=& JFactory::getDBO();
	
	$db->setQuery($query);
	$rows = $db->loadObjectList();

	if (!$rows)
		return "";
	
	$vals = array();
	
	foreach ($rows as $row)
	{
		$vals["{$row->frequency}"] = $row->frequency;
	}
	$maxFreq = max($vals);
	$minFreq = min($vals);
	
	$freqSize = $maxFreq - $minFreq;
	$freqSpacing	= $freqSize / $clouds;
	
	if($freqSpacing < 1)
	{
		$freqSpacing = 1;
	}
	
	foreach ($rows as $row)
	{
		$tagClass = round($row->frequency / $freqSpacing);
		$result[] = array (
			'name' => $row->name,
			'cloud' => $tagClass,
			'slug'	=> $row->slug
		);
	}
	
	usort($result, 'mySortTags');
	return $result;	
}

/**
 *	sort tags alphabetically 
 */ 
function mySortTags($a, $b)
{
	return (strtoupper($a['name']) < strtoupper($b['name'])) ? -1 : 1;
}

/**
 *	Display page navigation, non ajaxed 
 */ 
function myWritePagesLinks($link, $total, $limitstart, $limit)
{
	$total = (int) $total;
	$limitstart = (int) max($limitstart, 0);
	$limit = (int) max($limit, 0);
	$txt = '';
	$displayed_pages = 10;
	$total_pages = $limit ? ceil($total / $limit) : 0;
	$this_page = $limit ? ceil(($limitstart +1) / $limit) : 1;
	$start_loop = (floor(($this_page -1) / $displayed_pages)) * $displayed_pages +1;
	
	if ($start_loop + $displayed_pages -1 < $total_pages) {
		$stop_loop = $start_loop + $displayed_pages -1;
	} else {
		$stop_loop = $total_pages;
	}
	
	$link .= '&amp;limit=' . $limit;
	
	if (!defined('_PN_LT') || !defined('_PN_RT'))
	{
		DEFINE('_PN_LT', '&lt;');
		DEFINE('_PN_RT', '&gt;');
	}
	
	if (!defined('_PN_START'))
		define('_PN_START', 'Start');
	
	if (!defined('_PN_PREVIOUS'))
		define('_PN_PREVIOUS', 'Previous');
	
	if (!defined('_PN_END'))
		define('_PN_END', 'End');
	
	if (!defined('_PN_NEXT'))
		define('_PN_NEXT', 'Next');
	
	$pnSpace = '';
	
	if (_PN_LT || _PN_RT)
		$pnSpace = "&nbsp;";
	
	if ($this_page > 1) {
		$page = ($this_page -2) * $limit;
		$txt .= '<a href="' . sefRelToAbs("$link&amp;limitstart=0") . '" class="pagenav" title="' . _PN_START . '"><img border=0 src="components/com_myblog/images/Backward_16x16.png" alt=""/>' . $pnSpace . _PN_START . '</a> ';
		$txt .= '<a href="' . sefRelToAbs("$link&amp;limitstart=$page") . '" class="pagenav" title="' . _PN_PREVIOUS . '"><img border=0 src="components/com_myblog/images/Play2_16x16.png" alt=""/>' . $pnSpace . _PN_PREVIOUS . '</a> ';
	} else {
		$txt .= '';
		$txt .= '';
	}
	
	for ($i = $start_loop; $i <= $stop_loop; $i++) {
		$page = ($i -1) * $limit;

		if ($i == $this_page) {
			$txt .= '<span class="pagenav">' . $i . '</span> ';
		} else {
			$txt .= '<a href="' . sefRelToAbs($link . '&amp;limitstart=' . $page) . '" class="pagenav"><strong>' . $i . '</strong></a> ';
		}
	}
	
	if ($this_page < $total_pages) {
		$page = $this_page * $limit;
		$end_page = ($total_pages -1) * $limit;
		$txt .= '<a href="' . sefRelToAbs($link . '&amp;limitstart=' . $page) . ' " class="pagenav" title="' . _PN_NEXT . '">' . _PN_NEXT . $pnSpace . '<img border=0 src="components/com_myblog/images/Play_16x16.png" alt=""/></a> ';
		$txt .= '<a href="' . sefRelToAbs($link . '&amp;limitstart=' . $end_page) . ' " class="pagenav" title="' . _PN_END . '">' . _PN_END . $pnSpace . '<img border=0 src="components/com_myblog/images/Forward_16x16.png" alt=""/></a>';
	} else {
		$txt .= '';
		$txt .= '';
	}
	
	return $txt;
}

/**
 *	Gets the blog description for a user's blog
 */ 
function myGetAuthorDescription($userid)
{
	$db		=& JFactory::getDBO();
	
	$db->setQuery("SELECT description FROM #__myblog_user WHERE user_id=".$db->Quote($userid));
	$desc = $db->loadResult();
	
	if (!$desc)
	{
		$desc = "<p>No desc available</p>";
	}
	
	return $desc; 
}

function myGetAuthorTitle($userid)
{
	$db		=& JFactory::getDBO();

	$db->setQuery("SELECT `title` FROM #__myblog_user WHERE user_id=".$db->Quote($userid));
	
	$title = $db->loadResult();

	return $title;
}


/**
 *	close all open HTML tags. works as HTML cleanup function
 */ 
function myCloseTags($html)
{
	#put all opened tags into an array
	preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU",$html,$result);
	$openedtags=$result[1];
	
	#put all closed tags into an array
	preg_match_all("#</([a-z]+)>#iU",$html,$result);
	$closedtags=$result[1];
	$len_opened = count($openedtags);
	// all tags are closed
	if(count($closedtags) == $len_opened){
		return $html;
	}
	$openedtags = array_reverse($openedtags);
	// close tags
	for($i=0;$i < $len_opened;$i++) {
		if (!in_array($openedtags[$i],$closedtags) && $openedtags[$i] != 'img'){
			$html .= '</'.$openedtags[$i].'>';
		} else {
			unset($closedtags[array_search($openedtags[$i],$closedtags)]);
		}
	}
	return $html;
}

// Create a suitable link from a given title
function myTitleToLink($link)
{
    global $_MY_CONFIG;

    // Replace non-ASCII characters.
    $link = strtr($link,
	 "\xe1\xc1\xe0\xc0\xe2\xc2\xe4\xc4\xe3\xc3\xe5\xc5".
	 "\xaa\xe7\xc7\xe9\xc9\xe8\xc8\xea\xca\xeb\xcb\xed".
	 "\xcd\xec\xcc\xee\xce\xef\xcf\xf1\xd1\xf3\xd3\xf2".
	 "\xd2\xf4\xd4\xf6\xd6\xf5\xd5\x8\xd8\xba\xf0\xfa\xda".
	 "\xf9\xd9\xfb\xdb\xfc\xdc\xfd\xdd\xff\xe6\xc6\xdf\xf8",
	 "aAaAaAaAaAaAacCeEeEeEeEiIiIiIiInNo".
	 "OoOoOoOoOoOoouUuUuUuUyYyaAso");
	$link = strtr($link, $_MY_CONFIG->replacements_array);
	
    // remove quotes, spaces, and other illegal characters
    $link = preg_replace(array('/\'/', '/[^a-zA-Z0-9\-.+]+/', '/(^_|_$)/'), array('', '-', ''), $link);
    
    // Replace multiple '-' with a single '-'
	//$link = ereg_replace('-(-)*', '-', $link); ereg_replace deprecated in php5.3 and above
	$link = preg_replace('{-(-)*}', '-', $link);

	
	// Remove first occurence of '-'
	//$link = ereg_replace('^-','', $link);
	$link = preg_replace('{^-}', '', $link);
	
	$link	= JString::str_ireplace('.' , '' , $link );
	$link	= JString::strtolower( $link );
	
    return $link;
}

// Similar to trim, but with reference
function myTrim(&$string)
{
	$string = trim($string);
}

function i8n_date($date)
{
	global $MYBLOG_LANG;

	if($MYBLOG_LANG)
	{
		foreach($MYBLOG_LANG as $key => $val)
		{
			$date = str_replace($key, $val, $date);
		}
	}
	
	return $date;
}

/**
 * Return the pdf link for specific content
 **/
function myGetPDFLink($contentId, $ItemId)
{
	if(JVERSION <= 1.5){
            $link	= JRoute::_('index.php?view=article&id=' . $contentId . '&option=com_content&format=pdf');
        }
        else{
           $link	= JRoute::_('index.php?task=pdf&id=' . $contentId . '&option=com_myblog');

        }

	$str    = '<a title="PDF" onClick="window.open(\'' . $link . '\',\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;" ';
	$str    .= 'target="_blank" href="' . $link . '">';

	$str    .= (JVERSION >= 1.6) 
			? '<img border="0" name="PDF" alt="PDF" src="' . MY_COM_LIVE . '/images/pdf_button.png"></a>' 
			: '<img border="0" name="PDF" alt="PDF" src="' . rtrim( JURI::root() , '/' ) . '/images/M_images/pdf_button.png"></a>';

	return $str;
}

/**
 * Return the Print link for specific content
 **/
function myGetPrintLink($contentId, $ItemId)
{
	$link   = JRoute::_('index.php?index.php?view=article&id=' . $contentId . '&tmpl=component&print=1&task=printblog');
	$str    = '<a title="Print" onClick="window.open(\'' . $link . '\',\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;" ';
	$str    .= 'target="_blank" href="' . $link . '">';

	$str    .= (JVERSION >= 1.6) 
			? '<img border="0" name="Print" alt="Print" src="' . MY_COM_LIVE . '/images/printButton.png"></a>' 
			: '<img border="0" name="Print" alt="Print" src="' . rtrim( JURI::root() , '/' ) . '/images/M_images/printButton.png"></a>';

	return $str;
}

/**
 * Return the Back link for specific content
 **/
function myGetBackLink()
{
	global $MYBLOG_LANG;
	
	$str    = '<div class="mbNavi"><a href="javascript:void(0);" onClick="javascript:history.go(-1);">&larr; ' . JText::_( 'COM_MY_BACK' ) . '</a></div>';

	return $str;
}

/**
 * Returns the Itemid for the main menu item so that
 * it doesn't conflict with the "dashboard" menu's item id
 **/  
function myGetBlogItemId()
{
	static $mbItemid = -1;
	
	if($mbItemid == -1)
	{	
		global $Itemid;
		$db			=& JFactory::getDBO();
		$mbItemid	= $Itemid;
		
		$strSQL	= "SELECT `id` FROM #__menu WHERE "
				. "`link` LIKE '%option=com_myblog%' "
				. "AND `link` NOT LIKE '%option=com_myblog&task=adminhome%' "
				. "AND `published`='1' "
				. "AND `id`=".$db->Quote($Itemid);
		$db->setQuery($strSQL);

		if(!$db->loadResult())
		{
			// The current itemId is not myblog related, ignore it, and find a valid one
			// Menu type is 'component' for Joomla 1.0 and 'components' for Jooma 1.5
			$strSQL	= "SELECT `id` FROM #__menu WHERE "
					. "(type='component' OR type='components') "
					. "AND `link`='index.php?option=com_myblog' "
					. "AND `published`='1'";
			$db->setQuery($strSQL);
			$mbItemid = $db->loadResult();
		}
	}
	return $mbItemid;
}

/**
 * Return valid Itemid for my blog links
 * If 'option' are currently on myblog, then use current ItemId 
 */  
function myGetItemId()
{
	static $mbItemid = -1;
	
	if($mbItemid == -1)
	{	
		global $Itemid;
		$db			=& JFactory::getDBO();
		$mbItemid	= $Itemid;
		$db->setQuery("select id from #__menu where link LIKE '%option=com_myblog%' and published='1' AND `id`=".$db->Quote($Itemid));
		if(!$db->loadResult())
		{
			// The current itemId is not myblog related, ignore it, and find a valid one
			// Menu type is 'component' for Joomla 1.0 and 'components' for Jooma 1.5
			$db->setQuery("select id from #__menu where (type='component' or type='components') and link='index.php?option=com_myblog' and published='1'");
			$mbItemid = $db->loadResult();
		}
	}

        // check current itemID empty (from Dashbord menu during installer), should check another itemID for myblog
        $mbItemid = empty($mbItemid) ? myGetAdminItemId():$mbItemid;

	return $mbItemid;
}

// Return the adminhome itemid
function myGetAdminItemId()
{
	static $mbItemid = -1;
	
	if($mbItemid == -1)
	{
		global $Itemid;
		
		$db			=& JFactory::getDBO();
		$mbItemid	= $Itemid;
		$db->setQuery("select id from #__menu where link LIKE '%option=com_myblog%' AND published='1' AND `id`='$Itemid' ");
		if(!$db->loadResult())
		{
			// The current itemId is not myblog related, ignore it, and find a valid one
			$db->setQuery("select id from #__menu where `link` LIKE '%option=com_myblog&task=adminhome%' AND published='1' AND `menutype`='usermenu' ");
			$mbItemid = $db->loadResult();
		}
		
		// If no link from the preferred 'usermenu' Itemid, just use whatever myblog task=adminhome itemid we can find
		if(!$mbItemid)
		{
			$db->setQuery("select id from #__menu where `link` LIKE '%option=com_myblog&task=adminhome%' AND published='1'");
			$mbItemid = $db->loadResult();
		}
		
		// If still cannot found, just search for a normal com_myblog link
		if(!$mbItemid)
		{
			$db->setQuery("select id from #__menu where `link` LIKE '%option=com_myblog%' AND published='1'");
			$mbItemid = $db->loadResult();
		}
	}
	
	return $mbItemid;
}

/** Admin Stuffs **/

// Return array of links for the dashboard tabs
function myGetDashboardLinks()
{
	global $_MY_CONFIG;

	$myitemid = myGetAdminItemId();

	$links   = array(
			JRoute::_('index.php?option=com_myblog&task=adminhome&Itemid='.$myitemid),
			JRoute::_('index.php?option=com_myblog&task=bloggerpref&Itemid='.$myitemid),
			JRoute::_('index.php?option=com_myblog&task=bloggerstats&Itemid='.$myitemid)
	);	


 	// Check if integration with Jom comment is set.
	if($_MY_CONFIG->get('useComment'))
	{
		$links[]	= JRoute::_('index.php?option=com_myblog&task=showcomments&Itemid='.$myitemid);		
	}
	return $links;
}

function myGetDashboardLinksTitle()
{
	global $_MY_CONFIG;

	include(MY_COM_PATH.'/language/'.$_MY_CONFIG->language);
	
	$captions   = array(
	                    $MYBLOG_LANG['TPL_MNU_MYBLOGS'],
	                    $MYBLOG_LANG['TPL_MNU_PREF'],
	                    $MYBLOG_LANG['TPL_MNU_STATS']
						);

 	// Check if integration with Jom comment is set.
	if($_MY_CONFIG->get('useComment')){
		$captions[] = $MYBLOG_LANG['TPL_MNU_COMMENTS'];
	}
	return $captions;
}

function myUnhtmlspecialchars( $string ){
	$string = str_replace ( '&amp;', '&', $string );
	$string = str_replace ( '&#039;', '\'', $string );
	$string = str_replace ( '&quot;', '"', $string );
	$string = str_replace ( '&lt;', '<', $string );
	$string = str_replace ( '&gt;', '>', $string );
	$string = str_replace ( '&uuml;', 'ü', $string );
	$string = str_replace ( '&Uuml;', 'Ü', $string );
	$string = str_replace ( '&auml;', 'ä', $string );
	$string = str_replace ( '&Auml;', 'Ä', $string );
	$string = str_replace ( '&ouml;', 'ö', $string );
	$string = str_replace ( '&Ouml;', 'Ö', $string );    
	return $string;
}

// Return list of all category for  the target section
function myGetCategoryList($sectionid)
{
	$categories = array();
	$db	=& JFactory::getDBO();
        $whereSection = (JVERSION >= 1.6)
			? " AND id IN (" . $sectionid . ")"
			: " AND section IN (" . $sectionid . ")";
	$db->setQuery("SELECT * FROM #__categories WHERE  `published`='1' $whereSection ORDER BY title");

        $categories = $db->loadObjectList();
	 
	return $categories;
}

function myGetCategoryId($categoryName)
{
	$db			=& JFactory::getDBO();
	$db->setQuery("SELECT `id` FROM #__categories WHERE `name`=".$db->Quote($categoryName) ); 
	return $db->loadResult();
}

// Custom callback function for tag sorting
// @param $a/$b is a myblog_category object
function myTagCmp($a, $b) 
{
	return strcmp($a->name, $b->name);
}

// Return the number of times the tag is used
function myCountTagUsed($tagid)
{
	$db			=& JFactory::getDBO();
	$query		= "SELECT COUNT(*) FROM #__myblog_content_categories WHERE `category`=".$db->Quote($tagid);
	$db->setQuery($query);
	return $db->loadResult();
}

function myGetTrackbacks($contentId)
{
	$db			=& JFactory::getDBO();
	
	$strSQL	= "SELECT `url` FROM #__myblog_tb_sent WHERE `contentid`=".$db->Quote($contentId);
	$db->setQuery($strSQL);
	
	$trackbacks	= $db->loadObjectList();
	
	$retVal		= '';
	
	foreach($trackbacks as $trackback)
	{
		$retVal	.= $trackback->url . ',';
	}
	return $retVal;
}

// Return the html code for categories list, if content id is specified, 
// Set the checkbox as slected
function myGetTagsSelectHtml($contentid = 0, $display='table')
{
	$db			=& JFactory::getDBO();
	$query		= "SELECT * FROM #__myblog_categories";
	$db->setQuery($query);
	
	$result			= $db->loadObjectList();
	$currentTags	= array();
	
	if($contentid)
	{
		$ctags = myGetTags($contentid);
		if(!empty($ctags))
		{
			foreach($ctags as $ct)
				$currentTags[] = $ct->id;
		}
	}
	
	// Sort the tag alphabetically
	usort($result, 'myTagCmp');
	
	switch($display){
	
		case 'ul':
			$html = '<ul class="tagListings">';
			foreach($result as $row)
			{
				$row->useCount = myCountTagUsed($row->id);
				
				$checked = in_array($row->id, $currentTags) ? 'checked="checked"' : '';
				$html .= '<li>
				<input name="qptags[]" id="taglistchk_'.$row->id.'" type="checkbox" value="'. $row->name.'"  '.$checked.' />
				<label for="taglistchk_'.$row->id.'" title="'.$row->useCount.' blog entries">'. $row->name.'</label>
				</li>';
			}
			$html .= '</ul>';
			break;
		case 'table':
		default:
			$html = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" ><tbody id="tagListings">';
			foreach($result as $row)
			{
				$row->useCount = myCountTagUsed($row->id);
				
				$checked = in_array($row->id, $currentTags) ? 'checked="checked"' : '';
				$html .= '<tr>
						<td valign="middle"><input type="checkbox" value="'. $row->name.'"  '.$checked.' /></td>
						<td width="100%" valign="middle"><label class="catitem" style="vertical-align:middle" title="'.$row->useCount.' blog entries">'. $row->name.'</label></td>
					</tr>';
			}
			$html .= '</tbody></table>';
			break;
	}
	return $html;
}

// Return the html code for categories list, if content id is specified,
// Set the checkbox as slected
function myGetCategoryHtml($contentid = 0)
{
	global $_MY_CONFIG;
	
	$db			=& JFactory::getDBO();
	$sections   = $_MY_CONFIG->get('postSection');
        $managedSectionsCategory = $_MY_CONFIG->get('managedSections');

	// Get current category if the content has been set a category
	$strSQL     = "SELECT `catid` FROM #__content WHERE `id`=".$db->Quote($contentid);
	$db->setQuery($strSQL);
	$selCat     = $db->loadResult();

	//use selected Default Category
	if(!$selCat){
		$selCat = $sections;
	}

	// Load the list of categories on the site and dont show unpublished categories
        // in >J1.6 Joomla doesn't have section category, so we should all category
        // @todo : should we change section with parent category in MyBlog General Setting?
	if(JVERSION >= 1.6){
            $strSQL = 'SELECT * FROM #__categories WHERE extension=\'com_content\' AND `published`=\'1\' AND `id` IN (' . $managedSectionsCategory . ') ';
        }else{
            $strSQL = 'SELECT * FROM #__categories WHERE `section` IN (' . $sections . ') AND `published`=\'1\'';
        }

        $db->setQuery($strSQL);

	$categories = $db->loadObjectList();

	$html = '<select id="catid" name="catid" size="1" class="text">';
	
	foreach($categories as $row)
	{
		$rowName	= $row->title;

	    if($selCat && $selCat == $row->id)
		{
	        $html   .= '<option value="' . $row->id . '" selected="selected">' . $rowName . '</option>';
		}
		else
		{
		    $html   .= '<option value="' . $row->id . '">' . $rowName .'</option>';
		}
    }
    $html   .= '</select>';
    
	return $html;
}


function myGetJoomlaCategoryName($id)
{
	global $_MY_CONFIG;
	
	$db		=& JFactory::getDBO();
	
	$query	= "SELECT `title` FROM #__categories WHERE `id`=".$db->Quote($id);
	$db->setQuery( $query );
	return $db->loadResult();
}

function getPoweredByLink()
{
	$powered_by_link = '<div align="center" style="text-align:center; font-size:90%"><a href="http://www.azrul.com">Powered by Azrul&#39;s MyBlog for Joomla!</a></div>';
	return " "; 
}


function myAddPathway($title, $link='')
{
	$mainframe	=& JFactory::getApplication();
	$pathway	=& $mainframe->getPathway();
	
	$pathway->addItem( $title , $link );
}

function myAddPageTitle($title)
{
	$document =& JFactory::getDocument();
	$document->setTitle($title);
}

function myAddEditorHeader()
{
	static $added	= false;
	
	if( !$added )
	{
	    $document =& JFactory::getDocument();
	    
		/**
		 * Add additional JS and CSS files
		 */
		$document->addScript( JURI::root() . 'components/com_myblog/js/myblog.js' );
		$document->addStylesheet( JURI::root() . 'components/com_myblog/css/azwindow.css' );
		$document->addStylesheet( JURI::root() . 'components/com_myblog/css/style.css' );
		$document->addStylesheet( JURI::root() . 'components/com_myblog/css/ui.css' );
		$added	= true;
	}
}

function myPostSocialMedia($row,$socialMedia){
    global $_MY_CONFIG;

    $tag = '#MyBlog';
    $permalink = myGetPermalinkUrl($row->id,'');
    $row->permalink = JUri::base().substr($permalink,1);
     $message = JText::sprintf('COM_MY_CHECK_MY_NEW_ENTRY',$tag,'');

     $status = array();
    if(is_array($socialMedia)){
        foreach($socialMedia as $itemSocialMedia){
            require_once(MY_LIBRARY_PATH . DS . 'socialmedia' . DS .''. $itemSocialMedia.'.php');
            $className = 'MYSocialMedia_'.ucfirst($itemSocialMedia);
            $objSocialMedia = new $className;
            $objSocialMedia->setLink($row->permalink);
            $objSocialMedia->setMessage($message);
            $status[$itemSocialMedia] = $objSocialMedia->post();
	    
        }
    }else{
        $itemSocialMedia = $socialMedia;
        require_once(MY_LIBRARY_PATH . DS . 'socialmedia' . DS . $itemSocialMedia.'.php');
        $className = 'MYSocialMedia_'.ucfirst($itemSocialMedia);
        $objSocialMedia = new $className;
        $objSocialMedia->setLink($row->permalink);
        $objSocialMedia->setMessage($message);
        $status[$itemSocialMedia] = $objSocialMedia->post();
    }


    return $status;
}

/*
 * save request/access token from twitter API permission
 */
function mySaveOauth($user_token,$user_token_secret){
    $my			=& JFactory::getUser();
    $db		=& JFactory::getDBO();

    // delete the current data if available
    $strSQL	= "DELETE FROM ".$db->nameQuote('#__myblog_oauth')." WHERE ".$db->nameQuote('user_id')."=".$db->Quote($my->id);
    $db->setQuery( $strSQL );
    $db->query();

    // insert the user token
    $strSQL	= "INSERT INTO ".$db->nameQuote('#__myblog_oauth')." SET ".$db->nameQuote('user_id')."=".$db->Quote($my->id).",".$db->nameQuote('user_token')."=".$db->Quote($user_token).",".$db->nameQuote('user_token_secret')."=".$db->Quote($user_token_secret);
    $db->setQuery( $strSQL );
    return $db->query();
}

function myDeleteOauth(){
    $my			=& JFactory::getUser();
    $db		=& JFactory::getDBO();

    // delete the current data if available
    $strSQL	= "DELETE FROM ".$db->nameQuote('#__myblog_oauth')." WHERE ".$db->nameQuote('user_id')."=".$db->Quote($my->id);
    $db->setQuery( $strSQL );

    return $db->query();
}

function myGetOauth(){
    $my			=& JFactory::getUser();
    $db		=& JFactory::getDBO();

    $query	= "SELECT * FROM ".$db->nameQuote('#__myblog_oauth')." WHERE ".$db->nameQuote('user_id')."=".$db->Quote($my->id);
    $db->setQuery( $query );
    return $db->loadObjectList();
}

function myFbShare($permalink){
    $doc =& JFactory::getDocument();
    $permalink = JUri::base().substr($permalink,1);
    
    return '<script src="http://connect.facebook.net/en_GB/all.js#xfbml=1"></script><fb:like href="'.$permalink.'" send="false" layout="box_count" action="like" locale="en_GB" colorscheme="light" show_faces="true" style="height: 70;" height="70" width="55"></fb:like>';


}

function myGooglePlusShare($permalink){
    $doc =& JFactory::getDocument();
    $permalink = JUri::base().substr($permalink,1);
    return '<script type="text/javascript" src="http://apis.google.com/js/plusone.js"></script><div class="g-plusone" data-size="tall" href="'.$permalink.'"></div>';
}

function myTweetShare($permalink){
    $doc =& JFactory::getDocument();
    $permalink = JUri::base().substr($permalink,1);

    $tweet = '<a href="http://twitter.com/share" class="twitter-share-button" data-url="'.$permalink.'" data-counturl="'.$permalink.'" data-count="vertical">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
    return $tweet;
}