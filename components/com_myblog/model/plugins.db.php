<?php
/**
 * @copyright (C) 2007 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 *
 * Rem:
 *
 * 
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class MYPluginsDB{
	var $table	= '#__myblog_mambots';
	var $key	= '';
	var $db		= null;
	
	var $_plugins	= '';
	function MYPluginsDB()
	{
		$this->db		=& JFactory::getDBO();
		$this->_plugins	= '#__plugins';
	}
	
	function getPlugins($type = 'content', $published = true){
	
		if($type == 'content')
			$type	= " AND a.".$this->db->nameQuote('folder')."=".$this->db->Quote('content');
		else
			$type	= " AND a.".$this->db->nameQuote('folder')."=".$this->db->Quote($type);

		// Check if only get published mambots
		if($published)
			$published	= " AND b.my_published='1' ";

		$strSQL = (JVERSION >= 1.6) 
				? "SELECT a.".$this->db->nameQuote('element').", a.".$this->db->nameQuote('ordering')
					. " FROM #__extensions AS a, ".$this->db->nameQuote($this->table)." AS b "
					. " WHERE b.".$this->db->nameQuote('mambot_id')."=a.".$this->db->nameQuote('extension_id')
					. " AND a.".$this->db->nameQuote('enabled').'=1 AND a.type=\'plugin\' '
					. $published
					. $type
					. " AND a.".$this->db->nameQuote('element')." !=".$this->db->Quote('jom_comment_bot')
					. " ORDER BY a.".$this->db->nameQuote('ordering')
					
				: "SELECT a.".$this->db->nameQuote('element').", a.".$this->db->nameQuote('ordering')
					. " FROM ".$this->db->nameQuote($this->_plugins)." AS a, ".$this->db->nameQuote($this->table)." AS b "
					. " WHERE b.".$this->db->nameQuote('mambot_id')."=a.".$this->db->nameQuote('id')
					. " AND a.".$this->db->nameQuote('published').'=1'
					. $published
					. $type
					. " AND a.".$this->db->nameQuote('element')." !=".$this->db->Quote('jom_comment_bot')
					. " ORDER BY a.".$this->db->nameQuote('ordering');

		$this->db->setQuery($strSQL);
		return $this->db->loadObjectList();
	}
	
	function getTotal()
	{
		$query	= (JVERSION >= 1.6) 
				? 'SELECT COUNT(*) FROM #__extensions '
					. 'WHERE '.$this->db->nameQuote('enabled').'=1 AND type=\'plugin\' '
					. 'AND '.$this->db->nameQuote('folder').'='.$this->db->Quote("content").' '
					. 'AND '.$this->db->nameQuote('element').' != '. $this->db->Quote("jom_comment_bot")
				: 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( $this->_plugins ) . ' '
					. 'WHERE '.$this->db->nameQuote('published').'=1 '
					. 'AND '.$this->db->nameQuote('folder').'='.$this->db->Quote("content").' '
					. 'AND '.$this->db->nameQuote('element').' != '. $this->db->Quote("jom_comment_bot");
		$this->db->setQuery( $query );
		
		return $this->db->loadResult();
	}
	
	function get($limitstart , $limit)
	{
		$limiter = ($limit <= 0) ? '' : "LIMIT {$limitstart}, {$limit}";

		$strSQL = (JVERSION >= 1.6) 
			? "SELECT a.".$this->db->nameQuote('name').", b.".$this->db->nameQuote('mambot_id').", b.".$this->db->nameQuote('my_published')
				. " FROM #__extensions AS a, ".$this->db->nameQuote($this->table)." AS b "
				. "WHERE b.".$this->db->nameQuote('mambot_id').'=a.'.$this->db->nameQuote('extension_id')
				. "AND a.".$this->db->nameQuote('enabled')."=1 "
				. "AND a.".$this->db->nameQuote('folder').'='.$this->db->Quote('content')
				. " AND a.".$this->db->nameQuote('element').' != '.$this->db->Quote('jom_comment_bot')
				. $limiter
				
			: "SELECT a.".$this->db->nameQuote('name').", b.".$this->db->nameQuote('mambot_id').", b.".$this->db->nameQuote('my_published')
				. " FROM ".$this->db->nameQuote($this->_plugins)." AS a, ".$this->db->nameQuote($this->table)." AS b "
				. "WHERE b.".$this->db->nameQuote('mambot_id').'=a.'.$this->db->nameQuote('id')
				. "AND a.".$this->db->nameQuote('published')."=1 "
				. "AND a.".$this->db->nameQuote('folder').'='.$this->db->Quote('content')
				. " AND a.".$this->db->nameQuote('element').' != '.$this->db->Quote('jom_comment_bot')
				. $limiter ;

		$this->db->setQuery($strSQL);
		return $this->db->loadObjectList();
	}

	function initPlugins($type = 'content')
	{
		if($type == 'content')
			$type	= "AND a.".$this->db->nameQuote('folder')."=".$this->db->Quote('content');
		else
			$type	= "AND a.".$this->db->nameQuote('folder')."=".$this->db->Quote($type);

		$strSQL	= (JVERSION >= 1.6) 
				? "SELECT a.".$this->db->nameQuote('name').", a.".$this->db->nameQuote('extension_id')."as id"
					." FROM #__extensions AS a "
					. "LEFT OUTER JOIN ".$this->db->nameQuote($this->table)." AS b "
					. "ON (a.".$this->db->nameQuote('extension_id').'=b.'.$this->db->nameQuote('mambot_id').") "
					. "WHERE b.".$this->db->nameQuote('mambot_id')." IS NULL "
					. $type
					. "AND a.".$this->db->nameQuote('enabled')."=1 AND a.".$this->db->nameQuote('type')."='plugin' "
					. "AND a.".$this->db->nameQuote('element')." !=".$this->db->Quote('jom_comment_bot')
					
				: "SELECT a.".$this->db->nameQuote('name').", a.".$this->db->nameQuote('id')
					." FROM ".$this->db->nameQuote($this->_plugins)." AS a "
					. "LEFT OUTER JOIN ".$this->db->nameQuote($this->table)." AS b "
					. "ON (a.".$this->db->nameQuote('id').'=b.'.$this->db->nameQuote('mambot_id').") "
					. "WHERE b.".$this->db->nameQuote('mambot_id')." IS NULL "
					. $type
					. "AND a.".$this->db->nameQuote('published')."=1 "
					. "AND a.".$this->db->nameQuote('element')." !=".$this->db->Quote('jom_comment_bot');

		$this->db->setQuery($strSQL);
		$plugins	= $this->db->loadObjectList();
		
		if($plugins)
		{
			foreach($plugins as $plugin)
			{
				$strSQL	= "INSERT INTO ".$this->db->nameQuote($this->table)." SET ".$this->db->nameQuote('mambot_id').'='.$this->db->Quote($plugin->id);
				$this->db->setQuery($strSQL);
				$this->db->query();
			}
		}
	}
}
