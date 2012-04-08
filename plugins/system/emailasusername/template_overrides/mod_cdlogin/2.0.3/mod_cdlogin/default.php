<?php
/**
 * Core Design Login module for Joomla! 1.7
 * @author		Daniel Rataj, <info@greatjoomla.com>
 * @package		Joomla
 * @subpackage	Content
 * @category	Module
 * @version		2.0.3
 * @copyright	Copyright (C) 2007 - 2011 Great Joomla!, http://www.greatjoomla.com
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL 3
 *
 * This file is part of Great Joomla! extension.
 * This extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined('_JEXEC') or die;

// Scriptegrator check
if (!class_exists('JScriptegrator')) {
	echo JText::_('MOD_CDLOGIN_ENABLE_SCRIPTEGRATOR');
	return false;
} else {
	$JScriptegrator = JScriptegrator::getInstance('2.1.0');
	$JScriptegrator->importLibrary('highslide');
	if ($error = $JScriptegrator->getError()) {
		echo $error;
		return false;
	}
}

JHtml::_('behavior.keepalive');
?>

<?php if ($type == 'logout'): ?>
	<?php if ($cdlogin_border == 'top' or $cdlogin_border == "both"): ?>
	<div class="cdlogin_border-top"></div>
	<?php endif; ?>
	<div class="cdlogin-logout-greeting">
		<?php echo $name; ?>
		<a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html-logoutform', wrapperClassName: 'mod_cdlogin', outlineType: '<?php echo $outlineType; ?>', align: '<?php echo $align; ?>', anchor: '<?php echo $anchor; ?>', dimmingOpacity: <?php echo $dimmingOpacity; ?>, slideshowGroup: 'mod_cdlogin_logoutform' } )" title="<?php echo JText::_('MOD_CDLOGIN_BUTTON_LOGOUT'); ?>"></a>
	</div>
		<div class="highslide-html-content" id="highslide-html-logoutform" style="width: 350px">
			<div class="highslide-html-content-header">
				<div class="highslide-move"
					title="<?php echo JText::_('MOD_CDLOGIN_TITLE_MOVE'); ?>"><a href="#"
					onclick="return hs.close(this)" class="control"
					title="<?php echo
					JText::_('MOD_CDLOGIN_CLOSELABEL'); ?>"><?php echo JText::_('MOD_CDLOGIN_CLOSELABEL'); ?></a>
				</div>
			</div>
			
			<div class="highslide-body">
				<p class="cdlogin-bold"><?php echo JText::_('MOD_CDLOGIN_LOGOUT_CONFIRM'); ?></p>
				<div class="cdlogin-logoutform">
					<form action="index.php" method="post" name="form-login" id="form-login">
						<input type="submit" name="Submit" class="cdlogin-logoutbutton" value="<?php echo JText::_('MOD_CDLOGIN_BUTTON_LOGOUT'); ?>" title="<?php echo JText::_('MOD_CDLOGIN_BUTTON_LOGOUT'); ?>" />
						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.logout" />
						<input type="hidden" name="return" value="<?php echo $return; ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</form>
				</div>
			</div>
			<?php if (JText::_('MOD_CDLOGIN_LOGOUT_MESSAGE') == ''): ?> <?php else: ?>
				<div class="cdlogin_message_to_users"><span><?php echo JText::_('MOD_CDLOGIN_LOGOUT_MESSAGE'); ?></span></div>
				<div style="height: 5px"></div>
			<?php endif; ?>
		</div>
	<?php if ($cdlogin_border == 'bottom' or $cdlogin_border == "both"): ?>
	<div class="cdlogin_border-bottom"></div>
<?php endif; ?>
<?php else: ?>

<?php
$document = JFactory::getDocument(); // set document for next usage

$document->addScriptDeclaration("
	hs.Expander.prototype.onAfterExpand = function () {
		document.getElementById('modlgn_username').focus();
	};
	");
?>

<?php echo $params->get('pretext'); ?>
<?php if ($cdlogin_border == 'top' or $cdlogin_border == "both"): ?>
<div class="cdlogin_border-top"></div>
<?php endif; ?>

<div class="cd_moduletitle_logo"><a href="#"
	onclick="return hs.htmlExpand(this, { contentId: 'highslide-html-loginform', wrapperClassName: 'mod_cdlogin', outlineType: '<?php echo
        $outlineType; ?>', align: '<?php echo $align; ?>', anchor: '<?php echo $anchor; ?>', dimmingOpacity: <?php echo
        $dimmingOpacity; ?>, slideshowGroup: 'mod_cdlogin_loginform' } )"
	title="<?php echo
	JText::_('MOD_CDLOGIN_MODULE_TITLE'); ?>"><?php echo JText::_('MOD_CDLOGIN_MODULE_TITLE'); ?>
</a></div>

	<?php if ($cdlogin_border == 'bottom' or $cdlogin_border == "both"): ?>
<div class="cdlogin_border-bottom"></div>
	<?php endif; ?>
<div class="highslide-html-content" id="highslide-html-loginform">

	<div class="highslide-html-content-header">
	<div class="highslide-move"
		title="<?php echo JText::_('MOD_CDLOGIN_TITLE_MOVE'); ?>"><a href="#"
		onclick="return hs.close(this)" class="control"
		title="<?php echo
		JText::_('MOD_CDLOGIN_CLOSELABEL'); ?>"><?php echo JText::_('MOD_CDLOGIN_CLOSELABEL'); ?></a>
	</div>
	</div>
	
	<div class="highslide-body">
	
		<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="cdlogin_form_login" id="cdlogin_form_login">
			<fieldset class="input">
				<div>
					<p id="cdlogin-form-login-username">
						<label for="modlgn_username"><?php echo JText::_('JGLOBAL_EMAIL') ?></label>
						<br />
						<input id="modlgn_username" type="text" name="username" class="inputbox" title="<?php echo JText::_('MOD_CDLOGIN_USERNAME') ?>" alt="username" size="18" />
					</p>
					<p id="cdlogin-form-login-password">
						<label for="modlgn_passwd"><?php echo JText::_('MOD_CDLOGIN_PASSWORD') ?></label><br />
						<input id="modlgn_passwd" type="password" name="password" class="inputbox" size="18" title="<?php echo JText::_('MOD_CDLOGIN_PASSWORD') ?>" alt="password" />
					</p>
					<p id="cdlogin-form-login-remember">
						<input <?php if (!JPluginHelper::isEnabled('system', 'remember')): ?> disabled="disabled" <?php endif; ?> id="modlgn_remember" type="checkbox" name="remember" class="inputbox" value="yes" title="<?php echo JText::_('MOD_CDLOGIN_REMEMBER_ME') ?>" alt="<?php echo JText::_('MOD_CDLOGIN_REMEMBER_ME') ?>" />
						<label for="modlgn_remember"><?php echo JText::_('MOD_CDLOGIN_REMEMBER_ME') ?></label>
					</p>
					<p id="cdlogin-form-login-submit">
						<input type="submit" name="Submit" id="cdlogin_loginbutton" title="<?php echo JText::_('MOD_CDLOGIN_BUTTON_LOGIN') ?>" value="" />
					</p>
				</div>
			</fieldset>
		<?php if ($display_links): ?>
			<ul>
				<li>
					<a href="<?php echo $forgot_password_link; ?>" title="<?php echo JText::_('MOD_CDLOGIN_FORGOT_YOUR_PASSWORD'); ?>"> <?php echo JText::_('MOD_CDLOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
				</li>
				<?php
				$usersConfig = JComponentHelper::getParams('com_users');
				if ($usersConfig->get('allowUserRegistration')): ?>
					<li>
						<a href="<?php echo $register_link; ?>" title="<?php echo JText::_('MOD_CDLOGIN_REGISTER'); ?>"> <?php echo JText::_('MOD_CDLOGIN_REGISTER'); ?></a>
					</li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>
		<?php if (!$display_links): ?>
			<div style="height: 10px"></div>
		<?php endif; ?>
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="user.login" />
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
	<?php if (JText::_('MOD_CDLOGIN_LOGIN_MESSAGE') == ''): ?>
	<?php else: ?>
		<div class="cdlogin_message_to_users">
			<span><?php echo JText::_('MOD_CDLOGIN_LOGIN_MESSAGE'); ?></span>
		</div>
	<?php endif; ?>
</div>
	<?php echo $params->get('posttext'); ?>
<?php endif; ?>