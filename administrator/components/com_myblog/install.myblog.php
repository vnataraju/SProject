<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

define( "SITE_ROOT_PATH", JPATH_ROOT );

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.archive' );

function com_install()
{
	global $_VERSION, $option, $my;
	
	$installername = 'Myblog_installer'.( (JVERSION >= 1.6) ? '_16' : '_15' ) ;
	$installer = new $installername;
	
	ob_start();
	
	$mainframe	=& JFactory::getApplication();
	
	// Include the admin functions
	require_once (SITE_ROOT_PATH. "/administrator/components/com_myblog/functions.admin.php");
	
	// Check if older Jom COmment is installed.
	if(myCheckOldJomComment())
	{
		echo '<h3 style="color: red;">Installation FAILS! Older version of Jom Comment detected!</h3>';
		echo "<p>Please update your version of Jom Comment before proceeding with the installation of My Blog!</p>";
		return;
	}

	return $installer->run();
}

//==================================================
// BASE INSTALLER
//==================================================
class Myblog_installer{

	protected $db = NULL;
	protected $default_section = 'MyBlog';
	protected $default_category = 'MyBlog';
	protected $config = NULL;

	function __construct(){
		$this->db =& JFactory::getDBO();
	}
		
	/**
	* Main function to run stuff
	*/
	function run(){
	
		echo '<p><img src="'.rtrim( JURI::root() , '/').'/administrator/components/com_myblog/assets/images/logo.png" alt="logo" /></p>';
		echo '<p><strong>My Blog</strong><br/><code><br />';
	
		$this->saveAdminIcon();
		
		$this->myFixDashboardLinks();
		$this->myFixMenuLinks();
		$this->myFixDBTables();
		
		$this->prepareDefaultSection();
		$this->prepareDefaultCategory();
		$this->upgradeFeedBurner();
		$this->upgradeCategoryTable();
		$this->getMyblogConfig();
		$this->checkAzrulSystemMambot();
		$this->checkAzrulVideoBot();
		
		echo '<br/>';
		echo 'Installation completed. <br/> Thank you for using MyBlog! <br/>Click the <a href="index.php?option=com_myblog">here</a> to configure MyBlog.<br/><br/>';
		echo '<br />
			</code>
  			<font class="small">&copy; Copyright 2011 by Azrul.com<br />
  			This component is copyrighted commercial software. Distribution is prohibited.</font></p>
			<p><a href="http://www.azrul.com">www.azrul.com</a></p><br /><br /><br /><br />';
	}
	
	function sysBotGetVersion(){return 0;}
	
	function myFixDashboardLinks(){}
	
	function myFixMenuLinks(){}
	
	/** 
	 * Fix myblog related tables
	 */ 
	function myFixDBTables()
	{		
		$query	= 'SHOW FIELDS FROM `#__myblog_categories`';
		
		// Make sure the tags are unique
		$this->db->setQuery( $query );
		$fields = $this->db->loadObjectList();
	
		// Add missing fields in the table
		if(empty($fields[1]->Key))
		{
			$this->db->setQuery("ALTER TABLE `#__myblog_categories` ADD UNIQUE (`name`)");
			$this->db->query();
		}
	}

	function saveAdminIcon(){return true;}
	
	function prepareDefaultSection(){return 0;}
	
	function prepareDefaultCategory(){return 0;}
	
	function upgradeFeedBurner(){
		// Upgrade for feedburner
		$fields	= $this->db->getTableFields( '#__myblog_user' );
		
		if(!empty($fields))
		{
			if(!array_key_exists("feedburner", $fields['#__myblog_user'] ))
			{
				// Add new 'feedburner' column
				$strSQL = "ALTER TABLE `#__myblog_user` ADD `feedburner` TEXT NOT NULL";
				$this->db->setQuery($strSQL);
				$this->db->query();
			}
	
			if(!array_key_exists("googlegears", $fields['#__myblog_user'] ))
			{
				// Add new 'googlegears' column
				$strSQL = "ALTER TABLE `#__myblog_user` ADD `googlegears` INT NOT NULL DEFAULT '0'";
				$this->db->setQuery($strSQL);
				$this->db->query();
			}
	
			if(!array_key_exists("style", $fields['#__myblog_user'])){
				// Add new 'feedburner' column
				$strSQL = "ALTER TABLE `#__myblog_user` ADD `style` TEXT NOT NULL";
				$this->db->setQuery($strSQL);
				$this->db->query();
			}
			
			if(!array_key_exists("title", $fields['#__myblog_user'] )){
				// Add new 'feedburner' column
				$strSQL = "ALTER TABLE `#__myblog_user` ADD `title` TEXT NOT NULL";
				$this->db->setQuery($strSQL);
				$this->db->query();
			}
		}
	}

