<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

DEFINE("MB_SECTION",8);
DEFINE("MB_CATEGORY",18);

class myblogBlogContent extends JTable
{ 
	var $id =0;
	var $title =null;
	var $title_alias =null;
	var $introtext =null;
	var $images =null;
	var $fulltext =null;
	var $state =null;
	var $sectionid =null;
	var $mask =null;
	var $catid =null;
	var $created =null;
	var $created_by =null;
	var $created_by_alias=null;	
	var $modified =null;	
	var $modified_by =null;	
	var $checked_out =null;	
	var $checked_out_time=null;	
	var $publish_up =null;	
	var $publish_down =null;	
	var $urls =null;	
	var $attribs =null;	
	var $version =null;	
	var $parentid =null;	
	var $ordering =null;	
	var $metakey =null;	
	var $metadesc =null;	
	var $access =null;
	var $hits =null;	
	
	/* Custom params */
	var $permalink = null;
	var $tags	= null; // string, saperated by comma
	var $tagobj	= null;	// array, obj(id, name) of the tags
	var $rating = null;
	var $rating_count	= null;
	var $secret	= null;
	
	var $is_quickpost = null;
	var $quickpost_type = null;

	function __construct(&$db)
	{
		parent::__construct('#__content','id', $db);
	}
	
	/**
	* In Joomla >= 1.6, table Asset::Name column contain wrong value when article is saved from Myblog
	* Creating this function to return the right Assets Name
	* @since Joomla 1.6 
	*/
	function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_content.article'.'.'.(int) $this->$k;
	}

	function autoCloseTags($string) {
		// automatically close HTML-Tags
		// (usefull e.g. if you want to extract part of a blog entry or news as preview/teaser)
		// coded by Constantin Gross <connum at googlemail dot com> / 3rd of June, 2006
		// feel free to leave comments or to improve this function!
		$donotclose=array('br','img','input'); //Tags that are not to be closed
		//prepare vars and arrays
		$tagstoclose='';
		$tags=array();
		//put all opened tags into an array
		preg_match_all("/<(([A-Z]|[a-z]).*)(( )|(>))/isU",$string,$result);
		$openedtags=$result[1];
		$openedtags=array_reverse($openedtags); //this is just done so that the order of the closed tags in the end will be better
		//put all closed tags into an array
		preg_match_all("/<\/(([A-Z]|[a-z]).*)(( )|(>))/isU",$string,$result2);
		$closedtags=$result2[1];
		//look up which tags still have to be closed and put them in an array
		for ($i=0;$i<count($openedtags);$i++) {
			if (in_array($openedtags[$i],$closedtags)) { unset($closedtags[array_search($openedtags[$i],$closedtags)]); }
			else array_push($tags, $openedtags[$i]);
		}
		$tags=array_reverse($tags); //now this reversion is done again for a better order of close-tags
		//prepare the close-tags for output
		for($x=0;$x<count($tags);$x++) {
			$add=strtolower(trim($tags[$x]));
			if(!in_array($add,$donotclose)) $tagstoclose.='</'.$add.'>';
		}
		//and finally
		return $tagstoclose;
	}

	function getParagraphCount($text)
	{
		$position = -1;
		$count	  = 0;

		while( ( $position = JString::strpos($text , '</p>' , $position + 1) ) !== false )
		{
			$count++;
		}
			
		return $count;
	}
	
	function getBrowseText(&$row)
	{
		global $_MY_CONFIG;
		
		if($_MY_CONFIG->get('useIntrotext'))
		{
			if(empty($row->fulltext))
			{
				$ending = JString::strpos($row->introtext, '</p>');
				
				$pos=-1;
				$pos_array = array();
				while (($pos=JString::strpos($row->introtext,'</p>',$pos+1))!==false) 
					$pos_array[]=$pos;
				
				$pNum = $_MY_CONFIG->get('autoReadmorePCount');
				if (count($pos_array) <= $pNum) {
				   $row->text = $row->introtext;
				} else {
					$ending = $pos_array[$pNum-1];
					$row->introtext = JString::substr($row->introtext, 0, $ending + 4);
					$row->introtext = myCloseTags(preg_replace('#\s*<[^>]+>?\s*$#','',$row->introtext));
				}
			}
			else if( !empty($row->fulltext) && empty($row->introtext) )
			{
				// Strip x paragraphs
				
				$ending = JString::strpos($row->fulltext, '</p>');
				
				$pos=-1;
				$pos_array = array();
				while (($pos=JString::strpos($row->fulltext,'</p>',$pos+1))!==false) 
				$pos_array[]=$pos;
				
				$pNum = $_MY_CONFIG->get('autoReadmorePCount');
				if (count($pos_array) <= $pNum) {
					$row->text = $row->fulltext;
				} else {
					$ending = $pos_array[$pNum-1];
					$row->fulltext = JString::substr($row->fulltext, 0, $ending + 4);
					$row->fulltext = myCloseTags(preg_replace('#\s*<[^>]+>?\s*$#','',$row->fulltext));
				}
			}
			
			// If user set to display introtext but introtext might be empty
			// due to the way previous version of My Blog stores the entries.
			if( empty($row->introtext) )
			{
				$row->text = $row->fulltext;
			}
			else
			{
				$row->text = $row->introtext;
			}
		}
		else
		{
			if( empty( $row->fulltext ) || $row->checkTagReadMore=='1')
			{
				$row->text	= $row->introtext;
			}
			else
			{
				$row->text	= $row->fulltext;
			}
		}
	}
	
	function _splitReadmoreOnSave(){
		// During save, the text in the editor will be stored in $this->fulltext.
		// If readmore is detected, we split it up and place it in introtext / fulltext
		// If it doesn't exists just place it in introtext like the default Joomla.
		
		
		// we are assuming everything is now in fulltext
		$this->fulltext = preg_replace('/<p id="readmore">(.*?)<\\/p>/i', '{readmore}', $this->fulltext);
		$pos = JString::strpos($this->fulltext, '{readmore}');
		if ($pos === false) {
			$this->introtext = $this->fulltext;
			$this->fulltext = '';
		} 
		else
		{
			$this->introtext = JString::substr($this->fulltext, 0, $pos);
			$this->fulltext  = JString::substr($this->fulltext, $pos + 10);
		} 
	}
	
	// We can load the row using either numeric id or a permalink string
	function load($id)
	{
		$mainframe	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$originalid = $id;
		
		if(is_numeric($id))
		{
			parent::load($id);
		}
		else
		{
			$query	= "SELECT ".$db->nameQuote('contentid')." FROM ".$db->nameQuote('#__myblog_permalinks')." WHERE ".$db->nameQuote('permalink')."=".$db->Quote($id);
			$db->setQuery( $query );
			$id = $db->loadResult();
			
			// IF we cannot find it, need to try and convert ':' to '-'. Joomla 1.5
			// seems to convert this
			if(!$id)
			{
				$id = str_replace(':', '-', $originalid);
				$sql = "SELECT ".$db->nameQuote('contentid')." FROM ".$db->nameQuote('#__myblog_permalinks')." WHERE ".$db->nameQuote('permalink')."=".$db->Quote($id);
				
				$db->setQuery($sql);
				$id = $db->loadResult();
			}
			
			// If we still can't locate it, perhaps they might be using an older permalink
			if( !$id )
			{
				$id		= JRequest::getVar( 'show' , '' , 'GET' );
				
				$query	= 'SELECT * FROM ' . $db->nameQuote( '#__myblog_redirect' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'permalink' ) . '=' . $db->Quote( $id );
				$db->setQuery( $query );
				
				$permalink	= $db->loadObject();
				
				// If its an older permalink, then we should do a 301 redirect.
				if( $permalink )
				{
					$id			= $permalink->contentid;
					
					$query	= 'SELECT ' . $db->nameQuote( 'permalink' ) . ' FROM ' . $db->nameQuote( '#__myblog_permalinks' )
							. 'WHERE ' . $db->nameQuote('contentid') . '=' . $db->Quote( $permalink->contentid );
					$db->setQuery( $query );
					
					$link	= $db->loadResult();
					
					$url		= JRoute::_('index.php?option=com_myblog&show=' . $link , false );
					$mainframe->redirect( $url );
					exit;
				}

			}
			
			parent::load($id);
		}
		
		// get the permalink && quickpost props
		if(is_numeric($id) && ($id != 0))
		{
			$db->setQuery("SELECT ".$db->nameQuote('permalink').
						" FROM ".$db->nameQuote('#__myblog_permalinks').
						" WHERE ".$db->nameQuote('contentid')."=".$db->Quote($id));
			$this->permalink = $db->loadResult();
		
			$db->setQuery("SELECT ".$db->nameQuote('is_quickpost').','.$db->nameQuote('quickpost_type').
						" FROM ".$db->nameQuote('#__myblog_entry_attr').
						" WHERE ".$db->nameQuote('contentid')."=".$db->Quote($id));
			$props = $db->loadObject();
			if(!empty($props)){
				$this->is_quickpost = $props->is_quickpost;
				$this->quickpost_type = $props->quickpost_type ;
			}
		}
		
		// if the fulltext contain the '{readmore}', that means this is the old data and we need to clean them up a bit
		// $this->_split_fulltext_readmore();
		// @gotcha. if fulltext contain readmore, and {introtext}
		$pos = JString::strpos($this->fulltext, '{readmore}');
		
		if ($pos === false)
		{
		}
		else
		{
			$this->introtext .= JString::substr($this->fulltext, 0, $pos);
			$this->fulltext  = JString::substr($this->fulltext, $pos + 10);
		}
		
		// We store all the text in the introtext if no {readmore} is present. 
		// Otherwise it is stored in introtext
		// and fulltext appropriately.
 		if(!empty($this->fulltext) && empty($this->introtext))
		{
 			$this->introtext = $this->fulltext;
 			$this->fulltext = '';
 		}

		// Load the tags into a string
		$sql = "SELECT ".$db->nameQuote('tag').".".$db->nameQuote('id')." ,".$db->nameQuote('tag').".".$db->nameQuote('name')
			." FROM ".$db->nameQuote('#__myblog_categories')." as tag ,  ".$db->nameQuote('#__myblog_content_categories')." as c "
			." WHERE tag.".$db->nameQuote('id')." = c.".$db->nameQuote('category')." AND c.".$db->nameQuote('contentid')."=".$db->Quote($this->id);
		$db->setQuery($sql);
		$this->tagobj = $db->loadObjectList();
		$tags = array();
		if($this->tagobj)
		{
			foreach($this->tagobj as $tag)
			{
				$tags[] = $tag->name;
			}
		}
		$this->tags = implode(',', $tags);
		
		# Get the rating
		$db->setQuery("SELECT *, round( ".$db->nameQuote('rating_sum')." / ".$db->nameQuote('rating_count')." ) AS ".$db->nameQuote('rating').
					" FROM ".$db->nameQuote('#__content_rating').
					" WHERE ".$db->nameQuote('content_id')."=".$db->Quote($this->id) );
		$rating = $db->loadObject();
		
		if($rating)
		{
			$this->rating = $rating->rating;
			$this->rating_count = $rating->rating_count;
		}
		
		//Change all relative url to absolute url
		$this->introtext = str_replace('src="images', 'src="'. rtrim( JURI::root() , '/' ) .'/images', $this->introtext);
		$this->fulltext  = str_replace('src="images', 'src="'. rtrim( JURI::root() , '/' ) .'/images',  $this->fulltext);
		
		// Convert back to htmlentities
		$this->title        = htmlspecialchars($this->title);
		
		// make sure no bloody {readmore} tag ever
		$this->introtext = str_replace('{readmore}', '', $this->introtext);
		$this->fulltext = str_replace('{readmore}', '', $this->fulltext);

		# Trim all necessary text
		$this->introtext = trim($this->introtext);
		$this->fulltext = trim($this->fulltext);
		$this->permalink = trim($this->permalink);
	}
	
	function bind($vars, $editorSave=false)
	{
		if(empty($vars))
			return;
			
		parent::bind($vars);

		// if saving from editor, everything is in the fulltext, need to split it
		if($editorSave && empty($vars['introtext']))
		{
			$this->_splitReadmoreOnSave();
		}
	}
	
	function store()
	{
		global $_MY_CONFIG;
		
		$mainframe	=& JFactory::getApplication();
		$temp		= $this->permalink;
		$tags		= $this->tags;
		$my			=& JFactory::getUser();
		$db			=& JFactory::getDBO();
		$isNew		= ($this->id == 0 ) ? true : false;
		
		$is_quickpost = (empty($this->is_quickpost)) ? 0 : 1 ;
		$quickpost_type = $this->quickpost_type;
		unset($this->is_quickpost);
		unset($this->quickpost_type);

		unset($this->permalink);
		unset($this->tags);
		unset($this->rating);
		unset($this->rating_count);
		
		if($this->id != NULL && $this->id != 0)
		{
			$this->modified = strftime("%Y-%m-%d %H:%M:%S", time() + ( $mainframe->getCfg('offset') * 60 * 60 ));	
		}
		else
		{			
			$creator = $my->id;
			if(!$creator){
				$tmpuser = myGetUser($this->secret);
				$creator = $tmpuser->id;
				unset($tmpuser);
			}
			$this->created_by = $creator;
		}
		
		unset($this->secret);
		
		if(empty($this->publish_up))
		{
			$this->publish_up = $this->created;
		}
		
		if(empty($this->sectionid))
		{
			$this->sectionid = $_MY_CONFIG->get('postSection');
		}
		
		if(empty($this->catid))
		{
			$this->catid = $_MY_CONFIG->get('catid');
		}
						
		//$this->_splitReadMoreOnSave();
		
		// Decode back to normal
		$this->title    = html_entity_decode($this->title);
		//$this->title = htmlspecialchars($this->title);

		$currentId		= $this->id;

		parent::store();
		
		// restore custom fields
		$this->permalink = $temp;
		$this->tags =  $tags;
		
		// If the permalink is empty, we need to create them or update if necessary
		if(empty($this->permalink))
		{
			$this->permalink    = myGetPermalink($this->id);
		}
		else
		{
			// @rule: if no .html is provided, we must enforce it.
			if( !JString::stristr( $this->permalink , '.html' ) )
			{
				$this->permalink	.= '.html';
			}
			
			// @rule: check if permalink is valid
			if( !myIsValidPermalink( $this->permalink , $this->id ) )
			{
				$this->permalink	= myGetPermalink( $this->id );
			}
		}

		jimport( 'joomla.filesystem.file' );
		
		if( $_MY_CONFIG->get('jomsocialActivity') )
		{
			// Test if jomsocial file really exists.
			$core	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
			if( JFile::exists( $core ) )
			{
				if( $this->state == 1 )
				{
					require_once( $core );
					CFactory::load ( 'libraries', 'activities' );

					$command		= ( $currentId == 0 ) ? 'blog.create' : 'blog.update';
					$title			= JString::substr( $this->title , 0 , 20 ) . '...';
					$link			= JRoute::_('index.php?option=com_myblog&show=' . $this->permalink . '&Itemid=' . myGetItemId() ); 
					$act			= new stdClass();
					$act->cmd 		= $command;
					$act->actor   	= $my->id;
					$act->target  	= 0;
					$act->title		= ( $currentId == 0 ) ? JText::sprintf('COM_MY_JS_BLOG_ENTRY_CREATED_ACTIVITY' , $link , $title ) : JText::sprintf('COM_MY_JS_BLOG_ENTRY_UPDATED_ACTIVITY' , $link , $title );
					if($_MY_CONFIG->get('jomsocialActivityTextLimit') && $is_quickpost==0){
					    $this->introtext = strip_tags($this->introtext);
					    $this->introtext = JString::substr($this->introtext, 0,$_MY_CONFIG->get('jomsocialActivityTextLimit')).'...';
					}
					$act->content	= ( $currentId == 0 ) ? $this->introtext : "<a href='".$link."'>".$title."</a>";
					$act->app		= 'myblog';
					$act->cid		= $this->id;
		
					// Add activity logging
					$act->comment_id  = CActivities::LIKE_SELF;
					$act->like_type   = $command;
					$act->like_id     = CActivities::LIKE_SELF;
					
					CActivityStream::add($act);
				}
			}
		}

		if( $_MY_CONFIG->get('jomsocialPoints') )
		{
			if( $this->id != 0 && $isNew )
			{
				$file	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php';
				
				if( JFile::exists( $file ) )
				{
					require_once( $file );

					CUserPoints::assignPoint( 'myblog.add' , $my->id );
				}
			}
		}
		
		//save quickpost props
		$query	= 'REPLACE INTO '.$db->nameQuote( '#__myblog_entry_attr' ).
				' SET is_quickpost=' . $db->Quote( $is_quickpost ). 
				' , quickpost_type=' . $db->Quote( $quickpost_type ).
				' , contentid='.$db->Quote( $this->id );
		$db->setQuery( $query );
		$db->query();

		if($this->id != 0)
		{
			$sql = "SELECT ".$db->nameQuote('permalink')." FROM ".$db->nameQuote('#__myblog_permalinks')." WHERE ".$db->nameQuote('contentid')."=".$db->Quote($this->id);
			$db->setQuery($sql);
			
			$oldPermalink	= $db->loadResult();
			
			if($oldPermalink)
			{
				// @rule: Remove any references for this new permalink in the redirect table.
				$query	= 'DELETE FROM ' . $db->nameQuote( '#__myblog_redirect' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'permalink' ) . '=' . $db->Quote( $this->permalink ) . ' '
						. 'AND '.$db->nameQuote('contentid'). '=' . $db->Quote( $this->id );
				$db->setQuery( $query );
				$db->query();
				
				// If there is a change in permalink, need to add a redirection
				if( $oldPermalink != $this->permalink )
				{
					// Add a record into the redirect table so we can do a 300 redirect later.
					$query	= 'INSERT INTO ' . $db->nameQuote( '#__myblog_redirect' ) . ' (' . $db->nameQuote( 'contentid' ) . ',' . $db->nameQuote('permalink') . ') '
							. 'VALUES (' . $db->Quote( $this->id ) . ',' . $db->Quote( $oldPermalink ) . ')';
					$db->setQuery( $query );
					$db->query();
				}

				$db->setQuery("UPDATE ". $db->nameQuote('#__myblog_permalinks') .
							" SET ".$db->nameQuote('permalink')." = ".$db->Quote($this->permalink).
							" WHERE ".$db->nameQuote('contentid')." = ".$db->Quote($this->id));
				$db->query();
			}
			else
			{
				$db->setQuery("INSERT INTO ".$db->nameQuote('#__myblog_permalinks').
							" (".$db->nameQuote('contentid').", ".$db->nameQuote('permalink').
							") VALUES (".$db->Quote($this->id).", ".$db->Quote($this->permalink).")");
				$db->query();
			}
		}
	}
	
	/**
	 * Return an array of strings with all the validation error of the given entry.	  	
	 * The data given will be blogContent object.
	 * 
	 * If no error is found, return an empty array	 	 
	 */	 	
	function validate_save(){
		$validate = array();
		
		# Title cannot be empty
		if(empty($this->title)){
			$validate[] = "Title is empty"; 
		} 
		
		# Fulltext area cannot be empty
		if(empty($this->fulltext)){
			$validate[] = "You cannot save a blank entry. "; 
		}
		
		# Check if permalink contains any unallowed characters and no duplicate is allowed
		if (preg_match('/[!@#$%\^&*\(\)\+=\{\}\[\]|\\<">,\\/\^\*;:\?\']/', $this->permalink)) {
			$validate[] = "Permanent link can only contain ASCII alphanumeric characters and.-_ only";
		} else {
			$db->query("SELECT count(*) from ".$db->nameQuote('#__myblog_permalinks').
						" WHERE ".$db->nameQuote('permalink')."=".$db->Quote($this->permalink).
						" AND ".$db->nameQuote('contentid')." != ".$db->nameQuote($this->id));
			
			if ($db->get_value() and $this->permalink != "") {
				$validate[] = "Permanent link has already been taken. Please choose a different permanent link.";
			} 
		}
		
		return $validate;
	}
	
	function delete()
	{
		global $_MY_CONFIG;
		
		$my	=& JFactory::getUser();
		$id	= $this->id;

		if( $id != 0 )
		{
			$db	=& JFactory::getDBO();
			$query	= "DELETE FROM ".$db->nameQuote('#__content')." WHERE ".$db->nameQuote('id')."=".$db->Quote($id);
			$db->setQuery( $query );
			$db->query();
			
			$query	= "DELETE FROM ".$db->nameQuote('#__myblog_permalinks')." WHERE ".$db->nameQuote('contentid')."=".$db->Quote($id);
			$db->setQuery( $query );
			$db->query();
	
			$query	= "DELETE FROM ".$db->nameQuote('#__myblog_images')." WHERE ".$db->nameQuote('contentid')."=".$db->Quote($id);
			$db->setQuery( $query );
			$db->query();
			
			$query	= "DELETE FROM ".$db->nameQuote('#__myblog_content_categories')." WHERE ".$db->nameQuote('contentid')."=".$db->Quote($id);
			$db->setQuery( $query );
			$db->query();
			
			$query	= "DELETE FROM ".$db->nameQuote('#__myblog_entry_attr')." WHERE ".$db->nameQuote('contentid')."=".$db->Quote($id);
			$db->setQuery( $query );
			$db->query();
	
			if( $_MY_CONFIG->get('jomsocialPoints') )
			{
				$file	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php';
				
				if( JFile::exists( $file ) )
				{
					require_once( $file );

					CUserPoints::assignPoint( 'myblog.remove' , $my->id );
				}
			}
		}
		return true;
	}
} 
