
<script lang="JavaScript">

function submitForm() {
	document.addform.submit();	
}

</script>

<? // add_site_form.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

printc("<input type=hidden name=site value='".$thisSite->name."'>");
//printc("<input type=hidden name=section value=$_SESSION[settings][section]>");
//printc("<input type=hidden name=page value=$_SESSION[settings][page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$_SESSION[settings][page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

if ($_SESSION[settings][add]) printc("<div class=title>Add ".(($type == 'section')?"Section":"Link")." to site \"".$thisSite->getField("title")."\"</div>");
else  printc("<div class=title>Edit Item \"".$_SESSION[sectionObj]->getField("title")."\" in Site \"".$thisSite->getField("title")."\"</div>");

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


if ($_SESSION[sectionObj]->getField("type")=='section') {
	printc("<div class=title style='font-size: 14px'>Permissions (for editing this section)".helplink("editors")." &nbsp; - &nbsp; <i>Optional</i></div>");
	printc("<div class=desc>Here you can designate people for whom you wish to give editing access to this section. No access is given unless an add/edit/delete box is checked.</div>");

	printc("<div class=leftmargin>");

	// output permissions table
	permissions::outputForm($_SESSION[sectionObj]);
/* 	printc("<style type='text/css'>th, .td0, .td1 {font-size: 10px;}</style>"); */
/* 	printc("<table width=100% style='border: 1px solid gray'>"); */
/* 	printc("<tr><th rowspan=2>Copy Down<br>Permissions</th><th width=50%>name</th><th colspan=3 width=30%>permissions</th><th>del</th></tr>"); */
/* 	printc("<tr><th>&nbsp;</th><th>add</th><th>edit</th><th>delete</th><th>&nbsp;</th></tr>"); */
/* 	if ($_SESSION[settings][editors]) { */
/* 		$edlist = explode(",",$_SESSION[settings][editors]); */
/* 		if (count($edlist)) { */
/* 			$color = 0; */
/* 			foreach ($edlist as $e) { */
/* 				printc("<tr><td class=td$color align=center>"); */
/* 				if ($_SESSION[settings][edit]) printc("<input type=checkbox name='copydownpermissions[]' value='$e' ".(($_SESSION[settings][copydownpermissions] && in_array($e,$_SESSION[settings][copydownpermissions]))?" checked":"")."></td><td class=td$color>"); */
/* 				else printc(" </td><td class=td$color>"); */
/* 				printc(ldapfname($e)." ($e)</td>"); */
/* 				 */
/* 				for ($i = 0; $i<3; $i++) { */
/* 					printc("<td class=td$color align=center>"); */
/* 					printc("<input type=checkbox name='permissions[$e][$i]' value=1".(($_SESSION[settings][permissions][$e][$i])?" checked":"").">"); */
/* 					printc("</td>"); */
/* 				} */
/* 				printc("</td>"); */
/* 				printc("<td class=td$color align=center>"); */
/* 				printc("<a href='#' onClick='delEditor(\"$e\");'>remove</a>"); */
/* 				printc("</td></tr>"); */
/* 				$color = 1-$color; */
/* 			} */
/* 			 */
/* 		} */
/* 	} else printc("<tr><td class=td1 > &nbsp; </td><td class=td1 colspan=5>no editors added</td></tr>"); */
/* 	printc("<tr><th colspan=5>".((isclass($_SESSION[settings][site]))?"<a href='#' onClick='addClassEditor();'>Add students in $_SESSION[settings][site]</a>":"&nbsp;")."</th><th><a href='add_editor.php?$sid' target='addeditor' onClick='doWindow(\"addeditor\",400,250);'>add editor</a></th></tr>"); */
/* 	printc("</table>"); */
/*  */
/* 	if ($_SESSION[settings][edit]) printc("<a href='editor_access.php?$sid&site=$_SESSION[settings][site]' onClick='doWindow(\"permissions\",600,400)' target='permissions'>Permissions as of last save</a>"); */
	
	if ($site_owner == $_SESSION[auser]) {
		printc("<br><br>Locked: <input type=checkbox name=locked value=1".(($_SESSION[sectionObj]->getField("locked"))?" checked":"").">");
		printc("<div class=desc>By checking the above box, you are preventing any editors from editing the content of this page of the site, regardless of their editor privileges.</div>");
		printc("<br>");
	} else printc("<input type=hidden name=locked value=".$_SESSION[sectionObj]->getField("locked").">");
			
	printc("</div>");
}

printc("<br><br>");

printc("</form>");

