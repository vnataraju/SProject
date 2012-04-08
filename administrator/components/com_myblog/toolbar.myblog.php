<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

$mainframe	=& JFactory::getApplication();

if(JVERSION >= 1.6){
	jimport( 'joomla.application.helper' );
	require_once (JApplicationHelper::getPath('toolbar'));

}else{
	$mainframe	=& JFactory::getApplication();
	require_once ($mainframe->getPath('toolbar_default'));

}

//$mainframe	=& JFactory::getApplication();
//require_once ($mainframe->getPath('toolbar_default'));

class menuMyBlog
{
	function CONFIG_MENU() {
		JToolBarHelper::title( JText::_( 'MyBlog Configuration' ), 'config.png' );
		JToolBarHelper::save('savesettings');
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	  
	}

	function FILE_MENU() {
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	function ABOUT_MENU() {
		JToolBarHelper::title( JText::_( 'About MyBlog' ), 'systeminfo.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
	
	function TAGS_MENU(){
		JToolBarHelper::title( JText::_( 'Tags Manager' ), 'categories.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
	 
	function STATS_MENU() {
		JToolBarHelper::title( JText::_( 'Statistics' ), 'generic.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
	
	function LICENSE_MENU() {
		JToolBarHelper::title( JText::_( 'License Information' ), 'systeminfo.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
	
	function MAINTD_MENU() {
		JToolBarHelper::title( JText::_( 'Maintenance' ), 'systeminfo.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
	
	function UPDATES_MENU(){
		JToolBarHelper::title( JText::_( 'Latest updates' ), 'systeminfo.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
	
	function DASHBOARD_MENU() {
		JToolBarHelper::title( JText::_( 'Write blog entry' ), 'addedit.png' );
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
	 
	 
	function MENU_Default() {
		JToolBarHelper::title( JText::_( 'MyBlog' ), 'addedit.png' );
		JToolBarHelper::publish();
		JToolBarHelper::unpublish();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
	}
	 
	function ENTRIES_MENU() {
		JToolBarHelper::title( JText::_( 'Blog Entries' ), ((JVERSION >= 1.6) ? 'article-add.png' : 'addedit.png') );
		JToolBarHelper::publish();
		JToolBarHelper::unpublish();
		JToolBarHelper::deleteList();
	}
	 
	function DRAFT_MENU() {
		JToolBarHelper::title( JText::_( 'Drafts' ), ((JVERSION >= 1.6) ? 'article-add.png' : 'addedit.png') );
		JToolBarHelper::deleteList();
	}
	
	function BOTS_MENU() {
		JToolBarHelper::title( JText::_( 'MyBlog Plugins' ), 'generic.png' );
		JToolBarHelper::publish();
		JToolBarHelper::unpublish();
		JToolBarHelper::addNewX();
	}
	 
	function MAMBOTS_MENU() {
	
		JToolBarHelper::title( JText::_( 'MyBlog Plugins' ), 'plugin.png' );
		JToolBarHelper::publish('publishMambots');
		JToolBarHelper::unpublish('unpublishMambots');
	}
	 
	function ADDBOT_MENU(){
		mosMenuBar::startTable();
		mosMenuBar::save('saveBot');
		mosMenuBar::cancel('bots');
		mosMenuBar::endTable();
	}
	 
	function INSTALL_MENU(){
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
}

switch ($task) {
	case 'latestnews':	
		menuMyBlog :: UPDATES_MENU();
		break;
		
	case 'dashboard':	
		menuMyBlog :: DASHBOARD_MENU();
		break;
		
	case "config" :
		menuMyBlog :: CONFIG_MENU();
		break;

	case "edit" :
		menuMyBlog :: FILE_MENU();
		break;
		
	case "about" :
		menuMyBlog :: ABOUT_MENU();
		break;

	case "stats" :
		menuMyBlog :: STATS_MENU();
		break;
		
	case "category":
		menuMyBlog :: TAGS_MENU();
	   break;
	
	case "draft":
		menuMyBlog :: DRAFT_MENU();
	   break; 
	
	case "blogs":
		menuMyBlog :: ENTRIES_MENU();
		break;
	
	case "bots":
		menuMyBlog :: BOTS_MENU();
		break;
	
	case "addBot":
		menuMyBlog :: ADDBOT_MENU();
		break;
	
	case "install":
		menuMyBlog :: INSTALL_MENU();
		break;

	case "contentmambots":
		menuMyBlog :: MAMBOTS_MENU();
		break;
		
	case "license":
		menuMyBlog :: LICENSE_MENU();
		break;
		
	case "maintenance":
		menuMyBlog :: MAINTD_MENU();
		break;
		
	default :
		menuMyBlog :: ENTRIES_MENU();
		break;
}
