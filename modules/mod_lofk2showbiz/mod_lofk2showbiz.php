<?php
/**
 * $ModDesc
 * 
 * @version   $Id: $file.php $Revision
 * @package   modules
 * @subpackage  $Subpackage.
 * @copyright Copyright (C) November 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>.All rights reserved.
 * @license   GNU General Public License version 2
 */
// no direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once dirname(__FILE__).DS.'helper.php';
$list = modK2ShowbizHelper::processFunction( $params, $module->module );

//echo "<pre>".print_r($list,1);die;

if( !empty($list) ):
	// split pages following the max items display on each page.
	$maxItemsPerRow = (int)$params->get( 'max_items_per_row', 3 );
	$maxPages = (int)$params->get( 'max_items_per_page', 5 );
	$pages = array_chunk( $list, $maxPages  );
	$totalPages = count($pages);
	// calculate width of each row.
	$itemWidth = 100/$maxItemsPerRow -0.1;
	
	$tmp = $params->get( 'module_height', '380' );
	$moduleHeight   =  ( $tmp=='auto' ) ? 'auto' : (int)$tmp.'px';
	$tmp = $params->get( 'module_width', '980' );
	$moduleWidth    =  ( $tmp=='auto') ? 'auto': (int)$tmp.'px';
	$openTarget 	= $params->get( 'open_target', '_parent' ); 
	$class 			= !$params->get( 'navigator_pos', 0 ) ? '':'lof-'.$params->get( 'navigator_pos', 0 );
	$class .= ' '. ($params->get('display_button','2')=='2'?'lof-horizontal':'lof-vertical'); 
	$mainWidth    = (int)$params->get( 'main_width', 200 );
	$mainHeight   = (int)$params->get( 'main_height', 110 );
    $thumbWidth    = (int)$params->get( 'thumbnail_width', 180 );
	$thumbHeight   = (int)$params->get( 'thumbnail_height', 60 );
	$thumbnailAlignment = $params->get( 'thumbnail_alignment', '' );	
	$displayButton	= trim($params->get( 'display_button', '2' ));
	$itemLayout 	= 'sliding-image';
	$theme		    =  $params->get( 'theme', '' ); 
	$showReadmore	= $params->get( 'show_readmore', '1' );
	$enablejQuery	= $params->get( 'enable_jquery', '1' );
	$showTitle 		= $params->get( 'show_title', '1' );
	$linkTitle 		= $params->get( 'link_title', '1' );
	$linkImage 		= $params->get( 'link_image_showbiz', '1' );
	$mousewheel     = $params->get( 'mousewheel', 'on' );
	$showDesc 		= $params->get( 'show_desc', '1' );
	$itemHits 		= $params->get( 'itemHits', '1' );
	$itemDateCreated 		= $params->get( 'itemDateCreated', '1' );
	$itemComments 		= $params->get( 'itemComments', '1' );
	$itemAuthor 		= $params->get('itemAuthor','1');
	$showImage 		= $params->get( 'show_image', 'with-link' );
	$durationSlide 		= $params->get( 'duration_slide', '300' );
	$durationCarousel 		= $params->get( 'duration_carousel', '1500' );
	$slideSpacing 		= $params->get( 'slidespacing', '30' );
	$showbiz 		= $params->get( 'style_showbiz', '0' );
    $moduleicon = $params->get('moduleicon', '');
    if( $moduleicon == 0 ){
        $iconmodule = "module-featured-icon";
    }elseif( $moduleicon == 1 ){
        $iconmodule = "module-hot-icon";
    }elseif( $moduleicon == 2 ){
        $iconmodule = "module-new-icon";
    }elseif( $moduleicon == -1 ){
        $iconmodule = '';
    }
    if( $displayButton == 0 || $displayButton == 2 ){
        $lofdisplay = "none";
    }elseif( $displayButton == 1 ){
        $lofdisplay = "block";
    }
     
	modK2ShowbizHelper::loadMediaFiles( $params, $module, $theme );
	$itemLayoutPath = modK2ShowbizHelper::getItemLayoutPath($module->module, $theme, $itemLayout  );
	$style = 'style="height:'.$params->get('item_height','auto');
	if( $params->get('hover_effect',0) ){
		$style .= ';background-color:#'.$params->get('mouseout_bg','F0F0F0');
	}
	$style .='"';
	if( !empty($theme) ){
		$layout = trim($theme).DS.'default';
		require( JModuleHelper::getLayoutPath($module->module, $layout ) );
	} else {
		require( JModuleHelper::getLayoutPath($module->module) );
	}
	
	?>
	
	
<?php endif; ?>

