<?php 
/**
 * $ModDesc
 * 
 * @version		$Id: helper.php $Revision
 * @package		modules
 * @subpackage	mod_lofshowbiz
 * @copyright	Copyright (C) OCTOBER 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>. All rights reserved.
 * @website 	htt://landofcoder.com
 * @license		GNU General Public License version 2
 */
// no direct access
defined('_JEXEC') or die;
require_once JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';
if( !defined('PhpThumbFactoryLoaded') ) {
	require_once dirname(__FILE__).DS.'libs'.DS.'phpthumb'.DS.'ThumbLib.inc.php';
	define('PhpThumbFactoryLoaded',1);
}
if( !class_exists("LofShowbizDataSourceBase") ){
	require_once dirname(__FILE__).DS."libs".DS."source".DS."source_base.php";
}

abstract class modK2ShowbizHelper {
	
	static $COOKIE_NAME='LOFK2BIZ';
	/**
	 * get list articles
	 */
	public static function getList( $params, $moduleName ){
		return self::getListBySourceName( $params, $moduleName );
	}
	
	/**
   	 * Processing action, getting data following to each function selected
     */
	public static function processFunction( $params, $moduleName ){
		switch( trim($params->get('function','showcase')) ){
			case 'in-category': 
					if( JRequest::getCmd('view') == 'item' ){
						$id = (int)JRequest::getVar("id");
						$catid = self::getCatIdByItemId( $id ); 
						$params->set('k2_category', $catid);
						$params->set( 'exclude_ids', $id );
						return self::getList( $params, $moduleName ); 
					}
					return array();
			case 'recent-viewed': 
        $cookieName = 'LOFK2BIZ';
        if( JRequest::getCmd('view') == 'item' && ($id = (int)JRequest::getVar("id")) ){
             if( !isset($_COOKIE[$cookieName]) ){
               JRequest::setVar($cookieName, "", 'COOKIE', 'STRING');
                setcookie ($cookieName, "", 0, '/' );  
             } 
              $currentIds=$_COOKIE[$cookieName];    
       
             if( !preg_match('#\-'.$id.'-#',"-".$currentIds) ){//  die("D");
                 JRequest::setVar($cookieName, ($id)."-".$currentIds, 'COOKIE', 'STRING');
                 $_COOKIE[$cookieName]= ($id)."-".$currentIds; 
                  $exp = time() + 60*60*24*355;

                 setcookie ($cookieName, ($id)."-".$currentIds,  $exp, '/' );
             }         
        }
        if( isset($_COOKIE[$cookieName]) && $ids=trim($_COOKIE[$cookieName]) ){
    			 $params->set('k2_source','k2_items_ids');
    			 $params->set('k2_items_ids', str_replace("-",",",$ids."0") );
    			 $params->set('k2_category', '');
    			 return self::getList( $params, $moduleName ); 
        }
        return array();
			default :
        // get List of K2 items
				return self::getList( $params, $moduleName ); 
		}
	}
	
	/**
  	 * Get list of articles follow conditions user selected
     * 
     * @param JParameter $params
     * @return array containing list of article
     */ 
	public static function getListBySourceName( &$params, $moduleName ) {
	 	// create thumbnail folder 	

	 	$tmppath = JPATH_SITE.DS.'cache';//.'lofthumbs';
		$moduleName = 'lofthumbs' ;
	 	$thumbPath = $tmppath.DS. $moduleName.DS;
		if( !file_exists($tmppath) ) {
			JFolder::create( $tmppath, 0777 );
		}; 
		if( !file_exists($thumbPath) ) {
			JFolder::create( $thumbPath, 0777 );
		}; 
		// get call object to process getting source
		$source =  $params->get( 'data_source', 'k2' );
		$path = dirname(__FILE__).DS."libs".DS."source".DS.$source.DS."source.php";
	
		if( !file_exists($path) ){
			return array();	
		}
		require_once $path;
		$objectName = "Lof".ucfirst($source)."ShowbizDataSource";
	
	 	$object = new $objectName();
	 	$items= $object->setThumbPathInfo($thumbPath, JURI::base()."cache/".$moduleName."/" )
			->setImagesRendered( array( 'mainImage' => array( (int)$params->get( 'main_width', 200 ), (int)$params->get( 'main_height', 110 )),
										'thumbnail' => array( (int)$params->get( 'thumbnail_width', 490 ), (int)$params->get( 'thumbnail_height', 300 )) ) )
			->getList( $params );
  		return $items;
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
				JHTML::stylesheet('http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700');	
			}
		} else {
           JHTML::stylesheet( 'modules/'.$module->module.'/assets/'.'style.css' );
		}
        if($enablejQuery == 1){
            JHTML::script( 'modules/'.$module->module.'/assets/'.'jquery.min.js');
        }
        JHTML::script( 'modules/'.$module->module.'/assets/'.'jquery.cssAnimate.mini.js');
        JHTML::script( 'modules/'.$module->module.'/assets/'.'jquery.easing.1.3.js');
        JHTML::script( 'modules/'.$module->module.'/assets/'.'jquery.mousewheel.min.js');
        JHTML::script( 'modules/'.$module->module.'/assets/'.'jquery.script.js');
        JHTML::script( 'modules/'.$module->module.'/assets/'.'jquery.touchwipe.min.js');
  	
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