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

if ($_SESSION[settings][add]) printc("<div class=title>Add Item to Section \"".$thisSection->getField("title")."\"</div>");
else  printc("<div class=title>Edit Item \"".$_SESSION[pageObj]->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
	//printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");


if ($_SESSION[pageObj]->getField("type")=='page') {
	printc("<div class=title style='font-size: 14px'>Display Options</div>");
	printc("<div class=leftmargin>");
	
	printc("Order the Content Blocks on this page by: ");
	printc("<select name='storyorder'>");
		printc("<option value=''".(($_SESSION[pageObj]->getField("storyorder")=='')?" selected":"").">Custom (Default)");
		printc("<option value='addeddesc'".(($_SESSION[pageObj]->getField("storyorder")=='addeddesc')?" selected":"").">Recent First");
		printc("<option value='addedasc'".(($_SESSION[pageObj]->getField("storyorder")=='addedasc')?" selected":"").">Recent Last");
		printc("<option value='editeddesc'".(($_SESSION[pageObj]->getField("storyorder")=='editeddesc')?" selected":"").">Recently Edited First");
		printc("<option value='editedasc'".(($_SESSION[pageObj]->getField("storyorder")=='editedasc')?" selected":"").">Recently Edited Last");
		printc("<option value='author'".(($_SESSION[pageObj]->getField("storyorder")=='author')?" selected":"").">Author");
		printc("<option value='editor'".(($_SESSION[pageObj]->getField("storyorder")=='editor')?" selected":"").">Editor");
		printc("<option value='category'".(($_SESSION[pageObj]->getField("storyorder")=='category')?" selected":"").">Category");
		printc("<option value='titleasc'".(($_SESSION[pageObj]->getField("storyorder")=='titleasc')?" selected":"").">Title Ascending");
		printc("<option value='titledesc'".(($_SESSION[pageObj]->getField("storyorder")=='titledesc')?" selected":"").">Title Descending");
	printc("</select><br><br>");
	
	printc("Show horizontal rule: <input type=checkbox name='showhr' value=1". (($_SESSION[pageObj]->getField("showhr"))?" checked":"") . "><br>");
	printc("<div class=desc>By checking the above box, a horizontal rule will be placed between content blocks. A horizontal rule will also be displayed if either of the following two checkboxes are checked.</div>");
		
	printc("Show added/edited by <i>username</i> for content: <input type=checkbox name='showcreator' value=1". (($_SESSION[pageObj]->getField("showcreator"))?" checked":"") . "><BR>");
	printc("<div class=desc>By checking the above box, the usernames of the people that added or edited the content on this page will be displayed along with the story text.</div>");
		
	printc("Show added/edited timestamp: <input type=checkbox name='showdate' value=1". (($_SESSION[pageObj]->getField("showdate"))?" checked":"") . "><br>");
	printc("<div class=desc>By checking the above box, the date and time content was added (and edited, if that be the case), will be displayed along with the story text.</div>");
	
	printc("Archive content on this site by: ");
	printc("<select name=archiveby>");
	$l = array("none"=>"Do not archive","week"=>"Week","month"=>"Month","year"=>"Year");
	foreach ($l as $n=>$v) {
		printc("<option value='$n'".(($_SESSION[pageObj]->getField("archiveby")==$n)?" selected":"").">$v\n");
	}
	printc("</select><br>");
	printc("<div class=desc>You can organize the content in this site by week added, month added, or year added. This is useful for journal-like pages, where content is long and added often.</div>");
			
	printc("</div>");
}

printc("<br><br>");


printc("</form>");

