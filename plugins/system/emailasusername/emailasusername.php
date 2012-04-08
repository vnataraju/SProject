<?php
/**
* @version		$Id: emailasusername.php 2012-03-15 (version 3.67) $
* @package		Joomla 1.6, 1.7 & 2.5 Native version
* @copyright	Copyright (C) 2011 LunarHotel.co.uk. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
/*
This version:
Update for JomSocial 2.4.x

Fix for possible failure on ' in name

Updated support for HikaShop 1.5.6

Added support for K2 

Updated support for RedShop 1.9 with Joomla 1.7

Added support for Seblod CCK & support for Core Design Login Module

Fixed bug when changing email address in profile field.

Updated support for JMExperts Login Register

Updated com_users support for profile fields

Updated JomSocial support to allow for firstname, lastname field definitions

Fixed bug in getPreferredLanguage function line 173.
Updated JMExperts LoginRegister Support
Added option to redirect to JomSocial profile page if the Joomla profile page is ever shown
fixed certain mentions of usernames in front end
fixed plugin disable detection in plugin parameters

JomSocial 2.2 Facebook connect support
JomSocial 2.2 RC4 support (may support later versions also)
Fixed invalid token error on Joomla 1.6.1 on logout
Support for HikaShop
*/



defined('JPATH_BASE') or die;

define( "OVERRIDE_PATH" 	, 	"template_overrides" );
define( "LE_CLASS_PREFIX"	,	"le");
define( "JPATH_PLUGIN_ROOT" ,   JPATH_ROOT . DS . "plugins" . DS . "system" . DS . "emailasusername");


jimport( 'joomla.filesystem.file' );
jimport( 'joomla.plugin.plugin' );

