<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

global $_MYBLOG, $_MY_CONFIG;

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'defines.myblog.php' );
require_once( MY_COM_PATH . DS . 'task' . DS . 'base.php' );
require_once( MY_COM_PATH . DS . 'functions.myblog.php' );
require_once( MY_LIBRARY_PATH . DS . 'datamanager.php' );
require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_myblog' . DS . 'config.myblog.php' );


// Add inclusion path for tables
JTable::addIncludePath( MY_COM_PATH . DS . 'table' );

$_MY_CONFIG 	= new MYBLOG_Config();

$mainframe		=& JFactory::getApplication();

$sectionid 	= $_MY_CONFIG->postSection;
$catid 		= $_MY_CONFIG->catid;
$sections	= $_MY_CONFIG->get('managedSections');

// For AJAX calls, we need to load the language file manually.
$lang =& JFactory::getLanguage();
$lang->load( 'com_myblog' );

// Sections should never been empty
if ($sections == "")
{
	$sections = "-1";
}

function myxGetPermalink( $title )
{
	$objResponse    = new JAXResponse();
	$mainframe		=& JFactory::getApplication();
	
	// Strip out " and & characters
	$title			= JString::str_ireplace( '&' , '' , $title );
	$title			= JString::str_ireplace( '"' , '' , $title );
	
	$title			= JFilterOutput::stringURLSafe( $title );
	$link			= myTitleToLink(trim($title));
	
	if( empty( $link ) )
	{
		$now	=& JFactory::getDate();
		$now->setOffset( $mainframe->getCfg('offset') );
		$link	= $now->toFormat("%Y-%m-%d-%H-%M-%S");
	}
	$link			.= '.html';

	$objResponse->addAssign( 'permalink-data' , 'innerHTML' , $link );
	$objResponse->addScriptCall('jQuery("#permalink").val("' . $link . '");');
	$objResponse->sendResponse();
}

function myxDeleteBlogDraft( $contentid, $userid ){
	$db    =& JFactory::getDBO();
	$query = "DELETE FROM ".$db->nameQuote('#__myblog_drafts').
			" WHERE ".$db->nameQuote('content_id').'='.$db->Quote($contentid).
			' AND '.$db->nameQuote('user_id').'='.$db->Quote($userid);
	$db->setQuery($query);
	$db->query();
}

function myxDiscardBlogDraft( $data ){
	$contentid = $data['id'];
	
	$my	=& JFactory::getUser();
	$uid = $my->id;
	
	if(!$uid){
		$my = myGetUser($data['secret']);
		$uid = $my->id;
		if(!$uid){
			$objResponse	= new JAXResponse();
			$objResponse->addScriptCall("alert('Draft cannot be removed');");
			return $objResponse->sendResponse();
		}
	}
	myxDeleteBlogDraft( $contentid, $uid );
	$objResponse	= new JAXResponse();
	$objResponse->addScriptCall("window.location.reload");
	return $objResponse->sendResponse();
}


