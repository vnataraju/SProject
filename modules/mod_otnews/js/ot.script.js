/**
 # mod_otnews - OT News Module for Joomla! 1.7
 # author       OmegaTheme.com
 # copyright    Copyright(C) 2011 - OmegaTheme.com. All Rights Reserved.
 # @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Website:     http://omegatheme.com
 # Technical support: Forum - http://omegatheme.com/forum/
**/
/**------------------------------------------------------------------------
 * file: ot.script.js 1.7.0 00001, March 2011 12:00:00Z OmegaTheme $
 * package: OT News Module
 *------------------------------------------------------------------------*/
/* ++++++++++++++++++++  Document OT Script ++++++++++++++++++++++  */
function equalHeightTop () {
	var elements = document.getElements('.blog-news-i');
	var maxHeight = 0;
	/* Get max height */
	elements.each(function(item, index){
		var height = parseInt(item.getStyle('height'));
		if(height > maxHeight){ maxHeight = height; }
	});
	elements.setStyle('height', maxHeight+'px');
}
window.addEvent ('load', function() {
	equalHeightTop ();
});