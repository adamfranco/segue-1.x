
<script lang="JavaScript">

function submitForm() {
	document.addform.submit();	
}

</script>

<? // add_site_form_4_permissions.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid&action=".(($settings[add])?"add_site":"edit_site")."' method=post name=addform>");

// --- buttons ---
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($settings[edit]) printc("<input type=submit name=save value='Save Changes'>\n");
	if ($settings[add]) printc("<input type=submit name=save value='Finish Now'>\n"); 
printc("</td></tr></table>");
if ($settings[add]) printc("<div class=title>Add Site</div>");
if ($settings[edit]) printc("<div class=title>Edit Site</div>");
printc("<br>");

// --- title ---
printc("<div class=title>".(($settings[edit])?"5":"6").". Editing Permissions".helplink("editors")." &nbsp; - &nbsp; <i>Optional</i></div>");
printc("<div class=desc>In this section you can designate people for whom you may later give section-level or page-level editing access.</div>");

// --- form contents ---
printc("<input type=hidden name=edaction value=''>");
printc("<input type=hidden name=editors value='$settings[editors]'>");
printc("<input type=hidden name=edname value=''>");

if (isclass($settings[sitename])) {
	?>
	<script lang='javascript'>
	function addClassEditor() {
		f = document.addform;
		f.edaction.value='add';
		f.edname.value='<?echo $settings[sitename]?>';
		f.submit();
	}
	</script>
	<?
}

// output permissions table
printc("<style type='text/css'>th, .td0, .td1 {font-size: 10px;}</style>");
printc("<table width=100% style='border: 1px solid gray'>");
printc("<tr><th rowspan=2>Copy Down<br>Permissions</th><th width=50%>name</th><th colspan=3 width=30%>permissions</th><th>del</th></tr>");
printc("<tr><th>&nbsp;</th><th>add</th><th>edit</th><th>delete</th><th>&nbsp;</th></tr>");
if ($settings[editors]) {
/* 	$edlist = array(); */
/* 	foreach (explode(",",$settings[editors]) as $n) { */
/* 		array_push($edlist, trim($n)); */
/* 	} */
	$edlist = explode(",",$settings[editors]);
/* 	$editorsa = implode(",",$edlist); */
	if (count($edlist)) {
		$color = 0;
		foreach ($edlist as $e) {
			printc("<tr><td class=td$color align=center>");
			/*if ($settings[edit])*/ printc("<input type=checkbox name='copydownpermissions[]' value='$e' ".(($settings[copydownpermissions] && in_array($e,$settings[copydownpermissions]))?" checked":"")."></td><td class=td$color>");
			printc(ldapfname($e)." ($e)</td>");
			
			for ($i = 0; $i<3; $i++) {
				printc("<td class=td$color align=center>");
				printc("<input type=checkbox name='permissions[$e][$i]' value=1".(($settings[permissions][$e][$i])?" checked":"").">");
				printc("</td>");
			}
			printc("</td>");
			printc("<td class=td$color align=center>");
			printc("<a href='#' onClick='delEditor(\"$e\");'>del</a>");
			printc("</td></tr>");
			$color = 1-$color;
		}
		
	}
} else printc("<tr><td class=td1 > &nbsp; </td><td class=td1 colspan=5>no editors added</td></tr>");
printc("<tr><th colspan=5>".((isclass($settings[sitename]))?"<a href='#' onClick='addClassEditor();'>Add students in $settings[sitename]</a>":"&nbsp;")."</th><th><a href='add_editor.php?$sid' target='addeditor' onClick='doWindow(\"addeditor\",400,250);'>add editor</a></th></tr>");
printc("</table>");

if ($settings[edit]) printc("<a href='editor_access.php?$sid&site=$settings[sitename]' onClick='doWindow(\"permissions\",600,400)' target='permissions'>Permissions as of last save</a>");

printc("<br><br><div class=desc>By checking \"Add\", \"Edit\", or \"Delete\" on here, you give the editor site-level permissions. Site-level permissions allow the Editor to either Add, Edit (includes changing the order of), or Delete sections of the site. Editors with permissions specified here will not have section-level or page-level permissions unless the \"Copy Down Permissions\" box is checked.</div> <div class=desc>To give someone section-level or page-level permissions, add them as an editor here, but with no boxes checked. On the page for which you want to give them access, check the permissions boxes next to their name in the \"Advanced View\" of the section or page edit screen.</div>");

/*if ($settings[edit])*/ printc("<div class=desc><b>NOTE:</b> if you would like Segue to copy the permissions for a certain user down to all sections and pages contained in this site, check the box to the left of their name. You also have the option later to lock sections of the site, preventing any editor access.</div>");
//if ($settings[editors]) printc("<div class=desc>Example: if you want <i>$e</i> to be able to edit the settings (everything but the actual content) of sections in this site, check the <b>Edit</b> box associated with <i>$e</i>'s name. You also have the option later to lock sections of the site, preventing any editor access.</div>");	
// end permissions table

printc("</div>");
printc("<br>");
printc("<br>");

printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid'>");

// --- buttons ---
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=submit value='Cancel' name='cancel'>\n");
printc("</td><td width=50% align=right>");
	printc("<input type=submit name=prevbutton value='&larr; Previous'>\n");
	printc("<input type=submit name=nextbutton value='Continue &rarr;'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align=right>");
	if ($settings[edit]) printc("<input type=submit name=save value='Save Changes'>\n");
	if ($settings[add]) printc("<input type=submit name=save value='Finish Now'>\n"); 
printc("</td></tr></table>");

printc("</form>");


