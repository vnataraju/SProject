<?php

/**
 * Show the maintenance page
 */ 
function myAdminMaintenance(){
	?>
	<table class="mytable" width="100%" cellspacing="0" cellpadding="0" border="0">
		<th width="30%">Action</th>
		<th width="70%">Description</th>
		<tr>
		    <td>
		        <a style="text-decoration: none !important;" href="index.php?option=com_myblog&task=clearcache">
		            <span class="CommonTextButtonSmall" onClick="">Clear all My Blog Cache</span>
				</a>
			</td>
			<td>
				Clears My Blog's cache.
			</td>
		</tr>
<!--
		<tr>
			<td>
				<a style="text-decoration:none !important;" href="index.php?option=com_myblog&task=fixlinks">
					<span class="CommonTextButtonSmall" onclick="">Fix permalinks</span>
				</a>
			</td>
			<td>
				This tool will fix permalinks associated with content, and should be used if you are getting
				a <i>'content is not found or has been unpublished'</i> error when clicking into a blog entry.<br />
				<b>Actions performed:</b><br />
				- Go through each My Blog content and makes sure each permalink consists of alphanumeric characters, '.', '-' and '_' characters only.
				<br />- Ensure each content managed through My Blog has a permalink			</td>
		</tr>
-->
		<tr>
			<td><a style="text-decoration:none !important;" href="index.php?option=com_myblog&amp;task=fixdashboardlinks"><span class="CommonTextButtonSmall" onclick="">Fix dashboard link</span></a> </td>
			<td>Fix any corrupted link to My Blog dashboard or create a new one if necessary in the 'User menu'</td>
		</tr>
	</table>
<?php
}

?>
