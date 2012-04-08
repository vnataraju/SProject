<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

global $_MY_CONFIG;

// Include defines.php if not included
if(!defined('MY_COM_PATH'))
	require_once( JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'defines.myblog.php' );

require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_myblog' . DS . 'config.myblog.php' );
require_once( MY_COM_PATH . DS . 'libraries' . DS . 'datamanager.php' );
require_once( MY_COM_PATH . DS . 'functions.myblog.php' );

$_MY_CONFIG = new MYBLOG_Config();

jimport( 'joomla.filesystem.file' );

if(!class_exists("MyblogModule"))
{

	class MyblogModule
	{
	
		function showArchive()
		{
		    global $_MY_CONFIG;
		    
		    $db				=& JFactory::getDBO();
			$mbItemid		= $this->myGetItemID();
			$bloggerUrl		= '';
			$where			= "";
			$sections		= $_MY_CONFIG->get('managedSections');
			$blogger		= JRequest::getVar( 'blogger' , '' , 'REQUEST' );
			$content		= '';
			
			//@rule: If request is made for blogger, display appropriately.
			if( !empty( $blogger ) )
			{
				$id			= myGetAuthorId( $blogger );
				$where		= ' AND a.created_by=' . $db->Quote( $id ) . ' ';
			}

                        $where .= (JVERSION >= 1.6)
                            ? " AND catid IN (" . $sections . ")"
                            : " AND sectionid IN (" . $sections . ")";

			$query	= 'SELECT count(*) as `count`, date_format( a.created , "%M" ) AS month, date_format( a.created , "%Y" ) AS year '
					. 'FROM #__content as a '
					. 'WHERE a.state = 1 AND a.created < NOW() '
					. $where
					. 'GROUP BY month,year '
					. 'ORDER BY a.created DESC';

			$db->setQuery( $query );

			$rows	= $db->loadObjectList();
                        return $rows;
                        
		}
	
		function myGetItemID()
		{
			return myGetItemId(); 
		}
	
		function showLatestEntriesIntro(&$params)
		{
			global $_MY_CONFIG;
			
			$limit				= $params->get('numLatestEntries', 5);
			$sections           = $_MY_CONFIG->get('managedSections');
			$titleMaxLength 	= $params->get('titleMaxLength', 20);
			$blogger			= JRequest::getVar( 'blogger' , '' , 'GET' );
			$authorid			= myGetAuthorId( $blogger );
			
			if($authorid == '0')
				$authorid	= '';
	
			if (!is_numeric($titleMaxLength) or $titleMaxLength == "0")
				$titleMaxLength = 20;
	
			if (!is_numeric($limit))
				$limit = 5;
	
	
			if(function_exists('mb_get_entries'))
			{
				$filter = array(
									'limit'=> $limit,
									'limitstart' => 0,
									'authorid' => $authorid
								);
				$entries = mb_get_entries($filter);
			}
			else
			{
				$objDataMngr = new MY_DataManager();
				$entries = $objDataMngr->getEntries($total,$limit,0,$authorid);
			}

                        return $entries;
			
		}
	
		function _getCSS($type)
		{
			global $_MY_CONFIG;
			
			
			
			$mainframe	=& JFactory::getApplication();
			
			$path	= JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'templates' . DS . '_default' . DS . 'module.' . $type . '.css';
			$custom	= '';
			
			if( $_MY_CONFIG->get('overrideTemplate') )
			{
				$custom	= JPATH_ROOT . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'com_myblog' . DS . 'module.' . $type . '.css';
			}
			else
			{
				$custom	= MY_TEMPLATE_PATH . DS . $_MY_CONFIG->get('template') . DS . 'module.' . $type . '.css';
			}
			
			if( !empty( $custom ) && JFile::exists( $custom ) )
			{
				$path	= $custom;
			}
			
			$contents	= JFile::read( $path );
			$data		= '';
			
			if(!empty($contents))
			{
				$data		= '<style type="text/css">';
				$data		.= $contents;
				$data		.= '</style>';
			}
			return $data;
		}
		
		function _getAvatar($creator)
		{
			global $_MY_CONFIG;
			
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'libraries' . DS . 'avatar.php' );
						
			$avatar	= 'My' . ucfirst($_MY_CONFIG->get('avatar')) . 'Avatar';
			$avatar	= new $avatar($creator);
			
			$avatar	= $avatar->get();

			return $avatar;
		}
	
		function showLatestEntries(&$params)
		{
			global $_MY_CONFIG;
	
			$sections           = $_MY_CONFIG->get('managedSections');
			$titleMaxLength 	= $params->get('titleMaxLength',20);

			$blogger			= JRequest::getVar( 'blogger' , '' , 'GET' );
			$authorid			= myGetAuthorId( $blogger );
			
			if($authorid == '0')
				$authorid	= '';
	
			if (!is_numeric($titleMaxLength) or $titleMaxLength == "0")
				$titleMaxLength = 20;
			
			$limit = 5;
			if(isset($params))
				$limit	= $params->get('numLatestEntries', 5);
	
			if (!is_numeric($limit))
				$limit = 5;
		
			if(function_exists('mb_get_entries'))
			{
				$filter = array(
					'limit'=> $limit,
					'limitstart' => 0,
					'authorid' => $authorid);
				$entries = mb_get_entries($filter);
			}
			else
			{
				$objDataMngr = new MY_DataManager();
				$entries = $objDataMngr->getEntries($total,$limit,0,$authorid);
			}
                        return $entries;
		}
	
		function showTagClouds($params)
		{
			global $_MY_CONFIG;

                        $wrapTag = $params->get('wrapTag');
                        $wrapTag = $wrapTag=='ul'?$wrapTag:'div';
			$subWrap = 'li';
                        if($wrapTag == 'ul')
                        {
                                $subWrap= 'li';
                        }
                        else
                        {
                                $subWrap = '';
                        }

                        $blogger	= JRequest::getVar( 'blogger' , '' , 'GET' );
                        $mbItemid	= myGetItemId();
                        $content = '<'.$wrapTag.' class="blog-tags">';
                        $query = "SELECT c.slug, c.name, count(c.name) frequency FROM #__myblog_categories c,#__myblog_content_categories c2 where c.id=c2.category GROUP BY c.name ORDER BY frequency ASC";
                        $categoriesArray = myGetTagClouds($query, 8);
                        $categories = "";

                        if ($categoriesArray)
                        {
                                foreach ($categoriesArray as $category)
                                {
                                        $catclass = "tag" . $category['cloud'];
                                        $catname = $category['name'];
                                        $tagSlug	= $category['slug'];
                                        $tagSlug	= ($tagSlug == '') ? $category['name'] : $category['slug'];
                                        $tagSlug	= urlencode($tagSlug);

                                        if(!empty($subWrap))
                                        {
                                                $categories .= "<{$subWrap} class=\"$catclass\">";

                                                if(isset($blogger) && !empty($blogger))
                                                {
                                                        $categories .= "<a href=\"" . JRoute::_("index.php?option=com_myblog&category=" . $tagSlug . "&blogger=$blogger&Itemid=$mbItemid") . "\">$catname</a> ";
                                                } else {
                                                        $categories .= "<a href=\"" . JRoute::_("index.php?option=com_myblog&task=tag&category=" . $tagSlug . "&Itemid=$mbItemid") . "\">$catname</a> ";
                                                }			
                                                $categories .= "</$subWrap>";
                                        }
                                        else
                                        {
                                                if(isset($blogger) && !empty($blogger))
                                                {
                                                        $categories .= "<a class=\"$catclass\" href=\"" . JRoute::_("index.php?option=com_myblog&category=" . $tagSlug . "&blogger=$blogger&Itemid=$mbItemid") . "\">$catname</a> ";
                                                }
                                                else
                                                {
                                                        $categories .= "<a class=\"$catclass\" href=\"" . JRoute::_("index.php?option=com_myblog&task=tag&category=" . $tagSlug . "&Itemid=$mbItemid") . "\">$catname</a> ";
                                                }

                                        }
                                }
                        }

                        $content .= trim($categories, ",");
                        $content .= "</{$wrapTag}>";
                        return $content;
                        /*
                        $wrapperTag   = isset($params) ? $params->get('wrapTag', 'div') : 'div';

			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'task' . DS . 'categories.php' );
			
			$mbItemid		= $this->myGetItemID();
			$objFrontView	= new MyblogCategoriesTask();
			$tagCloud		= $objFrontView->display('id="blog-tags-mod"', $wrapperTag);
			$mainframe		=& JFactory::getApplication();
				
			$tagCloud 		= JString::str_ireplace("<p>","",$tagCloud);
			$tagCloud 		= JString::str_ireplace("</p>","",$tagCloud);
			$tagCloud 		= JString::str_ireplace("<br/>","",$tagCloud);
		
			// Add the custom css file. Firstly, check in the current template folder if
			// module.tags.css exits. If tempate overriede is used, check in those.
			// If both of the above fails, use the default one in _default
			$cssFilePath 	= MY_COM_PATH . '/templates/_default/module.tags.css';
			$file			= '';
			
			if ($_MY_CONFIG->get('overrideTemplate'))
			{
				$file		= JPATH_ROOT . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'com_myblog' . DS . 'module.tags.css';
			}
			else
			{
				$file		= MY_TEMPLATE_PATH . DS . $_MY_CONFIG->get('template') . DS . 'module.tags.css';
			}

			if( JFile::exists( $file ) )
			{
				$cssFilePath	= $file;
			}
			
			$contents	= JFile::read( $cssFilePath );
	
			if(!empty($contents))
			{
				echo '<style type="text/css">';
				echo $contents;
				echo '</style>';
			}
	
			if (trim($tagCloud)!="")
				echo $tagCloud;
			else
				echo '<i>None</i>';*/
		}
	
		function showLatestComment(&$params)
		{
		    global $_MY_CONFIG;
	
			$mbItemid = $this->myGetItemID();
			
			$postedByDisplay    = $params->get('latestCommentsPostedBy', 1);
	   		$titleMaxLength		= $params->get('titleMaxLength', 20);
			$db					=& JFactory::getDBO();
			
			if (!is_numeric($titleMaxLength) or $titleMaxLength == "0")
				$titleMaxLength = 20;
	
			$where				= "";
		
		    $sections   = $_MY_CONFIG->get('managedSections');
	
			$blogger    		= JRequest::getVar( 'blogger' , '' , 'GET');
			$authorid			= myGetAuthorId( $blogger );
			
			if($authorid == 0)
			{
				$authorid = "";
			}
			else
			{
				$where = "and created_by = '".$authorid."'";
			}
		
			$limit = $params->get('numLatestComments', 5);
	
			if (!is_numeric($limit))
				$limit = 5;
	
			$strSQL	= "SELECT a.id, a.comment, a.preview AS preview, a.contentid, a.date, a.user_id FROM #__jomcomment AS a "
					. "JOIN #__content AS b ON a.contentid=b.id "
					. "WHERE b.sectionid IN ($sections) "
					. "AND b.state=1 "
					. "AND a.published=1 "
					. "AND a.option='com_myblog' $where ORDER BY date DESC LIMIT 0, $limit";
		
		    $db->setQuery($strSQL);
		    
			$results    = $db->loadObjectList();
			$Loop		= 0;
			$content 	= "";

			$content = '<div class="'.$params->get('moduleclass_sfx').'"><ul class="blog-comments">';
			
			if( $results )
			{
				foreach($results as $row)
				{
					// Test if the preview is available, if it is, just use the preview field
					if($row->preview)
						$row->comment	= $row->preview;
	
					if(JString::strlen($row->comment) > $titleMaxLength)
						$row->comment	= JString::substr(strip_tags(trim($row->comment)), 0, $titleMaxLength) . ' ...';
				
					if($row->contentid)
					{
						$permalink	= myGetPermalinkUrl($row->contentid);
						$titleHref	= $permalink . '#comment-' . $row->id;
					}
					$authorLink	= '';
					
					if($postedByDisplay != '0')
					{
						if($row->user_id == '0')
						{
							$authorLink	= '<span class="blog-comment-author">by Guest</span>';
						}
						else
						{
							$author			= myUserGetName($row->user_id, ($postedByDisplay == '1' ? '0' : '1'));
							$authorLinkHref	= JRoute::_('index.php?option=com_myblog&blogger=' . urlencode($author) . '&Itemid=' . $mbItemid);
							$authorLink		= '<span class="blog-comment-author">by <a href="' . $authorLinkHref . '">' . $author . '</a></span>';
						}
					}
					$content	.= "<li><a href='".$titleHref."'>". $row->comment ."</a>$authorLink</li>";
				}
			}
			else
			{
				$content	.= '<li>' . JText::_('No comments yet.') . '</li>';
			}
			$content .= "</ul></div>";
			echo $content;
		}
	
	
		function showPopularBlogger(&$params)
		{
		    global $_MY_CONFIG;
		    
			$myItemid	= $this->myGetItemID();
	
			$content = "";
			
			// Get the proper param values.
			$db					=& JFactory::getDBO();
			$limit = $params->get('numPopularBlogs', 5);

			if (!is_numeric($limit))
				$limit = 5;
	
			$sections   	= $_MY_CONFIG->get('managedSections');

                        $whereSection = (JVERSION >= 1.6)
			? " AND catid IN (" . $sections. ")"
			: " AND sectionid IN (" . $sections . ")";

			$strSQL         = "SELECT `created_by`,count(created_by) as `count`, sum(hits) AS `hits` "
			                . "FROM #__content WHERE "
			                . " `state`='1'"
                                        . $whereSection
                                        . "GROUP BY `created_by` "
			                . "ORDER BY `hits` DESC "
			                . "LIMIT 0,{$limit}";

			$db->setQuery($strSQL);
			$rows    = $db->loadObjectList();
                        
                        return $rows;
                        
			if($rows)
			{
                            if( $showAvatar )
                            {
    ?>
                                    <table width="100%" cellpadding="3" cellspacing="0">
    <?php
                            }
                            else
                            {
    ?>
                                    <ul class="blog-bloggers">
    <?php
                            }
				foreach($rows as $row)
				{
					$date	=& JFactory::getDate();
					$strSQL	= "SELECT COUNT(*) FROM #__content WHERE `created_by`='{$row->created_by}'  $whereSection AND `state` != '0' AND created <= " . $db->Quote( $date->toMySQL() );
					$db->setQuery($strSQL);
					$count	= $db->loadResult();
					$authorname =  myGetAuthorName( $row->created_by , $_MY_CONFIG->get('useFullName') );
				    $link   = JRoute::_("index.php?option=com_myblog&blogger={$authorname}&Itemid={$myItemid}");
				    
				    if($showAvatar)
				    {
?>
					<tr style="border-bottom: 1px solid #eee;">
						<td width="70%">
								<a href="<?php echo $link;?>">
									<?php echo myUserGetName($row->created_by, ($postedByDisplay == '1' ? '0' : 1 )); ?>
								</a>
								(<?php echo $count; ?>)
						</td>
						<td align="right">
							<?php echo ($showAvatar) ? $this->_getAvatar( $row->created_by ) : ''; ?>
						</td>
					</tr>
<?php				}
					else
					{
?>
					<li>
						<a href="<?php echo $link;?>">
							<?php echo myUserGetName($row->created_by, ($postedByDisplay == '1' ? '0' : 1 )); ?>
						</a>
						(<?php echo $count; ?>)
					</li>
<?php
					}
				}
				
				if( $showAvatar )
				{
?>
					</table>
<?php
				}
				else
				{
?>
					</ul>
<?php
				}
			}
			else
			{
?>
				<p>No bloggers yet.</p>
	<?php
			}
		}
		
		function showCategories()
		{
		    global $_MY_CONFIG;
		    
                    $section = JVERSION >= 1.6 ? $_MY_CONFIG->get('managedSections'): $_MY_CONFIG->get('postSection');
		    $categories	= myGetCategoryList( $section );
                    return $categories;
                   ?>
			
<?php
		}
		
	}//end class
}//end outher if