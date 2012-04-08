<?php
/*------------------------------------------------------------------------
# Joomla Carousel Module by JoomShaper.com
# ------------------------------------------------------------------------
# author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomshaper.com
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<script type="text/javascript">
window.addEvent("domready", function() {  
    new iCarousel("carousel_content", {  
        idPrevious: "carousel_previous",  
        idNext: "carousel_next",  
        idToggle: "undefined",  
        item: {  
            klass: "carousel_item",  
            size: <?php echo $size; ?>  
        },  
		animation: {
			duration: 250,
			amount: <?php echo $amount; ?>
		}
 
    });  
});  
</script>

<div id="shaper_carousel">
	<div id="carousel_inner" style="width:<?php echo $mainwidth?>px;height:<?php echo $mainheight?>px">
		<ul id="carousel_content">
			<?php foreach ($list as $item): ?>
				<li class="carousel_item"><a href="<?php echo $item->link; ?>"><img src="<?php echo $item->image; ?>" alt="" width="<?php echo $width ?>" height="<?php echo $height ?>" /></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<div id="carousel_previous" style="height:<?php echo $mainheight?>px"></div>
	<div id="carousel_next" style="height:<?php echo $mainheight?>px"></div>
	<?php 
		if ($params->get( 'cssinfo',1 )==1) {
			echo '<br style="clear:both" /><b>Total Width : </b>' . $tmwidth. 'px';
			echo '&nbsp;&nbsp;';
			echo '<b>Total Height : </b>' . $mainheight. 'px';
		}		
	?>	
</div>	