function myxSaveBlogDraft( $data ){
	$drf = array();
	$drf['id'] = $data['id'];
	$drf['title'] = $data['title'];
	$drf['state'] = (isset($data['state'])) ? $data['state'] : 0;
	$drf['created'] = $data['created'];
	$drf['catid'] = $data['catid'];
	$drf['metakey'] = $data['metakey'];
	$drf['metadesc'] = $data['metadesc'];
	$drf['trackbacks'] = $data['trackbacks'];
	$drf['permalink'] = $data['permalink'];
	$drf['fulltext'] = $data['fulltext'];
	
	$my	=& JFactory::getUser();
	$uid = $my->id;
	
	if(empty($drf['fulltext'])){
		$objResponse	= new JAXResponse();
		$objResponse->addScriptCall("jQuery('#btn_savedraft').val('".JText::_('COM_MY_SAVE_DRAFT')."').removeAttr('disabled');");
		$objResponse->addScriptCall('myblog.pingqueue=setTimeout("myblog.saveDraft()", '.MY_PING_INTERVAL.');');
		return $objResponse->sendResponse();
	}

	if(!$uid){
		$my = myGetUser($data['secret']);
		$uid = $my->id;
		if(!$uid){
			$objResponse	= new JAXResponse();
			$objResponse->addScriptCall("jQuery('#btn_savedraft').val('".JText::_('COM_MY_SAVE_DRAFT')."').removeAttr('disabled');");
			$objResponse->addScriptCall("alert('".JText::_('COM_MY_DRAFT_SAVE_FAIL')."');");
			return $objResponse->sendResponse();
		}
	}

	$date		=& JFactory::getDate();
	$mainframe	=& JFactory::getApplication();
	$date->setOffSet( $mainframe->getCfg( 'offset' ) );
	
	$db		=& JFactory::getDBO();
	
	$query = "INSERT INTO #__myblog_drafts (content_id, user_id, draft_last_updated, draft_json_content) 
			VALUES(".$db->Quote($drf['id']).",".$db->Quote($uid).",".$db->Quote($date->toFormat()).",".$db->Quote(json_encode($drf)).")
			ON DUPLICATE KEY UPDATE draft_json_content=".$db->Quote(json_encode($drf)).", draft_last_updated=".$db->Quote($date->toFormat());
	
	$db->setQuery($query);
	$db->query();
	
	$objResponse	= new JAXResponse();
	$objResponse->addScriptCall("jQuery('#btn_savedraft').val('".JText::_('COM_MY_SAVE_DRAFT')."').removeAttr('disabled');");
	$objResponse->addScriptCall('myblog.pingqueue=setTimeout("myblog.saveDraft()", '.MY_PING_INTERVAL.');');
	return $objResponse->sendResponse();
}


