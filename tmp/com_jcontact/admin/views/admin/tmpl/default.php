<?php
/************************************************************/
/* Title……..: J!Contact
/* Description..: An integration of iContact and Joomla
/* Author…….: Joomlashack LLC
/* Version……: For Joomla! 1.5.x Stable ONLY
/* Created……: 04/13/07
/* Contact……: support@joomlashack.com
/* Copyright….: Copyright© 2007 Joomlashack LLC. All rights reserved.
/* License……: Commercial
/************************************************************/

defined('_JEXEC') or die('Restricted access');

$user =& JFactory::getUser();
JHTML::_('behavior.tooltip');

JToolBarHelper::title( JText::_( 'JContact Admin (wrapper)' ), 'generic.png' );

JToolBarHelper::save();
JToolBarHelper::cancel();

$editor =& JFactory::getEditor();
?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	<?php
	echo $editor->save( 'mailcont' );
	?>
	
	submitform( pressbutton );
}
-->
</script>

<form action="index.php" method="post" name="adminForm">

<iframe src="http://joomlashack.icontact.com" width="100%" height="500"></iframe>

<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="admin" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>