<? // determines browser and platform and then choses appropriate html editor
//

//list of editors
//include("htmleditor/editor_activex.inc.php");
//include("htmleditor/editor_txt.inc.php");

function addeditor($textarea,$cols,$rows,$text,$context="story", $texttype = NULL) {
	//sniffer determines browser and os
	include("sniffer.inc.php");
	
	//chose editor based on browser and platfrom
	if ($winIEVersion > 5.5) {
		if ($context=="story" && $_SESSION[settings][editor]=='activex') {	
			include("htmleditor/editor_activex.inc.php");
			editor_activex($textarea,$cols,$rows,$text);
		} else if ($context=="story" && $_SESSION[settings][editor]=='htmlarea') {
			editor_htmlarea($textarea,$text,$context);
		} else if ($context=="story" && $_SESSION[settings][editor]=='fckeditor') {
			editor_fck($textarea,$text,$context);			
		} else if ($context=="story" && $_SESSION[settings][editor]=='txt') {
			$replaceBRs = ($texttype == 'html')?FALSE:TRUE;
			editor_txt($textarea,$cols,$rows,$text, $replaceBRs);		
		} else if ($context=="discuss") {
			editor_fck($textarea,$text,$context);
		} else {
			editor_fck($textarea,$text,$context);
		}
		
	} else if ($supported == 0) {
		$replaceBRs = ($texttype == 'html')?FALSE:TRUE;
		editor_txt($textarea,$cols,$rows,$text, $replaceBRs);
	} else {
		if ($_SESSION[settings][editor]=='fckeditor') {	
			editor_fck($textarea,$text,$context);
		} else if ($_SESSION[settings][editor]=='htmlarea') {
			editor_htmlarea($textarea,$text,$context);
		} else if ($_SESSION[settings][editor]=='txt') {
			$replaceBRs = ($texttype == 'html')?FALSE:TRUE;
			editor_txt($textarea,$cols,$rows,$text, $replaceBRs);		
		} else {
			editor_fck($textarea,$text,$context);
		}
		//editor_htmlarea($textarea,$text,$context);
		//editor_fck($textarea,$text,$context);
	}
	
	//return $editorType;
}

/**
 * Clean the editor text to remove/convert line-returns.
 * 
 * @param string $text The text to convert
 * @return string
 * @access public
 * @date 9/15/04
 */
function cleanEditorText ($text, $texttype = NULL) {
	// If we are using a plain text-field convert any linereturns to <br /> tags
	// Make sure that we have the content formatted correctly.
		
	include ("sniffer.inc.php");
	
	// If we just have a text box, replace new lines with <br /> tags
	// If the user didn't specify "html" as the texttype.
	if (!$supported && $texttype != 'html') {
		$text = htmlbr($text);
	}
	// This is a hack, but HTMLAREA adds a \n at the begining of the text.
	// Let remove it instead of wading through all of the htmlarea code.
	else if ($supported) {
		$text = preg_replace("/$\n/", "", $text);
	}
	
	return $text;
}



/******************************************************************************
 * replaces textarea with HTMLarea WYSISYG 
 * if $context is story includes full menu
 * if $context is discuss includes limited menu
 * $textarea can be shorttext, longtext or discuss
 * $text is content of textarea
 ******************************************************************************/
function editor_htmlarea($textarea,$text,$context="story") {	
	ob_start();	
	if ($context == "story") {
		include("htmlarea/story.php");	
	} else if ($context == "discuss" || $context == "email") {
		include("htmlarea/discuss.php");	
	} else if ($context == "page") {
		include("htmlarea/discuss.php");
	}

	$neweditor=ob_get_contents();
	ob_end_clean();ob_start();
	printc($neweditor);
}

/******************************************************************************
 * includes standard textarea
 ******************************************************************************/

function editor_txt($textarea,$cols,$rows,$text,$replaceBRs=TRUE) {		   
	printc("<textarea name='$textarea' id='$textarea' cols='$cols' rows='$rows'>");
	// Replace the <br /> and <br /> tags with \n's for the textarea.
	if ($replaceBRs)
		printc(preg_replace("/<br(\s\/)?>/", "\n", $text));
	else
		printc($text);
	printc("</textarea>");
}

/******************************************************************************
 * includes FCKeditor
 ******************************************************************************/

function editor_fck($textarea,$text,$context="story") {	
	ob_start();	
	include("fckeditor_create.php");
		
	$neweditor=ob_get_contents();
	ob_end_clean();ob_start();
	printc($neweditor);
}



?>