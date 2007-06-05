<?php
/*
If you wish to change CaRP's default configuration values, we recommeng doing
it in this file rather than modifying carp.php. That way, when you upgrade to
a new version, you won't need to copy your override settings into the new
version.

See the online documentation for details.
http://www.mouken.com/rss/carp/manual/
*/
function MyCarpConfReset($set='default') {
	global $carpconf;
	CarpConfReset();
	
	// Override any settings you wish to change here
	
	// Modifications have been made to carpinc.php that provided functionality
	// for displaying authors and dates.
	
	$carpconf['clinktarget'] = "_blank";
	$carpconf['clinkstyle'] = "font-size: 100%;";
	$carpconf['acb'] = "<hr />";
	
	$carpconf['bitems'] = "<table>";
	$carpconf['bi'] = "<tr><td>";
	
	
	$carpconf['ilinktarget'] = "_blank";
	$carpconf['ilinkstyle'] = "font-size: 100%;";
	
	$carpconf['bauthor'] = "<em> by <strong>";
	$carpconf['aauthor'] = "</strong></em>";
	
	$carpconf['idateformat'] = "D, M j G:i:s T Y";
	$carpconf['bdate'] = "<em> on ";
	$carpconf['adate'] = "</em>";
	
	$carpconf['bidesc'] = "<br /> &nbsp; &nbsp; &nbsp; &nbsp; ";
	$carpconf['aidesc'] = "<br /><br />";
	
	$carpconf['ai'] = "</td></tr>";
	$carpconf['aitems'] = "</table>";

	$carpconf['poweredby'] = "";
	$carpconf['cacheinterval'] = 10;
	
	$carpconf['cacheinterval'] = 10;
	
	global $cfg;
	$carpconf['cachepath'] = $cfg[uploaddir]."/RSScache/";
	$carpconf['cacherelative'] = FALSE;	
	
	// Create alternative configuration sets here
	if ($set=='default') {
		
	} else if ($set=='rss_titles') {
		CarpConf('cborder','link');
		$carpconf['clinktarget'] = "_blank";
		$carpconf['clinkstyle'] = "font-size: 9px%;";
		$carpconf['acb'] = "\n<div style='border-bottom: 1px solid gray'></div>";
		$carpconf['iorder'] = "link";
		$carpconf['ilinktarget'] = "_blank";
		$carpconf['ilinkstyle'] = "font-size: 9px;";
		$carpconf['maxititle'] = 50;
		$carpconf['maxitems'] = 10;
		
	} else if ($set=='rss_contentblock') {
		CarpConf('cborder','link');
		$carpconf['iorder'] = "link,desc,author,date";
		$carpconf['clinktarget'] = "_blank";
		$carpconf['clinkstyle'] = "font-size: 100%;";
		$carpconf['actitle'] = " (RSS)";
		$carpconf['acb'] = "\n<div class='hr'><hr /></div>";
		
		$carpconf['bitems'] = "\n<table>";
		$carpconf['bi'] = "\n\t<tr>\n\t\t<td>\n\t\t\t";
				
		$carpconf['ilinktarget'] = "_blank";
		$carpconf['ilinkstyle'] = "font-size: 100%; font-weight: bold;";
		
		
		$carpconf['bidesc'] = " ";
		$carpconf['aidesc'] = "\n\t\t\t<div class='contentinfo' align='right'>";
		
		$carpconf['ai'] = "\n\t\t</td>\n\t</tr>";
		$carpconf['aitems'] = "\n</table>";

		$carpconf['bauthor'] = "\n\t\t\t\tby ";
		$carpconf['aauthor'] = " via RSS";
		
		$carpconf['idateformat'] = "n/j/Y g:i A";
		$carpconf['bdate'] = "\n\t\t\t\t on ";
		$carpconf['adate'] = "\n\t\t\t</div>\n\t\t\t<div class='hr'><hr /></div>";
	
		$carpconf['poweredby'] = "";
		$carpconf['cacheinterval'] = 10;
		
		$carpconf['cacheinterval'] = 10;
	

		$carpconf['maxititle'] = 100;
		$carpconf['maxitems'] = 10;	
	}
}
MyCarpConfReset();
?>