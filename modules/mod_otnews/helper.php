<?php
/**
 # mod_otnews - OT News Module for Joomla! 1.7
 # author       OmegaTheme.com
 # copyright    Copyright(C) 2011 - OmegaTheme.com. All Rights Reserved.
 # @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Website:     http://omegatheme.com
 # Technical support: Forum - http://omegatheme.com/forum/
**/
/**------------------------------------------------------------------------
 * file: helper.php 1.7.0 00001, April 2011 12:00:00Z OmegaTheme $
 * package: OT News Module
 *------------------------------------------------------------------------*/
 
//No direct access!
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';

jimport('joomla.application.component.model');

JModel::addIncludePath(JPATH_SITE.'/components/com_content/models');

abstract class modOtNewsHelper
{
    public static function getCategory(&$params){
        $catid_array = $params->get('catid');
        $list_all = array();
        foreach($catid_array as $catid){
            $articles_list = modOtNewsHelper::getList($params, $catid);
            if(!empty($articles_list)){
                $list_all[] = $articles_list;
            }
        }
        return $list_all;
    }
    public static function getList(&$params, $catid)
    {
        // Get the dbo
        $db = JFactory::getDbo();
        // Get an instance of the generic articles model
        $model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

        // Set application parameters in model
        $app = JFactory::getApplication();
        $appParams = $app->getParams();
        $model->setState('params', $appParams);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', (int) $params->get('count', 3));
        $model->setState('filter.published', 1);

        // Access filter
        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Category filter
        //$model->setState('filter.category_id', $params->get('catid', array()));
        $model->setState('filter.category_id', $catid);
        
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

        // Filter by language
        $model->setState('filter.language',$app->getLanguageFilter());

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

        // Set ordering
        $order_map = array(
            'm_dsc' => 'a.modified DESC, a.created',
            'mc_dsc' => 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
            'c_dsc' => 'a.created',
            'p_dsc' => 'a.publish_up',
            'h_dsc' =>  'a.hits'
        );
        $ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.publish_up');
        $dir = 'DESC';
        
        $model->setState('list.ordering', $ordering);
        $model->setState('list.direction', $dir);
        
        $items = $model->getItems();
        $menu_itemid = trim($params->get('Itemid'));
        $menu_itemid = ($menu_itemid != '') ? "&Itemid=".$menu_itemid : '';
        foreach ($items as &$item) {
            $item->slug = $item->id.':'.$item->alias;
            $item->catslug = $item->catid.':'.$item->category_alias;
            
            if ($access || in_array($item->access, $authorised))
            {
                // We know that user has the privilege to view the article
                $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug).$menu_itemid);
            }
            else {
                $item->link = JRoute::_('index.php?option=com_user&view=login');
            }
            
            $item->content= strip_tags(preg_replace('/<img([^>]+)>/i',"",$item->introtext));
            $item->content = substr($item->content, 0, $params->get('introtext_limit'));
            
            preg_match_all('/img.+src="([^"]+)"/i', $item->introtext, $matches);
            if(empty($matches[1][0])){
                $item->images="";
            }else{
                $item->images= $matches [1] [0];
            }
            
            //Show thumbnails
            if($params->get('showthumbnails_head_item')==1){
                if($item->images == ""){
                    $item->thumbnails = '<img src="'.$params->get('directory_thumbdefault').'" width="'.intval($params->get('thumbwidth')+15).'" alt="'.$item->title.'" />';
                }else{
                    $item->thumbnails = '<img src="' .$item->images.'" width="'.intval($params->get('thumbwidth')+15).'" alt="'.$item->title.'" />'  ;    //show images
                }
            }
        }
        return $items;
    }
}
?>