function myxSaveBlog( $data , $closeWindow )
{
	global $MYBLOG_LANG, $_MY_CONFIG;
		
	$mainframe	=& JFactory::getApplication();
	$row		=& JTable::getInstance( 'BlogContent' , 'Myblog' );
	
	$isNew		= true;

	if(isset($data['is_quickpost']) && $data['is_quickpost'] == 1){
		
		$qp_type = (isset($data['quickpost_type'])) ?  $data['quickpost_type'] : NULL ;
		
		$qpdate =& JFactory::getDate();

		//set default infos for an entry
		$data['id'] = $data['qpid'];
		$data['state'] = 1; //always publish a quickpost
		$data['catid'] = $_MY_CONFIG->get('postSection'); //select the default category ID
		$data['created'] = $qpdate->toFormat();
		$data['trackbacks'] = '';
		if(!empty($data['qptags[]'])){
		    $data['tags[]'] = $data['qptags[]'];
		    unset($data['qptags[]']);
		}else{
		     $data['tags[]'] = array();
		}
		
		$data['secret'] = $data['quickpost_secret'];

		switch($qp_type){
			case 'text':
				$data['fulltext'] = '<p>'.$data['content'].'</p>';
				break;
			case 'photo':
                                $date = &JFactory::getDate();
				$data['title'] = (empty($data['title'])) ? 'Photo Post on '.$date->toFormat() : $data['title'];
				$data['fulltext'] = '<a href="'.$data['source_image'].'" target="_blank"><img src="'.$data['source_image'].'" alt="image" /></a>'
						.'<p>'.$data['content'].'</p>';
				break;
			case 'video':
				$date = &JFactory::getDate();		
				$data['title'] = (empty($data['title'])) ? 'Video Post on '.$date->toFormat() : $data['title'];
				$data['fulltext'] = '[video:'.$data['url'].']'
						.'<p>'.$data['content'].'</p>';
				break;
				break;
			case 'quote':
				$date =& JFactory::getDate();	
				$data['title'] = (empty($data['source'])) ? 'Quote posted on '.$date->toFormat() : $data['source'];
				$data['fulltext'] = '<blockquote>'.$data['content'].'</blockquote>';
				break;
			default:
				break;
		}
	}

	if( (isset($data['edit_is_quickpost']) && $data['edit_is_quickpost'] == 1)  ){
		$data['is_quickpost'] = 1;
		$data['quickpost_type'] = $data['qptype'];
	}

	$row->bind( $data , true);

	if($row->id != '0' || $row->id != 0)
		$isNew	= false;

	$title		= $data['title'];
	$validation	= array();
	// perform validation, if it validates, redirect to success page
	// Make sure the title not empty
	if( (empty( $title ) || $title == JText::_('COM_MY_BLOG_TITLE')) && empty($data['is_quickpost']) )
	{
		$validation['title'] = JText::_('COM_MY_TITLE_IS_REQUIRED');
	}

	// Send technorati ping if enabled and xmlrpc extensions is enabled.
	if($_MY_CONFIG->get('pingTechnorati') && extension_loaded('xmlrpc'))
	{
		$my		=& JFactory::getUser();
		require_once( MY_LIBRARY_PATH . DS . 'technorati.php' );
		$technorati	= new MYTechnorati(myGetAuthorName($my->id), $_SERVER['HTTP_USER_AGENT']);
		$technorati->ping();
		
		// Debugging
		//$debug	= $technorati->ping();
		//var_dump($debug);
		//exit();
	}	

	$fulltext	= $data['fulltext'];

	// Make sure the title not empty
	if( empty( $fulltext ) )
	{
		$validation['fulltext'] = JText::_("COM_MY_BLOG_CANNOT_BE_EMPTY");
	}

	$categoryId	= $data['catid'];

	// If category is specified, set the category to the $_POST value. Make
	// Sure that it is actually part of the post section
	if(empty( $categoryId ) )
	{
		$validation['catid'] = JText::_('COM_MY_CATEGORY_MUST_BE_SELECTED');
	}

	// Set the date time
	$createdDate	= $data['created'];

	if(isset($createdDate) && !empty($createdDate))
	{
		$date		=& JFactory::getDate( $row->created );
		$date->setOffSet( -$mainframe->getCfg( 'offset' ) );
		$row->created	= $date->toFormat();
	}
	else
	{
		$date			=& JFactory::getDate();
		$row->created	= $date->toFormat();
	}
	
	// Set the publish date same with the created date as myblog does not have publish settings?
	$row->publish_up    = $row->created;

	// Get the params
	$jcStatus		= isset( $data['jcState'] ) && !empty( $data['jcState'] ) ? $data['jcState'] : false;
	$row->fulltext 	= stripslashes($row->fulltext);
	$row->introtext = stripslashes($row->introtext);
	$row->title 	= stripslashes($row->title);
	
	// Check if jcState is set
	if( $jcStatus !== false )
	{
		// Check if user enables or disables comment
		// default we do not need to add any tags.
		if($jcStatus == 'enabled')
		{
			$row->fulltext  .= '{jomcomment}';
		}
		else if($jcStatus == 'disabled')
		{
		    $row->fulltext  .= '{!jomcomment}';
		}
	}

	$objResponse	= new JAXResponse();
		
	// Is everything ok?
	// This is the one and only save point
	if(empty($validation))
	{
		// No error, save the entry
		$origid = $row->id;
		$row->store();

		//Load the content object again so that initiale variables will be initialized.
		$row->load($row->id);

		//remove any related draft
		myxDeleteBlogDraft($origid, $row->created_by);

                //if( isset( $data['tags[]'] ) )
		//{
                        // the validation moved to function myAddTags()
                    	$tags	= isset( $data['tags[]'] )?$data['tags[]']:'';
			myAddTags($row->id, $tags);
		//}

		myNotifyAdmin($row->id, myGetAuthorName($row->created_by, $_MY_CONFIG->get('useFullName')), $row->title, $row->introtext . $row->fulltext, $isNew);
		
		// Set the odering so that it doesn't mess up.
		mySortOrder($row);

                // post to twitter
                $listSocialMedia = isset($data['postSocialMedia[]'])?$data['postSocialMedia[]']:'';

		$share_message = '';
		if(!empty($listSocialMedia) && $data['state']==1){
                    $status = myPostSocialMedia($row,$listSocialMedia);
		    foreach($status as $val=>$key){
			$itemSocialMedia = strtoupper($val);
			if($key==200){
			    $share_message .= JText::_('COM_MY_SHARE_'.$itemSocialMedia.'_SUCCESS').'<br />';
			}else{
			    $share_message .= JText::_('COM_MY_SHARE_'.$itemSocialMedia.'_FAIL').'<br />';
			}
		    }
                }
                
		// Send trackback, if necessary
		if( $data['trackbacks'] )
		{
			mySendTrackbacks($row->id, $data['trackbacks'] );
		}

// 		if( $closeWindow == "1")
// 		{
			$result_message = JText::_('COM_MY_BLOG_ENTRY_SAVED').'<br /><br />'.$share_message;
			$objResponse->addAssign('save-loading-message' , 'innerHTML' , $result_message );
			$objResponse->addScriptCall('jQuery("#save-loading-message").css("color","green");');
			// Reload parent's page
 			$objResponse->addScriptCall( 'setTimeout("parent.document.getElementById(\'azrulWindow\').style.visibility = \'hidden\';parent.location.reload();", 720 );' );
//  			
// 		}
// 		else
// 		{
// 			// Update the id value
// 			$objResponse->addScriptCall( 'jQuery("input[name=id]").val("' . $row->id . '");');
// 			
// 			// Add a save successfull message.
// 			$objResponse->addScriptCall( 'if( jQuery("#apDiv1").css("display") == "none" ) jQuery("#apDiv1").slideToggle("fast");' );
// 			$objResponse->addScriptCall( 'jQuery("#apDiv1").removeClass("error").addClass("save-success");');
// 			$objResponse->addScriptCall( 'jQuery("#statusMessage").html("' . JText::_('COM_MY_BLOG_ENTRY_SAVED') . '");' );
// 		}
	}
	else
	{
		//$objResponse->addScriptCall( 'if( jQuery("#apDiv1").css("display") == "none" ) jQuery("#apDiv1").slideToggle("fast");' );
		$errors			= '';
		foreach( $validation as $error )
		{
			$errors	.= '<div style="margin-bottom: 5px">' . $error . '</div>';
		}
		
		$errors	.= '[ <a href="javascript:void(0);" onclick="myblog.loading(\'hide\');">' . JText::_('COM_MY_DASHBOARD_HIDE_ERROR') . '</a> ]';
		$objResponse->addAssign('save-loading-message' , 'innerHTML' , $errors );
		$objResponse->addScriptCall('jQuery("#save-loading-message").css("color","red");');
		//$objResponse->addScriptCall( 'jQuery("#statusMessage").html("' . $errors . '");' );
	}
	

	return $objResponse->sendResponse();
}

