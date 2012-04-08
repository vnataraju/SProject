   <?php
/************************************************************/
/* Title��..: J!Contact
/* Description..: An integration of iContact and Joomla
/* Author��.: Joomlashack LLC
/* Version��: For Joomla! 1.5.x Stable ONLY
/* Created��: 04/13/07
/* Contact��: support@joomlashack.com
/* Copyright�.: Copyright� 2007 Joomlashack LLC. All rights reserved.
/* License��: Commercial
/************************************************************/

defined('_JEXEC') or die('Restricted access');

$user =& JFactory::getUser();
JHTML::_('behavior.tooltip');

JToolBarHelper::title( JText::_( 'JContact Confirmation Email' ), 'generic.png' );

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
	echo $editor->save( 'mailcont2' );
	?>
	
	submitform( pressbutton );
}
-->
</script>

<form action="index.php" method="post" name="adminForm">

<div width="100%">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Email Details' ); ?></legend>

		<table class="admintable" cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td>
				<label for="name">
					<?php echo JText::_( 'Email subject - Registration' ); ?>:
				</label>
			</td>
			<td >
				<input class="inputbox" type="text" name="mailsubj2" id="mailsubj2" size="60" maxlength="255" value="<?php echo $this->config_data->mailsubj2; ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="name">
					<?php echo JText::_( 'Email content - Registration' ); ?>:
				</label>
			</td>
			<td>
				<?php
				echo $editor->display( 'mailcont2',  $this->config_data->mailcont2 , '100%', '550', '75', '20' ) ;
				?>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="id" value="<?php echo $this->config_data->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="email" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>