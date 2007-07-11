<? /* $Id$ */

include("output_modules/common.inc.php");

include_once (dirname(__FILE__)."/carprss/carp.php");
/*  */
/* if ($a[category]) { */
/* 	printc("<div class='contentinfo' align='right'>"); */
/* 	printc("Category: <b>".spchars($a[category])."</b>"); */
/* 	printc("</div>"); */
/* } */

//  print "<pre>"; 
//  print_r($o->data); 
//  print "</pre>";
 
ob_start();
print "\n\n";

$url = $o->getField("url");
// if (ereg("^".$cfg[full_uri], $url)
// 	|| ereg("^".$cfg[personalsitesurl], $url)
// 	|| ereg("^".$cfg[classsitesurl], $url))
// {
// 	$replacement = "index.php?".session_name()."=".session_id();
// 	$url = ereg_replace("index.php\?", $replacement, $url);
// }

MyCarpConfReset();
MyCarpConfReset('rss_contentblock');

if (is_numeric($o->getField("shorttext"))) {
	$num_per_page = $o->getField("shorttext");
	CarpConf('maxitems',$num_per_page);						
} else {
	CarpConf('maxitems',5);
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
printc("<div class='story'>");
printc (ob_get_contents());
printc("</div>");
ob_clean();


if ($o->getField("title")) {

	printc("\n\n<div class='contentinfo'>".spchars($o->getField("title"))."</div>\n");
// printc("<div><a href='".$o->getField("url")."' target='_blank'>".$o->getField("url")."</a></div>");
// if ($o->getField("shorttext")) printc("<div class='desc'>".stripslashes($o->getField("shorttext"))."</div>");

}

if ($o->getField("longertext") && !ereg("^[\n\r]*<br />$", $o->getField("longertext"))) {
	if ($action == 'viewsite')
		$discussAction = 'viewsite';
	else if (ereg("preview_edit_as|preview_as", $action))
		$discussAction = ereg_replace("preview_edit_as", "preview_as", $action);
	else
		$discussAction = 'site';
		
	$link = "index.php?$sid&amp;action=".$discussAction."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$o->id."&amp;detail=".$o->id;
	printc("<div align='right'><a href='".$link."'>"." More >></a></div>\n");
}

if ($o->getField("discuss")) {
	include (dirname(__FILE__)."/discussionLink.inc.php");
}