/** Ajax calls **/
function myxSetVideoType($url, $width, $height)
{	
	$objResponse    = new JAXResponse();
	$js		= "tinyMCE.execCommand('mceFocus',false, 'fulltext'); ";
	$js    = "tinyMCE.execCommand('mceInsertContent',false, '[video:$url {$width}x{$height}]');";
	$objResponse->addScriptCall($js);
	$objResponse->sendResponse();
}

/**
 * Ajax function for addition of tags from user dashboard.
 **/
function myxUserAddTag($tagName,$contentId = 0)
{
	global $_MY_CONFIG;

	require_once( MY_COM_PATH . DS . 'language' . DS . $_MY_CONFIG->get('language') );
	require_once( MY_LIBRARY_PATH . DS . 'tags.php' );

	$tagObj			= new MYTags();
	$objResponse    = new JAXResponse();
	$my				=& JFactory::getUser();
	
	// Check if user is allowed to create tags from configurations,
	// some nasty users may issue a javascript even the form is disabled.
	// Need to check also if user is an administrator	
	
	if((!$_MY_CONFIG->get('enableUserCreateTags') && !myxIsAdmin() ) || (!isValidMember()))
	{
	    // Display error message to hacker/spammer/bad user
	    $objResponse->addScriptCall('alert("' . JText::_('Not allowed.') . '");');
		$objResponse->sendResponse();
		return;
	}
	
	if($tagObj->add($tagName))
	{
		$objResponse->addAssign('tagList','innerHTML',myGetTagsSelectHtml($contentId));
	    $objResponse->addScriptCall('window.alert("' . JText::sprintf('Tag %1$s successfully added!' , $tagName ) . '");');
	}
	else
	{
		$objResponse->addScriptCall('window.alert("' . JText::sprintf('Tag %1$s exists!', $tagName) . '");');
	}

	$objResponse->sendResponse();
}

function isValidMember()
{
	$my		=& JFactory::getUser();
		
	if($my->id == '0')
		return false;
	
	return true;
}

