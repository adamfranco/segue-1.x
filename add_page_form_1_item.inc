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
else printc("<div class=title>Edit Item \"".$_SESSION[pageObj]->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
	
//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align=right>");
/* 	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n"); */
	if ($_SESSION[pageObj]->getField("type") == 'page' || $_SESSION[pageObj]->getField("type") == 'link') printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");


printc("Type:".helplink("pagetypes"));

if ($_SESSION[settings][add]) printc("<div class=desc>Please choose what type of content you would like to add. The choices are:</div>");
else printc("<br><br>");


if ($_SESSION[settings][add] || $_SESSION[pageObj]->getField("type") == 'page') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='page'".(($_SESSION[pageObj]->getField("type")=='page')?" checked":"").">");
	printc(" Content Page</b>".(($_SESSION[pageObj]->getField("type")=='page')?" - will allow you to add content to your site, along with user discussions if necessary.":"")."</div>");
}

if ($_SESSION[settings][add] || $_SESSION[pageObj]->getField("type") == 'link') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='link'".(($_SESSION[pageObj]->getField("type")=='link')?" checked":"").">");
	printc(" Link/URL</b>".(($_SESSION[pageObj]->getField("type")=='link')?" - is a link to a page outside of your site (like <i>http://www.middlebury.edu/</i>). URLs will open in a new window, allowing users to continue browsing your site.":"")."</div>");
}

if ($_SESSION[settings][add] || $_SESSION[pageObj]->getField("type") == 'heading') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='heading'".(($_SESSION[pageObj]->getField("type")=='heading')?" checked":"").">");
	printc(" Heading</b>".(($_SESSION[pageObj]->getField("type")=='heading')?" - appears in the navigation area, but with no link attached. It is useful to categorize your pages or links. Organization is sometimes a good thing.":"")."</div>");
}

if ($_SESSION[settings][add] || $_SESSION[pageObj]->getField("type") == 'divider') {
	printc("<div class=desc><b>");
	printc("<input type=radio name=type ".(($_SESSION[settings][add])?"onClick='typeChange()'":"")." value='divider'".(($_SESSION[pageObj]->getField("type")=='divider')?" checked":"").">");
	printc(" Divider</b>".(($_SESSION[pageObj]->getField("type")=='divider')?" - will put some sort of dividing space between items in your navigation list. This may just be some white space, or a thin line, depending on what theme you chose for your site.":"")."</div>");
}


if ($_SESSION[pageObj]->getField("type")=='divider') {
	printc("<div class=title>Divider</div>");
	printc("<div class=desc>Press <b>Save Now</b> above to add a divider to your page navigation bar.</div>");
}
if ($_SESSION[pageObj]->getField("type")=='heading') {
	printc("<div class=title>Heading</div>");
	printc("<div class=desc>Enter a title for your header below.</div>");
}
if ($_SESSION[pageObj]->getField("type")=='link') {
	printc("<div class=title>URL</div>");
	printc("<div class=desc>Enter a title and URL for your link.</div>");
}
if ($_SESSION[pageObj]->getField("type")=='page') {
	printc("<div class=title>Content Page</div>");
	printc("<div class=desc>Enter a title for your page below.</div>");
}


if ($_SESSION[pageObj]->getField("type") !='divider') {
	printc("<div class=leftmargin>");
	printc("Title: <input type=text size=35 name=title value='".spchars($_SESSION[pageObj]->getField("title"))."'>");
	printc("</div>");
}

/*   enter author's name for publication site */
if ($thisSite->getField("type") == 'publication') {
	printc("<div class=leftmargin>");
	printc("Author: <input type=text name=url value='".$_SESSION[pageObj]->getField("url")."' size=25>");
	$authors = $thisSite->getAllValues("page","url");
	$nauthors = array();
	foreach ($authors as $auth) {
		if (ereg("http",$auth)) continue;
		$parts = explode(" ",$auth);
		$nauthors[$auth] = (ereg(",",$auth))?$parts[0]:$parts[1];
	}
	asort($nauthors);
	$list = '';
	foreach (array_keys($nauthors) as $auth) {
		$list .= "<option>$auth\n";
	}
	if ($list != '') {
		printc(" <select name=authortemp onChange='document.addform.url.value = document.addform.authortemp.value;'>");
		printc("<option value=''>Choose...\n");
		printc($list);
		printc("</select>");
	}
	printc("</div>");
}
	

if ($_SESSION[pageObj]->getField("type")=='link') {	
	printc("<div class=leftmargin>");
	printc("URL: <input type=text name=url value='".$_SESSION[pageObj]->getField("url")."' size=50>");
	printc("</div>");
}


printc("<br><br>");

printc("</form>");

