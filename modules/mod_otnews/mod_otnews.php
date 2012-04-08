<?php
/**
 # mod_otnews - OT News Module for Joomla! 1.7
 # author       OmegaTheme.com
 # copyright    Copyright(C) 2011 - OmegaTheme.com. All Rights Reserved.
 # @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Website:     http://omegatheme.com
 # Technical support: Forum - http://omegatheme.com/forum/
**/
/**------------------------------------------------------------------------
 * file: mod_otnews.php 1.7.0 00001, April 2011 12:00:00Z OmegaTheme $
 * package: OT News Module
 *------------------------------------------------------------------------*/
//No direct access!
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).DS.'helper.php';

$doc = &JFactory::getDocument();
$doc->addStyleSheet(JURI::base().'modules/mod_otnews/css/layout.css');
$doc->addScript(JURI::base().'modules/mod_otnews/js/ot.script.js');

$list_all = modOtNewsHelper::getCategory($params);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_otnews', $params->get('layout', 'default'));