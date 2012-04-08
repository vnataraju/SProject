$(document).ready(function() {
	
	jQuery.noConflict();
	jQuery(document).ready( function(){
	    jQuery('#myBlogTab').tabs();
	    jQuery( "#myBlogTab" ).tabs({ fx: { opacity: 'toggle' } });
	
	    //remember the last tab
	    jQuery('#preferenceTab li a').click(function(){
		var tab = jQuery(this).attr('href');
		jQuery.cookie('myBlog-adminpreftab', tab);										 
	    });
		
	    if(jQuery.cookie('myBlog-adminpreftab') != null){
		var lasttab = jQuery.cookie('myBlog-adminpreftab');
		jQuery('#preferenceTab li a[href="' + lasttab + '"]').click();
	    }
	});
 
});
