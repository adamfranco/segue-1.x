<? // determines browser and platform and then choses appropriate html editor
//

//list of editors
include("htmleditor/editor_activex.inc.php");
include("htmleditor/editor_txt.inc.php");


function addeditor($textarea,$cols,$rows,$text) {
	//sniffer determines browser and os
	include("sniffer.inc.php");
		
	//chose editor based on browser and platfrom
	if ($browser_os == "pcie5+") {		
		editor_activex($textarea,$cols,$rows,$text);
	} else {
		editor_txt($textarea,$cols,$rows,$text);
	}
	//return $editorType;
}

?>