function isEditable($contentId)
{
	$db		=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$strSQL	= "SELECT `created_by` FROM #__content WHERE `id`=".$db->Quote($contentId);
	
	$db->setQuery($strSQL);
	$creator	= $db->loadResult();
	
	if($my->id != $creator && !myxIsAdmin() )
		return false;
	
	return true;
}

/**
* Check if the current user is an admin
*/
function myxIsAdmin(){
	$my		=& JFactory::getUser();
	$isAdmin = (JVERSION >= 1.6) 
			? ( isset($my->groups['Super Users']) || (array_search('8', $my->groups) !== FALSE) ) //8 is the super Admin
			: ( $my->usertype == "Super Administrator" || $my->usertype == "Administrator" );
	return $isAdmin;
}


// Toggle publish state of article in user dashboard
function myxTogglePublish($id)
{
	global $mainframe;
	
	$objResponse	= new JAXResponse();
	$db				=& JFactory::getDBO();	
	$my				=& JFactory::getUser();
	
	// Check if user is really allowed to publish this article.
	if(!isValidMember() || !isEditable($id))
	{
	    // Display error message to hacker/spammer/bad user
	    $objResponse->addScriptCall('alert("' . JText::_('Not allowed.') . '");');
		$objResponse->sendResponse();
		return;
	}
	
	while (@ ob_end_clean());
	$db->setQuery("SELECT state FROM #__content WHERE id=".$db->Quote($id));
	$publish = $db->loadResult();
	
	$publish = intval(!($publish));
	$db->setQuery("UPDATE #__content SET state=".$db->Quote($publish)." WHERE id=".$db->Quote($id));
	$db->query();
	
	if ($publish)
	{
		$objResponse->addAssign('pubImg' . $id, 'src', MY_COM_LIVE.'/images/published.png');
	}
	else
	{
		$objResponse->addAssign('pubImg' . $id, 'src', MY_COM_LIVE.'/images/unpublished.png');
	}

	$objResponse->sendResponse();
	
	return true;
}


/***
 * Search related article through ajax in the writing pad
 */ 