require ( dirname(__FILE__) . DS . "apollo.php" );
/**
 * Plugin class for Removing Username from supported Joomla 1.6 Components.
 */
  
 /*
	This is a beta release which supports Joomla's inbuilt registration & login only.
	support for more extensions will be included as extension authors build in Joomla! 1.6
	compatibility
 */
 
 class plgSystemEmailAsUsername extends apolloPlugin {
	
	function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );
		
		// we dont need to check if we're in admin mode, because eventDisable cant be
		// set unless we are.
		//die(print_r(JRequest::get("request")));
		if( $this->eventDisable ) {
			// the plugin has been disabled / uninstalled.
			// do the clean up and set everything back to the way it was
			// restore will also remove the backup table, so that the plugin will
			// correctly assume it needs creating when the plugin is next enabled
			$this->log("Plugin has been disabled, restoring backups");
			$this->restoreBackup();
			

		} else {
			// check for the first time run
			if( $this->firstRun() ) {
				$this->log("First run detected");
				// do the preflight checks
				// create the backup table
				$this->createBackupTable();
				// call the respective extention classes
				$this->hideUserNames();
			}
		}
	}
	
	// function hides the username field in supported extentions.
	function hideUserNames() {
		// first we need to get a list of supported extentions. we can get that from the
		// template override store.
		
		
		$extLocation = dirname(__FILE__) . DS . OVERRIDE_PATH;
		
		$extentions=$this->getFolderList( $extLocation );
		
		foreach ($extentions as $extention ) {
			// first, check the extension is actually installed
			if ( $this->extentionInstalled( $extention ) ) {
				
				// it is, see if we can locate an extension class for it.
				$extClassFile=$extLocation . DS . $extention . 
					DS .	LE_CLASS_PREFIX . "_" . $extention . ".php";

				// so whats going on here?
				// well, not ALL supported extensions (specifically modules) need any custom code
				// for overriding the output. So, this is kind of a class override system, where if
				// an extention class for the current extension exists, it is used, otherwise, we just
				// use the default one
				// this will cut down on the messing around that needs to be done in order to get new
				// extensions supported
				$this->log("checking for existence of [". $extClassFile  . "]");
				if( JFile::exists( $extClassFile ) ) {
					// it does exist, so go ahead and include it
					include_once ($extClassFile);
					// now create the class and let it do the rest
					$extClassName = LE_CLASS_PREFIX . "_" . $extention;
					// create the new class, tell it what extention its for (more to minimise editing of
					// the extention class, than anything else. Also pass it a reference to this object
					// so it can use the logging features, and anything else we might choose
					
				} else {
					// otherwise just include the lunarExtention class and use that.
					$this->log("couldnt find [". $extClassFile  . "] so instigating parent class");
					include_once ( dirname(__FILE__) . DS . "lunarExtention.php" );
					$extClassName = "lunarExtention";
				}
				
				$extClass= new $extClassName ($extention, $this);

				// a bit inefficient, but will only be run when the plugin is enabled
				// and not everytime a page loads.
				if($this->parameters->backupTemplateOverrides) {
					$extClass->backupTemplateOverrides();
				}
				$this->log("Running hideUsername()");
				$extClass->hideUsername();
					
				// now kill it
				unset( $extClass );	
			} else {
				$this->log("Extension [" . $extention . "] is not installed");
			}
		}
	}
	
	
	function extentionInstalled($extentionName) {
		$exttype=substr($extentionName,0,3);

		$this->log("Extention type is [". $exttype . "]");

		$q="SELECT extension_id FROM #__extensions WHERE `element`='". $extentionName . "';";
		
		$db = &JFactory::getDBO();
		$db->setQuery( $q );
		return $db->loadResult();
	}
	
	
	// returns true if it appears its the plugins first time running (e.g. if its just been
	// enabled)
	function firstRun() {
		$this->log("detecting firstRun");
		$q="insert into #__eau_backups values(NULL,'A','B','" . date("Y-m-d H:i:s") . "',1);";
		// need to use this method for query because we need insertid later
		$db = &JFactory::getDBO();
		$db->setQuery( $q );
		
		if( !$db->query() ) {
			return true;
		} else {
			// delete the record if it worked
			$this->log("firstrun cleaning up..");
			$q="delete from #__eau_backups where id=" . $db->insertid();
			$this->execQuery( $q );
		}
		return false;
	}
	
	
	// go through the backup table and restore anything that needs restoring, delete anything that needs
	// deleting
	function restoreBackup() {
		
		$this->log("Restoring files from the backup table");
		$db = &JFactory::getDBO();
		// get everything
		$q="select * from #__eau_backups";
		$db->setQuery( $q );
		$results=$db->loadObjectList();
		foreach($results as $result) {
			// first check what we need to do
			if(!$result->justdelete) {
				// write the file
				// decode the escaped / slashed content first
				$content = rawurldecode($result->contents);
				file_put_contents( rawurldecode($result->filename), $content);
			} else {
				// just get rid of the one thats there
				JFile::delete($result->filename);
			}
		}
		
		$this->log("Removing the backup table");
		$q="drop table #__eau_backups;";
		$this->execQuery($q);
	}
	
	// returns an array of folders contained in the given $dir
	function getFolderList( $dir ) {
		$dir = dir($dir);
		if($dir) {
			while (false !== $entry = $dir->read()) {
				// Skip pointers
				if (!($entry == '.' || $entry == '..')) {
					$extentions[]=$entry;
				}
			}
		}
		return $extentions;
	}
	
	
	// create the backup table - table is processed and removed by restoreBackup
	function createBackupTable() {
		$q=" CREATE TABLE IF NOT EXISTS `#__eau_backups` (" .
				"id bigint(20) NOT NULL auto_increment," .
				"filename tinytext NOT NULL," .
				"contents MEDIUMBLOB," .
				"backupdate datetime NOT NULL," .
				"justdelete tinyint(4) NOT NULL default '0'," .
				"PRIMARY KEY  (`id`)" .
				") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		
		if($this->execQuery( $q )) {
			$this->log("File backup table created successully");
		} else {
			$this->log("WARNING:File backup table creation FAILED",true);
		}
	}
	
	
	function onAfterRoute() {
		// dont run if in admin mode, there's no point
		if(!$this->inadmin) {
			// figure out which component we are dealing with
			$this->pageData = $this->getPageData();
			
			$this->log("In onAfterRoute");
			
			$this->log("Page data is [" . print_r($this->pageData,true) . "]");
			
			// the jomsocial redirect
			$this->log("Checking for JomSocial Profile page redirect");
			$this->log("Pagedata is [" . print_r($this->pageData,true) . "]");
			@$this->log("jsprofileredirect is [" . $this->parameters->jsprofileredirect . "]");
			if(!$this->inadmin && @$this->pageData->option=="com_users" && @$this->pageData->view=="profile"
				&& $this->parameters->jsprofileredirect) {
				// check to see if jomsocial is installed, if so, redirect the user to the jomsocial profile
				// page instead of the Joomla one.
				$this->log("Destination is Joomla profile page, checking JomSocial is installed");
				if( $this->extentionInstalled("com_community")) {
					$this->log("JomSocial is indeed confirmed, redirecting to JomSocial profile page");
					$app =& JFactory::getApplication();
					$app->redirect("/index.php?option=com_community&view=frontpage&Itemid=" . 
						$this->parameters->jsItemid);
				}
			}
			
			
			$extensionClassFile="";
			
			
			// if jomsocial (com_community) makes an ajax call, it uses community as the option (for some reason)
			// so this just makes sure that all option command include com_
			if( strpos($this->pageData->option,"com_")===false ) {
				$this->pageData->option="com_" . $this->pageData->option;
			}
			
			if(@$this->pageData->option) {
				$extensionClassFile = JPATH_PLUGIN_ROOT . DS . OVERRIDE_PATH . DS . 	
					$this->pageData->option . DS . LE_CLASS_PREFIX . "_" . $this->pageData->option . ".php";
			}
			
			$this->log("Checking for extension class [" . $extensionClassFile . "]");
			
			if(JFile::exists( $extensionClassFile )) {
				// we have an extension class for the current component, so lets load 'er up!
				$this->log("including extension class");
				include_once ( $extensionClassFile );
				// now lets create the object
				$this->log("Creating object");
				$extensionClassName = LE_CLASS_PREFIX . "_" . $this->pageData->option;
				$extensionClass= new $extensionClassName ($this->pageData->option, $this);
				// now call the input handler, which will decide if any action needs taking, and if so, 
				// take it
				$this->log("Calling processInput");
				$extensionClass->processInput();
			}
		}
	}
	
	function onAfterDispatch() {
		if(!$this->inadmin) {
			// figure out which component we are dealing with
			$this->pageData = $this->getPageData();
			$extensionClassFile = JPATH_PLUGIN_ROOT . DS . OVERRIDE_PATH . DS . 	
				$this->pageData->option . DS . LE_CLASS_PREFIX . "_" . $this->pageData->option . ".php";

			if(JFile::exists( $extensionClassFile )) {
				// we have an extension class for the current component, so lets load 'er up!
				$this->log("including extension class in AfterDispatch");
				include_once ( $extensionClassFile );
				// now lets create the object
				$this->log("Creating object");
				$extensionClassName = LE_CLASS_PREFIX . "_" . $this->pageData->option;
				$extensionClass= new $extensionClassName ($this->pageData->option, $this);
				
				// now call the input handler, which will decide if any action needs taking, and if so, 
				// take it
				$this->log("Calling processInput");
				$extensionClass->processInput();
			}
		}
	}
	
	/*function onAfterRender() {
		$this->pageData = $this->getPageData();
		$this->log("in AfterDispatch ");
		
		if( $this->pageData->action=="register.terms.getTerms" ) {
			die("here");
		}
		
		$doc= & JFactory::getDocument();
		$buffer=$doc->getBuffer("component");
			
		$html1="Login Username";
		$buffer=str_replace($html1,"Argh!",$buffer);
		$doc->setBuffer($buffer, "component");
	}*/
	
	
	
	
	
	
 }
?>