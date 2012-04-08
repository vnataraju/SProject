<?php
/**
* @version		$Id: LunarHotel EmailAsUsername Extention class instance (com_users) $
* @package		Joomla 1.6 Native version
* @copyright	Copyright (C) 2011 LunarHotel.co.uk. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

require_once( JPATH_ROOT . DS . "plugins" . DS . "system" . 
	DS . "emailasusername" . DS . "lunarExtention.php" );

// now we can inherit the class in lunarExtention.php, and make it specific to com_users
// there wont be much work customising it.

class le_com_virtuemart extends lunarExtention {
	
	function __construct( $name, & $parentObject ) {
		parent::__construct( $name, $parentObject );
	}
	
	/*function hideUsername() {
		// get the extension version
		parent::hideUsername();
	}*/
	
	// this function will process the input from pages affected by the template
	// overrides. in most cases this will mean generating a username from the posted
	// information, and adding the generated username to the post variable, for processing
	// by the target extension.
	
	function processInput() {
		// get the pagedata so we have a fresh copy.
		parent::processInput();
		
		if(@$this->pageData->task=="registercartuser" ) {
			//| (@$this->pagedata->task=="save" && @$this->pagedata->view=="user")) {
			
			$this->log("com_virtuemart:Editing the billing / shipping details / registration");
			//die(print_r(JRequest::get("post")));
			// need to generate a username, hopefully at least one of these will give us a seed
			if(!$usernameseed=$this->pageData->name) {
				if(!$usernameseed=$this->pageData->company) {
					$usernameseed=$this->pageData->email;
				}
			}
			
			$username = $this->genUserName($usernameseed);
			$this->log("com_virtuemart:setting username to " . $username);

			// now set the value in the post variable
			JRequest::set(array("username" => $username),"post");
			
			return;
		}
		
		if(@$this->pageData->task=="reset.confirm") {
			// user is in the process of resetting thier password
			// we changed the second stage of the reset process so it asks for email address and token
			// so we need to translate the email address into a username, and send it on its way
			$this->log("com_user:password reset second stage (dont have to get involved in the first stage)");
			// get jform array from the post var
			$jform = $this->pageData->jform;
			// add the username field
			$jform['username'] = $this->getUsername ( $this->pageData->jform['email'] ) ;
			
			$this->addJformPost( $jform );
			
			return;
			
		}
		
		if(@$this->pageData->task=="profile.save") {
			// user is in the process of updating thier profile information
						
			$this->log("com_user:Profile update");
			// get jform array from the post var
			$jform = $this->pageData->jform;
			// add the username field
			$userinfo= & JFactory::getUser();
			$jform['username'] = $this->getUsername ( $userinfo->email ) ;

			$this->addJformPost( $jform );
			
			return;
			
		}
		$this->log("processInput for com_users complete");
	}
	
}
?>