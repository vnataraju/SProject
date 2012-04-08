<?php 
/*------------------------------------------------------------------------
 # mod_lofk2showbiz - Lof K2Showbiz Module
 # ------------------------------------------------------------------------
 # author    LandOfCoder
 # copyright Copyright (C) 2010 landofcoder.com. All Rights Reserved.
 # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.landofcoder.com
 # Technical Support:  Forum - http://www.landofcoder.com/forum.html
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<div class="lofshowbiz-wrapper" style="width: <?php echo $moduleWidth;?>;height: <?php echo $moduleHeight;?>;">
    <?php if($iconmodule):?><span class="<?php echo $iconmodule;?> <?php echo $theme;?>">&nbsp;</span><?php endif;?>
    <div id="lofshowbiz-services-<?php echo $module->id; ?>" class="<?php echo $theme;?>">
    	<ul>
    	<!--	###############		-	SLIDE 1	-	###############	 -->
            <?php foreach ($list as $item):?>
    		<li>
    			<?php if( $linkImage == 1 ):?><a href="<?php echo $item->link;?>"><?php endif;?>
                    <?php if( $item->icon ):?><span class="<?php echo 'lof-'.$item->icon;?>">&nbsp;</span><?php endif;?>
                    <img class="thumb" src="<?php echo $item->mainImage;?>" data-bw="<?php echo $item->mainImage;?>" />
                <?php if( $linkImage == 1 ):?></a><?php endif;?>	
    			<div style="margin-top:16px"></div>
    			<?php if( $showTitle == 1 ):?><h2>
                    <?php if( $linkTitle == 1 ):?><a href="<?php echo $item->link;?>"><?php endif;?><?php echo $item->subtitle;?><?php if( $linkTitle == 1 ):?></a><?php endif;?>
                </h2><?php endif;?>
                <?php if($itemDateCreated==1):?><span style="font-style: italic;"><?php echo $item->date;?></span><br /><?php endif;?>
                <?php if($itemAuthor==1):?><span><?php echo JText::_('Author:');?>&nbsp;<?php echo $item->author;?></span><?php endif;?>
    			<?php if( $showDesc == 1 ):?><p><?php echo $item->description;?></p><?php endif;?>
                <div>
                    <?php if($itemHits == 1):?><span><?php echo JText::_('Hits:');?>&nbsp;<?php echo $item->hits;?></span>&nbsp;&nbsp;<?php endif;?>
                    <?php if($itemComments == 1):?><span><?php echo JText::_('Comments:');?>&nbsp;<?php echo count($item->commentcount);?></span><?php endif;?>
                </div>							
    			<?php if( $showReadmore == 1 ):?><a class="buttonlight <?php if( $showbiz == 1 ){echo "";}else{echo "morebutton";}?>" href="<?php if( $showbiz == 1 ){echo $item->link;}else{echo "#";}?>"><?php echo JText::_('View More');?></a><?php endif;?>
    			
    			<!-- 
    			***********************************************************************************************************
    				-	HERE YOU CAN DEFINE THE EXTRA PAGE WHICH SHOULD BE SHOWN IN CASE THE "BUTTON" HAS BEED PRESSED -	
    			***********************************************************************************************************									
    			-->
    			<div class="page-more">
    				<?php echo $item->introtext;?>
    				<div  class="closer"></div>
    			</div>
    		</li>
    		<?php endforeach;?>					
    	</ul>
    	
    	<!--	###############		-	TOOLBAR (LEFT/RIGHT) BUTTONS	-	###############	 -->
    	<div class="showbiz-toolbar lof-button-control-<?php echo $module->id;?>" style="display: <?php echo $lofdisplay;?>;">
    		<div class="left"></div>
            <div class="right"></div>
    	</div>
    </div> 
</div>

<script type="text/javascript">
				
	jQuery(document).ready(function() {
		jQuery.noConflict();
		jQuery('#lofshowbiz-services-<?php echo $module->id; ?>').lofservices(
		{										
			width:<?php echo (intval($moduleWidth) - 40);?>,
			height:<?php echo (intval($moduleHeight) - 40);?>,							
			slideAmount:<?php echo $maxPages;?>,
			slideSpacing:30,							
			transition:0,							
			touchenabled:"on",
			mouseWheel:"<?php echo $mousewheel;?>"
			
		});
        <?php if($displayButton == 2):?>
        jQuery("#lofshowbiz-services-<?php echo $module->id;?>").hover(
            function(){jQuery('.lof-button-control-<?php echo $module->id;?>').addClass("button-control")},
            function(){jQuery('.lof-button-control-<?php echo $module->id;?>').removeClass("button-control")}
        );
        <?php endif;?>
    });
</script>
