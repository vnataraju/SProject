<?php
/**
 * @category	Modules
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');
?>
<div class="<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php
	if( !empty( $row ) )
	{
	?>
		<div id="cLatestMembers">		
			<ul>
			<?php		
			foreach( $row as $data )
			{
				$user 		= CFactory::getUser($data->id);				
				$userName 	= CStringHelper::escape( $user->getDisplayName() );
				$userLink 	= CRoute::_('index.php?option=com_community&view=profile&userid='.$data->id);
				
				$html  = '<li>';
				$html .= '	<a href="'.$userLink.'">';
				if($tooltips)
				{
					$html .= '	<img width="32" src="'.$user->getThumbAvatar().'" class="cAvatar jomNameTips" alt="'.$userName.'" title="'.cAvatarTooltip($user).'" style="border: solid 1px #ccc;" />';
				}
				else
				{
					$html .= '	<img width="32" src="'.$user->getThumbAvatar().'" alt="'. $userName.'" title="'.$userName.'" style="padding: 2px; border: solid 1px #ccc;" />';
				}
				$html .= '	</a>';
				$html .= '</li>';
				echo $html;
			}
			?>
			</ul>
		</div>
		<div>
			<a style='float:right;' href='<?php echo CRoute::_("index.php?option=com_community&view=search&task=browse&sort=latest"); ?>'><?php echo JText::_("SHOW_ALL"); ?></a>
		</div>
	<?php
	}
	else
	{
		echo JText::_('NO_MEMBERS_YET');
	}
	?>
	<div style='clear:both;'></div>
</div>