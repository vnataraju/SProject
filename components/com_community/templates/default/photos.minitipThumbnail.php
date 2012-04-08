<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2011 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 */
defined('_JEXEC') or die();

?>	 

<div id="thumbnail-container">
	<?php //var_dump($thumbnails);
		foreach ($thumbnails as $num => $thumbnail_data):
			$photo = JTable::getInstance('Photo' , 'CTable');
			$photo->bind($thumbnail_data);
	?>
	<span>
		<img name="photo_thumbnail<?php echo $num;?>" width="40" height="40" src="<?php echo $photo->getThumbURI(); ?>"/>
	</span>
	<?php endforeach; ?>
	<br class="clr" \>
</div>	