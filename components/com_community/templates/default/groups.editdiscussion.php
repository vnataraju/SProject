<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @params	isMine		boolean is this group belong to me
 * @params	members		An array of member objects 
 */
defined('_JEXEC') or die();
?>

<?php
if( !CStringHelper::isHTML($discussion->message) 
	&& $config->get('htmleditor') != 'none' 
	&& $config->getBool('allowhtml') )
{
	$discussion->message = CStringHelper::nl2br($discussion->message);
}

?>
<script type="text/javascript">
function saveContent()
{
	<?php echo $editor->saveText( 'message' ); ?>
	return true;
}
</script>
<form name="jsform-groups-editdiscussion" action="<?php echo CRoute::getURI(); ?>" method="post">
<table class="formtable">
	<?php echo $beforeFormDisplay;?>
	<?php if ( $config->get( 'htmleditor' ) == 'jce' ) : ?>

	<tr>
		<td class="key">
			<label for="title" class="label" style="text-align: left;">*<?php echo JText::_('COM_COMMUNITY_GROUPS_DISCUSSION_TITLE'); ?></label>
		</td>
		<td class="value" colspan="2">
			<input type="text" name="title" id="title" size="40" class="inputbox" style="width: 90%" value="<?php echo $discussion->title;?>" />
		</td>
	</tr>
	
	<tr>
		<td class="key>
			<label for="message" class="label" style="text-align: left;">*<?php echo JText::_('COM_COMMUNITY_GROUPS_DISCUSSION_BODY'); ?></label>
		</td>

		<td class="value" colspan="2">			
			<?php if( $config->get( 'htmleditor' ) && $config->getBool( 'allowhtml' ) ) : ?>
				<?php echo $editor->displayEditor( 'message',  $discussion->message , '95%', '450', '10', '20' , false ); ?>
			<?php else : ?>
				<textarea rows="3" cols="40" name="message" id="message" class="inputbox" style="width: 90%"><?php echo $discussion->message;?></textarea>
			<?php endif; ?>
		</td>
	</tr>
	
	<?php else : ?>
	
	<tr>
		<td class="key">
			<label for="title" class="label">*<?php echo JText::_('COM_COMMUNITY_GROUPS_DISCUSSION_TITLE'); ?></label>
		</td>
		<td class="value">
			<input type="text" name="title" id="title" size="40" class="inputbox" style="width: 90%" value="<?php echo $discussion->title;?>" />
		</td>
	</tr>
	
	<tr>
		<td class="key">
			<label for="message" class="label">*<?php echo JText::_('COM_COMMUNITY_GROUPS_DISCUSSION_BODY'); ?></label>
		</td>
		<td class="value">
			<?php 
			if( $config->get( 'htmleditor' ) == 'none' && $config->getBool('allowhtml') )
			{
			?>
			<div class="htmlTag"><?php echo JText::_('COM_COMMUNITY_HTML_TAGS_ALLOWED');?></div>
			<?php
			}?>
			
			<?php if( $config->get( 'htmleditor' ) && $config->getBool( 'allowhtml' ) ) : ?>
				<?php echo $editor->displayEditor( 'message',  $discussion->message , '95%', '450', '10', '20' , false ); ?>
			<?php else : ?>
				<textarea rows="3" cols="40" name="message" id="message" class="inputbox" style="width: 90%"><?php echo $discussion->message;?></textarea>
			<?php endif; ?>

		</td>
	</tr>
	
	<?php endif; ?>  
	
	<tr>
		<td class="key">
			<label for="lock" class="label">*<?php echo JText::_('COM_COMMUNITY_LOCK_DISCUSSION'); ?></label>
		</td>
		<td class="value">  
			<div>
				<input type="radio" name="lock" id="lock-yes" value="1"<?php echo ($discussion->lock == true ) ? ' checked="checked"' : '';?> />
				<label for="lock-yes" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_YES');?></label>
			</div>
			<div>
				<input type="radio" name="lock" id="lock-no" value="0"<?php echo ($discussion->lock == false ) ? ' checked="checked"' : '';?> />
				<label for="lock-no" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_NO');?></label>
			</div>	
		</td>
	</tr>
	
	<?php echo $afterFormDisplay;?>
	
	<tr>
		<td class="key"></td>
		<td class="value">
			<span class="hints"><?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?></span>
		</td>
	</tr>
	
	<tr>
		<td class="key"></td>
		<td class="value">
			<input type="hidden" value="<?php echo $group->id; ?>" name="groupid" />
			<input type="hidden" value="<?php echo $discussion->id;?>" name="topicid" />
			<input type="submit" class="button" value="<?php echo JText::_('COM_COMMUNITY_SAVE');?>" onclick="saveContent();" />
			<input type="button" name="cancel" value="<?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON'); ?>" onclick="javascript:history.go(-1);return false;" class="button" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</td>
	</tr>
</table>
</form>