<?php 
/*------------------------------------------------------------------------
 # mod_leouserpanel - Leo UserPanel Module
 # ------------------------------------------------------------------------
 # author    LeoTheme
 # copyright Copyright (C) 2010 leotheme.com. All Rights Reserved.
 # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.leotheme.com
 # Technical Support:  Forum - http://www.leotheme.com/forum.html
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );  
/**
 * Get a collection of categories
 */
class JFormFieldLoftoolbar extends JFormField {
	
	/*
	 * Category name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'fgroup'; 	
	
	/**
	 * fetch Element 
	 */
	protected function getInput(){		
		// echo '<pre>'.print_r($this->element,1);  
	  ?> 
	<ul class="lof-toolbar-items">
    	<?php foreach( $this->element->children() as $option ) { ?>
    	<li><?php echo $option->data();?></li>
        <?php } ?>
    </ul>			
<?php 
	}
	function getLabel(){
		return ;	
	}
}

?>
