<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

//load the lang file. Language strings are not loaded on AJAX calls
$lang =& JFactory::getLanguage();
$extension = 'com_myblog';
$base_dir = JPATH_SITE;
$lang->load($extension, $base_dir);


/**
 *	Delete a tag
 */ 
function myxDeleteCategory($catID)
{
	$db		=& JFactory::getDBO();

	$db->setQuery("SELECT name FROM #__myblog_categories WHERE id=$catID");
	$catname = $db->loadResult();

	if ($catname)
	{
		$objResponse = new JAXResponse();

		$db->setQuery("SELECT COUNT(*) FROM #__myblog_content_categories WHERE category = $catID");
		$tagExistinPost = $db->loadResult(); 
		
// 		if ($tagExistinPost) 
// 		{
// 			$objResponse->addAssign("categoryerror", "innerHTML", "<span class=\"successMsg\" style=\"color:red\">'$catname' exists in post! Error deleting tag.</span>");
// 		}
// 		else
// 		{
			$db->setQuery("DELETE FROM #__myblog_categories WHERE id=$catID");
			$db->query();
			
			$db->setQuery("DELETE FROM #__myblog_content_categories WHERE category=$catID");
			$db->query();
			
			$objResponse->addRemove("row" . $catID);
			# convert from utf8 to iso 8859
// 			$catname = preg_replace("/([\x80-\xFF])/e", "chr(0xC0|ord('\\1')>>6).chr(0x80|ord('\\1')&0x3F)", $catname);
			$objResponse->addAssign("categoryerror", "innerHTML", "<span class=\"successMsg\" style=\"color:green\">'$catname' deleted.</span>");
// 		}
		$objResponse->sendResponse();
	}
	else
	{
		$objResponse = new JAXResponse();
		$objResponse->addAssign("categoryerror", "innerHTML", "<span class=\"errorMsg\" style=\"color:red\">Error deleting tag.</span>");
		$objResponse->sendResponse();
	}
	return;
}

/**
*	Toggle publishing of content
*/ 
function myxTogglePublishAdmin($id)
{
	$db		=& JFactory::getDBO();
	
	/*$db->setQuery("SELECT state FROM #__content WHERE id=$id");
	$publish = $db->loadResult();
	$publish = intval(!($publish));*/

	//$db->setQuery("UPDATE #__content SET state='$publish' WHERE id=$id");
	$db->setQuery("UPDATE #__content SET state=((state+1)%2) WHERE id=$id");
	$db->query();
	
	$db->setQuery("SELECT state FROM #__content WHERE id=$id");
	$publish = $db->loadResult();
		
	$objResponse = new JAXResponse();
	if ($publish){
		$objResponse->addAssign('pubImg' . $id, 'src', 'components/com_myblog/assets/images/published.png' );
		
		//check if Activity Stream trigger is needed
		$row =& JTable::getInstance( 'BlogContent' , 'Myblog' );
		$row->load($id);
		
		//trigger activity stream and points if the entry has just been approved by the admin.
		if($row->hits == 0){
		
			global $_MY_CONFIG;
			if( $_MY_CONFIG->get('jomsocialActivity') )
			{
				// Test if jomsocial file really exists.
				$core	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
				if( JFile::exists( $core ) )
				{					
					require_once( $core );
					$command		= 'blog.create' ; //( $row->id == 0 ) ? 'blog.create' : 'blog.update';
					$title			= JString::substr( $row->title , 0 , 20 ) . '...';
					$link			= JRoute::_(JURI::root().'index.php?option=com_myblog&show=' . $row->permalink . '&Itemid=' . myGetItemId() ); 
					
					$act			= new stdClass();
					$act->cmd 		= $command;
					$act->actor   	= $row->created_by;
					$act->target  	= 0;
					
					$act->title		= JText::sprintf('COM_MY_JS_BLOG_ENTRY_CREATED_ACTIVITY' , $link , $title );
					$act->content	= $row->introtext;
					$act->app		= 'myblog';
					$act->cid		= $row->id;
		
					// Add activity logging
					CFactory::load ( 'libraries', 'activities' );
					CActivityStream::add($act);	
					
					//increase the hits by 1 to avoid another trigger on this jomsocial activity stream API
					$db->setQuery("UPDATE #__content SET hits=hits+1 WHERE id=$id");
					$db->query();		
				}
			}
		}
	
	}else{
		$objResponse->addAssign('pubImg' . $id, 'src', 'components/com_myblog/assets/images/unpublished.png');
	}
	$objResponse->sendResponse();
	return;
}

