<?php
/**
 * @version		$Id: EmailAsUsername.php version 3.0 $
 * @package		EmailAsUsername
  * @copyright	Copyright (C) 2005 - 2011 www.lunarhotel.co.uk, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * EmailAsUsername Authentication plugin
 */
class plgAuthenticationEmailAsUsername extends JPlugin
{
		 
	function onUserAuthenticate( & $credentials, $options, &$response)
	{
		jimport('joomla.user.helper');

		$response->type = 'EmailAsUsername';
		// Joomla does not like blank passwords
		if (empty($credentials['password'])) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
			return false;
		}

		// Initialise variables.
		$conditions = '';

		// Get a database object
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('id, password, username');
		$query->from('#__users');
		$query->where('email =' . $db->Quote($credentials['username']));

		$db->setQuery($query);
		$result = $db->loadObject();
		
		
		if ($result) {
			
			$credentials['username'] = $result->username;
			
			$parts	= explode(':', $result->password);
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);

			if ($crypt == $testcrypt) {
				$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
				
				$response->email = $user->email;
				$response->fullname = $user->name;
				if (JFactory::getApplication()->isAdmin()) {
					$response->language = $user->getParam('admin_language');
				}
				else {
					$response->language = $user->getParam('language');
				}
				$response->status = JAUTHENTICATE_STATUS_SUCCESS;
				$response->error_message = '';
			} else {
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
			}
		} else {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
		}
	}
}
