<? // editor control --

function addeditor($textarea,$cols,$rows,$text) {	
	global $HTTP_SERVER_VARS;
	// browser/platform sniffer
	$uagent = $HTTP_SERVER_VARS["HTTP_USER_AGENT"];
	$isMac = ereg("mac",$uagent);
	$uagent = explode("; ",$uagent);
	$uagent = explode(" ",$uagent[1]);
	$bname = strtoupper($uagent[0]);
	$bvers = $uagent[1];
	if(($bname == "MSIE") && (intval($bvers) >= 5) && (!$isMac) ) {
		$editorType = "activex";
	} else {
		$editorType = "txt";
	}
	
	if ($editorType == "activex") {
		include("htmleditor/editor_activex.inc.php");
	
	}
		
}

?>