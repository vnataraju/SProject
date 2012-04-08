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

class le_com_hikashop extends lunarExtention {
	
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
		
		if(@$this->pageData->task=="register" && @$this->pageData->data) {
			$username = $this->genUserName( $this->pageData->data['register']['name'] );
			$this->log("com_user:setting username to " . $username);

			$allpost=JRequest::get("post");
			$allpost['data']['register']['username'] = $username;
			// now set the post variable
			JRequest::set( $allpost ,"post");
		}
		
		$this->log("processInput for com_hikashop complete");
	}
	
}
?>