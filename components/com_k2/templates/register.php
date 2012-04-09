<?php
/**
 * @version		$Id: register.php 1492 2012-02-22 17:40:09Z joomlaworks@gmail.com $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<!-- K2 user register form -->
<?php if(isset($this->message)) $this->display('message'); ?>

<form action="<?php echo JRoute::_('index.php'); ?>" enctype="multipart/form-data" method="post" id="josForm" name="josForm" class="form-validate">
	<?php if($this->params->def('show_page_title',1)): ?>
	<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>
	<div id="k2Container" class="lk2AccountPage">
		<table class="admintable" cellpadding="0" cellspacing="0">
			<tr>
				<th colspan="2" class="k2ProfileHeading">
					<?php echo JText::_('K2_ACCOUNT_DETAILS'); ?>
				</th>
			</tr>
			<tr>
				<td class="key">
					<label id="namemsg" for="name"><?php echo JText::_('K2_NAME'); ?></label>
				</td>
				<td>
					<input type="text" name="<?php echo (K2_JVERSION=='16')?'jform[name]':'name'?>" id="name" size="40" value="<?php echo $this->escape($this->user->get( 'name' )); ?>" class="inputbox required" maxlength="50" />
					*
				</td>
			</tr>

			<tr>
				<td class="key">
					<label id="emailmsg" for="email"><?php echo JText::_('K2_EMAIL'); ?></label>
				</td>
				<td>
					<input type="text" id="email" name="<?php echo (K2_JVERSION=='16')?'jform[email1]':'email'?>" size="40" value="<?php echo $this->escape($this->user->get( 'email' )); ?>" class="inputbox required validate-email" maxlength="100" />
					*
				</td>
			</tr>

			
			<!-- Joomla! 1.6+ JForm implementation -->
			<?php if(K2_JVERSION == '16'): ?>
			<?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
				<?php if($fieldset->name != 'default'): ?>
				<?php $fields = $this->form->getFieldset($fieldset->name);?>
				<?php if (count($fields)):?>
					<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
					<tr>
						<th colspan="2" class="k2ProfileHeading">
							<?php echo JText::_($fieldset->label);?>
						</th>
					</tr>
					<?php endif;?>
					<?php foreach($fields as $field):// Iterate through the fields in the set and display them.?>
						<?php if ($field->hidden):// If the field is hidden, just display the input.?>
							<tr><td colspan="2"><?php echo $field->input;?></td></tr>
						<?php else:?>
							<tr>
								<td class="key">
									<?php echo $field->label; ?>
									<?php if (!$field->required && $field->type != 'Spacer'): ?>
										<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL');?></span>
									<?php endif; ?>
								</td>
								<td><?php echo $field->input;?></td>
							</tr>
						<?php endif;?>
					<?php endforeach;?>
				<?php endif;?>
				<?php endif; ?>
			<?php endforeach;?>
			<?php endif; ?>
			
		</table>
		
		<?php if($this->K2Params->get('recaptchaOnRegistration') && $this->K2Params->get('recaptcha_public_key')): ?>
		<label class="formRecaptcha"><?php echo JText::_('K2_ENTER_THE_TWO_WORDS_YOU_SEE_BELOW'); ?></label>
		<div id="recaptcha"></div>
		<?php endif; ?>
		
		<div class="k2AccountPageNotice"><?php echo JText::_('K2_REGISTER_REQUIRED'); ?></div>
		<div class="k2AccountPageUpdate">
			<button class="button validate" type="submit">
				<?php echo JText::_('K2_REGISTER'); ?>
			</button>
		</div>
	</div>
	<input type="hidden" name="option" value="<?php echo (K2_JVERSION=='16')?'com_users':'com_user'?>" />
	<input type="hidden" name="task" value="<?php echo (K2_JVERSION=='16')?'registration.register':'register_save'?>" />
	<input type="hidden" name="id" value="0" />
	<input type="hidden" name="gid" value="0" />
	<input type="hidden" name="K2UserForm" value="1" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
