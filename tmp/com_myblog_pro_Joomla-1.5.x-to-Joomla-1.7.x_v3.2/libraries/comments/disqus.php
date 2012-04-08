<?php
/**
 * MyBlog
 * @package MyBlog
 * @copyright (C) 2006 - 2008 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/
 
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class MYComments_Disqus{
	
	var $shortname;
	
	function __construct(){
		global $_MY_CONFIG;
		$mconfig = $_MY_CONFIG; //MYBLOG_Factory::getConfig();
        $this->shortname = $mconfig->get('disqusShortname');
	}
	
	function getHTML($id=''){
		$html = '<div id="disqus_thread"></div>
				<script type="text/javascript">
			    var disqus_shortname = "'.$this->shortname.'"; 
				var disqus_developer = 1;			
    			var disqus_identifier = "'.$id.'";

   				 (function() {
					var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
					dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
					(document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
				})();
			</script>
			<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
			<a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>';
		return $html;
	}
	
	function getCount($id='', $permalink=''){
		if(empty($id)) return '';
		$html = '<a href="'.dirname(JURI::base()).$permalink.'#disqus_thread" data-disqus-identifier="'.$id.'"></a>';    
		$disquscountjs = '<script type="text/javascript">
							var disqus_shortname = "'.$this->shortname.'";
							(function () {
								var s = document.createElement("script"); s.async = true;
								s.type = "text/javascript";
								s.src = "http://disqus.com/forums/" + disqus_shortname + "/count.js";
								(document.getElementsByTagName("HEAD")[0] || document.getElementsByTagName("BODY")[0]).appendChild(s);
							}());
						 </script>';
		
		return $html.$disquscountjs;
	}	
}