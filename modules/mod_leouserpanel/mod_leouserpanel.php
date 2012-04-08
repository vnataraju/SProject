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
 JHTML::_('behavior.modal');
// Include the syndicate functions only once
require_once dirname(__FILE__).DS.'helper.php';
$theme		    =  $params->get( 'theme', '' );

modLeoUserPanelHelper::loadMediaFiles( $params, $module, $theme );
$loginPath 	    = modLeoUserPanelHelper::getItemLayoutPath( $module->module, $theme, 'login' );
$registerPath   = modLeoUserPanelHelper::getItemLayoutPath( $module->module, $theme, 'register' );
$logoutPath		= modLeoUserPanelHelper::getItemLayoutPath( $module->module, $theme, 'logout' );

$type	= modLeoUserPanelHelper::getType();
$return	= modLeoUserPanelHelper::getReturnURL($params, $type);
 

$view 			= JRequest::getCmd( "view" );
$layout 		= trim($theme).DS.'default';
$user 			= JFactory::getUser();
$loginedIn 		= $user->id>0?true:false;

require( JModuleHelper::getLayoutPath($module->module, $layout ) );


?>
<script type="text/javascript">
<?php if( $params->get("open_style", "") == "modal" ) : ?>
window.addEvent('domready', function() {
	 SqueezeBox.assign($$('a[rel=leo-boxed][href^=#]'), {

	 });
});
<?php else: ?>
var object = new LeoUserPanel( '<?php echo $params->get("open_style"); ?>' );
<?php endif;  ?>
</script>