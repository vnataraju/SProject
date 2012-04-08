<?php
/**
 * @version		$Id: mod_LoginRadius.php 1.3 16 Team LoginRadius
 * @copyright	Copyright (C) 2011 - till Open Source Matters. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
global $mainframe;
$params->def('greeting', 1);
$type = modLoginRadiusHelper::getType();
$return = modLoginRadiusHelper::getReturnURL($params,$type);
$user =& JFactory::getUser();
require JModuleHelper::getLayoutPath('mod_LoginRadius', $params->get('layout', 'default'));