function myxSearchPosts($text, $page)
{
	global $_MY_CONFIG;
	
	$db			=& JFactory::getDBO();
	
	$text 		= "+" . $text;
	//$text		= ereg_replace(' ', "+", $text);
	$text		= preg_replace('{ }', "+", $text);

        // check current itemID empty (from Dashbord menu during installer), should check another itemID for myblog
        $Itemid 	= myGetItemId();
        $dashboardItemid	= myGetAdminItemId();
        $Itemid = empty($Itemid) ? $dashboardItemid:$Itemid;
	
	$sections	= $_MY_CONFIG->get('managedSections');
	
	$objResponse = new JAXResponse();

// 	$text		= preg_replace("/([\xC2\xC3])([\x80-\xBF])/e", "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)", $text);
		
	$strSQL	= (JVERSION >= 1.6) 
			? "SELECT COUNT(*) FROM #__content AS c, "
				. "#__myblog_permalinks AS p "
				. "WHERE c.catid IN ({$sections}) "
				. "AND c.id=p.contentid "
				. "AND c.state='1' "
				. "AND c.publish_up < now() "
				. "AND MATCH(c.`title`,c.`fulltext`) AGAINST(".$db->Quote($text)." in boolean mode)"
				 
			: "SELECT COUNT(*) FROM #__content AS c, "
				. "#__myblog_permalinks AS p "
				. "WHERE c.sectionid IN ({$sections}) "
				. "AND c.id=p.contentid "
				. "AND c.state='1' "
				. "AND c.publish_up < now() "
				. "AND MATCH(c.`title`,c.`fulltext`) AGAINST(".$db->Quote($text)." in boolean mode)";
                                
	$db->setQuery($strSQL);
	$total = $db->loadResult();

	$maxPerPage = 5;
	$start = min($total, max(0, ($page -1) * $maxPerPage));
	$max_this_page = min($total - $start, $maxPerPage);
	$end = $start + $max_this_page;

	$strSQL	= (JVERSION >= 1.6) 
			? "SELECT c.*,p.permalink FROM #__content AS c, "
				. "#__myblog_permalinks AS p "
				. "WHERE c.catid IN ({$sections}) "
				. "AND c.id=p.contentid "
				. "AND c.state='1' "
				. "AND c.publish_up < now() "
				. "AND MATCH(c.`title`,c.`fulltext`) AGAINST(".$db->Quote($text)." in boolean mode)"
				. "ORDER BY c.created DESC LIMIT {$start},{$max_this_page}"
				 
			: "SELECT c.*,p.permalink FROM #__content AS c, "
				. "#__myblog_permalinks AS p "
				. "WHERE c.sectionid IN ({$sections}) "
				. "AND c.id=p.contentid "
				. "AND c.state='1' "
				. "AND c.publish_up < now() "
				. "AND MATCH(c.`title`,c.`fulltext`) AGAINST(".$db->Quote($text)." in boolean mode)"
				. "ORDER BY c.created DESC LIMIT {$start},{$max_this_page}";
	
	$db->setQuery($strSQL);

	$rows = $db->loadObjectList();
	$searchresult = "";
	$excerptLength = 60;
	$titleLength = 45;
	
	if ($rows)
	{
		foreach ($rows as $row)
		{
			$excerpt = substr(strip_tags($row->fulltext), 0, $excerptLength);
// 			$row->title = preg_replace("/([\x80-\xFF])/e", "chr(0xC0|ord('\\1')>>6).chr(0x80|ord('\\1')&0x3F)", $row->title);
// 			$row->permalink = preg_replace("/([\x80-\xFF])/e", "chr(0xC0|ord('\\1')>>6).chr(0x80|ord('\\1')&0x3F)", $row->permalink);
			$title = substr(strip_tags($row->title), 0, $titleLength);
			
			if (strlen(strip_tags($row->title)) > $titleLength)
				$title .= "...";
			
			if (strlen(strip_tags($row->fulltext)) > $excerptLength)
				$excerpt .= "...";
			
			$titleLink = JRoute::_("index.php?option=com_myblog&show={$row->permalink}&Itemid={$Itemid}");
			$author = myUserGetName($row->created_by);
			
			// add 'myblog_insert' as a cue to editor that the targe need to be removed when saving
			$searchresult .= "<div class=\"result\"><div><a title=\"$row->title\" class=\"title\" href=\"$titleLink\" target=\"_blank myblog_insert\">$title</a></div> <div class=\"posted\">Posted:$row->created</div> <div>$excerpt</div></div>";
		}
		
		if ($start > 0 or $end < $total)
			$searchresult .= "<br/>";
		
	}
	
	if ($searchresult == "")
		$searchresult = JText::_('COM_MY_NO_RESULT_FOUND');
	
	$objResponse->addAssign("searchResults", "innerHTML", $searchresult);
	$objResponse->sendResponse();
}

function myxPingMyBlog()
{
	$objResponse = new JAXResponse();
	//$objResponse->addScriptCall("setTimeout('jax.call(\\'myblog\\',\\'myxPingMyBlog\\');',180000);");
	$objResponse->addScriptCall('myblog.pingqueue = setTimeout("myblog.doPing()", '.MY_PING_INTERVAL.');');	
	$objResponse->sendResponse();
	
	return;
}

/**
 * Loads a directory based on parameter
 **/
function myxLoadFolder($folder, $isAdvance= false)
{
	global $_MY_CONFIG;
	
	$objResponse = new JAXResponse();

	if(!class_exists('MYMediaBrowser'))
		include_once(MY_LIBRARY_PATH . DS .'imagebrowser.php');

	$imgBrowser	= new MYMediaBrowser();
	$my			=& JFactory::getUser();

	// Check if user really allowed to go out of current directory.
	if($_MY_CONFIG->get('imgFolderRestrict') && $folder == '/..' && !myxIsAdmin())
	{
	    $objResponse->addScriptCall('alert("' . JText::_('Not allowed to browse parent directory.') . '");');
	}
	
	$data = $imgBrowser->_getFiles($folder, false);
	$objResponse->addScriptCall('myblog.browserBuild', $data, $isAdvance);
	return $objResponse->sendResponse();
}

/**
 * Toggle publish/unpublish state of the comment
 * Must verify that the comments article does belong to current logged user
 */ 
