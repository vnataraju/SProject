<?php 
/*------------------------------------------------------------------------
 # mod_leouserpanel - Leo UserPanel Module
 # ------------------------------------------------------------------------
 # author    LeoTheme
 # copyright Copyright (C) 2010 leotheme.com. All Rights Reserved.
 # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.leotheme.com
 # Technical Support:  Forum - http://www.leotheme.com/forum.html
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );  
 

abstract class modLeoUserPanelHelper {
	 	
    static function getReturnURL($params, $type)
	{
		$app	= JFactory::getApplication();
		$router = $app->getRouter();
		$url = null;
		if ($itemid =  $params->get($type))
		{
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select($db->nameQuote('link'));
			$query->from($db->nameQuote('#__menu'));
			$query->where($db->nameQuote('published') . '=1');
			$query->where($db->nameQuote('id') . '=' . $db->quote($itemid));

			$db->setQuery($query);
			if ($link = $db->loadResult()) {
				if ($router->getMode() == JROUTER_MODE_SEF) {
					$url = 'index.php?Itemid='.$itemid;
				}
				else {
					$url = $link.'&Itemid='.$itemid;
				}
			}
		}
		if (!$url)
		{
			// stay on the same page
			$uri = clone JFactory::getURI();
			$vars = $router->parse($uri);
			unset($vars['lang']);
			if ($router->getMode() == JROUTER_MODE_SEF)
			{
				if (isset($vars['Itemid']))
				{
					$itemid = $vars['Itemid'];
					$menu = $app->getMenu();
					$item = $menu->getItem($itemid);
					unset($vars['Itemid']);
					if (isset($item) && $vars == $item->query) {
						$url = 'index.php?Itemid='.$itemid;
					}
					else {
						$url = 'index.php?'.JURI::buildQuery($vars).'&Itemid='.$itemid;
					}
				}
				else
				{
					$url = 'index.php?'.JURI::buildQuery($vars);
				}
			}
			else
			{
				$url = 'index.php?'.JURI::buildQuery($vars);
			}
		}

		return base64_encode($url);
	}

	static function getType()
	{
		$user = JFactory::getUser();
		return (!$user->get('guest')) ? 'logout' : 'login';
	}   
	/**
	 * load css - javascript file.
	 * 
	 * @param JParameter $params;
	 * @param JModule $module
	 * @return void.
	 */
	public static function loadMediaFiles( $params, $module, $theme='' ){

		$mainframe = JFactory::getApplication();
		$template = self::getTemplate();
		//load style of module
		if(file_exists(JPATH_BASE.DS.'templates/'.$template.'/css/'.$module->module.'.css')){
			JHTML::stylesheet(  'templates/'.$template.'/css/'.$module->module.'.css' );		
		}			
		// load style of theme follow the setting
		if( $theme && $theme != -1 ){
			$tPath = JPATH_BASE.DS.'templates'.DS.$template.DS.'css'.DS.$module->module.'_'.$theme.'.css';
			if( file_exists($tPath) ){
				JHTML::stylesheet( 'templates/'.$template.'/css/'.$module->module.'_'.$theme.'.css');
			} else {
				JHTML::stylesheet('modules/'.$module->module.'/tmpl/'.$theme.'/assets/'.'style.css');	
			}
		} else {
           JHTML::stylesheet( 'modules/'.$module->module.'/assets/'.'style.css' );
		}
		JHTML::script( 'modules/'.$module->module.'/assets/'.'script_12.js');
  	
	}
	
	/**
	 * get name of current template
	 */
	public static function getTemplate(){
		$mainframe = JFactory::getApplication();
		return $mainframe->getTemplate();
	}
	
	/**
	 * Get Layout of the item, if found the overriding layout in the current template, the module will use this file
	 * 
	 * @param string $moduleName is the module's name;
	 * @params string $theme is name of theme choosed
	 * @params string $itemLayoutName is name of item layout.
	 * @return string is full path of the layout
	 */
	public static function getItemLayoutPath($moduleName, $theme ='', $itemLayoutName='_item' ){

		$layout = trim($theme)?trim($theme).DS.'_item'.DS.$itemLayoutName:'_item'.DS.$itemLayoutName;	
		$path = JModuleHelper::getLayoutPath($moduleName, $layout);	
		if( trim($theme) && !file_exists($path) ){
			// if could not found any item layout in the theme folder, so the module will use the default layout.
			return JModuleHelper::getLayoutPath( $moduleName, '_item'.DS.$itemLayoutName );
		}
		return $path;
	}
}
?>