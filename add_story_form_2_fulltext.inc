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
	include("htmleditor/editor.inc.php");
		
	printc("<br>");
	printc("<table width=80%><tr><td style='padding-left: 15px'>");
	printc("Extended Content (Optional):");
	printc("<br>");
		
	//	print out text areas with editor (determined by addeditor function)
	//  addeditor function (defined in editor.inc.php)
	//  addeditor function variables (textarea, cols, rows, text)
	printc(addeditor ("longertext",80,16,$_SESSION[storyObj]->getField("longertext")));
	printc("</td></tr></table>");

	printc("<div align=right>");
	printc("What type of text did you enter?");
//	printc (" <select name=texttype>");
//	printc("<option value=text".(($_SESSION[settings][texttype]=='text')?" selected":"").">Plain text");
//	printc("<option value=html".(($_SESSION[settings][texttype]=='html')?" selected":"").">Preformatted HTML");
//	printc("</select>");
	printc("<input type=textarea disabled value='".(($_SESSION[storyObj]->getField("texttype")=='text')?" Plain text":" Preformatted HTML")."'>");
	printc("</div>");
	printc("<div class=desc align=right>Change this option on the previous screen.</div>");
	//	end modification 1 - 11.1.02 - achapin
}

if ($_SESSION[storyObj]->getField("type") != 'story') printc("<br><br>");

printc("</form>");

