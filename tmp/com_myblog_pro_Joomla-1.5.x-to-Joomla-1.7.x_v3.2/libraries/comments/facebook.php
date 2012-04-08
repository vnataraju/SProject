<?php
/**
 * MyBlog
 * @package MyBlog
 * @copyright (C) 2006 - 2008 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class MYComments_Facebook{


	function __construct(){
		global $_MY_CONFIG;
		$mconfig = $_MY_CONFIG; //MYBLOG_Factory::getConfig();
	}

	function getHTML($permalink=''){
		global $_MY_CONFIG;

		$doc =& JFactory::getDocument();
		$doc->addCustomTag('<meta property="fb:app_id" content="'.$_MY_CONFIG->get('fbAppID').'"/>');
		$doc->addCustomTag('<meta property="fb:admins" content="'.$_MY_CONFIG->get('fbUserID').'"/>');

		$html = $this->getCount($permalink, true).'<br /><fb:comments href="'.$permalink.'" width="500" num_posts="20"></fb:comments>';
		return $html;
	}


	function getCount($permalink='',$fullPermalink=''){
		$doc =& JFactory::getDocument();
		$doc->addScript("http://connect.facebook.net/en_US/all.js#xfbml=1");
		if($fullPermalink==''){
		    $permalink = JUri::base().substr($permalink,1);
		}
		$countComment = '<fb:comments-count href="'.$permalink.'"></fb:comments-count>';
		$preWrap = '<span class="mbCommentCount">';
		$postWrap = '</span>';
		
		return $countComment=='0' || $countComment=='1' ? $preWrap.JText::sprintf('COM_MY_COMMENT', $countComment).$postWrap : $preWrap.JText::sprintf('COM_MY_COMMENTS', $countComment).$postWrap;
	}
}