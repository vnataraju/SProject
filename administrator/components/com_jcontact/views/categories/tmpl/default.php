<?php
/************************************************************/
/* Title……..: J!Contact
/* Description..: An integration of iContact and Joomla
/* Author…….: Joomlashack LLC
/* Version……: For Joomla! 1.5.x Stable ONLY
/* Created……: 04/13/07
/* Contact……: support@joomlashack.com
/* Copyright….: Copyright© 2007 Joomlashack LLC. All rights reserved.
/* License……: Commercial
/************************************************************/

defined('_JEXEC') or die('Restricted access');
?>
<script language="javascript" type="text/javascript">
<!--
/*
$jq("#submit_category").click(function () { 
	$jq("#maillist").attr("value", $jq("select option:selected").val());
	$jq("#maillist_text").attr("value", $jq("select option:selected").text());
	$jq("#_maillist_text").text($jq("select option:selected").text());

	$jq.nyroModalRemove();
});
*/
jQuery(function($) { 
	$jq("#submit_category").click(function () { 
		$jq("#maillist").attr("value", $jq("select option:selected").val());
		$jq("#maillist_text").attr("value", $jq("select option:selected").text());
		$jq("#_maillist_text").text($jq("select option:selected").text());
		$jq.nyroModalRemove();
	});
});

-->
</script>

<form action="index.php" method="post" name="adminForm">
	
	<h1><?php echo JText::_( 'Select category' ); ?></h1>
	
	<div>	
		<?php echo $this->lists['icontact_lists']; ?>
	</div>	
	
	<br />
	
	<div>	
		<input type="button" name="submit_category" id="submit_category" value="<?php echo JText::_( 'Choose' ); ?>" />
	</div>

<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="categories" />
<?php echo JHTML::_( 'form.token' ); ?>

</form>
<?php die(); ?> 