/**
*	Toggle publishing of myblog bot
*/ 
function myxToggleBotPublish($id)
{
	$db		=& JFactory::getDBO();
	
	$db->setQuery("SELECT published FROM #__myblog_bots WHERE id=$id");
	$publish = $db->loadResult();
	$publish = intval(!($publish));
	
	$db->setQuery("UPDATE #__myblog_bots SET published=$publish WHERE id=$id");
	$db->query();
	
	$objResponse = new JAXResponse();
	if ($publish)
		$objResponse->addAssign('pubImg' . $id, 'src', 'images/publish_g.png');
	else
		$objResponse->addAssign('pubImg' . $id, 'src', 'images/publish_x.png');
	
	$objResponse->sendResponse();
	return;
}

/**
*	Toggle publishing of mambots
*/ 
function myxToggleMambotPublish($id)
{
	$db		=& JFactory::getDBO();

	$db->setQuery("SELECT my_published FROM #__myblog_mambots WHERE mambot_id=$id");
	$publish = $db->loadResult();
	$publish = intval(!($publish));
	
	$db->setQuery("UPDATE #__myblog_mambots SET my_published=$publish WHERE mambot_id=$id");
	$db->query();
	
	$objResponse = new JAXResponse();
	
	if ($publish){
		$objResponse->addAssign('pubImg' . $id, 'src', ((JVERSION >= 1.6) ? 'components/com_myblog/assets/images/published.png' : 'images/publish_g.png') );
	}else{
		$objResponse->addAssign('pubImg' . $id, 'src', ((JVERSION >= 1.6) ? 'components/com_myblog/assets/images/unpublished.png' : 'images/publish_x.png'));
	}
	
	$objResponse->sendResponse();
	return;
}

