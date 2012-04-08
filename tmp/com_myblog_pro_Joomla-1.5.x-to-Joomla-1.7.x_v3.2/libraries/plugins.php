<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

global $_MY_CONFIG;

class MYPlugins
{	
	/** Private Properties **/
	var $_folder	= '';		// Plugins / Mambots folder
	var $_events	= null;

	function MYPlugins()
	{

		// Include our custom cmslib if its not defined
		if(!defined('MYPluginsDB'))
			include_once (MY_MODEL_PATH . '/plugins.db.php');

		$this->_db	= new MYPluginsDB();

		// Set the plugins / mambots folder for this specific environment.
		$this->_folder	= JPATH_PLUGINS;
	}
	
	/**
	 * Load plugins for specific event.
	 **/	 	
	function load()
	{
		$mainframe	=& JFactory::getApplication();
		$plugins	= $this->_db->getPlugins();
		
		if($plugins)
		{
			foreach($plugins as $plugin)
			{
			    // Instead of including the mambots, we allow the mainframe mambot to include for us.
			    // so that other module that has triggers would not come up with an error.
			    $plugin->folder 		= 'content';
			    $plugin->published 	= '1';
			    //$plugin->params		= null; // no params
			
				JRequest::setVar( 'task' , 'view' , 'GET' );
				JRequest::setVar( 'option' , 'com_content' , 'GET' );
	
				// Import plugin.
				JPluginHelper::importPlugin('content', $plugin->element);
				$plg	= JPluginHelper::getPlugin('content' , $plugin->element);
				
				$plugin->params = $plg->params; 
				
				$dispatcher	=& JDispatcher::getInstance();
				$plgObj		= 'plgContent' . ucfirst($plg->name);
				
				if( class_exists( $plgObj ) )
				{	
					$instance = new $plgObj($dispatcher , (array) $plg);
					
					if( method_exists($instance , 'onPrepareContent') )
					{
						$this->register( 'onPrepareContent' , $instance , $plugin->params , $plugin->published );
					}elseif( method_exists($instance , 'onContentPrepare') ){
						$this->register( 'onPrepareContent' , $instance , $plugin->params , $plugin->published );
					}
					
					if( method_exists($instance , 'onBeforeDisplayContent') )
					{
						$this->register( 'onBeforeDisplayContent' , $instance , $plugin->params , $plugin->published );
					}elseif(method_exists($instance , 'onContentBeforeDisplay') ){
						$this->register( 'onBeforeDisplayContent' , $instance , $plugin->params , $plugin->published );
					}

					if( method_exists($instance , 'onAfterDisplayContent') )
					{
						$this->register( 'onAfterDisplayContent' , $instance, $plugin->params , $plugin->published );
					}elseif( method_exists($instance , 'onContentAfterDisplay') ){
						$this->register( 'onAfterDisplayContent' , $instance , $plugin->params , $plugin->published );
					}
				}
				else 
				{
					$dispatcher_observers = (JVERSION >= 1.6) 
										? $dispatcher->get('_observers') 
										: $dispatcher->_observers;
					
					foreach($dispatcher_observers as $observer)
					{
						if( is_array($observer) )
						{
							if($observer['event'] == 'onPrepareContent')
							{
								$this->register('onPrepareContent', $observer['handler'], $plugin->params, $plugin->published);
							}

							if($observer['event'] == 'onBeforeDisplayContent')
							{
								$this->register('onBeforeDisplayContent', $observer['handler'], $plugin->params, $plugin->published);
							}

							if($observer['event'] == 'onAfterDisplayContent')
							{
								$this->register('onAfterDisplayContent', $observer['handler'], $plugin->params, $plugin->published);
							}
						}
					}
				}
			}// End for loop
		}// End if
	}// End function

	/**
	 * Register specific plugin in our events list
	 **/	 	
	function register($event, $handler, $params, $published = 1)
	{		
		if(!isset($this->_events[$event])) 
			$this->_events[$event]	= array();
	
		//if handler is an object, lets get its name and keep in cache
		if(is_object($handler)){
			$handler_name = get_class($handler);
		}else{
			$handler_name = $handler;
		}
	
		//if(!in_array($handler, $this->_events[$event]))
		if(!isset( $this->_events[$event][$handler_name] ))
			$this->_events[$event][$handler_name] = $handler;
	}
	
	/**
	 * Call the necessary mambots to run.
	 **/	 	
	function _callFunction($handler, &$row, &$params, $page, $event)
	{
		//check if handler is object, if yes, lets trigger the event action
		if( is_object($handler) && class_exists(get_class($handler)) )
		{
			$dispatcher	=& JDispatcher::getInstance();

			// load plugin parameters		
			$pluginparams =& JPluginHelper::getPlugin('content' , $handler->get('_name') );
			
			// create the plugin
			$instance = new $handler($dispatcher, (array)($pluginparams));
			
			return (JVERSION >= 1.6) 
				? $instance->$event('com_myblog', $row, $params , $page)
				: $instance->$event($row, $params , $page);
		}
		else if( function_exists($handler) )
		{
			return $handler($row, $params, $page);
		}
	}
	
	function trigger($event, &$row, &$params, $page = '0')
	{	
		//on Joomla 1.6 or later, onPrepareContent is now known as onContentPrepare
		$overwrite_handler_event = NULL;
		if(JVERSION >= 1.6 && $event == 'onPrepareContent'){
			$overwrite_handler_event = 'onContentPrepare';
		}
		if(JVERSION >= 1.6 && $event == 'onBeforeDisplayContent'){
			$overwrite_handler_event = 'onContentBeforeDisplay';
		}
		if(JVERSION >= 1.6 && $event == 'onAfterDisplayContent'){
			$overwrite_handler_event = 'onContentAfterDisplay';
		}
		
		$result	= '';
		if(isset($this->_events[$event]))
		{
			foreach($this->_events[$event] as $handler_name => $handler)
			{
				if(empty($overwrite_handler_event)){ //no need to check for onContentPrepare
					$result	.= $this->_callFunction($handler, $row, $params, $page, $event);
					continue;
				}
				
				if(method_exists($handler , $overwrite_handler_event)){
					$result	.= $this->_callFunction($handler, $row, $params, $page, $overwrite_handler_event);
				}else{
					$result	.= $this->_callFunction($handler, $row, $params, $page, $event);
				}				
			}
		}
 		return $result;
	}
	
	/**
	 * Initialize all mambots / plugins to be stored into #__myblog_mambots	 
	 **/	 	
	function init()
	{
		$this->_db->initPlugins();
	}
	
	function get($limitstart, $limit)
	{
		return $this->_db->get($limitstart, $limit);
	}
	
	function getTotal()
	{
		return $this->_db->getTotal();
	}
}