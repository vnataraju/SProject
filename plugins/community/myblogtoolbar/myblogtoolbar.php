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

if(!class_exists('plgCommunityMyblogToolbar'))
{
	class plgCommunityMyblogToolbar extends CApplications
	{
		var $name 		= "My Blog Toolbar";
		var $_name		= 'myblogtoolbar';
		var $_path		= '';
	
	    function plgCommunityMyblogToolbar(& $subject, $config)
	    {
			$this->_path	= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_myblog';			 
			
			parent::__construct($subject, $config);
	    }
		
		function onSystemStart()
		{
		
			if( !file_exists( $this->_path . DS . 'config.myblog.php' ) )
				return;
		
			include_once (JPATH_ROOT . DS . "components" . DS . "com_myblog" . DS . "functions.myblog.php");
			
			if(! class_exists('CFactory'))
			{
				require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
			}
			
			//initialize the toolbar object	
			$toolbar	= CFactory::getToolbar();		
			
			//Load Language file.
			JPlugin::loadLanguage( 'plg_myblogtoolbar', JPATH_ADMINISTRATOR );
			
			// Attach myblog.js to this page so that the editor can load up nicely.
			$document	=& JFactory::getDocument();
			$document->addScript( JURI::base() . 'components/com_myblog/js/myblog.js' );
			$document->addStyleSheet( JURI::base() . 'components/com_myblog/css/azwindow.css' );
			$document->addStyleSheet( JURI::base() . 'plugins/community/myblog/style.css' );								
			
			$myblogItemId	= myGetItemId();
	
			//adding new 'tab' 'MyBlog' in JomSocial toolbar
			$toolbar->addGroup('MYBLOG', JText::_('PLG_MYBLOG_TOOLBAR_MYBLOG'), 'index.php?option=com_myblog&task=adminhome&Itemid='.$myblogItemId);
	
			if( myGetUserCanPost() )
			{
				$writeUrl	= 'myAzrulShowWindow(\''.JURI::root().'index.php?option=com_myblog&tmpl=component&task=write&no_html=1&id=0\')';
				$toolbar->addItem('MYBLOG', 'MYBLOG_WRITE', JText::_('PLG_MYBLOG_TOOLBAR_WRITE_BLOG'), $writeUrl, '', true);		 
			}
			
			
			$view	= JRequest::getVar('view', '', 'REQUEST');
			$my		= CFactory::getUser();

			$toolbar->addItem('MYBLOG', 'MYBLOG_VIEW', JText::_('PLG_MYBLOG_TOOLBAR_VIEW_BLOG'), 'index.php?option=com_myblog&blogger='. $my->getDisplayName() .'&Itemid='.$myblogItemId);

			$this->_user = null;
			$toolbar->addItem('MYBLOG', 'MYBLOG_ALL', JText::_('PLG_MYBLOG_TOOLBAR_VIEW_ALL_BLOG'), 'index.php?option=com_myblog&Itemid='.$myblogItemId);
			
		}	
	}	
}

