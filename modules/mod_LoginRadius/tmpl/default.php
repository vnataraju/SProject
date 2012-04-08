<?php 
/**
 * @version		$Id: default.php 1.3 16 Team LoginRadius
 * @copyright	Copyright (C) 2011 - till Open Source Matters. All rights reserved.
 * @license		GNU/GPL
 */
//no direct access
defined( '_JEXEC' ) or die('Restricted access');
JHtml::_('behavior.keepalive');?>
<?php if ($type == 'logout') : ?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">


<?php if ($params->get('greeting')) : ?>
	<div class="login-greeting">
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_LOGINRADIUS_HINAME', $user->get('name'));
	} else : {
		echo JText::sprintf('MOD_LOGINRADIUS_HINAME', $user->get('username'));
	} endif; ?>
</div>
<?php endif; ?>
	<div class="logout-button">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token');?>		
	</div>
</form>
<?php else : ?>
<?php if(JPluginHelper::isEnabled('authentication','openid')) :
      $lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
      $langScript =    'var JLanguage = {};'.
                  ' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
                  ' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
                  ' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
                  ' var modlogin = 1;';
      $document = &JFactory::getDocument();
      $document->addScriptDeclaration( $langScript );
      JHTML::_('script', 'openid.js');      
endif; ?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" >
<?php if ($params->get('usetrad') == 1): ?>
<div id='usetrad' name='usetrad'>
<?php	if ($params->get('pretext')): ?>
		<div class="pretext"></div>
	<?php endif; ?>
	
	<!--
	
	<fieldset class="userdata">
	<p id="form-login-username">
		<label for="modlgn-username"><?php // echo JText::_('MOD_LOGINRADIUS_VALUE_USERNAME') ?></label>
		<input id="modlgn-username" type="hidde" name="username" class="inputbox"  size="18" />
	</p>
	<p id="form-login-password">
		<label for="modlgn-passwd"><?php // echo JText::_('JGLOBAL_PASSWORD') ?></label>
		<input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18"  />
	</p>
	<?php // if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<p id="form-login-remember">
		<label for="modlgn-remember"><?php // echo JText::_('MOD_LOGINRADIUS_REMEMBER_ME') ?></label>
		<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
	</p>
	<?php // endif; ?>
	<input type="submit" name="Submit" class="button" value="<?php // echo JText::_('JLOGIN') ?>" />
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php // echo $return; ?>" />
	<?php // echo JHtml::_('form.token'); ?>
	</fieldset>
	
	-->
	
