<? // determines browser and platform and then choses appropriate html editor
//

//list of editors
//include("htmleditor/editor_activex.inc.php");
//include("htmleditor/editor_txt.inc.php");

function addeditor($textarea,$cols,$rows,$text,$context="story") {
	//sniffer determines browser and os
	include("sniffer.inc.php");
		
	//chose editor based on browser and platfrom
	if ($browser_os == "pcie5+") {
		if ($context=="story") {	
			include("htmleditor/editor_activex.inc.php");
			editor_activex($textarea,$cols,$rows,$text);
		} else if  ($context=="discuss") {
			editor_htmlarea($textarea,$text,$context);
		}	
		
	} else if ($browser_os != "macsafari") {
		editor_htmlarea($textarea,$text,$context);
	} else {
		editor_txt($textarea,$cols,$rows,$text);
	}
	
	//return $editorType;
}

/******************************************************************************
 * replaces textarea with HTMLarea WYSISYG 
 * if $context is story includes full menu
 * if $context is discuss includes limited menu
 * $textarea can be shorttext, longtext or discuss
 * $text is content of textarea
 ******************************************************************************/
function editor_htmlarea($textarea,$text,$context) {	
	ob_start();
	if ($context == "story") {
		include("htmlarea/story.php");	
	} else if ($context == "discuss" || $context == "email") {
		include("htmlarea/discuss.php");	
	}
	$neweditor=ob_get_contents();
	ob_end_clean();ob_start();
	printc("$neweditor");
}

/******************************************************************************
 * includes standard textarea
 ******************************************************************************/

function editor_txt($textarea,$cols,$rows,$text) {		   
	printc("<textarea name=$textarea id=$textarea cols=$cols rows=$rows>");
	printc($text);
	printc("</textarea>");
}


?>