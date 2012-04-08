<?php
/**
 * @category	Plugins
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');

if(!class_exists('plgCommunityJomComment'))
{
	class plgCommunityJomComment extends CApplications
	{
		var $name		= 'JomComment';
		var $_name		= 'jomcomment';
		var $_path		= '';
			
	    function plgCommunityJomComment(& $subject, $config)
	    {
			$this->db 		=& JFactory::getDBO();
			$this->_path	= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_jomcomment';
	
			parent::__construct($subject, $config);
	    }
	
		/**
		 * Ajax function to save a new wall entry
		 * 	 
		 * @param message	A message that is submitted by the user
		 * @param uniqueId	The unique id for this group
		 * 
		 **/	 	 	 	 	 		
		function onProfileDisplay()
		{	
			JPlugin::loadLanguage( 'plg_jomcomment', JPATH_ADMINISTRATOR );
			$mainframe =& JFactory::getApplication();
		
			// Attach CSS
			$document	=& JFactory::getDocument();
			$css		= JURI::base() . 'plugins/community/jomcomment/style.css';
			$document->addStyleSheet($css);
			
			if(JRequest::getVar('task', '', 'REQUEST') == 'app'){
				$app = 1;	
			}else{
				$app = 0;
			}
			
			$user	= CFactory::getRequestUser();
			$userid	= $user->id;
			
			$def_limit = $this->params->get('count', 10);
			$limit = JRequest::getVar('limit', $def_limit, 'REQUEST');
			$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');
			$text_limit = $this->params->get('limit', 50);
			$link_back = $this->params->get('link_back', 1);
			
			$caching = $this->params->get('cache', 1);		
			if($caching)
			{
				$caching = $mainframe->getCfg('caching');
			}
			
			if( file_exists( $this->_path . DS . 'config.jomcomment.php' ) ){
				$row = $this->getComment($userid, $limitstart, $limit);
				$total = $this->countComment($userid);
				
				$cache =& JFactory::getCache('plgCommunityJomComment');
				$cache->setCaching($caching);
				$callback = array('plgCommunityJomComment', '_getJomCommentHTML');		
				$content = $cache->call($callback, $userid, $limit, $limitstart, $row, $app, $total, $text_limit, $link_back);
			}else{
				
				$content = "<table>
							<tr>
								<td style=\"vertical-align: top;padding:4px\">
					            <img src='".JURI::base()."components/com_community/assets/error.gif' alt='' />
					        	</td>
					        	<td style=\"vertical-align: top;padding:4px\">
								 " .JText::_('PLG_JOMCOMMENT_NOT_INSTALLED') . "
								</td>
							</tr>
							</table>";
							
			}
			
			return $content; 	
		}
		
		function _getJomCommentHTML($userid, $limit, $limitstart, $row, $app, $total, $text_limit, $link_back){
			
			$mainframe =& JFactory::getApplication();
			
			$html = "";
					
			if(!empty($row)){	
				foreach($row as $data){
					if(!empty($data->referer)){
						$referer = $data->referer;
					}else{					
						$referer = plgCommunityJomComment::buildLink($mainframe, $data->id);
					}
					
					$comment = JString::substr($data->comment, 0, $text_limit);
					if(JString::strlen($data->comment) > $text_limit){
						$comment .= " .....";
					}
					
					$date	= new JDate($data->date);
					$html .= "<div>";
						$html .= "<div style='margin-top:10px;margin-bottom:5px'>";
							if(!empty($data->title)){
								$html .= "	<div style='float:left;font-weight:bold'><a href='".$referer."'>".$data->title."</a></div>";
							}
						$html .= "	<div style='float:right;font-weight:bold' class='createdate'>".$date->toFormat()."</div>";
						$html .= "	<div style='clear:both;'></div>";
						$html .= "</div>";
						$html .= "<div style='border-bottom:1px solid #CCCCCC; margin-bottom:4px'></div>";	
						$html .= "<div style='margin-bottom:2px'>".$comment."</div>";
						if($link_back){
							$html .= "<div style='margin-bottom:20px; float:right'><a href='".$referer."'>" . JText::_('PLG_JOMCOMMENT_GO_TO_ARTICLE') . "</a>.</div>";
							$html .= "<div style='clear:both;'></div>";
						}
					$html .= "</div>";		
				}
				
				if($app == 1){
					jimport('joomla.html.pagination');
					
					$pagination	= new JPagination( $total , $limitstart , $limit );
					$html .= '
					<!-- Pagination -->
					<div style="text-align: center;">
						'.$pagination->getPagesLinks().'
					</div>
					<!-- End Pagination -->';			
				}else{
					$showall = CRoute::_('index.php?option=com_community&view=profile&userid='.$userid.'&task=app&app=jomcomment');
					$html .= "<div style='margin-top:10px; float:right;'><a href='".$showall."'>".JText::_('PLG_JOMCOMMENT_SHOW_ALL')."</a></div>";
				}
			}else{
				$html .= "<div>" . JText::_('PLG_JOMCOMMENT_NO_COMMENTS_YET') . "</div>";
			}	
			
			$html .= "<div style='clear:both;'></div>";
			
			return $html;
		}
		
		function onAppDisplay()
		{
			ob_start();
			$limit=0;
			$html= $this->onProfileDisplay($limit);
			echo $html;
			
			$content	= ob_get_contents();
			ob_end_clean(); 
		
			return $content;
			
		}
		
		function buildLink($mainframe, $id){
			$db		=& JFactory::getDBO();
			
			$strSQL	= 'SELECT a.'.$db->nameQuote('id').',a.'.$db->nameQuote('contentid').',a.'.$db->nameQuote('comment').',a.'.$db->nameQuote('option')
					. ' FROM '.$db->nameQuote('#__jomcomment').' AS a '
					. ' WHERE a.'.$db->nameQuote('published').'='.$db->Quote('1')
					. ' AND a.'.$db->nameQuote('id').'='.$db->Quote($id);
			$strQuery = $db->setQuery($strSQL);
			$strRow	= $db->loadObject();
			
			if($strRow->option == 'com_seyret'){
				$strSQL	= 'SELECT '.$db->nameQuote('title')
							.' FROM '.$db->nameQuote('#__seyret_items')
							.' WHERE '.$db->nameQuote('id').'='.$db->Quote($strRow->contentid);
				$db->query( $strSQL );
				$title	= $db->loadResult();					
				$link	= JRoute::_('index.php?option=com_seyret&task=videodirectlink&id=' . $strRow->contentid) . '#comment-' . $strRow->id;
	
			}else if($strRow->option == 'com_comprofiler'){
				$strSQL	= 'SELECT '.$db->nameQuote('name')
						.' FROM '.$db->nameQuote('#__users')
						.' WHERE '.$db->nameQuote('id').'='.$db->Quote($strRow->contentid);
				$db->query($strSQL);			
				$title	= 'Comment on user ' . $db->loadResult();						
				$link	= JRoute::_('index.php?option=com_comprofiler&task=userProfile&user=' . $strRow->contentid);
			}else if($strRow->option == 'com_mmsblog'){
				require_once( JPATH_ROOT . '/components/com_mmsblog/helpers/route.php' );		
				$strSql = 'SELECT m.'.$db->nameQuote('subject').', m.'.$db->nameQuote('account').', m.'.$db->nameQuote('uid').', m.'.$db->nameQuote('category').','
				. ' CASE WHEN CHAR_LENGTH(m.'.$db->nameQuote('alias').') THEN CONCAT_WS('.$db->Quote(':').', m.'.$db->nameQuote('id').', m.'.$db->nameQuote('alias').') ELSE m.'.$db->nameQuote('id').' END as slug'
				. ' FROM '.$db->nameQuote('#__mmsblog_item').' AS m'
				. ' WHERE m.'.$db->nameQuote('id').' = '.$db->Quote((int)$strRow->contentid);
				
				$db->query($strSql);
				$mms = $db->loadObject();
	
				$title = $mms->subject;
				$link = MMSBlogHelperRoute::getItemRoute($mms->slug, $mms->account, $mms->uid, $mms->category);
			}else if($strRow->option == 'com_datsogallery'){
				$strSQL	= 'SELECT '.$db->nameQuote('imgtitle')
						.' FROM '.$db->nameQuote('#__datsogallery')
						.' WHERE '.$db->nameQuote('id').'='.$db->Quote($strRow->contentid);						
				$db->query( $strSQL );
				$title	= $db->loadResult();
				$link	= JRoute::_('index.php?option=com_datsogallery&func=detail&id=' . $strRow->contentid) . '#comment-' . $strRow->id;
			}else if($strRow->option == 'com_puarcade'){						
				$strSQL	= 'SELECT '.$db->nameQuote('title')
						.' FROM '.$db->nameQuote('#__puarcade_games')
						.' WHERE '.$db->nameQuote('id').'='.$db->Quote($strRow->contentid);						
				$db->query( $strSQL );						
				$title	= $db->loadResult();
				$link	= JRoute::_('index.php?option=com_puarcade&gid=' . $strRow->contentid) . '#comment-' . $strRow->id;
	
			}else if($strRow->option == 'com_myblog'){
				require_once( JPATH_ROOT . '/components/com_myblog/functions.myblog.php' );
				$strSQL	= 'SELECT '.$db->nameQuote('title')
						.' FROM '.$db->nameQuote('#__content')
						.' WHERE '.$db->nameQuote('id').'='.$db->Quote($strRow->contentid);
				$db->query($strSQL);
				$title	= $db->loadResult();		
				$link = myGetPermalinkUrl($strRow->contentid) . '#comment-' . $strRow->id;
			}else if($strRow->option == 'com_adsmanager'){
				$strSQL = 'SELECT '.$db->nameQuote('ad_headline')
						.' FROM '.$db->nameQuote('#__adsmanager_ads')
						.' WHERE '.$db->nameQuote('id').'='.$db->Quote($strRow->contentid);
				$db->query($strSQL);
				$title = $db->loadResult();
				$link = JRoute::_('index.php?option=com_adsmanager&page=show_ad&adid=' . $strRow->contentid) . '#comment-' . $strRow->id;
			}else if($strRow->option == 'com_comprofiler'){
				include_once( JPATH_ROOT . '/components/com_jomcomment/functions.jomcomment.php');
				$title = jcGetAuthorName($strRow->contentid);
				$link = JRoute::_('index.php?option=com_comprofiler&task=userProfile&user=' . $strRow->contentid);
			}else if($strRow->option == 'com_joomgallery'){
				$strSQL = 'SELECT '.$db->nameQuote('imgtitle')
						.' FROM '.$db->nameQuote('#__joomgallery')
						.' WHERE '.$db->nameQuote('id').'='.$db->Quote($strRow->contentid);
				$db->query( $strSQL );
				$title = $db->loadResult();
				$link = JRoute::_( 'index.php?option=com_joomgallery&func=detail&id=' . $strRow->contentid);
			}else{
				$strSQL	= 'SELECT '.$db->nameQuote('title')
						.' FROM '.$db->nameQuote('#__content')
						.' WHERE '.$db->nameQuote('id').'='.$db->Quote($strRow->contentid);
				$db->query($strSQL);
				$title	= $db->loadResult();
				include_once( JPATH_ROOT . '/components/com_jomcomment/helper/comments.helper.php');
				$link	= jcGetContentLink($strRow->contentid , $mainframe->getItemid($strRow->contentid)) . '#comment-' . $strRow->id;
			}		
			return $link;
		}
		
		function getComment($userid, $limitstart, $limit){		
			$sql  = "	SELECT
								* 
						FROM
								".$this->db->nameQuote('#__jomcomment')."
						WHERE
								".$this->db->nameQuote('user_id')." = ".$this->db->quote($userid)." AND
								".$this->db->nameQuote('published')."=".$this->db->quote(1)."
						ORDER BY
								".$this->db->nameQuote('id')." DESC
						LIMIT 
								".$limitstart.",".$limit;
			$query = $this->db->setQuery($sql);
			$row  = $this->db->loadObjectList();
			if($this->db->getErrorNum()) {
				JError::raiseError( 500, $this->db->stderr());
			}		
			return $row;
		}
		
		function countComment($userid){		
			$sql  = '	SELECT	count('.$this->db->nameQuote('id').') as total'
					.' FROM '.$this->db->nameQuote('#__jomcomment')
					.'	WHERE ' . $this->db->nameQuote('user_id').' = '.$this->db->quote($userid)
					.' AND '.$this->db->nameQuote('published').'='.$this->db->quote(1);
			$query = $this->db->setQuery($sql);
			$count  = $this->db->loadObject();
			if($this->db->getErrorNum()) {
				JError::raiseError( 500, $this->db->stderr());
			}		
			
			return $count->total;
		}
	}	
}


