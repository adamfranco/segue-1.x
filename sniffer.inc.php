<? /* $Id$ */

	$uagent = $_SERVER["HTTP_USER_AGENT"];
//	print $uagent;
	$isMac = (ereg("mac",$uagent) || ereg("Mac",$uagent));
//	print "<br>isMac = $isMac";
	$uagent = explode("; ",$uagent);
	$uagent = explode(" ",$uagent[1]);
	$bname = strtoupper($uagent[0]);
	$bvers = $uagent[1];
	if(($bname == "MSIE") && (intval($bvers) > 4) && (!$isMac) ) {
		$browser_os = "pcie5+";
	} else if ($bname == "MSIE" && (!$isMac)) {
		$browser_os = "pcie4";
	} else if ($bname == "MSIE" && ($isMac)) {
		$browser_os = "macie";
	} else {
		$browser_os = "mac";
	}

?>
