<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method='post' name='addform'>");
printc("<input type='hidden' name='step' />");

//printc("<input type='hidden' name='site' value='$_SESSION[settings][site]' />");
//printc("<input type='hidden' name='section' value='$_SESSION[settings][section]' />");
//printc("<input type='hidden' name='page' value='$_SESSION[settings][page]' />");
//if ($edit) printc("<input type='hidden' name='edit_page' value='$_SESSION[settings][page]' />");
printc("<input type='hidden' name='action' value='$action' />");

//printc("<input type='hidden' name='mode' value='$mode' />");
printc("<input type='hidden' name='typeswitch' value='0' />");

if ($_SESSION[settings][add]) printc("<div class='title'>Add Item to Section \"".$thisSection->getField("title")."\"</div>");
else  printc("<div class='title'>Edit Item \"".$_SESSION[pageObj]->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width='100%'><tr><td width='50%'>");
	printc("<input type='button' value='Cancel' onclick='cancelForm()' />\n");
printc("</td><td width='50%' align='right'>");
	printc("<input type='button' value='&larr; Previous' onclick='submitPrevButton()' />\n");
/* 	printc("<input type='button' value='Continue &rarr;' onclick='submitNextButton()' />\n"); */
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	printc("<input type='submit' name='save' value='Save Now' />\n");
printc("</td></tr></table>");
printc("<input type='hidden' value='0' name='cancel' />");
printc("<input type='hidden' value='0' name='prevbutton' />");
printc("<input type='hidden' value='0' name='nextbutton' />");


if ($_SESSION[pageObj]->getField("type")=='page') {
	printc("<div class='title' style='font-size: 14px'>Display Options</div>");
	printc("<div class='leftmargin'>");
	
	printc("Order the Content Blocks on this page by: ");
	printc("<select name='storyorder'>");
		printc("<option value='custom'".(($_SESSION[pageObj]->getField("storyorder")=='')?" selected='selected'":"").">Custom (Default)");
		printc("<option value='addeddesc'".(($_SESSION[pageObj]->getField("storyorder")=='addeddesc')?" selected='selected'":"").">Recent First");
		printc("<option value='addedasc'".(($_SESSION[pageObj]->getField("storyorder")=='addedasc')?" selected='selected'":"").">Recent Last");
		printc("<option value='editeddesc'".(($_SESSION[pageObj]->getField("storyorder")=='editeddesc')?" selected='selected'":"").">Recently Edited First");
		printc("<option value='editedasc'".(($_SESSION[pageObj]->getField("storyorder")=='editedasc')?" selected='selected'":"").">Recently Edited Last");
		printc("<option value='author'".(($_SESSION[pageObj]->getField("storyorder")=='author')?" selected='selected'":"").">Author");
		printc("<option value='editor'".(($_SESSION[pageObj]->getField("storyorder")=='editor')?" selected='selected'":"").">Editor");
		printc("<option value='category'".(($_SESSION[pageObj]->getField("storyorder")=='category')?" selected='selected'":"").">Category");
		printc("<option value='titleasc'".(($_SESSION[pageObj]->getField("storyorder")=='titleasc')?" selected='selected'":"").">Title Ascending");
		printc("<option value='titledesc'".(($_SESSION[pageObj]->getField("storyorder")=='titledesc')?" selected='selected'":"").">Title Descending");
	printc("</select><br /><br />");
	
	printc("Show horizontal rule: <input type='checkbox' name='showhr' value='1'". (($_SESSION[pageObj]->getField("showhr"))?" checked='checked'":"") . " /><br />");
	printc("<div class='desc'>By checking the above box, a horizontal rule will be placed between content blocks.</div>");
		
	printc("Show added by <i>username</i> for content: <input type='checkbox' name='showcreator' value='1'". (($_SESSION[pageObj]->getField("showcreator"))?" checked='checked'":"") . " /><br />");
	printc("<div class='desc'>By checking the above box, the usernames of the people that added the content on this page will be displayed along with the story text.</div>");

	printc("Show edited by <i>username</i> for content: <input type='checkbox' name='showeditor' value='1'". (($_SESSION[pageObj]->getField("showeditor"))?" checked='checked'":"") . " /><br />");
	printc("<div class='desc'>By checking the above box, the usernames of the people that edited the content on this page will be displayed along with the story text.</div>");
		
	printc("Show added/edited timestamp: <input type='checkbox' name='showdate' value='1'". (($_SESSION[pageObj]->getField("showdate"))?" checked='checked'":"") . " /><br />");
	printc("<div class='desc'>By checking the above box, the date and time content was added (and edited, if that be the case), will be displayed along with the story text.</div>");

	printc("Show versions: <input type='checkbox' name='showversions' value='1'". (($_SESSION[pageObj]->getField("showversions"))?" checked='checked'":"") . " /><br />");
	printc("<div class='desc'>By checking the above box, a link to previous versions of content will be displayed.</div>");
	
	printc("Number of content blocks to display: ");
	printc("<select name='archiveby'>");
//	$l = array("none"=>"Do not archive","week"=>"Week","month"=>"Month","year"=>"Year");
	$l = array("none"=>"All","1"=>1,"2"=>2,"3"=>3,"4"=>4,"5"=>5,"10"=>10,"20"=>20,"30"=>30,"50"=>50,"100"=>100,);
	
	foreach ($l as $n=>$v) {
		printc("<option value='$n'".(($_SESSION[pageObj]->getField("archiveby")==$n)?" selected='selected'":"").">$v\n");
	}
	printc("</select><br />");
//	printc("<div class='desc'>You can organize the content in this site by week added, month added, or year added. This is useful for journal-like pages, where content is long and added often.</div>");
	printc("<div class='desc'>You can limited the number of content blocks to display on a given instance of this page.  Links ( 1 | 2 | 3 ... etc) will be displayed to access other content blocks.");
	printc("  If you choose to display only one content block at a time, then you can create a sequence (useful for PowerPoint type presentations).</div>");
			
	printc("</div>");
}

printc("<br /><br />");


printc("</form>");

