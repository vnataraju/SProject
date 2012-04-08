<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

function myCheckOldJomComment()
{
	$path	= JPATH_ROOT . DS . 'components' . DS . 'com_jomcomment' . DS . 'cms' . DS . 'spframework.php';
	
	if( JFile::exists( $path ) )
		return true;

	return false;
}

/**
 * If there is a component that previosuly link to MyBlog, we need to update 
 * its component id  [MOVED TO INSTALLER]
 */ 
function myFixMenuLinks()
{
	$db		=& JFactory::getDBO();
	
	$query	= "SELECT `id` FROM #__components WHERE `name`='My Blog'";
	
	$db->setQuery( $query );
	
	$comid	= $db->loadResult();
	$query = "UPDATE #__menu SET `componentid`='$comid' WHERE `link` LIKE 'index.php?option=com_myblog%'";
	$db->setQuery($query);
	$db->query();
}

/**
 * Fix link to the dashboard in the user menu. [COPIED TO INSTALLER]
 */		 		
function myFixDashboardLinks()
{
	$db		=& JFactory::getDBO();
	
	$query	= (JVERSION >= 1.6) 
		? "SELECT `extension_id` FROM #__extensions WHERE `element`='com_myblog'"
		: "SELECT `id` FROM #__components WHERE `name`='My Blog'";
	$db->setQuery( $query );
	
	$comid	= $db->loadResult();
	
	$query	= (JVERSION >= 1.6)
		? "SELECT count(*) FROM #__menu
                WHERE
                        `menutype`='mainmenu'
                        AND `link` LIKE '%index.php?option=com_myblog%'
                        AND `type`='component'
                        AND `component_id`='$comid' "
		: "SELECT count(*) FROM #__menu 
		WHERE 
			`menutype`='usermenu' 
			AND `link` LIKE '%index.php?option=com_myblog%' 
			AND `type`='component' 
			AND `componentid`='$comid' ";
	
	$db->setQuery( $query );
	
	// Make sure it is there
	$linkExist = $db->loadResult();
	
	// If no correct one is found, delete all myblog links in usermenu
	// and create a new one
	if(!$linkExist)
	{
		$query = (JVERSION >= 1.6) 
			? "DELETE FROM #__menu
                                WHERE
                                `menutype`='mainmenu'
                                AND `link` LIKE '%index.php?option=com_myblog%'" 
			: "DELETE FROM #__menu 
				WHERE
				`menutype`='usermenu' 
				AND `link` LIKE '%index.php?option=com_myblog%'";
		$db->setQuery($query);
		$db->query();
		
		// Only Joomla 1.5 has menu alias
		$menuAlias  = " `alias`='myblog-admin', ";
		$type       = "component";

		$query = (JVERSION >= 1.6) 
			? "INSERT INTO `#__menu`
                                SET
                                `title`= 'My Blog Dashboard',
                                {$menuAlias}
				`path`='myblog-admin',
                                `menutype`='mainmenu',
                                `link`='index.php?option=com_myblog&task=adminhome',
                                `type`='{$type}',
				`level`= 1,
                                `access`='1',
                                `published`='1',
                                `component_id`='$comid'"
			: "INSERT INTO `#__menu`
				SET 
				`name`= 'My Blog Dashboard',
				{$menuAlias}
				`menutype`='usermenu',
				`link`='index.php?option=com_myblog&task=adminhome',
				`type`='{$type}',
				`access`='1',
				`published`='1',
				`componentid`='$comid'";
		
		
		$db->setQuery($query);
		$db->query();
	}
}

/** 
 * Fix myblog related tables [MOVED TO INSTALLER]
 */ 
function myFixDBTables()
{
	$db		=& JFactory::getDBO();
	
	$query	= 'SHOW FIELDS FROM `#__myblog_categories`';
	
	// Make sure the tags are unique
	$db->setQuery( $query );
	$fields = $db->loadObjectList();

	// Add missing fields in the table
	if(empty($fields[1]->Key))
	{
		$db->setQuery("ALTER TABLE `#__myblog_categories` ADD UNIQUE (`name`)");
		$db->query();
	}
}

/**
 * Extract the bundled file into
 */ 
function myExtractFiles()
{
	// unpack all front-end files
    echo '<strong>Installing MyBlog components</strong><br/>';
    echo '<img src="images/tick.png"> Installing front-end components<br/>';
    JArchive::extract(SITE_ROOT_PATH . "/administrator/components/com_myblog/com.zip", 
		SITE_ROOT_PATH . "/components/com_myblog/");

	// Unpack backend files
    echo '<img src="images/tick.png"> Installing back-end components<br/><br/>';
    JArchive::extract(SITE_ROOT_PATH . "/components/com_myblog/admin.zip", 
		SITE_ROOT_PATH . "/administrator/components/com_myblog/");
}

/**
 * Extract zip files
 */ 
function myExtractArchive($src, $destDir)
{
	$destDir =  JPath::clean($destDir);
	$src 	=  JPath::clean($src);
		
	JArchive::extract($src, $destDir);
	
	return true;
}

function myMkdir($destDir)
{
	return JFolder::create($destDir);
}

function myRmdir($destDir)
{
	$destDir =  JPath::clean($destDir);
	return JFolder::delete($destDir);
}

function myDeleteDir($src)
{
	return JFolder::delete($src);	
}

function myUnlink($src)
{
	return JFile::delete($src);
}

