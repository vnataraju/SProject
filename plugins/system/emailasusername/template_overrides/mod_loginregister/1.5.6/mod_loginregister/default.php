<?php // no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.mootools');
$document = &JFactory::getDocument();
// mod_loginregister 1.6.0 template override
require_once(JPATH_ROOT . DS . "modules" . DS . "mod_loginregister" .DS."tmpl" . DS . "element" . DS . "recaptchalib.php");
$publickey = $params->get('public');
$privatekey= $params->get('private');
$error='';
$document->addScript(JURI::root() .'media/system/js/validate.js');
$document->addCustomTag('<script type="text/javascript">

jQuery.noConflict(go);

 

   function checkcapcha(){
   
   
   
   
   var agree=""; 
		if(agree= document.getElementById("formagree"))
		{
     if (!agree.checked) 
	 {
        alert("You Must Agree to the Terms of Use.");
        return false;
		} 
	 }
   var chell;    
if(chell=document.getElementById("recaptcha_challenge_field"))
{
	   var chell=   document.getElementById("recaptcha_challenge_field").value;

       var resp = document.getElementById("recaptcha_response_field").value ;
       var prikey = "'.$privatekey.'";
       document.getElementById("myDiv").innerHTML="";


var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();

  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {

  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
                            var responss=xmlhttp.responseText;
                            //alert (responss);
                             if(responss =="false2"){
                                       document.getElementById("myDiv").innerHTML= "'.JText::_( 'Invalid Private Key').'" ;
                                    }else{
                                       if(responss =="true")  {
                                       document.josForm.submit();
                                                      }else   { document.getElementById("myDiv").innerHTML= "'.JText::_( 'Invalid Captcha').'" ;
                                                                Recaptcha.reload ();
                                    }
                                    }
    }else  document.getElementById("myDiv").innerHTML= "<img src=\"modules/mod_loginregister/tmpl/element/loads.gif\" border=\"0\">" ;
  }

xmlhttp.open("GET","modules/mod_loginregister/tmpl/element/captchacheck.php?field1="+chell+"&field2="+resp+"&field3="+prikey,true);
xmlhttp.send();

   return false;
 }
}

   function xi(s){
                    if(s=="y") {
                    jQuery(".popup_register").hide(300);
                    jQuery(".passwret").show(300);
                    jQuery("#form2").hide();
                     jQuery("#form1").show();

                      }
                    if(s=="n")
                    {jQuery(".popup_register").show(300);
                    jQuery(".passwret").hide(300);
                    jQuery("#form2").show();
                     jQuery("#form1").hide();
                    }
                  }



</script>');
//JQuery Load
	$jquery_source=$params->get('jqueryload');
	if($jquery_source=='local')
	{
		$document->addScript(JURI::root() .'modules/mod_loginregister/tmpl/element/jquery.min.js');
	}
	else
	{
	$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
	}
if(!$params->get('disablelog'))
	{

		$document->addCustomTag( '<script type="text/javascript">jQuery.noConflict();</script>' );
	
		$flags=1;
					}
	else
	{
		$flags=0;
	}
 if(!$params->get('disablelog')){$flags= 1;

$document->addCustomTag( '<script type="text/javascript">jQuery.noConflict();</script>' );
//$document->addScript(JURI::root() .'modules/mod_loginregister/tmpl/element/fade.js');
 }
 else{$flags=0; }

function jm_getthem($params)
	{
		switch ($params->get('jmtheme'))
		{
			case '0':
				return 'red';
				break;
			case '1':
				return 'white';
				break;
			case '2':
				return 'blackglass';
				break;
			case '3':
				return 'clean';
				break;
		}
	}
 ?>
 <?php
$usersConfig = &JComponentHelper::getParams( 'com_users' );
  if($flags && $usersConfig->get('allowUserRegistration') && $type != 'logout' ) :
  $check= $params->get('checkbox1'); 
 if($check==1) {
 ?>

  <div style="margin:0px;" id="login-form">
 <p style="padding:10px 0 0 20px;">
  <input type="radio" onclick="xi('y')"  name="group1" <?php if($params->get('view')==0) echo 'checked="checked"';?>  /> <?php echo JText::_('LOGIN') ?><br/>
<input type="radio" onclick="xi('n')"  name="group1" <?php if($params->get('view')==1) echo 'checked="checked"';?> /><?php echo JText::_('REGISTER'); ?><br/>
   </p>
   </div>
   <?php }  endif; ?>


<?php if($type == 'logout') : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
<?php if ($params->get('greeting')) : ?>
	<div class="login-greeting">
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_LOGINREGISTER_HINAME', $user->get('name'));
	} else : {
		echo JText::sprintf('MOD_LOGINREGISTER_HINAME', $user->get('username'));
	} endif; ?>
	</div>
<?php endif; ?>
	<div class="logout-button">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</div>
</form>
<?php elseif(!$params->get('disablelog')) : ?>
<?php if(JPluginHelper::isEnabled('authentication', 'openid')) :
		$lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
		$langScript = 	'var JLanguage = {};'.
						' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
						' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
						' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
						' var modlogin = 1;';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration( $langScript );
		JHTML::_('script', 'openid.js');
endif; ?>


<div style="margin:0px;display:<?php if($params->get('view')) {echo "none";} else {echo "block" ;}?>;" class="passwret">