function myxToggleCommentPublish($id)
{
	$objResponse	= new JAXResponse();
	$db				=& JFactory::getDBO();
	$my				=& JFactory::getUser();
	
	// Check the content, make sure it belongs to current user
	$query	= 'SELECT contentid FROM #__jomcomment WHERE `id`=' . $db->Quote( $id );
	$db->setQuery( $query );

	$cid	= $db->loadResult();

	$query	= 'SELECT created_by FROM #__content WHERE `id`=' . $db->Quote( $cid );
	$db->setQuery( $query );

	$author = $db->loadResult();
	
	if($author != $my->id)
	{
		// Permission error
		return;
	}
	
	$db->setQuery("SELECT published FROM #__jomcomment WHERE `id`=".$db->Quote($id));
	$publish = $db->loadResult();

	$publish = intval(!($publish));
	$db->setQuery("UPDATE #__jomcomment SET published='$publish' WHERE `id`=".$db->Quote($id));
	$db->query();
	
	if ($publish)
	{
		$objResponse->addAssign('pubImg' . $id, 'src', MY_COM_LIVE.'/images/publish_g.png');
		$d['id'] = $id;
	}
	else
	{
		$objResponse->addAssign('pubImg' . $id, 'src', MY_COM_LIVE.'/images/publish_x.png');
		$d['id'] = $id;
	}
	
	return $objResponse->sendResponse();
}

/**
 * Approve all comments for current users' article
 */ 
function myxCommentApproveAll()
{	
	$objResponse = new JAXResponse();
	$db				=& JFactory::getDBO();
	$my				=& JFactory::getUser();
	
	// Get blog entries that this user owns	
	$strSQL	= "SELECT id FROM #__content WHERE created_by=".$db->Quote($my->id);
	$db->setQuery( $strSQL );
	$result	= $db->loadObjectList();
	$rows	= array();
	
	foreach($result as $row)
	{
		$rows[]	= $row->id;
	}
	$rows	= implode(',', $rows);
	
	$strSQL	= "UPDATE #__jomcomment SET `published`='1' WHERE `contentid` IN({$rows}) "
			. "AND `published`='0'";
	$db->setQuery($strSQL);
	$db->query();
	
	$itemId	= myGetItemId();
	$link	= JRoute::_('index.php?option=com_myblog&task=showcomments&Itemid=' . $itemId , false);
	
	$objResponse->addScriptCall('alert' , 'Unpublished comments published.');
	$objResponse->addScriptCall('document.location.href="' . $link .'";');
		
	return $objResponse->sendResponse();
}

/**
 * remove all unpublished comment for current user's article
 */ 
function myxCommentRemoveUnpublished()
{
	$objResponse = new JAXResponse();
	$db				=& JFactory::getDBO();
	$my				=& JFactory::getUser();
	
	// Get blog entries that this user owns	
	$strSQL	= "SELECT id FROM #__content WHERE created_by=".$db->Quote($my->id);
	$db->setQuery($strSQL);
	$result	= $db->loadObjectList();
	$rows	= array();
	
	foreach($result as $row)
	{
		$rows[]	= $row->id;
	}
	$rows	= implode(',', $rows);
	
	$strSQL	= "DELETE FROM #__jomcomment WHERE `published`='0' AND `contentid` IN({$rows})";
	$db->setQuery($strSQL);
	$db->query();

	$link	= JRoute::_('index.php?option=com_myblog&task=showcomments' , false);

	$objResponse->addScriptCall('alert' , 'Unpublished comments removed.');
	$objResponse->addScriptCall('document.location.href="' . $link .'";');
	
	return $objResponse->sendResponse();
}

class Myblog
{
	var $task;
	var $adminTask ;
	
	function Myblog()
	{
		$this->adminTask = array('adminhome', 'edit', 'delete', 'write', 'showcomments', 
			'bloggerpref', 'bloggerstats', 'media','bloggerintegration');
	}
	
	function init()
	{
		$this->task	= JRequest::getVar( 'task' , '' , 'REQUEST' );

		if(empty($this->task))
		{
			// By default, it is browse
			$this->task = 'browse';
			
			# BLOODY MESS: task could be under a different name
			$show 	= false;
			$show	= JRequest::getVar( 'show' , '' , 'GET' );

			if(!empty($show))
				$this->task = 'show';

			// Display blogger's entries. Need to check the $show because, 
			// we need to append the index.php?show=xxx.html&blogger=USERNAME in the permalink and it will cause
			// issues with the permalink.
			$author	= JRequest::getVar( 'blogger' , '' , 'GET' );

			if(!empty($author) && $show == '')
				$this->task = 'author';
		}
	}
	