</div><?php endif; ?>
	<?php if ($params->get('apikey')): ?>
	<?php $LoginRadius_apikey=$params->get('apikey'); ?>
	<?php endif; ?>
	<?php if ($params->get('apisecret')): ?>
	<?php $LoginRadius_apisecret=$params->get('apisecret'); ?>
	<?php endif; ?>
	
    <?php echo $params->get('pretext');
	if(isset($_SERVER['REQUEST_URI'])) {
	    if(isset($_SERVER['HTTPS'])) {
		  $loc=urlencode("https://".$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);
		}
		else {
	      $loc=urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);
	    }
	}
	else {
	  if(isset($_SERVER['HTTPS'])) {
	    $loc=urlencode("https://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
	  }
	  else {
	    $loc=urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
	  }
	}?><br />
	 <?php require_once ( JPATH_BASE .DS.'modules'.DS.'mod_LoginRadius'.DS.'LoginRadiusSDK.php' );
	          if(isset($LoginRadius_apikey)) {
	                   $obj_auth = new LoginRadius_auth();
                       $UserAuth = $obj_auth->auth($LoginRadius_apikey, $LoginRadius_apisecret);
	                   $IsHttps=$UserAuth->IsHttps;
	          }
	 if($IsHttps == 1) {?>
	<iframe src="https://hub.loginradius.com/Control/PluginSlider.aspx?apikey=<?php echo $LoginRadius_apikey;?>&callback=<?php echo $loc;?>" width="169" height="49" frameborder="0" scrolling="no" ></iframe>
	<?php }else {?>
	<iframe src="http://hub.loginradius.com/Control/PluginSlider.aspx?apikey=<?php echo $LoginRadius_apikey;?>&callback=<?php echo $loc;?>" width="169" height="49" frameborder="0" scrolling="no" ></iframe>
<?php } if ($params->get('usetrad') == 1): ?>
<div id='usetrad1' name = 'usetrad1'>


<!-- 
	<ul>
		<li>
			<a href="<?php //echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php //echo JText::_('MOD_LOGINRADIUS_FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<li>
			<a href="<?php //echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php //echo JText::_('MOD_LOGINRADIUS_FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php
		//$usersConfig = JComponentHelper::getParams('com_users');
		//if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php //echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
				<?php //echo JText::_('MOD_LOGINRADIUS_REGISTER'); ?></a>
		</li>
		<?php // endif; ?>
	</ul>
-->
	
</div><?php endif; ?>
	<?php if ($params->get('posttext')): ?>
		<div class="posttext">
		<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
	</form>
<?php
 //including some joomla inbuilt files
        jimport('joomla.application.component.helper');
        require_once ( JPATH_BASE .DS.'modules'.DS.'mod_LoginRadius'.DS.'LoginRadiusSDK.php' );
		jimport('joomla.user.helper');
		jimport( 'joomla.mail.helper' ); 
        $uri = JFactory::getURI();
		$document =& JFactory::getDocument();
        $mainframe =& JFactory::getApplication();
		$password='';
        $salt = JUserHelper::genRandomPassword(32);
        $crypt = JUserHelper::getCryptedPassword($password, $salt);
        $password = $crypt.':'.$salt;
		$random = JUserHelper::genRandomPassword();
		$db = & JFactory::getDBO();
		$query="CREATE TABLE IF NOT EXISTS #__LoginRadius_users(id int(11) ,LoginRadius_id varchar(255))";
		$db->setQuery($query);
		$db->query();
    $FullName='';$ProfileName='';$fname='';$lname='';$id='';$Provider=''; $mail='';$open_id='';
 //get loginRadius variable
				            $obj = new LoginRadius();
                            $userprofile = $obj->construct($LoginRadius_apisecret);
                             if($obj->IsAuthenticated == true) {
							        $FullName=$userprofile->FullName;
							        $ProfileName=$userprofile->ProfileName;
									
							        $mail=$userprofile->Email[0]->Value;
							        $fname=$userprofile->FirstName; 
							        $lname=$userprofile->LastName;
							        $id=$userprofile->ID;
							        $Provider=$userprofile->Provider;
							        $open_id=$id;
							
if($params->get('dummyemail') == 0 && $mail == '') {
                      $query="SELECT u.id FROM #__users AS u
     INNER JOIN #__LoginRadius_users AS lu ON lu.id = u.id
     WHERE lu.LoginRadius_id = '$open_id'";
                      $db->setQuery($query);
                      $user_id = $db->loadResult();
		              $newuser = true;
                      if (isset($user_id)) {
		              $user =& JFactory::getUser($user_id);
                      if ($user->id == $user_id) {
                      $newuser = false;
					   }
}
else {
$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::root().'modules/mod_LoginRadius/style.css');?>
<div  class="LoginRadius_overlay" class="LoginRadius_content_IE">
<div id="popupouter">
  <div id="popupinner">
    <div id="textmatter"><p><b><?php echo JText::sprintf('MOD_LOGINRADIUS_EMAILENTER');?></b></p></div>
				<form method="post" action=""><div>
				<input type="text" name="email" id="email" class="inputtxt"/></div><div>
				<input type="submit" id="LoginRadiusRedSliderClick" name="LoginRadiusRedSliderClick" value="<?php echo JText::sprintf('MOD_LOGINRADIUS_SUBMIT');?>" class="inputbutton">
				<input type="submit" value="<?php echo JText::sprintf('MOD_LOGINRADIUS_CANCEL');?>" class="inputbutton" onClick="history.back()" />
				<input type="hidden" name="provider" id="provider" value="<?php echo $Provider;?>" />
				<input type="hidden" name="Fname" id="Fname" value="<?php echo $fname;?>" />
				<input type="hidden" name="Lname" id="Lname" value="<?php echo $lname;?>" />
				<input type="hidden" name="profileName" id="profileName" value="<?php echo $ProfileName;?>" />
				<input type="hidden" name="fullName" id="fullName" value="<?php echo $FullName;?>" />
				<input type="hidden" name="Open_id" id="Open_id" value="<?php echo $open_id;?>" />
				<input type="hidden" name="Id" id="Id" value="<?php echo $id;?>" /></div>
				</form>
				<div id="textdivpopup">Powered by <span class="spanpopup">Login</span><span class="span1">Radius</span></div>
</div></div></div><?php } }
}	
if(isset($_POST['LoginRadiusRedSliderClick'])) {
	$db =& JFactory::getDBO(); 
			$mail=urldecode($_POST['email']);
		$query = "SELECT id FROM #__users WHERE email='".$mail."'";
                      $db->setQuery($query);
                      $user_exist = $db->loadResult();
  if ( $user_exist != 0 || !JMailHelper::isEmailAddress($mail) ) {
          if(! JMailHelper::isEmailAddress($mail)) {
JError::raiseWarning('', JText::_('MOD_LOGINRADIUS_EMAILINVALID'));
           }
         else {
  JError::raiseWarning('', JText::_('MOD_LOGINRADIUS_EMAILEXIST'));
          }
   return false;
 }
else {
        $mail=$_POST['email'];
		$fname=$_POST['Fname'];
		$lname=$_POST['Lname'];
		$ProfileName=$_POST['profileName'];
		$FullName=$_POST['fullName'];
		$Provider=$_POST['provider'];
		$id=$_POST['Id'];
		$open_id=$_POST['Id'];
	}
}	
//if anything not found correctly 
$Email_id=substr($id,7);
$Email_id2=str_replace("/","_",$Email_id);
switch( $Provider ){
		case 'facebook':
					$username=$fname.$lname;
					$name=$fname;
					$email=$mail;
					$open_id=$id;
                    break;
        case 'twitter':
					$username=$ProfileName;
					$name=$ProfileName;
					$open_id=$id;
					if ($params->get('dummyemail')==0 )
					$email=$mail;
					else
					$email=$id.'@'.$Provider.'user.com';
					break;
        case 'google':
					$username=$fname.$lname;
					$name=$fname;
					$email=$mail;
					$open_id=$id;
					break;
        case 'yahoo':
				    $user_name=explode('@',$mail);
					$username=$user_name[0];
					$Name=explode('@',$username);
					$name=str_replace("_"," ",$Name[0]);
					$email=$mail;
					$open_id=$id;
		            break;
        case 'linkedin':
					$username=$fname.$lname;
					$name=$fname;
					$open_id=$id;
					if ($params->get('dummyemail')==0 )
					$email=$mail;
					else
					$email=$id.'@'.$Provider.'user.com';
					break;
		case 'aol':
					$user_name=explode('@',$mail);
					$username=$user_name[0];
					$Name=explode('@',$username);
					$name=str_replace("_"," ",$Name[0]);
					$email=$mail;
					$open_id=$id;
		            break;
		case 'hyves':
				 $username=$FullName;
				 $name=$FullName;
				 $email=$mail;
				 $open_id=$id;
				 break;
		default:
				if($fname=='' && $lname=='' && $FullName!='')
				{ $fname=$FullName;}
				if($fname=='' && $lname=='' && $FullName=='' && $ProfileName!='')
				   {$fname=$ProfileName;}
				$Email_id=substr($id,7);
				$Email_id2=str_replace("/","_",$Email_id);
				if($fname=='' && $lname==''  && $id!='')
				{
				$username=$id;
				$name=$id;
				$open_id=$id;
				if ($params->get('dummyemail')==0 )
					$email=$mail;
					else
				    $email=str_replace(".","_",$Email_id2).'@'.$Provider.'.com';
				}
					else if($fname!='' && $lname!=''  && $id!=''){
					$username=$fname.$lname;
					$name=$fname;
					$open_id=$id;
					if ($params->get('dummyemail')==0 )
					$email=$mail;
					else
					$email=str_replace(" ","_",$username).'@'.$Provider.'.com';
					}
					else if($fname=='' && $lname=='' && $mail!=''){
							$user_name=explode('@',$mail);
							$username=$user_name[0];
							$Name=explode('@',$username);
							$name=str_replace("_"," ",$Name[0]);
							$email=$mail;
							$open_id=$id;
							}
							else if($lname=='' && $fname!='' && $mail!=''){
							$username=$fname;
							$name=$fname;
							$email=$mail;
							$open_id=$id;
							}
							else {
								$username=$fname.$lname;
								$name=$fname;
								$email=$mail;
								$open_id=$id;
								}
               break;
              } 