<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>" method="post" name="loginregister" id="login-form" >
	<div class="pretext">
	<?php echo $params->get('pretext'); ?>
	</div>
	<fieldset class="userdata">
	<p id="form-login-username">
		<label for="modlgn-username"><?php echo JText::_('JGLOBAL_EMAIL') ?></label>
		<input id="modlgn-username" type="text" name="username" class="inputbox"  size="18" />
	</p>
	<p id="form-login-password">
		<label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
		<input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18"  />
	</p>
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<p id="form-login-remember">
		<label for="modlgn-remember"><?php echo JText::_('MOD_LOGINREGISTER_REMEMBER_ME') ?></label>
		<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
	</p>
	<?php endif; ?>

	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	</fieldset>
	<ul>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('MOD_LOGINREGISTER_FORGOT_YOUR_PASSWORD'); ?></a>
		</li>

	</ul><BR/>
	<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
	<div class="posttext">
	<?php echo $params->get('posttext'); ?>
	</div> <BR/>
</form>
</div>

<?php endif;   ?>

             <?php if($flags){if($params->get('view')) $flag2=1;else $flag2=0;}else{ $flag2=1; }     ?>

<div style="margin:0px; display:<?php if($flag2) {echo "block";} else {echo "none" ;}?>;" class="popup_register registration2">

 		<form action="<?php echo JRoute::_( 'index.php?option=com_users&task=registration.register' ); ?>" method="post" id="login-form" name="josForm" class="form-validate"  onSubmit="return checkcapcha()" >

                    <p>
						<label id="namemsg" for="name">
                        	<?php echo JText::_( 'JNAME' ); ?>:
						*</label><br/>
                     	<input style= "width:80%; " tabindex="1" type="text" name="jform[name]" id="jform_name" size="20" value="" class="inputbox required" />
                     </p>


                    <p>
						<label id="pwmsg" for="password">
							<?php echo JText::_( 'PASSWORD' ); ?>:
						*</label><br/>
                     	<input  style= "width:80%; " tabindex="3" class="inputbox validate-password required" type="password" id="jform_password1" name="jform[password1]" size="20" value=""  />
                    </p>

                    <p>
						<label id="pw2msg" for="password2">
                             <?php echo JText::_( 'VERIFY_PASSWORD' ); ?>:
						*</label><br/>
						<input style= "width:80%; " tabindex="4"  class="inputbox validate-password required" type="password" id="jform_password2" name="jform[password2]" size="20" value=""  />
                    </p>

                    <p>
						<label id="emailmsg" for="email">
							<?php echo JText::_( 'EMAIL' ); ?>:
						*</label> <br/>
						<input style= "width:80%; " tabindex="5" type="text" id="jform_email1" name="jform[email1]" size="20" value="" class="inputbox validate-email required" />
                    </p>

                    <p>
						<label id="emailmsg" for="email">
							<?php echo JText::_( 'VERIFY_EMAIL' ); ?>:
						*</label> <br/>
						<input style= "width:80%;" tabindex="6" type="text" id="jform_email2" name="jform[email2]" size="20" value="" class="inputbox validate-email required" />
                    </p>

	  <?php 
	 $tou= $params->get('tou');
	$articleid= $params->get('articleid');
	$newwindow= $params->get('newwindow');
	$title= $params->get('title');
	 $check= $params->get('checkbox');
	 if($check=='checked')
	 {
	$terms_of_use="<input name='terms' type='checkbox' checked='checked' tabindex='7' id='formagree' />&nbsp<a href='index.php?option=com_content&view=article&id=$articleid' target='$newwindow'> $title </a><br>";
		
	 }
	 else
	 $terms_of_use="<input name='terms' type='checkbox' tabindex='8' id='formagree' /><a href='index.php?option=com_content&view=article&id=$articleid' target='$newwindow'> $title </a><br>";
	
	if ($tou==1) {
	echo $terms_of_use;
	 } 
	 else 
	 {
	
		}
	?>


  <?php
 if ($params->get('enablecap') ) :
      if($publickey && $privatekey):
                    $theme= jm_getthem($params);

                    echo recaptcha_get_html($publickey, $error, $theme);
                    echo'<div style="height:130px; margin:0px; padding0px;"> </div>';
      else: echo '<div style="color:red;font-weight:bold; margin:0px; padding0px;">'.JText::_( 'Enter a valid Recaptcha Public and Private key.').'</div>';
      endif;
 endif; ?><br> 
 <input type="submit"  name="Submit" class="button validate" value="<?php echo JText::_('JREGISTER') ?>" /><BR/>
                      <input type="hidden" value="com_users" name="option">
			<input type="hidden" value="registration.register" name="task">
			<input type="hidden" value="1" name="adf3f0a374d112893c41c9de2abd5c54">
					<?php echo JHTML::_('form.token'); ?>
				</form>

</div>
<?php 
 $usersConfig = &JComponentHelper::getParams( 'com_users' );
  if($flags && $usersConfig->get('allowUserRegistration') && $type != 'logout' ) :
  $check= $params->get('checkbox1'); 
 if($check== 0) {?>

  <div style="margin:0px;" id="login-form">
 <p style="padding:10px 0 0 20px;">
   <input type="radio" onclick="xi('y')"  name="group1" <?php if($params->get('view')==0) echo 'checked="checked"';?>   /> <?php echo JText::_('LOGIN') ?><br/>
<input type="radio" onclick="xi('n')"  name="group1" <?php if($params->get('view')==1) echo 'checked="checked"';?>  /><?php echo JText::_('REGISTER'); ?><br/>
   </p>
   </div>
   <?php }
?>


<?php endif; ?>


  <div id="myDiv" style="color: #CF1919;  font-weight: bold;   margin: 0 0 0 20px;   padding: 0 0 0 20px; "></div>

<style>
#login-form label {
    display: block;
    float: left;
    margin-right: 10px;
    width: 9.4em;
}
</style>

