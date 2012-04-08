<?php

defined('_JEXEC') or die('Restricted access');

$assets = rtrim( JURI::root() , '/' ) . '/administrator/components/com_myblog/assets/';
$document =& JFactory::getDocument();
$document->addStyleSheet($assets.'css/admin_style.css');
$document->addStyleSheet($assets.'css/myblog.css');
$document->addStyleSheet($assets.'css/tabs.css');

$document->addStyleSheet(MY_COM_LIVE.'/css/azwindow.css');
$document->addStyleSheet(MY_COM_LIVE.'/css/style.css');
$document->addStyleSheet(MY_COM_LIVE.'/css/ui.css');

$document->addScript($assets.'js/jquery.min.js');
$document->addScript($assets.'js/jquery.tabs.pack.js');
$document->addScript($assets.'js/myblog.js');

$document->addScript( MY_COM_LIVE. '/js/myblog.js' );
$document->addScript($assets.'js/jquery.cookie.js');

require_once('sidepanel.php');
require_once('managepost.php');

?>
