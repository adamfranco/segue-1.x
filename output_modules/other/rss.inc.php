<? /* $Id$ */

include("output_modules/common.inc.php");

include_once (dirname(__FILE__)."/carprss/carp.php");
/*  */
/* if ($a[category]) { */
/* 	printc("<div class=contentinfo align='right'>"); */
/* 	printc("Category: <b>".spchars($a[category])."</b>"); */
/* 	printc("</div>"); */
/* } */

//  print "<pre>"; 
//  print_r($o->data); 
//  print "</pre>";
 
ob_start();

$url = $o->getField("url");
if (ereg("^".$cfg[full_uri], $url)
	|| ereg("^".$cfg[personalsitesurl], $url)
	|| ereg("^".$cfg[classsitesurl], $url))
{
	$url = ereg_replace("index.php\?", "index.php?".strip_tags(SID), $url);
	print "\n$url\n<br />";
}

// If we have an auser, create a cache just for them.
if ($_SESSION['auser']) {
	CarpCacheShow($url, '', 1,  $_SESSION['auser']);
} else {

	// If the user has a valid campus ip-address, then they are a
	// member of 'institute'.
	$ipIsInInstitute = FALSE;
	$ip = $_SERVER[REMOTE_ADDR];
	// check if our IP is in inst_ips
	if (is_array($cfg[inst_ips])) {
		foreach ($cfg[inst_ips] as $i) {
			if (ereg("^$i",$ip)) 
				$ipIsInInstitute = TRUE;
		}
	}
	
	// if we are in the institute IPs, use the institute
	// cache.
	if ($ipIsInInstitute) {
		CarpCacheShow($url, '', 1, 'institute');
	}
	// If we aren't logged in or in the institute IPs, just use the
	// everyone cache.
	else {
		CarpCacheShow($url);
	}
}

printc (ob_get_contents());
ob_clean();


if ($o->getField("title")) printc("<div class=leftmargin><b>".spchars($o->getField("title"))."</b></div>");
// printc("<div><a href='".$o->getField("url")."' target='_blank'>".$o->getField("url")."</a></div>");
// if ($o->getField("shorttext")) printc("<div class=desc>".stripslashes($o->getField("shorttext"))."</div>");
if ($o->getField("discuss")) {
	include (dirname(__FILE__)."/discussionLink.inc.php");
}