	function upgradeCategoryTable(){
		// Upgrade myblog_categories table structure.
		$fields	= $this->db->getTableFields( '#__myblog_categories' );
		
		if(!empty($fields))
		{
			if(!array_key_exists('default', $fields['#__myblog_categories'] ))
			{
				$strSQL	= 'ALTER TABLE `#__myblog_categories` ADD `default` TINYINT NOT NULL';
				$this->db->setQuery($strSQL);
				$this->db->query();
				
				$strSQL	= 'ALTER TABLE `#__myblog_categories` ADD INDEX ( `default` )';
				$this->db->setQuery($strSQL);
				$this->db->query();			
			}
	
			if(!array_key_exists('slug', $fields['#__myblog_categories'] ))
			{
				$strSQL	= 'ALTER TABLE `#__myblog_categories` ADD `slug` VARCHAR(255) NOT NULL';
				$this->db->setQuery($strSQL);
				$this->db->query();
			}
		}
	}
	
	function getMyblogConfig(){
		if($this->config === NULL){
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_myblog' . DS . 'config.myblog.php' );
			$this->config = new MYBLOG_Config();	
		}
		return $this->config;
	}
	
	function checkAzrulSystemMambot(){}
	
	function checkAzrulVideoBot(){}
}

//==================================================
// INSTALLER FOR 1.5 or less
//==================================================
class Myblog_installer_15 extends Myblog_installer{
	
	function sysBotGetVersion()
	{
		$version = 0;
		
		// Read the file to see if it's a valid component XML file
		$parser		=& JFactory::getXMLParser('Simple');
		$path		= JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'azrul.system.xml';
		
		if( JFile::exists( $path ) )
		{
			$parser->loadFile($path);
			$document	=& $parser->document;
			
			$element	=& $document->getElementByPath('version' , 1 );
			$version	= isset( $element ) ? $element->data() : '';
		}
		
		return doubleval( $version );
	}
	
