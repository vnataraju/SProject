<?php
/*------------------------------------------------------------------------
# mod_featcats - Featured Categories
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomla.vargas.co.cr
# Technical Support:  Forum - http://joomla.vargas.co.cr/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
?>
<ul class="featcats<?php echo $moduleclass_sfx; ?>">
	<?php foreach ($cats as $cat) : ?>
        <li class="<?php echo $cat->col_class; ?>" style="width:<?php echo $params->get('col_width'); ?>">
        <?php if ($cat_heading) : ?><?php echo '<h' . $cat_heading . '>'; ?><a href="<?php echo $cat->category_link; ?>"><?php echo $cat->category_title; ?></a><?php echo '</h' . $cat_heading . '>'; ?><?php endif; ?>
            <?php if ($cat->articles) : ?>
        	<ul class="featcats_leading">
			<?php foreach ($cat->articles as $article) : 
				if ( $params->get('bold_firstsentence', 0) ) {
					$regex = '/^(.*?)[\.\?!]\s/';
					$article->displayIntrotext = (preg_match($regex, $article->displayIntrotext) == 1 ? preg_replace($regex, '<strong>$0</strong>', $article->displayIntrotext) : '<strong>'.$article->displayIntrotext.'</strong>'); 	
				} ?>
        		<li><?php if ($item_heading) : ?><?php echo '<h' . $item_heading . '>'; ?>
				<?php if ($params->get('link_titles') == 1) : ?>
                <a class="mod_featcats-title<?php echo $article->active; ?>" href="<?php echo $article->link; ?>">
                <?php echo $article->title; ?>
                <?php if ($article->displayHits) :?>
                    <span class="mod_featcats-hits">
                    (<?php echo $article->displayHits; ?>)  </span>
                <?php endif; ?></a>
                <?php else :?>
                <?php echo $article->title; ?>
                    <?php if ($article->displayHits) :?>
                    <span class="mod_featcats-hits">
                    (<?php echo $article->displayHits; ?>)  </span>
                <?php endif; ?></a>
                <?php endif; ?>
                <?php echo '</h' . $item_heading . '>'; ?><?php endif; ?>
				<?php if ($params->get('show_author')) :?>
					<span class="mod_featcats-writtenby">
					<?php echo $article->displayAuthorName; ?>
					</span>
				<?php endif;?>
				<?php if ($article->displayDate) : ?>
					<span class="mod_featcats-date"><?php echo $article->displayDate; ?></span>
				<?php endif; ?>
                <p><?php echo $article->image; ?><?php echo $article->displayIntrotext; ?></p>
				<?php if ($params->get('show_readmore')) :?>
                    <p class="mod_featcats-readmore">
                        <a class="mod_featcats-title <?php echo $article->active; ?>" href="<?php echo $article->link; ?>">
                        <?php if ($article->params->get('access-view')== FALSE) :
                                echo JText::_('MOD_FEATCATS_REGISTER_TO_READ_MORE');
                            elseif ($readmore = $article->alternative_readmore) :
                                echo $readmore;
                                echo JHtml::_('string.truncate', $article->title, $params->get('readmore_limit'));
                                if ($params->get('show_readmore_title', 0) != 0) :
                                    echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
                                endif;
                            elseif ($params->get('show_readmore_title', 0) == 0) :
                                echo JText::sprintf('MOD_FEATCATS_READ_MORE_TITLE');	
                            else :
                                
                                echo JText::_('MOD_FEATCATS_READ_MORE');
                                echo JHtml::_('string.truncate', ($article->title), $params->get('readmore_limit'));
                            endif; ?>
                    </a>
                    </p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
            <?php if ( $cat->subarticles ) : ?>
        	<ul class="featcats_links">
			<?php foreach ($cat->subarticles as $subarticle) : ?>
        		<li>
				<?php if ($params->get('link_titles') == 1) : ?>
                <a class="mod_featcats-title <?php echo $subarticle->active; ?>" href="<?php echo $subarticle->link; ?>">
                <?php echo $subarticle->title; ?>
                <?php if ($subarticle->displayHits) :?>
                    <span class="mod_featcats-hits">
                    (<?php echo $subarticle->displayHits; ?>)  </span>
                <?php endif; ?></a>
                <?php else :?>
                <?php echo $subarticle->title; ?>
                    <?php if ($subarticle->displayHits) :?>
                    <span class="mod_featcats-hits">
                    (<?php echo $subarticle->displayHits; ?>)  </span>
                <?php endif; ?></a>
                <?php endif; ?>
                </li>
            <?php endforeach; ?>
            </ul>
			<?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
