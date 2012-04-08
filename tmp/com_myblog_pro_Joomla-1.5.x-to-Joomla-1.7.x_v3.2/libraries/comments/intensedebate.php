<?php
/**
 * MyBlog
 * @package MyBlog
 * @copyright (C) 2006 - 2010 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/
 
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class MYComments_Intensedebate {
    
    private $account = null;
    private $pageCommentUrl = null;
    
    
    
    function __construct(){
    	global $_MY_CONFIG;
    	$mconfig	= $_MY_CONFIG; //MYBLOG_Factory::getConfig();
            
            
    	
    	// get account name intese debate
            
    	$this->account = $mconfig->get('accountIntenseDebate');
            
    	
    	// get article ID page
            
    	$this->pageCommentId = JRequest::getVar('id');
            
            
    	
    	// get current url for comment
            
    	$url =& JURI::getInstance(); 
           
    	$this->pageCommentUrl =$url->toString();
        
    }
    
    
    
    /**
	* get JavaScript code intenseDebate for call form commenting
    
	* @param int article ID
   
	* @return string HTML/JS string
    
	*/
    
	function getHTML($id=''){
        
	
		$output = '<script type="text/javascript">
    		var idcomments_acct = "'.$this->account.'";
            var idcomments_post_id = "'.$id.'";
            var idcomments_post_url = "'.$this->pageCommentUrl.'";
    		</script>
    		<span id="IDCommentsPostTitle" style="display:none"></span>
    		<script type="text/javascript" src="http://www.intensedebate.com/js/genericCommentWrapperV2.js"></script>';
            
        
		return $output;
	}
    
    
	
	/**
    
	* get JavaScript code intenseDebate for call count comment
    
	* @param int article ID
    
	* @return string HTML/JS string
    
	*/
    
	function getCount($id=''){
        
		$output = '<script type="text/javascript">
    		var idcomments_acct = "'.$this->account.'";
            var idcomments_post_id = "'.$id.'";
            var idcomments_post_url = "'.$this->pageCommentUrl.'";
    		</script>
    		<script type="text/javascript" src="http://www.intensedebate.com/js/genericLinkWrapperV2.js"></script>';
            
        
		return $output;
    
	}
}