	/**
	 * Fix link to the dashboard in the user menu.
	 */		 		
	function myFixDashboardLinks()
	{	
		$query	= "SELECT `id` FROM #__components WHERE `name`='My Blog'";
		$this->db->setQuery( $query );
		
		$comid	= $this->db->loadResult();
		
		$query	= "SELECT count(*) FROM #__menu 
			WHERE 
				`menutype`='usermenu' 
				AND `link` LIKE '%index.php?option=com_myblog%' 
				AND `type`='component' 
				AND `componentid`='$comid' ";
		
		$this->db->setQuery( $query );
		
		// Make sure it is there
		$linkExist = $this->db->loadResult();
		
		// If no correct one is found, delete all myblog links in usermenu
		// and create a new one
		if(!$linkExist)
		{
			$query = "DELETE FROM #__menu 
				WHERE
					`menutype`='usermenu' 
					AND `link` LIKE '%index.php?option=com_myblog%'";
			$this->db->setQuery($query);
			$this->db->query();
			
			// Only Joomla 1.5 has menu alias
			$menuAlias	= " `alias`='myblog-admin', ";
			$type       = "component";
	
			$query = "INSERT INTO `#__menu`
				SET 
					`name`= 'My Blog Dashboard',
					{$menuAlias}
					`menutype`='usermenu',
					`link`='index.php?option=com_myblog&task=adminhome',
					`type`='{$type}',
					`access`='1',
					`published`='1',
					`componentid`='$comid'";
			
			
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
	
	/**
	 * If there is a component that previosuly link to MyBlog, we need to update 
	 * its component id  
	 */ 
	function myFixMenuLinks()
	{		
		$query	= "SELECT `id` FROM #__components WHERE `name`='My Blog'";
		$this->db->setQuery( $query );
		
		$comid	= $this->db->loadResult();
		$query = "UPDATE #__menu SET `componentid`='$comid' WHERE `link` LIKE 'index.php?option=com_myblog%'";
		$this->db->setQuery($query);
		$this->db->query();
	}
	
	function checkAzrulSystemMambot(){
		# check if azrul system mambot is installed (for ajax stuff)
		$src	= JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'azrul.zip';		
		
		//taken from sysbotupgrade()
		$installIt	= false;    
		$botVersion = $this->sysBotGetVersion();
		
		if($botVersion != 0 )
		{
			JFolder::delete( JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'pc_includes' );
			JFile::delete( JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'azrul.system.php' );
			JFile::delete( JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'azrul.system.xml' );
	
			$strSQL = "DELETE FROM #__plugins WHERE type='plugin' AND element='azrul.system'";
			$this->db->setQuery( $strSQL );
			$this->db->query();	
		}
	
		// No system bot detected, install it	
		//if($botVersion == 0)
		//{
			echo '<img src="images/tick.png"> Installing Azrul.com System mambots <br/>';
			
			$destination = JPATH_ROOT . DS . 'plugins' . DS . 'system' ;	
					
			JArchive::extract( $src , $destination );
			
			$strSQL = "DELETE FROM #__plugins WHERE element='azrul.system' OR `name`='Azrul.com System Mambot'";
			$this->db->setQuery($strSQL);
			$this->db->query();
			
			$strSQL = "INSERT INTO `#__plugins` SET `name`='Azrul.com System Mambot', "
					. "`element`='azrul.system', "
					. "`folder`='system', "
					. "`access`='0', "
					. "`ordering`='1', "
					. "`published`='1'";
			$this->db->setQuery($strSQL);
			$this->db->query();
		//}
	}
	
	# Check if azvideo bot is installed
	function checkAzrulVideoBot(){

		$src	= JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'azvideobot.zip';
	
		//taken from installAzVideoBot() ====================
		$exists		= false;
		$strSQL = "SELECT * FROM #__plugins WHERE `element`='azvideobot' AND `folder`='content'";
		$this->db->setQuery($strSQL);
		
		if($this->db->loadResult())
		{
			$exists = true;
	
			if( JFile::exists( JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS . 'azvideobot.php' ) )
			{
				JFile::delete( JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS  . 'azvideobot.php' );
			}
			
			if( JFile::exists( JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS  . 'azvideobot.xml' ) )
			{
				JFile::delete( JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS  . 'azvideobot.xml' );
			}
		}
	
		echo '<img src="images/tick.png"> Installing Azrul Videobot<br />';
		
		$destination = JPATH_ROOT . DS . 'plugins' . DS . 'content';	
		
		JArchive::extract($src , $destination);
	
		if(!$exists)
		{
			$query  	= "INSERT INTO #__plugins SET `name`='Azrul Video Mambot', "
						. "`element`='azvideobot', `folder`='content', "
						. "`access`='0', `ordering`='0', `published`='1', `params`='height=350px
						  width=425px'";
			$this->db->setQuery( $query );
			$this->db->query();
		}
		
		unset($archive);
	}
	
	function saveAdminIcon(){
		echo '<img src="images/tick.png"> Creating database tables<br/>';
		$q = "UPDATE #__components 
				SET admin_menu_img='../administrator/components/com_myblog/assets/images/myblog-icon.png' 
				WHERE admin_menu_link='option=com_myblog'";
			
		$this->db->setQuery($q);
		return $this->db->query();
	}
	
	function prepareDefaultSection(){
		
		$this->db->setQuery("SELECT id from #__sections WHERE title='".$this->default_section."'");
		$sectionid = $this->db->loadResult();
		
		if ($sectionid)
		{
			echo '<img src="images/tick.png"> Previous MyBlog section found. Republishing section.<br/>';
			$this->db->setQuery("UPDATE #__sections SET published = 1 WHERE id = $sectionid");
			$this->db->query();
		}
		else
		{
			echo '<img src="images/tick.png"> Creating MyBlog section. <br/>';
			$this->db->setQuery("INSERT INTO #__sections SET published = 1, title='.".$this->default_section."', name='".$this->default_section."', scope='content', count=1, ordering=1");
			$this->db->query();
			$sectionid = $this->db->insertid();
		}
		return $sectionid;
	}
	
	function prepareDefaultCategory(){
		$this->db->setQuery("SELECT id from #__categories WHERE title='".$this->default_category."'");
		$catid = $this->db->loadResult();
		
		if ($catid)
		{
			$this->db->setQuery("UPDATE #__categories SET published = 1 WHERE id = $catid");
			$this->db->query();
		}
		else
		{
			$this->db->setQuery("INSERT INTO #__categories SET published = 1, title='".$this->default_category."', name='".$this->default_category."', section=$sectionid, ordering=1");
			$this->db->query();
			$catid = $this->db->insertid();
		}
		return $catid;
	}
}

//==================================================
// INSTALLER FOR 1.6 or later
//==================================================
class Myblog_installer_16 extends Myblog_installer{
	
	function saveAdminIcon(){
		echo '<img style="vertical-align:middle" src="'.JURI::base().'components/com_myblog/assets/images/selected.gif"> Creating database tables<br/>';
	}
	
	/**
	 * If there is a component that previosuly link to MyBlog, we need to update 
	 * its component id  
	 */ 
	function myFixMenuLinks()
	{		
		$query	= "SELECT `extension_id` FROM #__extensions WHERE `name`='My Blog'";
		$this->db->setQuery( $query );
		
		$comid	= $this->db->loadResult();
		$query = "UPDATE #__menu SET `component_id`='$comid' WHERE `link` LIKE 'index.php?option=com_myblog%'";
		$this->db->setQuery($query);
		$this->db->query();
	}
	
	
	/**
	 * Fix link to the dashboard in the user menu.
	 */		 		
	function myFixDashboardLinks()
	{	
		return;
		$query	= "SELECT `extension_id` FROM #__extensions WHERE `element`='com_myblog' AND `type`='component'";
		$this->db->setQuery( $query );
		
		$comid	= $this->db->loadResult();
		
		$query	= "SELECT count(*) FROM #__menu 
			WHERE 
				`menutype`='mainmenu' 
				AND `link` LIKE '%index.php?option=com_myblog%' 
				AND `type`='component' 
				AND `component_id`='$comid' ";
		
		$this->db->setQuery( $query );
		
		// Make sure it is there
		$linkExist = $this->db->loadResult();
		
		// If no correct one is found, delete all myblog links in usermenu
		// and create a new one
		if(!$linkExist)
		{
			$query = "DELETE FROM #__menu 
				WHERE
					`menutype`='usermenu' 
					AND `link` LIKE '%index.php?option=com_myblog%'";
			$this->db->setQuery($query);
			$this->db->query();
			
			// Only Joomla 1.5 has menu alias
			$menuAlias	= " `alias`='myblog-admin', ";
			$type       = "component";
	
			$query = "INSERT INTO `#__menu`
				SET 
					`name`= 'My Blog Dashboard',
					{$menuAlias}
					`menutype`='usermenu',
					`link`='index.php?option=com_myblog&task=adminhome',
					`type`='{$type}',
					`access`='1',
					`published`='1',
					`componentid`='$comid'";
			
			
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
	
	
	function sysBotGetVersion()
	{
		$version = 0;
		
		// Read the file to see if it's a valid component XML file
		$parser		=& JFactory::getXMLParser('Simple');
		$path		= JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'azrul.system' . DS . 'azrul.system.xml';
		
		if( JFile::exists( $path ) )
		{
			$parser->loadFile($path);
			$document	=& $parser->document;
			
			$element	=& $document->getElementByPath('version' , 1 );
			$version	= isset( $element ) ? $element->data() : '';
		}
		
		return doubleval( $version );
	}
	
	
	# Check if azvideo bot is installed
	function checkAzrulVideoBot(){

		$zipSrc	= JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'azvideobot.zip';
	
		$exists		= false;
		$strSQL = "SELECT * FROM #__extensions WHERE `element`='azvideobot' AND `folder`='content'";
		$this->db->setQuery($strSQL);
		
		if($this->db->loadResult())
		{
			$exists = true;
	
			if( JFile::exists( JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS . 'azvideobot' . DS . 'azvideobot.php' ) )
			{
				JFile::delete( JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS .'azvideobot' . DS . 'azvideobot.php' );
			}
			
			if( JFile::exists( JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS . 'azvideobot' . DS . 'azvideobot.xml' ) )
			{
				JFile::delete( JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS . 'azvideobot' . DS . 'azvideobot.xml' );
			}
		}
	
		echo '<img style="vertical-align:middle" src="'.JURI::base().'components/com_myblog/assets/images/selected.gif"> Installing Azrul Videobot<br />';
		
		$destination = JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS . 'azvideobot';	
		
		JArchive::extract($zipSrc , $destination);
	
		if(!$exists)
		{
			$xml = $destination . DS . 'azvideobot.xml';
			jimport( 'joomla.application.helper' );
			$manifestcache = json_encode(JApplicationHelper::parseXMLInstallFile($xml));
			
			$query  	= "INSERT INTO #__extensions SET `name`='Azrul Video Mambot', "
						. "`element`='azvideobot', `folder`='content', "
						. "`manifest_cache`=".$this->db->Quote($manifestcache).", "
						. "`access`='1', `ordering`='0', `enabled`='1', `type`='plugin', `params`='height=350px
						  width=425px'";
			$this->db->setQuery( $query );
			$this->db->query();
		}
		
		unset($archive);
	}
	
	function checkAzrulSystemMambot(){
		
		# check if azrul system mambot is installed (for ajax stuff)
		$src	= JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'azrul.zip';		
		
		//taken from sysbotupgrade()
		$installIt	= false;    
		$botVersion = $this->sysBotGetVersion();
		
		if($botVersion != 0 )
		{
			JFolder::delete( JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'azrul.system' . DS . 'pc_includes' );
			JFile::delete( JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'azrul.system' . DS . 'azrul.system.php' );
			JFile::delete( JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'azrul.system' . DS . 'azrul.system.xml' );
	
			$strSQL = "DELETE FROM #__extensions WHERE type='plugin' AND element='azrul.system'";
			$this->db->setQuery( $strSQL );
			$this->db->query();	
		}
	
		// No system bot detected, install it	
		//if($botVersion == 0)
		{
			echo '<img style="vertical-align:middle" src="'.JURI::base().'components/com_myblog/assets/images/selected.gif"> Installing Azrul.com System mambots <br/>';
			
			$destination = JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'azrul.system';	
					
			JArchive::extract( $src , $destination );
	
			$xml = $destination . DS . 'azrul.system.xml';
	
			jimport( 'joomla.application.helper' );
			$manifestcache = json_encode(JApplicationHelper::parseXMLInstallFile($xml));
	
			$strSQL = "DELETE FROM #__extensions WHERE element='azrul.system' OR `name`='Azrul.com System Mambot'";
			$this->db->setQuery($strSQL);
			$this->db->query();
			
			$strSQL = "INSERT INTO `#__extensions` SET `name`='Azrul.com System Mambot', "
					. "`element`='azrul.system', "
					. "`folder`='system', "
					. "`access`='1', "
					. "`type`='plugin',"
					. "`ordering`='1', "
					. "`manifest_cache`=".$this->db->Quote($manifestcache).", "
					. "`enabled`='1'";
			$this->db->setQuery($strSQL);
			$this->db->query();
		}
	}
	
	/**
	* We need to simulate 2 levels of Section and Category for 1.6 as in Joomla 1.5 
	*/
	function prepareDefaultSection(){
		$this->db->setQuery("SELECT id from #__categories WHERE title='".$this->default_section."' AND parent_id=1");
		$sectionid = $this->db->loadResult();
		
		if ($sectionid)
		{
			echo '<img style="vertical-align:middle" src="'.JURI::base().'components/com_myblog/assets/images/selected.gif"> Previous MyBlog section found. Republishing section.<br/>';
			$this->db->setQuery("UPDATE #__categories SET published = 1 WHERE id = $sectionid");
			$this->db->query();
		}
		else
		{
			require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'.DS.'category.php';
		
			$catmodel = new CategoriesModelCategory();

			echo '<img style="vertical-align:middle" src="'.JURI::base().'components/com_myblog/assets/images/selected.gif"> Creating MyBlog section. <br/>';
			
			$newsection = array
			(
				'id' => 0,
				'parent_id' => 1,
				'extension' => 'com_content',
				'title' => $this->default_section,
				'alias' => '',
				'note' => '',
				'description' => "MyBlog's default section",
				'published' => 1,
				'access' => 1,
				'metadesc' => '',
				'metakey' => '',
				'created_user_id' => 0,
				'language' => '*',
				'rules' => array(
						'core.create' => array(),
						'core.delete' => array(),
						'core.edit' => array(),
						'core.edit.state' => array(),
						'core.edit.own' => array()
					),
			
				'params' => array(
						'category_layout' => '', 
						'image' => ''
				),
				'metadata' => array(
						'author' => '',
						'robots' => ''
					)
			);
			$saved = $catmodel->save($newsection);
			$this->db->setQuery("SELECT id from #__categories WHERE title='".$this->default_section."' AND parent_id=1");
			$sectionid = $this->db->loadResult();
		}
		return $sectionid;
	}
	
	function prepareDefaultCategory(){}
}

?>

<?php
/**
* Legacy Myblog function. Keep it for now 
*/
function showInstallWizard()
{
	$mainframe	=& JFactory::getApplication();
	$db			=& JFactory::getDBO();
?>
<link href="<?php echo rtrim( JURI::root() , '/' );?>/components/com_myblog/css/admin_style.css" rel="stylesheet" type="text/css">
<table width="100%" border="0" cellspacing="4" cellpadding="4">
    <tr>
        <td><table width="100%" class="mytable" border="0" cellspacing="2" cellpadding="2">
                <tr>
                    <th>Step 1 : Create a link to the blog-writing page in the user's menu </th>
                </tr>
                <tr>
                    <td><blockquote>You need to create a link to your new blog component in one of your menu. </blockquote></td>
                </tr>
                <tr>
                    <td><blockquote>
                            <form name="form1">
                                <label for="select"></label>
                                <select name="menutype" id="menutype">
                                <?php
									$db->setQuery("SELECT distinct(menutype) from #__menu");
									$menutypes = $db->loadObjectList();
									foreach($menutypes as $menu)
									{
										echo "<option value=\"$menu->menutype\">$menu->menutype</option>";
									}
								?>
                                </select>
                                <label for="Submit"></label>
                                <input onclick = "var w;w=window.open('index2.php?option=com_menus&menutype=mainmenu&task=edit&type=url&link=hello', 'menu1');

												el.value='MyBlog Entries';
												el=w.document.getElementByName('link');
												el.setAttribute('value', 'index.php?option=com_myblog&task=startadmin');" type="button" name="button" value="Create the menu now." id="button1">
                            </form>
                        </blockquote></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <table width="100%"  class="mytable"  border="0" cellspacing="2" cellpadding="2">
                <tr>
                    <th valign="middle">Step 2 : Create a link to the blog-writig page in the user's menu </th>
                </tr>
                <tr>
                    <td><blockquote>You need to create a link to your new blog component in one of your menu. </blockquote></td>
                </tr>
                <tr>
                    <td><blockquote>
                            <form name="form1" method="post" action="" target="_blank">
                                <label for="select"></label>
                                <select name="select" id="select">
                                    <?php
										$db->setQuery("SELECT distinct(menutype) from #__menu");
										$menutypes = $db->loadObjectList();
										foreach($menutypes as $menu)
										{
											echo "<option value=\"$menu->menutype\">$menu->menutype</option>";
										}
								?>
                                </select>
                                <label for="Submit"></label>
                                <input type="submit" name="Submit" value="Create the menu now." id="Submit">
                            </form>
                        </blockquote></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <table width="100%" class="mytable"  border="0" cellspacing="2" cellpadding="2">
                <tr>
                    <th>Step 3 : Make sure all the links are published </th>
                </tr>
                <tr>
                    <td><blockquote>You need to create a link to your new blog component in one of your menu. </blockquote></td>
                </tr>
                <tr>
                    <td><blockquote>
                            <form name="form2" method="post" action="">
                                <label for="label"></label>
                                <input type="submit" name="Submit2" value="Publish all the menu items" id="label">
                            </form>
                        </blockquote></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table></td>
    </tr>
</table>
<?php

}
