<?php
/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

$images		= rtrim( JURI::root() , '/' ) . '/administrator/components/com_myblog/assets/images';

require_once( MY_LIBRARY_PATH . DS . 'optionsetup.php' );
$opt = new MYOptionSetup();
?>

<form method="POST" name="adminForm">
<div id="myBlogTab">
	<ul id="preferenceTab">
		<li>
			<a href="#general">
				General
			</a>
		</li>
		<li>
			<a href="#permission">
				Permissions
			</a>
		</li>
		<li>
			<a href="#mediabrowser">
				Media
			</a>
		</li>
		<li>
			<a href="#workflow">
				Workflow &amp; Integrations
			</a>
		</li>
		<li>
			<a href="#layout">
				Layout
			</a>
		</li>
		<li>
			<a href="#mydashboard">
				Dashboard
			</a>
		</li>
		<!-- <li><a href="#sef_support"><span>SEF Options</span></a></li> -->
	</ul>

<?php require_once( MY_ADMIN_COM_PATH . DS . "templates/general.php"); ?>
<div id="general"><?PHP echo $opt->get_html();?></div>
<?php require_once( MY_ADMIN_COM_PATH . DS . "templates/permission.php"); ?>
<div id="permission"><?php echo $opt->get_html();?></div>
<?php require_once( MY_ADMIN_COM_PATH . DS . "templates/mediabrowser.php"); ?>
<div id="mediabrowser"><?php echo $opt->get_html();?></div>
<?php require_once( MY_ADMIN_COM_PATH . DS . "templates/workflow.php"); ?>
<div id="workflow"><?php echo $opt->get_html();?></div>
<?php require_once( MY_ADMIN_COM_PATH . DS . "templates/layout.php"); ?>
<div id="layout"><?php echo $opt->get_html();?></div>
<?php require_once( MY_ADMIN_COM_PATH . DS . "templates/dashboard.php"); ?>
<div id="mydashboard"><?php echo $opt->get_html();?></div>

<?php
////////////////////////////////////////////////////////////////////////////
// URL SEF
////////////////////////////////////////////////////////////////////////////
$opt    = null;
$opt    = new MYOptionSetup();
$opt->add_section('URL SEF');

$temp_lists = array();
$temp_lists[0] = '/myblog/[article title].html';
$temp_lists[1] = '/myblog/[blogger name]/[article title].html';

$opt->add(
			array(
					'type' 		=> 'select',
					'name' 		=> 'sefstyle',
					'value' 	=> $temp_lists,
					'selected'  => $config->get('sefstyle'),
					'size'      => 1,
					'title' 	=> 'Blog view url style',
					'desc'  	=> 'Select the default template for My Blog.'
				)
		);


?>
	<!-- <div id="sef_support"><?php echo $opt->get_html();?></div> -->
</div>
<input type="hidden" name="option" value="com_myblog">
<input type="hidden" name="task" value="savesettings">
<input type="hidden" name="boxchecked" value="0">
</form>
