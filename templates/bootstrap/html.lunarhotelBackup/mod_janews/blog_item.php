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
		
		$tmpTools = JA_Tools::getInstance();
		$dateformat = $tmpTools->getParam('janews-dateformat', 'd M Y');
		//get Itemid of category
		if ($catorsec) {
			$catlink   = JRoute::_(ContentHelperRoute::getCategoryRoute($rows[0]->catslug, $rows[0]->sectionid));
		}else{
			$catlink   = JRoute::_(ContentHelperRoute::getSectionRoute($rows[0]->sectionid));
		}
		
		$cattitle = ($catorsec) ? $rows[0]->cattitle:$rows[0]->sectitle;
		$catdesc = ($catorsec) ? $rows[0]->catdesc:$rows[0]->secdesc;
		
		$cls_sufix = trim($params->get('blog_theme',''));
		
		if($cls_sufix) $cls_sufix = '-'.$cls_sufix;
?>
		<div class="jazin-boxwrap jazin-theme<?php echo $cls_sufix;?>">
		<div class="jazin-box">
		<?php if ($showcattitle) : ?>
		<div class="jazin-section clearfix">
			<a href="<?php echo $catlink;?>" title="<?php echo trim(strip_tags($catdesc));?>">
				<span><?php echo $cattitle;?></span>
			</a>
		</div>
		<?php endif; ?>
<?php
		$i = 0;
		while($i < $introitems && $i<count($rows)) {
			$row = $rows[$i];
			$link   = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
			$image = modJANewsHelper::replaceImage ($row, $img_align, $autoresize, $maxchars, $showimage, $img_w, $img_h, $hiddenClasses);
			//Show the latest news
?>
			<div class="jazin-content clearfix">
				<?php if ($showimage) : ?>
					<?php echo $image; ?>
				<?php endif; ?>
				<h4 class="jazin-title"><a href="<?php echo $link;?>" title="<?php echo strip_tags($row->title);?>"><?php echo $row->title;?></a></h4>

				<?php 
				     if($maxchars > strlen($row->introtext1)) {
				      echo $row->introtext;
				     } else {
				      echo $row->introtext1;
				     }
				?>
				<?php if ($showreadmore) : ?>
				<a href="<?php echo $link; ?>" class="readon" title="<?php echo JText::sprintf('Read more...');?>"><?php echo JText::sprintf('Read more...');?></a>
				<?php endif; ?>
			</div>
<?php
			$i++;
		}
		
		if (count ($rows) > $i) {
			echo "<strong class=\"jazin-more\">More:</strong>\n";
			echo "<ul class=\"jazin-links\">\n";
  		
  		while (count ($rows) > $i) {
  			$row = $rows[$i];
  			$link   = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid.":testset"));
?>
  			<li>
  			<a title="<?php echo strip_tags($row->introtext); ?>" href="<?php echo $link; ?>">
  			<?php echo $row->title; ?></a>
  			</li>
<?php
  			$i++;
  		}
  		echo "</ul>\n";
		} 
?>
		</div>
		</div>
