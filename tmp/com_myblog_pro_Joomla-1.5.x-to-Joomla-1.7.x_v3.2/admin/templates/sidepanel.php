<?php
function showSidePanel()
{
	global $jax;
	
	$mainframe	=& JFactory::getApplication();
	
	$jax		= new JAX( AZRUL_SYSTEM_LIVE. '/pc_includes' );
	$jax->setReqURI( rtrim( JURI::root() , '/' ) . '/administrator/index.php?tmpl=component' );
	
	// Try login user.
	$user		=& JFactory::getUser();
	$db			=& JFactory::getDBO();
	showNewDashboard();
	
	$strSQL	= "SELECT session_id FROM #__session WHERE `userid`={$user->id}";
	$db->setQuery( $strSQL );
	$userId		= $user->id;
	$sessionId	= $db->loadResult();
	
	$link		= rtrim( JURI::root() , '/') . '/index.php?option=com_myblog&tmpl=component&task=write&tmpl=component&no_html=1&id=0&session=' . $userId . ':' . $sessionId;
	?> 	

	<div id="myBlogLogo">
	</div>
	<p class="version">
	<?php
	$parser		=& JFactory::getXMLParser('Simple');
	$path		= MY_ADMIN_COM_PATH . DS  . 'myblog.xml';
	$parser->loadFile($path);
	$document	=& $parser->document;
	$version	= 'N/A';
	$element	=& $document->getElementByPath('version' , 1 );
	$version	= isset( $element ) ? $element->data() : '';
	echo JText::sprintf( 'Version: %1$s' , $version );
	?>
	</p>

	<div id="myBlogNavigation">
	<ul>
		<li class="group"><a href="#">Post</a><span class="groupArrow"></span></li>
		<li><a href="javascript:void(0);" onclick="myAzrulShowWindow('<?php echo $link; ?>');" >Create new post</a></li>
		<li><a href="index.php?option=com_myblog">Manage post</a></li>
		<li><a href="index.php?option=com_myblog&task=draft&limitstart=0">Draft</a></li>
		<li class="group"><a href="#">Configuration</a><span class="groupArrow"></span></li>
		<li><a href="index.php?option=com_myblog&task=config">MyBlog preferences</a></li>
		<li><a href="index.php?option=com_myblog&task=contentmambots">Content plugins</a></li>
		<li><a href="index.php?option=com_myblog&task=maintenance">Maintenance</a></li>
		<li><a href="index.php?option=com_myblog&task=category">Tags</a></li>
	</ul>
<!-- 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td scope="col"> 
				<div><h3 align="center">Admin Panel</h3></div>
				<div class="sideNavTitle"><img hspace="2" style="vertical-align: middle;" src="components/com_myblog/images/Options_16x16.gif"/>Configuration</div> 
				<div class="sideNavContent"> <p><a href="index.php?option=com_myblog&task=config">General Settings </a></p>
	 			<p><a href="index.php?option=com_myblog&task=category">Tags</a></p> 
	 			<p><a href="index.php?option=com_myblog&task=contentmambots">Content Plugins Integration</a></p>
				<p><a href="index.php?option=com_myblog&task=maintenance">Maintenance</a></div> 
	 			
				<div class="sideNavTitle"><img hspace="2" style="vertical-align: middle;" src="components/com_myblog/images/Documents_16x16.gif"/>Manage Blog Entries</div>
				<div class="sideNavContent"> 
				<p><a href="javascript:void(0);" onclick="myAzrulShowWindow('<?php echo $link; ?>');">Write New Entry</a></p>
				<p><a href="index.php?option=com_myblog&task=draft&limitstart=0" >Drafts </a></p> 
				<p><a href="index.php?option=com_myblog&task=blogs&limitstart=0" >View all </a></p> 
				<p><a href="index.php?option=com_myblog&task=blogs&limitstart=0&publish=0" >View unpublished entries</a></p> 
	 			</div>
	 
	 			<div class="sideNavTitle"><img hspace="2" style="vertical-align: middle;" src="components/com_myblog/images/Information_16x16.gif"/>About / Support </div> <div class="sideNavContent"> 
	 			<p><a href="index.php?option=com_myblog&task=about">About My Blog </a></p> 
				<p><a href="index.php?option=com_myblog&task=latestnews">Check for latest news</a></p>
				<p><a href="index.php?option=com_myblog&task=license">License Information</a> </p> 
				</div>
			</td>
		</tr>
	</table> -->
	</div>
	<!-- SIDENAV END HERE -->

<?php
}
?>