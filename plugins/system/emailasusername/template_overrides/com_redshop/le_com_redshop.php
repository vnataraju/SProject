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

// now we can inherit the class in lunarExtention.php, and make it specific to com_redshop
// there wont be much work customising it.

class le_com_redshop extends lunarExtention {
	
	function __construct( $name, & $parentObject ) {
		parent::__construct( $name, $parentObject );
	}
	
	// this function will process the input from pages affected by the template
	// overrides. in most cases this will mean generating a username from the posted
	// information, and adding the generated username to the post variable, for processing
	// by the target extension.
	
	function processInput() {
		// get the pagedata so we have a fresh copy.
		parent::processInput();
		
		if(@$this->pageData->task=="registration.register" || @$this->pageData->task=="checkoutprocess" ) {
			//| (@$this->pagedata->task=="save" && @$this->pagedata->view=="user")) {
			
			$this->log("com_user:Normal registration ");

			// need to generate a username
			$username = $this->genUserName($this->pageData->firstname . $this->pageData->lastname);
			
			// now set the value in the post variable
			JRequest::set(array("username" => $username),"post");
			
			return;
		}
	}
	
	
	function processOutput() {
		/* cant do html editing in process input, because the component output hasnt eben rendered at that stage */
		
		if(@$this->pageData->view=="checkout") {
			// going to need the redshop language file
			$lang =& JFactory::getLanguage();
			$lang->load('com_redshop', JPATH_SITE, $lang->getTag());
			
			$html1 = '<tr><td width="100" align="right">'.JText::_('COM_REDSHOP_USERNAME' ).':</td>';
			$html2 ='<td><input class="inputbox required" type="text" name="username" id="username" size="32" maxlength="250" value="' . @$this->pagedata->username . '" /></td>';
			$html3 ='<td><span class="required">*</span></td></tr>';
			
				
			$doc= & JFactory::getDocument();
			$buffer=$doc->getBuffer("component");
			
			//$buffer=str_replace($usernamefield,"",$buffer);
			$buffer=str_replace($html1,"",$buffer);
			$buffer=str_replace($html2,"",$buffer);
			$buffer=str_replace($html3,"",$buffer);
			$doc->setBuffer($buffer, "component");
		}
	}
	
	/* need this here, not calling the parent otherwise a jfile_lib error results (non fatal but looks messy) */
	function hideUsername() {
		// get the extension version
		return true;
	}
	
	
	
}
?>