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
	$carpconf['clinkstyle'] = "font-size: 150%;";
	$carpconf['acb'] = "<hr>";
	
	$carpconf['bitems'] = "<table>";
	$carpconf['bi'] = "<tr><td>";
	
	
	$carpconf['ilinktarget'] = "_blank";
	$carpconf['ilinkstyle'] = "font-size: 125%;";
	
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
	
	$carpconf['cacheinterval'] = 1;
	
	global $cfg;
	$carpconf['cachepath'] = $cfg[uploaddir]."/RSScache/";
	$carpconf['cacherelative'] = FALSE;	
	
	// Create alternative configuration sets here
	if ($set=='default') {
		
	} else if ($set=='style1') {
		
	}
}
MyCarpConfReset();
?>