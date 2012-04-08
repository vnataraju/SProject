<?php
/*------------------------------------------------------------------------
# Joomla Carousel Module by JoomShaper.com
# ------------------------------------------------------------------------
# author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomshaper.com
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.framework', true);
// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$ctype				=$params->get( 'ctype',1 );
$width				= $params->get( 'width',85 );
$height				= $params->get( 'height',60 );
$tamount			= $params->get( 'tamount',6 );
$size				= $width+20;
$mainwidth			= $tamount*$size;
$tmwidth			= $mainwidth+60;
$mainheight			= $height+20;
$amount				= $params->get( 'amount',4 );

if($ctype==0) { 
	$list 			= modCarouselHelper::getList($params);
} else {
	$list 			= modCarouselHelper::getImageList($params);
}

$document = JFactory::getDocument();
$document->addScript(JURI::base(true) . '/modules/mod_shaper_carousel/assets/icarousel.js');
$document->addStylesheet(JURI::base(true) . '/modules/mod_shaper_carousel/assets/style.css');
require(JModuleHelper::getLayoutPath('mod_shaper_carousel'));
?>
