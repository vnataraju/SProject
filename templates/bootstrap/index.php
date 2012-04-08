<?php
/**
 * @version                $Id: index.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package                Joomla.Site
 * @subpackage  Templates.bootstrap
 * @copyright        Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license                GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// check modules
$showRightColumn        = ($this->countModules('position-3') or $this->countModules('position-6')or $this->countModules('position-right') or $this->countModules('position-8'));
$showbottom                        = ($this->countModules('position-9') or $this->countModules('position-10') or $this->countModules('position-11'));
$showleft                        = ($this->countModules('position-4') or $this->countModules('position-7') or $this->countModules('position-5'));

if ($showRightColumn==0 and $showleft==0) {
        $showno = 0;
}

JHtml::_('behavior.framework', true);

// get params
$color              = $this->params->get('templatecolor');
$logo               = $this->params->get('logo');
$navposition        = $this->params->get('navposition');
$app                = JFactory::getApplication();
$doc        = JFactory::getDocument();
$templateparams     = $app->getTemplate(true)->params;



$doc->addScript($this->baseurl.'/templates/bootstrap/javascript/md_stylechanger.js', 'text/javascript', true);
?>

<?php defined( '_JEXEC' ) or die( 'Restricted access' );?>
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
   <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
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

  <body>
 
    <div class="cloudbgtotal">
        <div class="cloudbg">
          <div id="header" class="container_16 clearfix">
            <div class="grid_11">
                     &nbsp;
                </div>
                <div class="grid_5 right">
                  <?php if($this->countModules('ja-login')) : ?>
              <div class="singupbg">
                 <jdoc:include type="modules" name="ja-login" style="beezDivision" headerLevel="3" />                            
            </div>
          <?php endif; ?>
                </div>
           </div>
            <div id="header" class="container_16 clearfix">
                
                <div class="slide">
                
                    <div class="wrapper boxheader">
                       <div class="grid_2 suffix_1"> <a href="index.php"> <span class="logo"></span> </a></div>
                         <div class="grid_9 menuheader">
              <jdoc:include type="modules" name="position-1" style="beezDivision" headerLevel="3" />
              </div>
                        <div class="grid_3">
                         
                                <input class="searchpadding" id="text2" type="text"
                                placeholder="Search" />
                                <a href="#">Go</a>
                         
                        </div>
                        <?php
                          $userdetail = &JFactory::getUser();
                          if ( $userdetail->id != 0 ) {
                            echo '<div class="grid_1 right">
                              <span class="samp"><a href="#"></a></span>
                            </div>';
                      }
                    ?> 
                        
                    </div><a href="#" class="btn-slide"></a>
                </div>
                <div class="container_16 clearfix">
                    <div class="grid_12">
                        <jdoc:include type="modules" name="position-2" style="beezDivision" headerLevel="3" />
                    </div>
                    <div class="grid_4">
                        <div class="left share">    
                        </div>
                       <!-- <div class="print1"></div>-->
                    </div>
                    
                </div>
                </div>
                
                <div class="middle">
               
                
                <?php if($this->countModules('position-3')) : ?>
                <div class="container_16 clearfix boxbg">
                <jdoc:include type="modules" name="position-3" style="beezDivision" headerLevel="3" /> </div>
          <?php endif; ?>
                  
                            <?php if($this->countModules('position-4')) : ?>
                <div class="container_16 clearfix ">
                <jdoc:include type="modules" name="position-4" style="beezDivision" headerLevel="3" /> </div>
          <?php endif; ?>                  
                  
                             <?php if($this->countModules('header')) : ?>
                <div class="container_16 clearfix" >
        
                <div class="headertransparent">
        <jdoc:include type="modules" name="header" style="beezDivision" headerLevel="3" />   </div>
        
                <div class="headerblack" ></div>
        
                </div>
          <?php endif; ?>
        
                  
                    
                                    
                    <div class="container_16 clearfix boxbg" >
                      
                      
                    <?php if($this->countModules('social-login')) : ?>
                    
                  <div class="grid_8 signup-social-login" >
                  
                    <jdoc:include type="modules" name="social-login" style="beezDivision" headerLevel="3" />
                    
                  </div>
              <?php endif; ?>
                                       
                    <div class="middlecontent">
                      
                       <jdoc:include type="message" />
            
                  <jdoc:include type="component" />
          
           </div>
          
                   
                   
                           <?php if($this->countModules('headertop')) : ?>
               
                <div class="container_16 clearfix ">
        <div class="grid_16" >
                             
                <jdoc:include type="modules" name="headertop" style="beezDivision" headerLevel="3" />
       </div>
        </div>
          <?php endif; ?>
                      <?php if($this->countModules('position-left')) : ?>
                      
                <div class="grid_10 clearfix " >
               
                <jdoc:include type="modules" name="position-left" style="beezDivision" headerLevel="3"/> </div>
                
               
          <?php endif; ?>
                  
                     <?php if($this->countModules('position-right')) : ?>
                <div class="clearfix right">
                            
                <jdoc:include type="modules" name="position-right" style="beezDivision" headerLevel="3" /> </div>
          <?php endif; ?>
                  
                  </div>
                  
<?php if($this->countModules('content')) : ?>
                <div class="container_16 clearfix ">
                <jdoc:include type="modules" name="content" /> </div>
          <?php endif; ?>  
                  
                
          
          
                
                 <?php if($this->countModules('position-5')) : ?>
                        <div class="container_16 clearfix boxbg">
                      <div class="headerbordertop"><h4>Capability Tasks</h4> </div>
                  <jdoc:include type="modules" name="position-5" style="beezDivision" headerlevel="3"/></div>
                  <?php endif; ?>
                  
                  <?php if($this->countModules('position-6')) : ?>
                        <div class="container_16 clearfix boxbg">
                        <div class="boxinnermargin">
                      
                  <jdoc:include type="modules" name="position-6" style="beezDivision" headerLevel="3" /></div></div>
                  <?php endif; ?>
                  
                    <?php if($this->countModules('position-7')) : ?>
                                   
                        <div class="container_16 clearfix">
                  <jdoc:include type="modules" name="position-7"  style="beezDivision" headerLevel="3" />
                  </div>
                 
                  
                  <?php endif; ?>
                  </div>
                        
             
        
        <div class="bootombg1">
        
          <div class="container_16 clearfix boxbg">
          <div class="bottommiddle" >
          
           <?php if($this->countModules('position-8')) : ?>
                                       
                 <div class="container_16 grid_3 clearfix ">
                     
                    <jdoc:include type="modules" name="position-8" style="beezDivision" headerlevel="3" />
                    
                    <?php 
                      $userdetail = &JFactory::getUser();
                        if ( $userdetail->id != 0 ) {
                          echo '<div><a href="index.php/jomsocial"><img src="images/visitcommunitysmall.png" alt="Visit Community"/></a></div>';
                      } else {
                        echo '<div><a href="index.php/signup-now"><img alt="Sign Up FREE!" src="images/signupfreebuttonsmall.png" /></a></div>';
                      }
                    ?>
                        
                </div>
                
                
                
          <?php endif; ?>
      

                    
                   <?php if($this->countModules('position-9')) : ?>
                                       
                        <div class="container_16 grid_8 clearfix ">
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
                  <jdoc:include type="modules" name="position-11" style="beezDivision" headerLevel="3"/>
                          </div>
                  
                  <?php endif; ?>
                                
                       
                            
                         
                                
                                <?php if($this->countModules('position-12')) : ?>
                                       
                         <div class="container_16 grid_8 border">
                  <jdoc:include type="modules" name="position-12" style="beezDivision" headerLevel="3" /></div>
                  
                  <?php endif; ?>
                                
                                
                        
                       
                                <?php if($this->countModules('position-13')) : ?>
                                       
                        <div class="clearfix ">
                        <jdoc:include type="modules" name="position-13" style="beezDivision" headerLevel="3" />
                        
                    </div>
                  
                  <?php endif; ?>
                                
                         
                            
                            
                        </div>
                        <div class="container_16 clearfix">
                          <div class="bottomboder">
                                
                                 <?php if($this->countModules('position-14')) : ?>
                                       
                        <div class="container_16  clearfix ">
                  <jdoc:include type="modules" name="position-14" style="beezDivision" headerLevel="3" /></div>
                  
                  <?php endif; ?>
                            <div class="bottomboder small">
                                <div class="copy">
                                    <div class="left">
                                        &copy; <script type="text/javascript">
  var dteNow = new Date();
  var intYear = dteNow.getFullYear();
  document.write(intYear);
</script> Wire Briar LLC.  All Rights Reserved.
                                    </div>
                                </div>
                                <div class="grid_5 push_3"><a href="index.php/sitemap">Site Map</a></div>
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
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script> 
<script type="text/javascript" src="//share.loginradius.com/Content/js/LoginRadiusSharing.js" id="lrsharescript"></script> <script type="text/javascript"> 
  $(document).ready(function () { $("#loginradiusshare").LoginRadiusShare(
{"providers":["Facebook","Twitter","Email","LinkedIn","Google"],"interface":"basic16","apikey":"5be9020f-c857-438c-ab2e-f031e4e9aaf7"}
);
$('.powerd').hide();
$('.powerdlink').hide();
 }); 

</script>
  
  </body>
</html>


