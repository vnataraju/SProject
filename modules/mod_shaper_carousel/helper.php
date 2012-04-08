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
require_once JPATH_SITE.'/components/com_content/helpers/route.php';

jimport('joomla.application.component.model');

JModel::addIncludePath(JPATH_SITE.'/components/com_content/models');

abstract class modCarouselHelper
{		
	public static function getList($params)
	{
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();

		// Get an instance of the generic articles model
		$model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set application parameters in model
		$appParams = JFactory::getApplication()->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		
		if ($params->get('count', 5) !=0) {
			$model->setState('list.limit', (int) $params->get('count', 5));
		}
		
		$model->setState('filter.published', 1);

		// User filter
		$userId = JFactory::getUser()->get('id');
		switch ($params->get('user_id'))
		{
			case 'by_me':
				$model->setState('filter.author_id', (int) $userId);
				break;
			case 'not_me':
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
				break;

			case '0':
				break;

			default:
				$model->setState('filter.author_id', (int) $params->get('user_id'));
				break;
		}		

		//  Featured switch
		switch ($params->get('show_featured'))
		{
			case '1':
				$model->setState('filter.featured', 'only');
				break;
			case '0':
				$model->setState('filter.featured', 'hide');
				break;
			default:
				$model->setState('filter.featured', 'show');
				break;
		}
		
		$model->setState('list.select', 'a.fulltext, a.id, a.title, a.alias, a.title_alias, a.introtext, a.state, a.catid, a.created, a.created_by, a.created_by_alias,a.images,' .
			' a.modified, a.modified_by,a.publish_up, a.publish_down, a.attribs, a.metadata, a.metakey, a.metadesc, a.access,' .
			' a.hits, a.featured,' .
			' LENGTH(a.fulltext) AS readmore');
			
		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));

		// Filter by language
		$model->setState('filter.language',$app->getLanguageFilter());

		// Set ordering
		$model->setState('list.ordering', $params->get('ordering', 'a.ordering'));
		$model->setState('list.direction', $params->get('ordering_direction', 'ASC'));

		//	Retrieve Content
		$items = $model->getItems();

		foreach ($items as &$item) {
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid.':'.$item->category_alias;

			$item->image 		= modCarouselHelper::getImage($item->introtext, $item->images);			
			$item->link 		= JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));			
		}

		return $items;
	}
	
	function getImage($text, $image_src="") {
		$image_src = json_decode($image_src);		
		if (JVERSION>=2.5 && @$image_src->image_intro) {
			return $image_src->image_intro;
		} elseif (JVERSION>=2.5 && @$image_src->image_fulltext) {
			return $image_src->image_fulltext;
		} else {
			preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $text, $matches);
			if (!isset($matches[1])) { 
				$matches[1]='modules/mod_freeslider_sp1/assets/images/no-image.jpg';
			}

			if (!file_exists($matches[1])) {
				$matches[1]='modules/mod_freeslider_sp1/assets/images/no-image.jpg';
			}	
			 
			return $matches[1];
		}
	}
	
	function getImageList($params) {
		jimport( 'joomla.filesystem.folder' );
		$filter = '\.png$|\.gif$|\.jpg$|\.jpeg$|\.bmp$';
		$path		= $params->get('path');
		$files 		= JFolder::files(JPATH_BASE.$path,$filter);
		
		$i=0;
		$lists = array();
		
		foreach ($files as $file) {
			$lists[$i]->image	= JURI::base().str_replace(DS,'/',substr($path,1)).'/'.$file;
			$lists[$i]->link	= 'javascript:;';
			$i++;
		}
		return $lists; 
	}	
}
 