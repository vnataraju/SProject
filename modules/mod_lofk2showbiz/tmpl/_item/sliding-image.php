<div class="lof-inner" <?php echo $style; ?>>
	<?php if( $row->featured ) :  ?> 
    	<div class="lof-featured"></div>
     <?php endif; ?>
  <?php if ( $params->get('function','recent-viewed') == "recent-viewed" ): ?>
    <p class="lof-remove" id="id<?php echo $row->id;?>"><?php echo JText::_("Remove");?></p>
  <?php endif; ?>
	<?php if( $showImage ): ?>	
        <div class="lof-image"  style="height:<?php echo $thumbHeight; ?>px; width:<?php echo $thumbWidth; ?>px">
        	<?php if( $showImage=="with-link"): ?>
				<a href="<?php echo $row->link; ?>" title="<?php echo $row->title;?>">
					<img  src="<?php echo $row->thumbnail; ?>" title="<?php echo $row->title;?>"/>
                </a>
            <?php else:?>
          	 <img  src="<?php echo $row->thumbnail; ?>" title="<?php echo $row->title;?>"/>
            <?php endif; ?>
			<?php if( $params->get('itemCategory',1) ) : ?>
                   <div class="lof-category">
                  <a title="<?php echo $row->category_title;?>" href="<?php echo (isset($row->categoryLink)?$row->categoryLink: JRoute::_(ContentHelperRoute::getCategoryRoute($row->catid)));?>"><?php echo $row->category_title;?></a>
                   </div>
            <?php endif; ?>
                <?php if( $params->get('itemDateCreated',1) ) : ?>
                <div class="lof-date"><?php echo $row->date;?></div>
                <?php endif; ?>
                
        </div>
	<?php endif; ?>
			 <?php if( $showTitle ): ?>
             <a class="lof-title" target="<?php echo $openTarget; ?>" title="<?php echo $row->title; ?>" href="<?php echo $row->link;?>">
               <?php echo $row->title; ?>
             </a>
             <?php endif; ?>
               <div class="lof-extrainfo"> 
                <?php if( $params->get('itemAuthor',1) ) : ?>
                  <span class="lof-author">
                   	<?php echo JText::_('Posted By'); ?> : <b><?php echo $row->author; ?></b>
                   </span>
                <?php endif; ?>
            </div>    
            <?php
				if( $row->description  && $row->description !="..." ):
					echo ''.$row->description.''; 
				endif; 
			?>
            <?php if( $showReadmore ) : ?>
              <a target="<?php echo $openTarget; ?>" class="lof-readmore" title="<?php echo $row->title;?>" href="<?php echo $row->link;?>">
                <?php echo JText::_('READ_MORE');?>
              </a>
            <?php endif; ?>
  
</div>