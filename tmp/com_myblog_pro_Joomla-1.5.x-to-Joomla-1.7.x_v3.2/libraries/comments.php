<?php
/**
 * MyBlog
 * @package MyBlog
 * @copyright (C) 2006 - 2010 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/
 
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class MYComments {
	private $handler = null;
	
	
	public function __construct() {
		
		global $_MY_CONFIG;
	
        $mconfig 	= $_MY_CONFIG; //MYBLOG_Factory::getConfig();
		$handler 	= '';
		$commentsys = strtolower($mconfig->get('useComment'));
		
		//check if there's a handler 
        if(file_exists(MY_LIBRARY_PATH . DS . 'comments' . DS . $commentsys.'.php')){
			require_once(MY_LIBRARY_PATH . DS . 'comments' . DS . $commentsys.'.php');
			$classname = 'MYComments_'. ucfirst($commentsys);
			$this->handler = new $classname();
		}
		
		/*
        if($mconfig->get('useComment')=='intensedebate'){
            $handler = 'intensedebate';
        }
        // @todo : code johny here, for call disqus
        elseif($mconfig->get('useComment')=='disqus'){
            $handler = 'disqus';
        }
        //@todo : this is for jomcomemnt
        else{
        
        }
        
		include_once(MY_LIBRARY_PATH . DS . 'comments' . DS . $handler.'.php');
		
		$classname = 'MYComments_'. ucfirst($handler);
		$this->handler = new  $classname();*/
	}

	
	/**
	 * Return the HTML code to be added to article
	 */	 	
	public function getHTML($id=''){
		return $this->handler->getHTML($id);
	}
	
	/**
	 * Return the number of count for the article
	 */	 	
	public function getCount($id=''){
		return $this->handler->getCount($id);
	}
	
	public function getHandler(){
		return $this->handler;
	}
}
