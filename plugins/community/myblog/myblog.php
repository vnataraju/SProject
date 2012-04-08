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

if(!class_exists('plgCommunityMyblog'))
{
	class plgCommunityMyblog extends CApplications
	{
		var $name 		= "My Blog Application";
		var $_name		= 'myblog';
		var $_path		= '';
	
	    function plgCommunityMyblog(& $subject, $config)
	    {
			$this->_path	= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_myblog'; 
			
			parent::__construct($subject, $config);
	    }
	
		function onProfileDisplay()
		{
			//Load Language file.
			JPlugin::loadLanguage( 'plg_myblog', JPATH_ADMINISTRATOR );
		
			// Get the document object
			$document	=& JFactory::getDocument();
			$my			= CFactory::getUser();
			$user		= CFactory::getRequestUser();
			
			// Attach myblog.js to this page so that the editor can load up nicely.
			$document->addScript( JURI::base() . 'components/com_myblog/js/myblog.js' );
			$document->addStyleSheet( JURI::base() . 'components/com_myblog/css/azwindow.css' );
			$document->addStyleSheet( JURI::base() . 'plugins/community/myblog/style.css' );
			
			// Test if myblog exists
			if( !file_exists( $this->_path . DS . 'config.myblog.php' ) )
			{
				$contents = "<table>
							<tr>
								<td style=\"vertical-align: top;padding:4px\">
					            <img src='".JURI::base()."components/com_community/assets/error.gif' alt='' />
					        	</td>
					        	<td style=\"vertical-align: top;padding:4px\">
								 " .JText::_('PLG_MYBLOG_NOT_INSTALLED') . "
								</td>
							</tr>
							</table>";
			}
			else
			{
				include_once (JPATH_ROOT . DS . "components" . DS . "com_myblog" . DS . "functions.myblog.php");
				
				global $_MY_CONFIG;
				$mainframe =& JFactory::getApplication();
	
				if (!class_exists("MYBLOG_Config"))
					include_once( $this->_path . DS . 'config.myblog.php' );
	
				if(!isset($_MY_CONFIG)){
					$_MY_CONFIG 	= new MYBLOG_Config();
				}
				
				if(file_exists(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_jomcomment' . DS . 'config.jomcomment.php' ) ){
					$jomcomment = 1;
				}else{
					$jomcomment = 0;
				}			
						
				$rows	= $this->_getEntries();
				if($rows){
					$data_exist = 1;
				}else{
					$data_exist = 0;
				}			
				
				$userId = $user->id;
				$userName = $user->getDisplayName();
				
				$isOwner	= ($my->id == $userId ) ? true : false;
				
				$myblogItemId = myGetItemId();
				
				$caching = $this->params->get('cache', 1);		
				if($caching)
				{
					$caching = $mainframe->getCfg('caching');
				}
				
				$cache =& JFactory::getCache('plgCommunityMyblog');
				$cache->setCaching($caching);
				$callback = array('plgCommunityMyblog', '_getMyBlogHTML');
				$contents = $cache->call($callback, $data_exist, $rows, $userId, $userName, $isOwner, $myblogItemId, $jomcomment, $this->params);
			}
			
			return $contents;
		}
	
		function _getMyBlogHTML($data_exist, $rows, $userId, $userName, $isOwner, $myblogItemId, $jomcomment, $params){
			
			ob_start();
			
			$writeNewLink = '<a class="azbutton" href="javascript:void(0);" onclick="myAzrulShowWindow(\''.JURI::root().'index.php?option=com_myblog&tmpl=component&task=write&keepThis=true&TB_iframe=true&no_html=1&id=0\');">
								<span>'.JText::_("PLG_MYBLOG_WRITE_NEW_ENTRY").'</span>
							</a>';
			
			if($data_exist){
				$count = 0;
				JPluginHelper::importPlugin('content');
				$dispatcher	=& JDispatcher::getInstance();
				
				foreach( $rows as $row ){
					$myBlogLink = JRoute::_("index.php?option=com_myblog&show=".$row->permalink."&Itemid=".$myblogItemId);
					
					$text_limit = $params->get('limit', 50);
					
					$blogContent = !empty($row->introtext)? $row->introtext : $row->fulltext;
									
					if(JString::strlen($blogContent) > $text_limit)
					{
						$introtext = strip_tags(JString::substr($blogContent, 0, $text_limit));
						if(!empty($text_limit))
						{
							$introtext .= " .....";
						}
					}
					else
					{
						$introtext = $blogContent;
					}
					
					$row->text =& $introtext;
					$result = $dispatcher->trigger('onPrepareContent', array (& $row, $params, 0));
				?>
					<div style="margin: 10px 0 20px;">				
						<div class="ctitle">
							<span class="createdate"><?php echo JHTML::_( 'date', $row->created , JText::_('DATE_FORMAT_LC'));?></span>
							<span>
								<a href ="<?php echo $myBlogLink;?>" >
									<?php echo $row->title;?>
								</a>
							</span>
							<?php if( $isOwner ): ?>
							<span>
								[&nbsp;<a href="javascript:void(0);" onclick="myAzrulShowWindow('<?php echo JURI::base(); ?>index.php?option=com_myblog&no_html=1&task=write&tmpl=component&id=<?php echo $row->id;?>');">
								<?php echo JText::_('PLG_MYBLOG_EDIT_ENTRY'); ?></a>&nbsp;]
							</span>
							<?php endif; ?>
						</div>			
						
						<div class="mb-content">
							<?php echo $introtext; ?>
						</div>
						<?php
						if($jomcomment==1){
						?>
							<div class="comments">
								<?php $comment = plgCommunityMyblog::myCountJomcomment($row->id); ?>
								<a href ="<?php echo $myBlogLink."#comments";?>" >
									<?php echo JText::sprintf('PLG_MYBLOG_COMMENTS' , $comment->counter ); ?>
								</a>
							</div>
							<div style="clear:both;"></div>
						<?php
						}
						?>
					</div>
				<?php
					$count++;
				}
				?>
				<div style="float:right">
				<?php 
				if($isOwner && myGetUserCanPost())
				{
					echo $writeNewLink;
				?>
					&nbsp;|&nbsp;
				<?php
				}
				?>
					<a href="<?php echo JRoute::_('index.php?option=com_myblog&blogger='.$userName.'&Itemid='.$myblogItemId);?>">
						<?php echo JText::_("PLG_MYBLOG_SHOW_ALL");?>
					</a>
				</div>
				<div style="clear:both"></div>
				<?php
			}else{
				?>
						<div class="icon-nopost">
				            <img src="<?php echo JURI::base(); ?>plugins/community/myblog/favicon.png" alt="" />
				        </div>
				        <div class="content-nopost">
				            <?php echo $userName . ' ' . JText::_('PLG_MYBLOG_NO_BLOG_ENTRY');?>
				        </div>
				<?php
					if($isOwner)
					{
				?>
						<div style="float:right">
							<?php echo $writeNewLink; ?>
						</div>
						<div style="clear:both;"></div>
				<?php
					}
				?>
				<?php
			}
			
			$content	= ob_get_contents();
			ob_end_clean();
			return $content;
		}
	
		function _getEntries(){
			$user	= CFactory::getRequestUser();
			
			if (!class_exists("MYBLOG_Config")){
				include_once( $this->_path . DS . 'config.myblog.php');
			}
			
			
			if(!isset($_MY_CONFIG)){
				$_MY_CONFIG 	= new MYBLOG_Config();
			}
			
			$sections       = $_MY_CONFIG->get('postSection');
			
			$db		=& JFactory::getDBO();
			
			$order_by = $this->params->get('order_by', 'ordering');
			$order = $this->params->get('order', 'DESC');
			$limit = $this->params->get('count', 5);
			
			$date	= new JDate();
			$now	= $date->toFormat();		
			
			$query	= 'SELECT a.*, p.'.$db->nameQuote('permalink').' FROM ' . $db->nameQuote('#__content') . ' AS a, ' 
					. $db->nameQuote('#__myblog_permalinks') . ' AS p '
					. ' WHERE a.'.$db->nameQuote('state').'=' . $db->Quote('1')
					. ' AND a.'.$db->nameQuote('publish_up').' <= ' . $db->Quote( $now ) 
					. ' AND a.'.$db->nameQuote('sectionid').' = ' . $db->Quote($sections)
					. ' AND a.'.$db->nameQuote('created_by').'=' . $db->Quote($user->id)
					. ' AND a.'.$db->nameQuote('id').'=p.'.$db->nameQuote('contentid')
					. ' ORDER BY a.' . $db->nameQuote($order_by) . ' ' . $order
					. ' LIMIT 0,'.$limit ;
			
			$db->setQuery( $query );
			$rows	= $db->loadObjectList();
	
			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
		    }
	
			return $rows;
		}
		
		function myCountJomcomment($article_Id, $com = "com_myblog") {
			$db		=& JFactory::getDBO();
			$db->setQuery('SELECT COUNT(*) as '.$db->nameQuote('counter')
						.' FROM '.$db->nameQuote('#__jomcomment')
						.' WHERE '.$db->nameQuote('contentid').'='.$db->Quote($article_Id)
						.' AND ('.$db->nameQuote('option').'='.$db->Quote($com)
						.' OR '.$db->nameQuote('option').'='.$db->Quote('com_content').') AND '.$db->nameQuote('published').'='.$db->Quote('1'));
			$result = $db->loadObject();
			
			return $result;
		}
	}	
}