// /**
//  *	Order  My blog bots
//  */ 
// function orderBot($uid, $i, $inc)
// {
// 	global $database;
// 
// 	$db	=& cmsInstance('CMSDb');
// 
// 	$objResponse = new JAXResponse();
// 	if ($inc == "1")
// 	{
// 		$db->query("SELECT * from #__myblog_bots WHERE id='$uid'");
// 		$ss = $db->get_object_list();
// 		$s = $ss[0];
// 		$db->query("SELECT * FROM #__myblog_bots WHERE ordering < '$s->ordering' ORDER BY ordering DESC LIMIT 0,1");
// 		$tt = $db->get_object_list();
// 		$db->query("SELECT count(*) from #__myblog_bots");
// 		$total = $db->get_value();
// 		$t_i = "";
// 	}
// 	else
// 	{
// 		$db->query("SELECT * from #__myblog_bots WHERE id='$uid'");
// 		$tt = $db->get_object_list();
// 		$t = $tt[0];
// 		$db->query("SELECT * FROM #__myblog_bots WHERE ordering > '$t->ordering' ORDER BY ordering ASC LIMIT 0,1");
// 		$ss = $db->get_object_list();
// 		$s = $ss[0];
// 		$db->query("SELECT count(*) from #__myblog_bots");
// 		$total = $db->get_value();
// 		$t_i = $i;
// 		$i = $i +1;
// 	}
// 	if ($s and $t) {
// 		$db->query("UPDATE #__myblog_bots SET ordering=$t->ordering WHERE id=$s->id");
// 		$db->query("UPDATE #__myblog_bots SET ordering=$s->ordering WHERE id=$t->id");
// 		$task = $s->published ? 'unpublish' : 'publish';
// 		$img = $s->published ? 'publish_g.png' : 'publish_x.png';
// 		$t_i = $i -1;
// 		$tContent = "<td><input type='checkbox' id='cb$t_i' name='cid[]' value='$s->id' onclick='isChecked(this.checked);' /></td> <td>&nbsp;$s->name</td> <td>&nbsp;$s->folder</td> <td>&nbsp;$s->filename</td> <td width=\"5%\" align=\"right\"><a href=\"javascript:void(0);\" onclick=\"jax.call('myblog','myxToggleBotPublish','$s->id');return false;\"><img id=\"pubImg$s->id\" src=\"images/$img\" hspace=\"8\" width=\"12\" height=\"12\" border=\"0\" alt=\"\" /></a></td> <td align=\"center\">";
// 		if ($t_i > 0) {
// 			$tContent .= '<a href="javascript:void(0);" onClick="jax.icall(\'myblog\',\'orderBot\',\'' . $s->id . '\',\'' . $t_i . '\',\'1\');" title="' . 'Order Up' . '"> <img src="images/uparrow.png" width="12" height="12" border="0" alt="' . 'Order Up' . '"> </a>';
// 		} else {
// 			$tContent .= '&nbsp;';
// 		}
// 		if ($t_i < $total -1 && $t_i > 0) {
// 			$tContent .= '<a href="javascript:void(0);" onClick="jax.icall(\'myblog\',\'orderBot\',\'' . $s->id . '\',\'' . $t_i . '\',\'-1\');" title="' . 'Order Down' . '"> <img src="images/downarrow.png" width="12" height="12" border="0" alt="' . 'Order Down' . '"> </a>';
// 		} else {
// 			$tContent .= '&nbsp;';
// 		}
// 		$tContent .= "</td>";
// 		$task = $t->published ? 'unpublish' : 'publish';
// 		$img = $t->published ? 'publish_g.png' : 'publish_x.png';
// 		$sContent = "<td><input type='checkbox' id='cb$i' name='cid[]' value='$t->id' onclick='isChecked(this.checked);' /></td> <td>&nbsp;$t->name</td> <td>&nbsp;$t->folder</td> <td>&nbsp;$t->filename</td> <td width=\"5%\" align=\"right\"><a href=\"javascript:void(0);\" onclick=\"jax.icall('myblog','myxToggleBotPublish','$t->id');return false;\"><img id=\"pubImg$t->id\" src=\"images/$img\" hspace=\"8\" width=\"12\" height=\"12\" border=\"0\" alt=\"\" /></a></td> <td>&nbsp;&nbsp;";
// 		if ($i > 0) {
// 			$sContent .= '<a href="javascript:void(0);" onClick="jax.icall(\'myblog\',\'orderBot\',\'' . $t->id . '\',\'' . $i . '\',\'1\');" title="' . 'Order Up' . '"> <img src="images/uparrow.png" width="12" height="12" border="0" alt="' . 'Order Up' . '"> </a>';
// 		} else {
// 			$sContent .= '&nbsp;';
// 		}
// 		if ($i < $total -1 && $i > 0) {
// 			$sContent .= '<a href="javascript:void(0);" onClick="jax.icall(\'myblog\',\'orderBot\',\'' . $t->id . '\',\'' . $i . '\',\'-1\');" title="' . 'Order Down' . '"> <img src="images/downarrow.png" width="12" height="12" border="0" alt="' . 'Order Down' . '"> </a>';
// 		} else {
// 			$sContent .= '&nbsp;';
// 		}
// 		$sContent .= "</td>";
// 		$objResponse->addAssign("bot$i", 'innerHTML', $sContent);
// 		$objResponse->addAssign("bot$t_i", 'innerHTML', $tContent);
// 	}
// 	$objResponse->sendResponse();
// }

/**
 *	Called via AJAX to add category.
 */ 
function myxAddCategory($newCat)
{
	global $_MY_CONFIG;
	$newCat = html_entity_decode($newCat);
	$newCat = preg_replace('/[\s]{2,}/', ' ', $newCat);

	$db		=& JFactory::getDBO();

	include_once(MY_LIBRARY_PATH.'/tags.php');
	
	
	$objResponse	= new JAXResponse();		
	$tagObj			= new MYTags();

	if ($tagObj->add($newCat))
	{
		$newId = $tagObj->getInsertId();
		
		if($_MY_CONFIG->get('allowDefaultTags'))
		{
			$objResponse->addScriptCall('addTagRow("' . $newCat . '","' . $tagObj->strip($newCat) . '","' . $newId . '", 1);');
		}
		else
		{
			$objResponse->addScriptCall('addTagRow("' . $newCat . '","' . $tagObj->strip($newCat) . '","' . $newId . '", 0);');
		}

		$objResponse->addAssign("categoryerror", "innerHTML", "<span class=\"successMsg\" style=\"color:green\">Success! '$newCat' added.</span>");
		$objResponse->sendResponse();
	}
	else
	{
		if (empty($newCat))
			$objResponse->addAssign("categoryerror", "innerHTML", "<span class=\"errorMsg\" style=\"color:red\">Error: Tag must be at least one character.</span>");
		else	
			$objResponse->addAssign("categoryerror", "innerHTML", "<span class=\"errorMsg\" style=\"color:red\">Error:Tag '$newCat' already exists.</span>");

		$objResponse->sendResponse();
	}
	return;
}

