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

if ($_SESSION[settings][add]) printc("<div class='title'>Add Content for this link in \"".$thisSection->getField("title")."\"</div>");
else printc("<div class='title'>Edit Item \"".$_SESSION[pageObj]->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
	
//----Cancel/Save/Next/Previous Buttons -----
printc("<table width='100%'><tr><td width='50%'>");
	printc("<input type='button' value='Cancel' onclick='cancelForm()' />\n");
printc("</td><td width='50%' align='right'>");
/* 	printc("<input type='button' value='&larr; Previous' onclick='submitPrevButton()' />\n"); */
	//if ($_SESSION[new_node][type] =='page') printc("<input type='button' value='Continue &rarr;' onclick='submitNextButton()' />\n");
printc("<input type='submit' name='save' value='Continue  &rarr;' />\n");
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
//printc("<input type='submit' name='save' value='Continue  &rarr;' />\n");
printc("</td></tr></table>");
printc("<input type='hidden' value='0' name='cancel' />");
printc("<input type='hidden' value='0' name='prevbutton' />");
printc("<input type='hidden' value='0' name='nextbutton' />");


printc("<div class='title'>Item Type:".helplink("pagetypes")."</div>");

/******************************************************************************
 * Content Type Select
 ******************************************************************************/

if ($_SESSION[settings][add]) {
	//printc("<div class='desc'>Choose a type of page.</div>");
} else {
	printc("<br /><br />");
}

/******************************************************************************
 * Print out link type options (new page, section, or content block
 ******************************************************************************/


printc("<table width='90%'cellspacing='5' border='0'>");
printc("<tr><td>");

if ($_SESSION[settings][add]) {
	printc("Add a new: ");
	printc("<select name='type' onchange='typeChange()'>");
	printc("<option value='section'".(($_SESSION[settings][type] =='section')?" selected='selected'":"").">Section</option>");
	printc("<option value='page'".(($_SESSION[settings][type] =='page')?" selected='selected'":"").">Page</option>");
	printc("<option value='content'".(($_SESSION[settings][type] =='content')?" selected='selected'":"").">Content</option>");

	printc("</select>");
	
	
	if ($_SESSION[settings][type] =='section') {
		printc(" to this site");
	
	} else if ($_SESSION[settings][type] =='page') {
		printc(" to the: ");
		printc("<select name='location'>");
		$site_sections = array();
		
		$site_sections = getSectionTitles($_SESSION[settings][site]);
//		printpre($site_sections);
		foreach ($site_sections as $section_title => $section_id) {
			printc("<option value='".$section_id."'".(($_SESSION[settings][location] == $section_id)?" selected='selected'":"").">".$section_title."</option>");
		}
		printc("</select> section");
		
	} else if ($_SESSION[settings][type] =='content') {
		$_SESSION[settings][location] = $_SESSION[settings][page];
		$site_sections = array();
		printc(" to: ");
		printc("<select name='location'>");
		
		
		$site_sections = getSectionTitles($_SESSION[settings][site]);
		foreach ($site_sections as $section_title => $section_id) {			
			$section_pages = array();
			$section_pages = getPageTitles($section_id);
			printc("<optgroup label ='".$section_title."'>");
			foreach ($section_pages as $page_title => $page_id) {	
				if ($page_title != "") printc("<option value='".$page_id."'".(($_SESSION[settings][location] == $page_id)?" selected='selected'":"").">".$page_title."</option>");
			}
			printc("<optgroup>");
		}
		printc("</select>");
		
	}
	
	printc("</td><td><br /><br />");
}



printc("</td></tr>");
printc("<tr><td>");

if ($_SESSION[new_node][type] =='page') {
	printc("<div class='desc'>A new page is a sidebar link to a location where you can add any number of content blocks.</div>");
} else if ($_SESSION[new_node][type] =='content') {
	printc("<div class='desc'>New Content adds content to whatever page you specify in this site.</div>");
} else if ($_SESSION[new_node][type] =='section') {
	printc("<div class='desc'>A new section is a link to a location where you can add any number of pages</div>");
}
printc("</td><td></td></tr>");
printc("</table>");
//printc("<br /><br />");


//printc("<div class='title'>Item Location</div>");
//printc("<div class='desc'>Chose which sidebar (left or right) to display this item.</div>");
//printc("<table width = '95%' cellspacing='3' cellpadding='3'>");
//printc("<tr><td  style='border: 1px  solid; background-color: #cccccc;' colspan='3' align='center'><br /></td></tr>");
//printc("<tr><td width='25%' align='center' style='border: 1px solid; background-color: #cccccc;'>Left<br /><input type='radio' value='left' name='location'".(($_SESSION[pageObj]->getField("location") == 'left' || !$_SESSION[pageObj]->getField("location"))?" checked='checked'":"")." /></td>");
//printc("<td width='50%' align='center' style='border: 1px solid; background-color: #cccccc;'>&nbsp;</td>");
//printc("<td width='25%' style='border: 1px solid; background-color: #cccccc;' align='center'>Right<br /><input type='radio' value='right' name='location'".(($_SESSION[pageObj]->getField("location") == 'right')?" checked='checked'":"")." /></td></tr>");
//printc("<tr><td style='border: 1px  solid; background-color: #cccccc;' colspan='3' align='center'><br /></td></tr>");
//printc("</table>");

printc("</form>");

