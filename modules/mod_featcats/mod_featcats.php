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

// Include the helper functions only once
require_once dirname(__FILE__).'/helper.php';

$idbase = $params->get('catid');

$cacheid = md5(serialize(array ($idbase,$module->module)));

$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'modFeatcatsHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = $cacheid;

$cats = JModuleHelper::moduleCache ($module, $params, $cacheparams);

if ( $params->get('add_css') ) :
	$document = JFactory::getDocument();
	$document->addStyleSheet('modules/mod_featcats/mod_featcats.css');
endif;


if (!empty($cats)) {
	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
	$item_heading = $params->get('item_heading');
	$cat_heading = $params->get('cat_heading');
    require JModuleHelper::getLayoutPath('mod_featcats', $params->get('layout', 'default'));
}
