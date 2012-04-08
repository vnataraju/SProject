<?php
/*------------------------------------------------------------------------
# mod_featcats - Featured Categories
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomla.vargas.co.cr
# Technical Support:  Forum - http://joomla.vargas.co.cr/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

$com_path = JPATH_SITE.'/components/com_content/';
require_once $com_path.'router.php';
require_once $com_path.'helpers/route.php';

jimport('joomla.application.component.model');

JModel::addIncludePath($com_path.DS.'models', 'ContentModel');

abstract class modFeatcatsHelper
{
	public static function getList(&$params)
	{
		$count = (int) $params->get('count', 3);
		$subcount = (int) $params->get('subcount', 0);
		$colcount = (int) $params->get('colcount', 3);
		
		$categories = JCategories::getInstance('Content');
		$catids = $params->get('catid');
		$groups = array();
		
		$col = 0;
		if ( $catids ) :
			foreach($catids as $catid)
			{	
				$col++;
			
				$groups[$catid] = new stdClass;
				$groups[$catid]->col_class = 'featcat col'.$col;
				if ( $col == 1  ) :
  					$groups[$catid]->col_class .= ' firstcol';
				elseif ( $col == $colcount  ) :
  					$groups[$catid]->col_class .= ' lastcol';
        		elseif ( $col == $colcount + 1 ) :
  					$groups[$catid]->col_class .= ' featcat_clr';
  					$col = 0;
  				endif;
			
				$category = $categories->get($catid);
				$groups[$catid]->category_title = $category->title;
				$groups[$catid]->category_link  = JRoute::_(ContentHelperRoute::getCategoryRoute($catid));
				$groups[$catid]->articles = array();		
				
				$articles = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
		
				$app = JFactory::getApplication();
				$appParams = $app->getParams();
				$articles->setState('params', $appParams);
		
				$articles->setState('list.start', 0);
				$articles->setState('list.limit', $count + $subcount);
				$articles->setState('filter.published', 1);
		
				$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
				$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
				$articles->setState('filter.access', $access);
		
				$articles->setState('filter.category_id.include', 1);
		
				if ($params->get('show_child_category_articles', 0) && (int) $params->get('levels', 0) > 0) {
					$subcategories = JModel::getInstance('Categories', 'ContentModel', array('ignore_request' => true));
					$subcategories->setState('params', $appParams);
					$levels = $params->get('levels', 1) ? $params->get('levels', 1) : 9999;
					$subcategories->setState('filter.get_children', $levels);
					$subcategories->setState('filter.published', 1);
					$subcategories->setState('filter.access', $access);
					$include_subcategories = array();
					$include_subcategories[] = $catid;	
					
					$subcategories->setState('filter.parentId', $catid);
					$recursive = true;
					$items = $subcategories->getItems($recursive);
	
					if ($items)
					{
						foreach($items as $subcategory)
						{
							$condition = (($subcategory->level - $subcategory->getParent()->level) <= $levels);
							if ($condition) {
								$include_subcategories[] = $subcategory->id;
							}
	
						}
					}	
					$articles->setState('filter.category_id', $include_subcategories);				
				} else {
					$articles->setState('filter.category_id', $catid);
				}
		
				$articles->setState('list.ordering', $params->get('article_ordering', 'a.ordering'));
				$articles->setState('list.direction', $params->get('article_ordering_direction', 'ASC'));
		
				$articles->setState('filter.featured', $params->get('show_front', 'show'));
				$articles->setState('filter.author_id', $params->get('created_by', ""));
				$articles->setState('filter.author_id.include', $params->get('author_filtering_type', 1));
				$articles->setState('filter.author_alias', $params->get('created_by_alias', ""));
				$articles->setState('filter.author_alias.include', $params->get('author_alias_filtering_type', 1));
				$excluded_articles = $params->get('excluded_articles', '');
		
				if ($excluded_articles) {
					$excluded_articles = explode("\r\n", $excluded_articles);
					$articles->setState('filter.article_id', $excluded_articles);
					$articles->setState('filter.article_id.include', false); // Exclude
				}
		
				$date_filtering = $params->get('date_filtering', 'off');
				if ($date_filtering !== 'off') {
					$articles->setState('filter.date_filtering', $date_filtering);
					$articles->setState('filter.date_field', $params->get('date_field', 'a.created'));
					$articles->setState('filter.start_date_range', $params->get('start_date_range', '1000-01-01 00:00:00'));
					$articles->setState('filter.end_date_range', $params->get('end_date_range', '9999-12-31 23:59:59'));
					$articles->setState('filter.relative_date', $params->get('relative_date', 30));
				}
		
				$articles->setState('filter.language',$app->getLanguageFilter());
		
				$items = $articles->getItems();
		
				$show_date = $params->get('show_date', 0);
				$show_date_field = $params->get('show_date_field', 'created');
				$show_date_format = $params->get('show_date_format', 'Y-m-d H:i:s');
				$show_hits = $params->get('show_hits', 0);
				$show_author = $params->get('show_author', 0);
				$show_introtext = $params->get('show_introtext', 0);
				$show_image = $params->get('show_image', 0);
				$introtext_limit = $params->get('introtext_limit', 100);
		
				$option = JRequest::getCmd('option');
				$view = JRequest::getCmd('view');
		
				if ($option === 'com_content' && $view === 'article') {
					$active_article_id = JRequest::getInt('id');
				}
				else {
					$active_article_id = 0;
				}
		
				$groups[$catid]->articles = array();
				$groups[$catid]->subarticles = array();
				$i = 0;
				foreach ($items as &$item)
				{
					$item->slug = $item->id.':'.$item->alias;
					$item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;
		
					if ($access || in_array($item->access, $authorised)) {
						$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
					}
					 else {
						$app	= JFactory::getApplication();
						$menu	= $app->getMenu();
						$menuitems	= $menu->getItems('link', 'index.php?option=com_users&view=login');
					if(isset($menuitems[0])) {
							$Itemid = $menuitems[0]->id;
						} else if (JRequest::getInt('Itemid') > 0) {
							$Itemid = JRequest::getInt('Itemid');
						}
		
						$item->link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$Itemid);
						}
		
					$item->active = $item->id == $active_article_id ? ' active' : '';
		
					$item->displayDate = '';
					if ($show_date) {
						$item->displayDate = JHTML::_('date', $item->$show_date_field, $show_date_format);
					}
		
					$item->image = "";
					$images = json_decode($item->images);
					if ($show_image) :
						if (isset($images->image_intro) and !empty($images->image_intro)) :
							$item->image = JHTML::_('image', $images->image_intro, '');
						elseif (isset($images->image_fulltext) and !empty($images->image_fulltext)) :
							$item->image = JHTML::_('image', $images->image_fulltext, '');
						else :
							$regex   = "/<img[^>]+src\s*=\s*[\"']\/?([^\"']+)[\"'][^>]*\>/";
							$search  = $item->introtext;
							preg_match ($regex, $search, $matches);
							$images = (count($matches)) ? $matches : array();
							if ( count($images) ) {
							  $item->image  = JHTML::_('image', $images[1], '');
							}
						endif;
					endif;
		
					$item->displayHits = $show_hits ? $item->hits : '';
					$item->displayAuthorName = $show_author ? $item->author : '';
					if ($show_introtext) {
						$item->introtext = JHtml::_('content.prepare', $item->introtext);
						$item->introtext = self::_cleanIntrotext($item->introtext);
					}
					$item->displayIntrotext = $show_introtext ? self::truncate($item->introtext, $introtext_limit) : '';
					$item->displayReadmore = $item->alternative_readmore;
					
					if ( $i < $count ) {
						$groups[$catid]->articles[] = $item;	
					} else {
						$groups[$catid]->subarticles[] = $item;	
					}
					$i++;
		
				}	
			}
			
		endif;

		return $groups;
	}

	public static function _cleanIntrotext($introtext)
	{
		$introtext = str_replace('<p>', ' ', $introtext);
		$introtext = str_replace('</p>', ' ', $introtext);
		$introtext = strip_tags($introtext);

		$introtext = trim($introtext);

		return $introtext;
	}

	public static function truncate($html, $maxLength = 0)
	{
		$printedLength = 0;
		$position = 0;
		$tags = array();

		$output = '';

		if (empty($html)) {
			return $output;
		}

		while ($printedLength < $maxLength && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position))
		{
			list($tag, $tagPosition) = $match[0];

			$str = JString::substr($html, $position, $tagPosition - $position);
			if ($printedLength + JString::strlen($str) > $maxLength) {
				$output .= JString::substr($str, 0, $maxLength - $printedLength);
				$printedLength = $maxLength;
				break;
			}

			$output .= $str;
			$lastCharacterIsOpenBracket = (JString::substr($output, -1, 1) === '<');

			if ($lastCharacterIsOpenBracket) {
				$output = JString::substr($output, 0, JString::strlen($output) - 1);
			}

			$printedLength += JString::strlen($str);

			if ($tag[0] == '&') {
				$output .= $tag;
				$printedLength++;
			}
			else {
				$tagName = $match[1][0];

				if ($tag[1] == '/') {
					$openingTag = array_pop($tags);

					$output .= $tag;
				}
				else if ($tag[JString::strlen($tag) - 2] == '/') {
					$output .= $tag;
				}
				else {
					$output .= $tag;
					$tags[] = $tagName;
				}
			}

			if ($lastCharacterIsOpenBracket) {
				$position = ($tagPosition - 1) + JString::strlen($tag);
			}
			else {
				$position = $tagPosition + JString::strlen($tag);
			}

		}

		if ($printedLength < $maxLength && $position < JString::strlen($html)) {
			$output .= JString::substr($html, $position, $maxLength - $printedLength);
		}

		while (!empty($tags))
		{
			$output .= sprintf('</%s>', array_pop($tags));
		}

		$length = JString::strlen($output);
		$lastChar = JString::substr($output, ($length - 1), 1);
		$characterNumber = ord($lastChar);

		if ($characterNumber === 194) {
			$output = JString::substr($output, 0, JString::strlen($output) - 1);
		}

		$output = JString::rtrim($output);

		return $output.'&hellip;';
	}
}