function myxSetDefaultCategory($id, $default)
{
	global $_MY_CONFIG;
	
	include_once(MY_LIBRARY_PATH.'/tags.php');
		
	$tagObj =& JTable::getInstance( 'Tag' , 'myblog' );
	$tagObj->load($id);
	
	$tagObj->setDefault($default);
	$tagObj->store();
	
	$response	= new JAXResponse();
	$response->sendResponse();
}

function myxUpdateCategory($id, $tag, $oldtag)
{
	global $_MY_CONFIG;
	
	include_once(MY_LIBRARY_PATH.'/tags.php');
	
	$tag	= html_entity_decode($tag);
	$tag	= preg_replace('/[\s]{2,}/', ' ', $tag);
		
	$response	= new JAXResponse();
	$tagObj 	=& JTable::getInstance( 'Tag' , 'myblog' );
	$tagObj->load($id);
	
	if($tagObj->setName($tag))
	{
		$tagObj->store();
		
		// Display notice
		$response->addScriptCall('jQuery(\'#tagnotice\').html(\'Tag updated! <br />\');');
		
		// Update original value and id
		$response->addScriptCall('jQuery(\'#row\' + ' . $id . '+ \' span.tagname\').attr(\'orgval\', \'' . addslashes($tag) . '\');');
		$response->addScriptCall('jQuery(\'#row\' + ' . $id . '+ \' span.editlink\').attr(\'orgval\', \'' . addslashes($tag) . '\');');
		
		// Re-update edit link
		$response->addScriptCall('showEditLink();');
		
	}
	else
	{
		$error	= 'Tag Exists!';
		
		if(empty($tag))
			$error	= 'Tag should consist of at least 1 character!';
		
		$response->addScriptCall('jQuery(\'#row' . $id . ' span.editlink span\').html(\'' . $error . '\');');
		
		// Restore old value
		$response->addScriptCall('jQuery(\'#row\' + ' . $id . '+ \' span.tagname\').html(\'' . $oldtag . '\');');
	}
	$response->sendResponse();
	return;
}

function myxUpdateSlug($id, $slug, $oldslug)
{
	global $_MY_CONFIG;
	
	include_once(MY_LIBRARY_PATH.'/tags.php');
	
	$slug	= html_entity_decode($slug);
	$slug	= preg_replace('/[\s]{2,}/', ' ', $slug);
	
	$response	= new JAXResponse();
	$tagObj 	=& JTable::getInstance( 'Tag' , 'myblog' );
	$tagObj->load($id);
	
	if($tagObj->setSlug($slug) && $slug != '')
	{
		$tagObj->store();
		
		// Display notice
		$response->addScriptCall('jQuery(\'#tagnotice\').html(\'Slug updated! <br />\');');
		
		// Update original value and id
		$response->addScriptCall('jQuery(\'#row\' + ' . $id . '+ \' span.slugname\').attr(\'orgval\', \'' . $tagObj->slug . '\');');
		$response->addScriptCall('jQuery(\'#row\' + ' . $id . '+ \' span.editslug\').attr(\'orgval\', \'' . $tagObj->slug . '\');');
		
		// Re-update edit link
		$response->addScriptCall('showEditSlug();');
		
	}
	else
	{
		$error	= 'Slug Exists!';
		
		if($slug == ''){
			$error	= '';
		}
					
		// Restore old value
		$response->addScriptCall('jQuery(\'#row\' + ' . $id . '+ \' span.slugname\').html(\'' . $oldslug . '\');');
		
		// Throw error			
		$response->addScriptCall('jQuery(\'#row' . $id . ' span.editslug span\').html(\'' . $error . '\');');
	
	}
	$response->sendResponse();
	return;
}
