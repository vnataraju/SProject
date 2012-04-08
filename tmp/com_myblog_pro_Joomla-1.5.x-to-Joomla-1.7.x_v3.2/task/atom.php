<?php
/**
 * @package		My Blog
 * @copyright (C) 2011 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

require_once( MY_COM_PATH . DS . 'task' . DS . 'base.php' );
require_once( MY_LIBRARY_PATH . DS . 'atom.php' );

class MyblogAtomTask extends MyblogBaseController
{
	function MyblogAtomTask()
	{
		$this->toolbar	= MY_TOOLBAR_ACCOUNT;
	}

	function display()
	{
		//header("Content-Type: application/x.atom+xml; charset=ISO-8859-1");
		$itemid = myGetItemId();
		$atom = new MYAtom($itemid);
		echo $atom->respond();
		exit;
	}
}


