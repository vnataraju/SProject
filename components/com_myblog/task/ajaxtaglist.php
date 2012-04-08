<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

$my		=& JFactory::getUser();
$db		=& JFactory::getDBO();

if( $my->id == 0 )
{
	$secret	= JRequest::getVar( 'secret' , '' , 'GET' );
	if($secret){
		$tmpuser = myGetUser($secret);
		if($tmpuser->id == 0){
			echo 'Not authenticated';
			return;
		}
	}else{
		echo 'Not authenticated';
		return;
	}
}

$search	= JRequest::getVar( 'q' , '' , 'GET' );
$search	= strtolower($search);

if(!$search)
	return;

$query	= 'SELECT name FROM #__myblog_categories';
$db->setQuery( $query );

$tags	= $db->loadObjectList();

if( !$tags )
	return;

foreach ($tags as $key)
{
	if (strpos(strtolower($key->name), $search) !== false)
	{
		echo $key->name . "|\n";
	}
}