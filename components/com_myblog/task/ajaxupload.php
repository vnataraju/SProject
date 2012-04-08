<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

class MyblogAjaxuploadTask
{
	
	function display()
	{
		$this->myxAjaxUpload();
	}

	function myxAjaxUpload()
	{
		global $_MY_CONFIG;
		
		require_once( MY_LIBRARY_PATH . DS . 'imagebrowser.php' );
		
		$retVal	= array('error' => '', 'msg' => '' , 'source' => '');
		$resize	= JRequest::getVar( 'resize' , false , 'GET' );
                $fileId	= JRequest::getVar( 'fileId' , false , 'GET' );
		$fileToUpload = $fileId;
                
		//check if there are files uploaded
		if( (isset($_FILES[$fileToUpload]['error']) && $_FILES[$fileToUpload] == 0)
		|| (!empty($_FILES[$fileToUpload]['tmp_name']) && $_FILES[$fileToUpload]['tmp_name'] != 'none'))
		{
			$browser	= new MYMediaBrowser();
			$uniqName = $fileId=='mbqpPhoto'?true:false;
			$quickPost = $uniqName;
			
			$retVal		= $browser->upload($_FILES[$fileToUpload], $resize, $uniqName, $uniqName);
		}
		else
		{
			$retVal['error'] = JText::_('COM_MY_NO_FILE_UPLOADED');
		}

		// Display JSON string to the caller
		echo "{";
		echo				"error: '" . $retVal['error'] . "',\n";
                echo				"image: '" . $fileToUpload . "',\n";


		// Test if 'source' index is set
		if( isset($retVal['source']) && !empty($retVal['source']))
		{
			echo				"msg: '" . $retVal['msg'] . "',\n";
			echo 				"source: '" . $retVal['source'] . "'\n";
		}
		else
		{
			echo				"msg: '" . $retVal['msg'] . "'\n";
		}
		
		echo "}";
		exit;
	}
	
	function execute()
	{
		$this->myxAjaxUpload();
	}
}
