<?php
/*------------------------------------------------------------------------
# Joomla Carousel Module by JoomShaper.com
# ------------------------------------------------------------------------
# author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomshaper.com
-------------------------------------------------------------------------*/
defined('JPATH_BASE') or die();
jimport('joomla.form.formfield');
jimport( 'joomla.filesystem.folder' );

class JFormFieldFolderTree extends JFormField
{
	protected $type = 'FolderTree';

	protected function getInput()
	{
		$options = array();
		// path to images directory
		$path		= JPATH_ROOT.DS.$this->element['directory'];
		$filter		= $this->element['filter'];
		$folders	= JFolder::listFolderTree($path, $filter);

		foreach ($folders as $folder)
		{
			$options[] = JHtml::_('select.option', str_replace(DS,"/",$folder['relname']), str_replace(DS,"/",substr($folder['relname'], 1)));
		}		

		return JHtml::_('select.genericlist', $options, $this->name, '', 'value', 'text', $this->value);
	}
}
