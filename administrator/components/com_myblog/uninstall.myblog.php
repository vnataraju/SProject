<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

function com_uninstall()
{
	# Empty the component folder
	return JFolder::delete( JPATH_ROOT . DS . 'components' . DS . 'com_myblog' );

}