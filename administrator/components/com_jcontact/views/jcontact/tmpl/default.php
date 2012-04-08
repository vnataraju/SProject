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

JToolBarHelper::title( JText::_( 'JContact Main Settings' ), 'generic.png' );

JToolBarHelper::save();
JToolBarHelper::cancel();

$editor =& JFactory::getEditor();
?>
<script language="javascript" type="text/javascript">
<!--

jQuery(function($) { 
	$jq("#show_for_all1").click(function () { 
		$jq("#optional_field").hide();
	});
	
	$jq("#show_for_all0").click(function () { 
		$jq("#optional_field").show();
	});
});

function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	submitform( pressbutton );
}

-->
</script>

<form action="index.php" method="post" name="adminForm">
<div class="col">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Main Settings' ); ?></legend>

		<table class="admintable">
		<tr>
			<td>
				<?php echo JText::_( 'Add newly registered users to JContact list?' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['regon']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="name">
					<?php echo JText::_( 'JContact registration list' ); ?>:
				</label>
			</td>
			<td>
				<div id="_maillist_text" style="float: left; width: 390px;"><?php echo $this->config_data->maillist_text; ?></div>
				<input class="inputbox" type="hidden" name="maillist_text" id="maillist_text" size="50" maxlength="255" value="<?php echo $this->config_data->maillist_text; ?>"
				<input class="inputbox" type="hidden" name="maillist" id="maillist" size="50" maxlength="255" value="<?php echo $this->config_data->maillist; ?>" />&nbsp;<a href="<?php echo $this->base_path; ?>/index3.php?option=com_jcontact&controller=categories" class="nyroModal"><strong><?php echo JText::_( 'Choose' ); ?></strong></a> 
			</td>
		</tr>
		<tr>
			<td>
				<label for="name">
					<?php echo JText::_( 'JContact username' ); ?>:
				</label>
			</td>
			<td >
				<input class="inputbox" type="text" name="username" id="username" size="60" maxlength="255" value="<?php echo $this->config_data->username; ?>" />
			</td>
		</tr>
				<tr>
			<td>
				<label for="name">
					<?php echo JText::_( 'JContact password' ); ?>:
				</label>
			</td>
			<td >
				<input class="inputbox" type="password" name="password" id="password" size="60" maxlength="255" value="<?php echo $this->config_data->password; ?>" />
			</td>
		</tr>
				<tr>
			<td>
				<label for="name">
					<?php echo JText::_( 'JContact API Key' ); ?>:
				</label>
			</td>
			<td >
				<input class="inputbox" type="text" name="apikey" id="apikey" size="60" maxlength="255" value="<?php echo $this->config_data->apikey; ?>" />&nbsp;&nbsp;&nbsp;<font color="red"><b><?php echo JText::_( 'Follow link' ); ?>:</b></font>
			</td>
		</tr>
		<tr>
			<td>
				<label for="name">
					<?php echo JText::_( 'JContact API URL' ); ?>:
				</label>
			</td>
			<td >
				<input class="inputbox" type="text" name="apiUrl" id="apiUrl" size="60" maxlength="255" value="<?php echo $this->config_data->apiUrl; ?>" />&nbsp;&nbsp;&nbsp;<?php echo JText::_( 'Link to iContact' ); ?>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="col">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Additional Settings' ); ?></legend>

		<table class="admintable">
		<tr>
			<td width="150">
				<?php echo JText::_( 'Sign up all who register' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['show_for_all']; ?>
			</td>
		</tr>
		<tr id="optional_field" <?php if($this->config_data->show_for_all == 1) echo 'style="display: none;"'; ?>>
			<td>
				<?php echo JText::_( 'Show optional sign up field' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['show_optional']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="name">
					<?php echo JText::_( 'Sign up message' ); ?>:
				</label>
			</td>
			<td >
				<textarea name="signup_message" id="signup_message" rows="5" cols="51" class="inputbox"><?php echo $this->config_data->signup_message; ?></textarea>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="id" value="<?php echo $this->config_data->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="jcontact" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>