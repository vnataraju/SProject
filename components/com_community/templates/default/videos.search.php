<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * 
 */
defined('_JEXEC') or die();
?>
<div id="community-video-wrap">
	<!--SEARCH FORM-->
	<div class="video-search-form">
		<form name="searchVideo" action="<?php echo CRoute::getURI(); ?>" method="get">
			<input type="text" class="inputbox" id="search-text" name="search-text" size="50" />
			<input type="hidden" name="option" value="com_community" />
			<input type="hidden" name="task" value="search" />
			<input type="hidden" name="view" value="videos" />
			<input type="submit" name="search" class="button" value="<?php echo JText::_('COM_COMMUNITY_SEARCH_BUTTON_TEMP');?>"/>
			<?php
				echo JText::_('COM_COMMUNITY_SEARCH_FOR');

				foreach ($searchLinks as $key => $value) {
			?>
				<a href="<?php echo $value; ?>"><?php echo $key; ?></a>
			<?php
				}
			?>
			<input type="hidden" name="Itemid" value="<?php echo CRoute::getItemId(); ?>" />
		</form>
	</div>
	<!--SEARCH FORM-->
	
	<?php
	if( !empty($search) )
	{
	?>
	
	<!--SEARCH DETAIL-->
	<div class="video-search-detail">
		<span class="search-detail-left">
			<?php echo JText::sprintf( 'COM_COMMUNITY_SEARCH_RESULT' , $search ); ?>
		</span>
		<span class="search-detail-right">
			<?php echo JText::sprintf( (CStringHelper::isPlural($videosCount)) ? 'COM_COMMUNITY_VIDEOS_SEARCH_RESULT_TOTAL_MANY' : 'COM_COMMUNITY_VIDEOS_SEARCH_RESULT_TOTAL' , $videosCount ); ?>
		</span>
		<div style="clear:both;"></div>
	</div>
	<!--SEARCH DETAIL-->
	
	<div class="video-index">
		<?php echo $videosHTML; ?>
	</div>
	
	<div class="pagination-container">
		<?php echo $pagination; ?>
	</div>
<?php
}
?>
</div>