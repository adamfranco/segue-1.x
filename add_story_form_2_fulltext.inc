<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");
printc("<input type=hidden name='step'>");

//printc("<input type=hidden name=site value='$_SESSION[settings][site]'>");
//printc("<input type=hidden name=section value=$_SESSION[settings][section]>");
//printc("<input type=hidden name=page value=$_SESSION[settings][page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$_SESSION[settings][page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

if ($_SESSION[settings][add]) printc("<div class=title>Add Content to Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
else  printc("<div class=title>Edit Content on Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
	
//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");


if ($_SESSION[storyObj]->getField("type")=='story') {
	//	Begin modification 1 - 11.1.02 - achapin
	// removed active-x editor code to editor_activex.php
	// editor.inc.php determines browser/platform and choses appropriate editor
	//print $shorttext;
	
	printc("<div style='font-size:10px; color:#D00'>");
	printc("<br>Note: Links to other pages in this site must use the address displayed in the PREVIEW site mode not the EDIT mode.  ");
	printc("To ensure correct link URL's to any page on this site, click Preview this Site button before copying URL.<br>");
	printc("</div>");

	//sniffer determines browser and os
	//sniffer included in editor.inc.php
	include("sniffer.inc.php");
	//$uagent = $_SERVER["HTTP_USER_AGENT"];
	//printc("<div style='font-size:10px; color:#999'>($uagent)</div>");

	if ($supported == 0) {
		printc("<br><div style='font-size:10px;'>Looks like you are using a browser that does not support a built-in HTML editor.  ");
		if ($isWin) {
			printc("We recommend you use either <a href=http://www.microsoft.com/windows/ie/ target=new_window>Internet Explorer or greater</a> 
			or <a href=http://www.mozilla.org/download.html target=new_window>Mozilla 1.3 or greater</a>.");
		} else {
			printc("We recommend you use <a href=http://www.mozilla.org/download.html target=new_window>Mozilla 1.3 or greater</a>.");
		}		
		//printc("<div style='font-size:10px; color:#999'>browser information: $uagent</div>");		
		printc("  In the meantime, if you are editing existing content that is in HTML format, you will need to work
		around the html tags.</div>");
	}

	if ($supported == 1) {
		if (!$_SESSION[settings][editor]) $_SESSION[settings][editor] = 'htmlarea';
		//printpre($_SESSION[settings][editor]);
		if ($isWinIE) {
			printc("<div style='font-size:10px;'>Your web browser supports two different HTML editors.  ");
			printc("We recommend you chose HTMLarea for the greatest cross-platform compatibility.</div>");
			printc("<div align=right>Current Editor: <select name=editor onChange='document.addform.submit()'>");
			printc("<option value=htmlarea".(($_SESSION[settings][editor]=='htmlarea')?" selected":"").">HTMLarea");
			printc("<option value=activex".(($_SESSION[settings][editor]=='activex')?" selected":"").">Active-X");
			printc("</select></div>");
			//printc("<input type=button value='Update' onClick='document.addform.submit()'>\n");						
		}	
	}

	include("htmleditor/editor.inc.php");
		
	printc("<br>");
	printc("<table width=80%><tr><td style='padding-left: 15px'>");
	printc("Extended Content (Optional):");
	printc("<br>");
	
		//	print out text areas with editor (determined by addeditor function)
	//  addeditor function (defined in editor.inc.php)
	//  addeditor function variables (textarea, cols, rows, text)
	//printpre($_SESSION[storyObj]);
	if ($_SESSION[settings][editorinitstate] == "text" && $supported == 1) {
		$text = htmlbr($_SESSION[storyObj]->getField("longertext"));
	} else {
		$text = $_SESSION[storyObj]->getField("longertext");
	}

		
	//	print out text areas with editor (determined by addeditor function)
	//  addeditor function (defined in editor.inc.php)
	//  addeditor function variables (textarea, cols, rows, text)
	printc(addeditor ("longertext",60,26,$text));
	printc("</td></tr></table>");

	//sniffer determines browser and os
	if ($supported == 1) {
		printc("<input type=hidden name='texttype' value='html'>");
	} else {
		printc("<div align=right>");
		printc("What type of text did you enter?");
	//	printc (" <select name=texttype>");
	//	printc("<option value=text".(($_SESSION[settings][texttype]=='text')?" selected":"").">Plain text");
	//	printc("<option value=html".(($_SESSION[settings][texttype]=='html')?" selected":"").">Preformatted HTML");
	//	printc("</select>");
		printc("<input type=textarea disabled value='".(($_SESSION[storyObj]->getField("texttype")=='text')?" Plain text":" Preformatted HTML")."'>");
		printc("</div>");
		printc("<div class=desc align=right>Change this option on the previous screen.</div>");
	}
}

if ($_SESSION[storyObj]->getField("type") != 'story') printc("<br><br>");

printc("</form>");