	/**
	 * Default view
	 */	 	
	function index()
	{
		include(MY_COM_PATH . '/jax.myblog.php');
		
		if(in_array($this->task, $this->adminTask))
		{
			jimport( 'joomla.filesystem.file' );
			
			$file	= MY_COM_PATH . DS . 'task' . DS . strtolower( $this->task ) . '.php';
			
			if( !JFile::exists($file ) )
			{
				JError::raiseError( 404 , JText::_('Invalid task' ) );
			}
			require_once( $file );
			
			$cName	= 'Myblog' . ucfirst($this->task) . 'Task';
			$obj	= new $cName();
			$obj->execute();
		}
		else if(in_array($this->task, $jaxFuncNames))
			return;
		else
			$this->browse();
	}
	
	function view()
	{
		$this->show();
	}
	
	function thumb()
	{
		require_once( MY_LIBRARY_PATH . DS . 'imagebrowser.php' );
		
		$path		= JRequest::getVar( 'fn' , '' , 'GET' );
		$maxWidth	= JRequest::getVar( 'maxwidth' , '' , 'GET' );

		$image		= new MYMediaBrowser($path);
		$image->thumbnail( $maxWidth );
	}

	function thumb_noresize()
	{
		$path	= JRequest::getVar( 'fn' , '' , 'GET' );
		mnPrintImage( $path );
	}
	
	function printblog()
	{
		$this->show();
	}
	
	function userblog()
	{
		$my		=& JFactory::getUser();

	    include_once(MY_COM_PATH . '/frontview.php');
		if ($my->id == "0")
		{
			# If user not logged in, cannot view his/her blog
			echo '<div id="fp-content">';
			echo JText::_('COM_MY_LOGIN_TO_VIEW_BLOG');
			echo '</div>';
		}
		else
		{
			echo '<div id="myBlog-wrap">';
			mb_showViewerToolbar("home");
			$frontview = new MB_Frontview();
			$frontview->attachHeader();
			echo $frontview->browse('userblog');
			echo '</div>';
			echo getPoweredByLink();
		}
	}
	
	function execute()
	{
		global $_MY_CONFIG;

		if(in_array($this->task, $this->adminTask))
		{
			$session	= JRequest::getVar( 'session' , '' , 'GET' );
			$my			=& JFactory::getUser();
			
			if ( ($my->id == "0" || !empty($session) )&& $this->task == 'write')
			{
				include_once(MY_COM_PATH . '/task/'.  strtolower($this->task) .'.php');
				
				$cname = 'Myblog'.ucfirst($this->task).'Task';
				$obj = new $cname();
				$obj->login();
				return;
			}
			
			if ($my->id == "0")
			{
				echo '<div id="fp-content">';
				echo JText::_('COM_MY_BLOG_ADMIN_NO_PERMISSIONS_TO_ACCESS');
				echo '</div>';
				return;
			}
		}
		
		// Dont process ajax calls.
		if($this->task != 'azrul_ajax' && $this->task != 'ajaxtaglist' && $this->task != 'thumb')
		{
			jimport( 'joomla.filesystem.file' );
			$this->task = JString::strtolower( $this->task );
			if (preg_match("/^[a-z]+$/i", $this->task)) {
				// all ok
			} else {
				echo 'invalid task';
				exit;
			}
			$file	= MY_COM_PATH . DS . 'task' . DS . $this->task . '.php';
			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$cname = 'Myblog'.ucfirst($this->task).'Task';
				$obj = new $cname();
				$obj->execute();
			}
			else
			{
				echo JText::_('Invalid task');
			}
		}
		
		if($this->task == 'ajaxtaglist')
		{
			include_once(MY_COM_PATH . '/task/ajaxtaglist.php');
		}
		
		if($this->task == 'thumb')
		{
			$this->thumb();
		}
	}
}

$myblog = new Myblog();
$myblog->init();
$task = $myblog->task;

$myblog->execute();	
