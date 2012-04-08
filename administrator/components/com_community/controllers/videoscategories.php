<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Jom Social Component Controller
 */
class CommunityControllerVideosCategories extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'publish' , 'savePublish' );
		$this->registerTask( 'unpublish' , 'savePublish' );
	}

	public function ajaxTogglePublish( $id , $type )
	{
		return parent::ajaxTogglePublish( $id , $type , 'videoscategories' );
	}
	
	public function ajaxSaveCategory( $data )
	{
		$response	= new JAXResponse();
	
		$row		=& JTable::getInstance( 'VideosCategory', 'CTable' );
		$row->load( $data['id'] );
		$row->name			= $data['name'];
		$row->description	= $data['description'];
		$row->parent		= $data['parent'];
		$row->store();
		
		if( $data['id'] != 0 )
		{
			// Update the rows in the table at the page.			
			$response->addAssign( 'videos-title-' . $data['id'] , 'innerHTML' , $row->name );
			$response->addAssign( 'videos-description-' . $data['id'] , 'innerHTML' , $row->description );
		}
		else
		{	
			$response->addScriptCall('azcommunity.redirect', JURI::base() . 'index.php?option=com_community&view=videoscategories');
		}
		$response->addScriptCall('cWindowHide');
		$this->cacheClean(array(COMMUNITY_CACHE_TAG_VIDEOS_CAT));
		return $response->sendResponse();
	}
	
	public function ajaxEditCategory( $id )
	{
		$response	= new JAXResponse();
		
		$uri		= JURI::base();
		$db			=& JFactory::getDBO();
		$data		= '';

		// Get the event categories
		$model		= $this->getModel( 'videoscategories' );
		$categories	= $model->getCategories();

		$row		=& JTable::getInstance( 'VideosCategory', 'CTable' );
		$row->load( $id );
		
		// Escape the output
		CFactory::load( 'helpers' , 'string' );
		$row->name	= CStringHelper::escape($row->name);
		$row->description	= CStringHelper::escape($row->description);
		
		ob_start();
?>
		<div style="line-height: 32px; padding-bottom: 10px;">
			<img src="<?php echo $uri; ?>components/com_community/assets/icons/groups_add.gif" style="float: left;" />
			<?php echo JText::_('COM_COMMUNITY_VIDEOS_CREATE_NEW_CATEGORIES');?>
		</div>
		<div style="clear: both;"></div>
		
		<form action="#" method="post" name="editVideosCategory" id="editVideosCategory">
		<table cellspacing="0" class="admintable" border="0" width="100%">
			<tbody>
				<tr>
					<td class="key" width="10%"><?php echo JText::_('COM_COMMUNITY_PARENT');?></td>
					<td>:</td>
					<td>
					    <select name="parent">
						<option value="<?php echo COMMUNITY_NO_PARENT; ?>"><?php echo JText::_('COM_COMMUNITY_NO_PARENT'); ?></option>
						<?php
						    for( $i = 0; $i < count( $categories ); $i++ )
						    {
						    	// Do not show itself as potential parent
						    	if($categories[$i]->id != $id) {
						    $selected	= ($row->parent == $categories[$i]->id ) ? ' selected="selected"' : '';
						    ?>
						    <option value="<?php echo $categories[$i]->id; ?>"<?php echo $selected; ?>><?php echo $categories[$i]->name; ?></option>
						    <?php } } ?>
					    </select>
					</td>
				</tr>
				<tr>
					<td class="key" width="10%"><?php echo JText::_('COM_COMMUNITY_NAME');?></td>
					<td>:</td>
					<td><input type="text" name="name" size="35" value="<?php echo ($id) ? $row->name : ''; ?>" /></td>
				</tr>
				<tr>
					<td class="key" valign="top"><?php echo JText::_('COM_COMMUNITY_DESCRIPTION');?></td>
					<td valign="top">:</td>
					<td>
						<textarea name="description" rows="5" cols="30"><?php echo ($id) ? $row->description : ''; ?></textarea>
					</td>
				</tr>
			</tbody>
		
			<input type="hidden" name="id" value="<?php echo ($id) ? $row->id : 0; ?>" />
		</table>
		</form>

<?php
		$contents	= ob_get_contents();
		ob_end_clean();

		$buttons	= '<input type="button" class="button" onclick="javascript:azcommunity.saveVideosCategory();return false;" value="' . JText::_('COM_COMMUNITY_SAVE') . '"/>';
		$buttons	.= '&nbsp;&nbsp;<input type="button" class="button" onclick="javascript:cWindowHide();" value="' . JText::_('COM_COMMUNITY_CANCEL') . '"/>';
		$this->cacheClean(array(COMMUNITY_CACHE_TAG_VIDEOS_CAT));
		$response->addAssign('cWindowContent', 'innerHTML' , $contents);
		$response->addScriptCall( 'cWindowActions' , $buttons );
		return $response->sendResponse();
	}

	/**
	 * Remove a category
	 **/	 	
	public function removecategory()
	{
		$mainframe	=& JFactory::getApplication();
		
		$ids	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$count	= count($ids);

		$row		=& JTable::getInstance( 'VideosCategory', 'CTable' );
		
		foreach( $ids as $id )
		{
			if(!$row->delete( $id ))
			{
				// If there are any error when deleting, we just stop and redirect user with error.
				$message	= JText::_('COM_COMMUNITY_VIDEOS_ASSIGNED_CATEGORIES');
				$mainframe->redirect( 'index.php?option=com_community&view=videoscategories' , $message);
				exit;
			}
		}
		$this->cacheClean(array(COMMUNITY_CACHE_TAG_VIDEOS_CAT));
		$message	= JText::sprintf( '%1$s Category(s) successfully removed.' , $count );
		$mainframe->redirect( 'index.php?option=com_community&view=videoscategories' , $message );
	}
	
	public function Publish()
	{
	    //TODO
		exit;
	}
}