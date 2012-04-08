<?php 
/*------------------------------------------------------------------------
 # mod_leouserpanel - Lof UserPanel Module
 # ------------------------------------------------------------------------
 # author    LeoTheme
 # copyright Copyright (C) 2010 leotheme.com. All Rights Reserved.
 # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.leotheme.com
 # Technical Support:  Forum - http://www.leotheme.com/forum.html
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );  
JHtml::_('behavior.formvalidation');
?>
<form id="leo-member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate">
  <fieldset>
    <h3><?php echo JText::_("MOD_LEOUP_REGISTRATION");?></h3>
    <dl>
      <dt> <span class="spacer"><span class="before"></span><span class="text">
        <label class="" id="jform_spacer-lbl"><strong class="red">*</strong> Required field</label>
        </span><span class="after"></span></span> </dt>
      <dd> </dd>
      <dt>
        <label title="" class="hasTip required" for="jform_name" id="jform_name-lbl"><?php echo JText::_( "MOD_LEOUP_REGISTER_NAME_LABEL" );?><span class="star">&nbsp;*</span></label>
      </dt>
      <dd>
        <input type="text" size="30" class="required" value="" id="jform_name" name="jform[name]">
      </dd>
      <dt>
        <label title="" class="hasTip required" for="jform_username" id="jform_username-lbl"><?php echo JText::_( "MOD_LEOUP_REGISTER_USERNAME_LABEL" );?><span class="star">&nbsp;*</span></label>
      </dt>
      <dd>
        <input type="text" size="30" class="validate-username required" value="" id="jform_username" name="jform[username]">
      </dd>
      <dt>
        <label title="" class="hasTip required" for="jform_password1" id="jform_password1-lbl"><?php echo JText::_( "MOD_LEOUP_REGISTER_PASSWORD1_LABEL" );?><span class="star">&nbsp;*</span></label>
      </dt>
      <dd>
        <input type="password" size="30" class="validate-password required" autocomplete="off" value="" id="jform_password1" name="jform[password1]">
      </dd>
      <dt>
        <label title="" class="hasTip required" for="jform_password2" id="jform_password2-lbl"><?php echo JText::_( "MOD_LEOUP_REGISTER_PASSWORD2_LABEL" );?><span class="star">&nbsp;*</span></label>
      </dt>
      <dd>
        <input type="password" size="30" class="validate-password required" autocomplete="off" value="" id="jform_password2" name="jform[password2]">
      </dd>
      <dt>
        <label title="" class="hasTip required" for="jform_email1" id="jform_email1-lbl"><?php echo JText::_( "MOD_LEOUP_REGISTER_EMAIL1_LABEL" );?><span class="star">&nbsp;*</span></label>
      </dt>
      <dd>
        <input type="text" size="30" value="" id="jform_email1" class="validate-email required" name="jform[email1]">
      </dd>
      <dt>
        <label title="" class="hasTip required" for="jform_email2" id="jform_email2-lbl"><?php echo JText::_( "MOD_LEOUP_REGISTER_EMAIL2_LABEL" );?><span class="star">&nbsp;*</span></label>
      </dt>
      <dd>
        <input type="text" size="30" value="" id="jform_email2" class="validate-email required" name="jform[email2]">
      </dd>
    </dl>
  </fieldset>
  <div>
    <button type="submit" class="validate"><?php echo JText::_('JREGISTER');?></button>
    <?php echo JText::_('MOD_LEOUP_OR');?> <a href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
    <input type="hidden" name="option" value="com_users" />
    <input type="hidden" name="task" value="registration.register" />
    <?php echo JHtml::_('form.token');?> </div>
</form>
