<?php
/*------------------------------------------------------------------------
# JA Cooper for Joomla 1.5 - Version 1.0 - Licence Owner jSharing
# ------------------------------------------------------------------------
# Copyright (C) 2004-2008 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: J.O.O.M Solutions Co., Ltd
# Websites:  http://www.joomlart.com -  http://www.joomlancers.com
# This file may not be redistributed in whole or significant part.
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

include_once (dirname(__FILE__).DS.'ja_vars_1.5.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <jdoc:include type="head" />

    <!-- HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- styles -->
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/system.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/css3.css" type="text/css" />

 

	<link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/bootstrap.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/bootstrap-responsive.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/docs.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/prettify.css" type="text/css" />
  
    
    <link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/reset.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/960_16_col.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/style.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/bootstrap-responsive.css" type="text/css" />
   

  </head>

  <body  id="bd" class="<?php echo $tmpTools->getParam(JA_TOOL_LAYOUT);?> <?php echo $tmpTools->getParam(JA_TOOL_SCREEN);?> fs<?php echo $tmpTools->getParam(JA_TOOL_FONT);?>">
 
    <div class="cloudbgtotal">
        <div class="cloudbg">
         <div id="header" class="container_16 clearfix" style="background: #dddddd;">
         <div class="grid_11">
                     
                    </div>
                    <div class="grid_5 right">
                       	<?php if ($this->countModules('ja-login')) { ?>
  	<div id="ja-loginwrap">
  	 <jdoc:include type="modules" name="ja-login" style="raw" />
  	</div>
  	<?php } ?>
                    </div>
         
         </div>
            <div id="header" class="container_16 clearfix">
                
                <div class="slide">
                
                    <div class="wrapper boxheader">
                        <div class="logo grid_2 suffix_1"></div>
                         <div class="grid_9 menuheader">
              <jdoc:include type="modules" name="position-1" />
              </div>
                        <div class="grid_3">
                         
                                <input class="searchpadding" id="text2" type="text"
                                placeholder="Search" />
                         
                        </div>
                        <div class="grid_1 right">
                            <span class="samp"><a href="#"></a></span>
                        </div>
                    </div><a href="#" class="btn-slide"></a>
                </div>
                <div class="container_16 clearfix">
                    <div class="grid_12">
                        <jdoc:include type="modules" name="position-2" />
                    </div>
                    <div class="grid_4">
                        <div class="left share">
                            
    
                        </div>
                        <div class="print1"></div>
                    </div>
                    
                </div>
                </div>
                
                <div class="middle">
               
                
                <?php if($this->countModules('position-3')) : ?>
                <div class="container_16 clearfix boxbg">
                <jdoc:include type="modules" name="position-3" /> </div>
				  <?php endif; ?>
                  
                  
                     <div class="container_16  clearfix boxbg">
                  <div class="middlecontent">   
                  <jdoc:include type="component" /> 
          
                   </div>
                   </div>
                  
                
                 <?php if($this->countModules('position-5')) : ?>
                        <div class="container_16 clearfix boxbg">
                      <div class="headerbordertop"><h4>Capability Tasks</h4> </div>
                  <jdoc:include type="modules" name="position-5" style="beezDivision" headerlevel="3"/></div>
                  <?php endif; ?>
                  
                  <?php if($this->countModules('position-6')) : ?>
                        <div class="container_16 clearfix boxbg">
                        <div class="boxinnermargin"> 
                      
                  <jdoc:include type="modules" name="position-6" /></div></div>
                  <?php endif; ?>
                  
                    <?php if($this->countModules('position-7')) : ?>
                                 	
                        <div class="container_16 clearfix">
                  <jdoc:include type="modules" name="position-7"  />
                  </div>
                 
                  
                  <?php endif; ?>
                        
                  </div>
        
        <div class="bootombg1">
        
          <div class="container_16 clearfix boxbg">
          <div class="bottommiddle" >
          
         <?php if($this->countModules('position-8')) : ?>
                                     	
                        <div class="container_16 grid_3 clearfix border">
                     
                  <jdoc:include type="modules" name="position-8" style="beezDivision" headerlevel="3" /></div>
                  
                  <?php endif; ?>
                   <?php if($this->countModules('position-9')) : ?>
                                     	
                        <div class="container_16 grid_8 clearfix border">
                           <div class="videobox">
                     
                  <jdoc:include type="modules" name="position-9" style="beezDivision" headerlevel="3" /></div></div>
                  
                  <?php endif; ?>
                  
                   <?php if($this->countModules('position-10')) : ?>
                                     	
                        <div class="container_16 clearfix ">
                  <jdoc:include type="modules" name="position-10" style="beezDivision" headerlevel="3" /></div>
                  
                  <?php endif; ?>
                  </div>
                  </div>
                    
                    <div class="footerbg">
                        <div class="container_16 clearfix bootomd">
                         <div> &nbsp;</div>
                        
                            
                            
                            <?php if($this->countModules('position-11')) : ?>
                                     	
                        <div class="container_16 grid_3 border">
                  <jdoc:include type="modules" name="position-11" /></div>
                  
                  <?php endif; ?>
                                
                       
                            
                         
                                
                                <?php if($this->countModules('position-12')) : ?>
                                     	
                         <div class="container_16 grid_8 border">
                  <jdoc:include type="modules" name="position-12" /></div>
                  
                  <?php endif; ?>
                                
                                
                        
                       
                                <?php if($this->countModules('position-13')) : ?>
                                     	
                        <div class="clearfix ">
                  <jdoc:include type="modules" name="position-13" /></div>
                  
                  <?php endif; ?> 
                                
                         
                            
                            
                        </div>
                        <div class="container_16 clearfix">
                        	<div class="bottomboder">
                                
                                 <?php if($this->countModules('position-14')) : ?>
                                     	
                        <div class="container_16  clearfix ">
                  <jdoc:include type="modules" name="position-14" /></div>
                  
                  <?php endif; ?> 
                            <div class="bottomboder small">
                                <div class="copy">
                                    <div class="left">
                                        &copy; 2012 Wire Briar LLC.  All Rights Reserved.
                                    </div>
                                </div>
                                <div class="grid_5 push_3">A <img src="images/logo-wb.jpg" /> website</div>
                                <div class="copy right">
                                    <a href="#">Terms Use</a> | <a  href="#">Privacy
                                    Policy</a>
                                </div>
                                <br />
                            </div>
                        </div>
                    </div>
                </div>
        
        
     
    <!-- Javascript at the end so the pages load faster -->
	<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/jquery.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/google-code-prettify/prettify.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-transition.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-alert.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-modal.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-dropdown.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-scrollspy.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-tab.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-tooltip.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-popover.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-button.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-collapse.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-carousel.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/bootstrap-typeahead.js"></script>
	<script src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/js/application.js"></script>


  </body>
</html>