global $mainframe;
function addJoomlaUser($name,$username,$email,$password,$open_id,$return) {
	$db = & JFactory::getDBO();
	jimport('joomla.user.helper'); 
	$mainframe = JFactory::getApplication();
	// Get required system objects
	$user = clone(JFactory::getUser());
	$pathway = $mainframe->getPathway();
	$config = JFactory::getConfig();
	$authorize = JFactory::getACL();
	$document = JFactory::getDocument();
	$language = JFactory::getLanguage();
	$language->load('com_users');
					 
			// check for exist LoginRadius_id
if( $open_id != "") {
			   $query="SELECT u.id FROM #__users AS u
     INNER JOIN #__LoginRadius_users AS lu ON lu.id = u.id
     WHERE lu.LoginRadius_id = '$open_id'";
            $db->setQuery($query);
            $user_id = $db->loadResult();
			// if not then check for email exist
			if(empty($user_id)) {
			    $query = "SELECT id FROM #__users WHERE email='".$email."'";
                $db->setQuery($query);
                $user_id = $db->loadResult();
			}
                  $newuser = true;
                  if (isset($user_id)) {
				        $user =& JFactory::getUser($user_id);
                  if ($user->id == $user_id) {
                      $newuser = false;
				  }
	             }
                  if ($newuser == true) {
			          $user = new JUser();
		    // If user registration is not allowed, show 403 not authorized.
             $usersConfig = JComponentHelper::getParams( 'com_users' );
             if($usersConfig->get('allowUserRegistration') == '0') {
                      JError::raiseWarning( '', JText::_( 'MOD_LOGINRADIUS_ALLOW_REG'));
                      return false;
				}
               //  Joomla 1.6 change
               $userConfig = JComponentHelper::getParams('com_users');
               // Default to Registered.
               $defaultUserGroup = $userConfig->get('new_usertype', 2);
               $user->set('name',$name);
               $user->set('username',$username);
			 // if username already exists
						  $nameexists = true;
						  $index = 0;
						  $userName = $username;
						  while ($nameexists == true) {
							if (JUserHelper::getUserId($userName) != 0) {
							  $index++;
							  $userName = $username.$index;
							} else {
							  $nameexists = false;
							}
						  }
						  $user->set('username',$userName);
                          $user->set('password',$password);
						  $user->set('password2',$password);
						  $user->set('email',$email);
					      $user->set('id', 0);
    					// Joomla 1.6 change
    					  $user->set('usertype', 'deprecated');
						  $user->set('LoginRadius_id',$open_id);
						// Joomla 1.6 change
    					 $user->set('groups', array($defaultUserGroup));
						 $date = JFactory::getDate();
						 $user->set('registerDate', $date->toMySQL());
// If user activation is turned on, we need to set the activation information
                         jimport('joomla.user.helper');
						 $user->set('activation',JUserHelper::genRandomPassword());
						 $user->set('block', '1');
						 
// If there was an error with registration, set the message and display form
if(!$user->save()){}
 //store user data
  $user_data = (array)$user;
  $removes = array('params', '_params', 'guest', '_errorMsg', '_errors');
  foreach($removes as $remove){
        unset($user_data[$remove]);
       }
}
// check for exist LoginRadius_id
	 $query="SELECT u.id FROM #__users AS u
     INNER JOIN #__LoginRadius_users AS lu ON lu.id = u.id
     WHERE lu.LoginRadius_id = '$open_id'";
    $db->setQuery($query);
    $user_id = intval($db->loadResult());
	// check for the community builder works
          $query = "SHOW TABLES LIKE '%__comprofiler'";
          $db->setQuery($query);
          $tableexists = $db->loadResult();

          if (isset($tableexists)) {
            $cbquery = "INSERT IGNORE INTO #__comprofiler(id,user_id) VALUES ('".$user->get('id')."','".$user->get('id')."')";
            $db->setQuery($cbquery);
            if (!$db->query()) {
              JERROR::raiseError(500, $db->stderror());
            }
          }
// if not then check for email exist
	if(empty($user_id)) {
	    $query = 'SELECT id FROM #__users WHERE email = '.$db->Quote($email);
        $db->setQuery($query);
        $user_id = intval($db->loadResult());
	}
		if($user_id) {
			$user =& JUser::getInstance((int)$user_id);
			$user->set('block', '0');
			$user->set('activation', '');
			//$user->set('lastvisitDate',$date->toMySQL());
			$query = "SELECT id FROM #__LoginRadius_users WHERE id='".$user_id."'";
                      $db->setQuery($query);
					  $check_user = intval($db->loadResult());
					  //$escape_openid=mysql_real_escape_string($open_id);
			if($check_user) {
			 $query = "UPDATE #__LoginRadius_users SET id='$user_id',LoginRadius_id='$open_id' WHERE id='".$user_id."' and LoginRadius_id='".$open_id."'";
            $db->setQuery($query);
		    $db->query();
			}
			else{
			$query = "INSERT INTO #__LoginRadius_users (id,LoginRadius_id)VALUES ('$user_id','$open_id')";
                      $db->setQuery($query);
					  $db->query();
			}
			$user->save();
		}
// Register session variables
         $session =& JFactory::getSession();
		 $session->set('user',$user);
		 // Getting the session object
        $table = & JTable::getInstance('session');
         $table->load( $session->getId());
         $table->guest = '0';
         $table->username  = $user->get('username');
         $table->userid    = intval($user->get('id'));
         $table->usertype  = $user->get('usertype');
		 $table->gid  = $user->get('gid');
         $table->update();
         $user->setLastVisit();
	     $user =& JFactory::getUser();
// redirect user to same page
         if($user->id) {
	          $mainframe->redirect(base64_decode($return));
	     }
	}
}
addJoomlaUser($name,$username,$email,$password,$open_id,$return);?>
<?php endif;?>