<? // browser/platform sniffer

	global $HTTP_SERVER_VARS;
	
	$uagent = $HTTP_SERVER_VARS["HTTP_USER_AGENT"];
	$isMac = ereg("mac",$uagent);
	$uagent = explode("; ",$uagent);
	$uagent = explode(" ",$uagent[1]);
	$bname = strtoupper($uagent[0]);
	$bvers = $uagent[1];
	if(($bname == "MSIE") && (intval($bvers) > 4) && (!$isMac) ) {
		$browser_os = "pcie5+";
	} else {
		$browser_os = "mac";
	}

?>
