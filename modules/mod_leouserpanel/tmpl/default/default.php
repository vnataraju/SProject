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
?>
<div class="leo-userpanel" id="leo-userpanel">
	
    <div class="leo-panels-wrapper">
    	<?php if( $user->id <=0 ): ?>
    	 
         <?php if( $view !="registration" ): ?>
            <div class="panel-register"> 
                <div  class="leo-button"> <a href="#leo-register-panel" rel="leo-boxed"> <span style="font-size:13px; font-weight: bold; color:#000; border-bottom:1px dotted #000;  "><?php echo JText::_("Singup Now");?></span></a><span style="font-size:13px; color:#000;">|Already a Member?</span></div>
                <!-- LEO REGISTER PANEL -->
                <div class="leo-pnregister leo-panel" id="leo-register-panel">
                    <div class="panel-wrapper">
                    <?php require( $registerPath ); ?> 
                    </div>
                </div>
                <!-- END_LEO REGISTER PANEL -->
                
            </div>
            <div class="panel-login">
        	<div  class="leo-button"> <a href="#leo-login-panel" rel="leo-boxed" ><span style="font-size:13px; font-weight: bold; color:#000;border-bottom:1px dotted #000;"><?php echo JText::_("Login");?></span></a></div>
                <!-- LEO LOGIN PANEL -->
                <div class="leo-pnlogin leo-panel" id="leo-login-panel">
                    <div class="panel-wrapper">
                    <?php require( $loginPath ); ?> 
                    </div>
                </div>
                <!-- END_LEO LOGIN PANEL -->
       	</div>
            <?php endif; ?>
        <?php endif; ?>
        
		<?php if( $user->id ): ?>
        <div class="panel-logout">
            <div class="leo-button" style="font-size: 12px;"><a href="#leo-logout-panel"  rel="leo-boxed"><span> 
                 <?php if($params->get('name') == 0) : {
                    echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('name'));
                } else : {
                    echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('username'));
                } endif; ?>
            </span></a></div>
            
            <div class="leo-pnlogout leo-panel" id="leo-logout-panel">
                <div class="panel-wrapper">
                <?php require( $logoutPath ); ?> 
                </div>
                <div class="clr clearfix"></div>
            </div>
    
        </div>    
        <?php endif; ?>
        <div class="clr"></div>
    </div>
</div>