
<script lang="JavaScript">

function submitForm() {
	document.addform.submit();	
}

</script>

<? // add_site_form.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

printc("<input type=hidden name=site value='$settings[site]'>");
//printc("<input type=hidden name=section value=$settings[section]>");
//printc("<input type=hidden name=page value=$settings[page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$settings[page]>");
printc("<input type=hidden name=action value='$action'>");
printc("<input type=hidden name=edaction>");
printc("<input type=hidden name=edname>");
printc("<input type=hidden name=editors value=$settings[editors]>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

if ($settings[add]) printc("<div class=title>Add ".(($type == 'section')?"Section":"Link")." to site \"".db_get_value("sites","title","name='$settings[site]'")."\"</div>");
else  printc("<div class=title>Edit Item \"".db_get_value("sections","title","id=$settings[section]")."\" in Site \"".db_get_value("sites","title","name='$settings[site]'")."\"</div>");

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


if ($settings[type]=='section') {
	printc("<div class=title style='font-size: 14px'>Permissions (for editing this section)".helplink("editors")." &nbsp; - &nbsp; <i>Optional</i></div>");
	printc("<div class=desc>Here you can designate people for whom you wish to give editing access to this section. No access is given unless an add/edit/delete box is checked.</div>");

	printc("<div class=leftmargin>");
	
	if (isclass($settings[site])) {
		?>
		<script lang='javascript'>
		function addClassEditor() {
			f = document.addform;
			f.edaction.value='add';
			f.edname.value='<?echo $settings[site]?>';
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
		$edlist = explode(",",$settings[editors]);
		if (count($edlist)) {
			$color = 0;
			foreach ($edlist as $e) {
				printc("<tr><td class=td$color align=center>");
				if ($settings[edit]) printc("<input type=checkbox name='copydownpermissions[]' value='$e' ".(($settings[copydownpermissions] && in_array($e,$settings[copydownpermissions]))?" checked":"")."></td><td class=td$color>");
				else printc(" </td><td class=td$color>");
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
	printc("<tr><th colspan=5>".((isclass($settings[site]))?"<a href='#' onClick='addClassEditor();'>Add students in $settings[site]</a>":"&nbsp;")."</th><th><a href='add_editor.php?$sid' target='addeditor' onClick='doWindow(\"addeditor\",400,250);'>add editor</a></th></tr>");
	printc("</table>");

	if ($settings[edit]) printc("<a href='editor_access.php?$sid&site=$settings[site]' onClick='doWindow(\"permissions\",600,400)' target='permissions'>Permissions as of last save</a>");
	
	
/*	
	// output permissions table
	$settings[editors] = db_get_value("sites","editors","name='$settings[site]'");
	if ($settings[editors] && $auser == $settings[site_owner]) {		
		printc("<style type='text/css'>th, .td0, .td1 {font-size: 10px;}</style>");
		printc("<table width=100% style='border: 1px solid gray'>");
		printc("<tr><th width=50%>name</th><th colspan=3 width=40%>permissions</th></tr>");
		printc("<tr><th>&nbsp;</th><th>add</th><th>edit</th><th>delete</th></tr>");
		$edlist = explode(",",$settings[editors]);
		if (count($edlist)) {
			$color = 0;
			foreach ($edlist as $e) {
				printc("<tr><td class=td$color>");
				printc(ldapfname($e)." ($e)</td>");
				
				for ($i = 0; $i<3; $i++) {
					printc("<td class=td$color align=center>");
					printc("<input type=checkbox name='permissions[$e][$i]' value=1".(($settings[permissions][$e][$i])?" checked":"").">");
					printc("</td>");
				}
				printc("</td>");
				printc("</tr>");
				$color = 1-$color;
			}
			
		}
		printc("</table>");	
		if ($settings[editors]) printc("<div class=desc>Example: if you want <i>$e</i> to be able to add and delete content to/from this page, check the <b>Add</b> and <b>Delete</b> boxes associated with <i>$e</i>'s name. You also have the option later to lock content to prevent editor access.</div>");	
	} else {
		printc("You have no editors specified. To enable a person to edit this page, first go to the site settings and add them to the list of editors. Then return here to give them editing permissions for this page.");
	}
*/		
	if ($settings[site_owner] == $auser) {
		printc("<br><br>Locked: <input type=checkbox name=locked value=1".(($settings[locked])?" checked":"").">");
		printc("<div class=desc>By checking the above box, you are preventing any editors from editing the content of this page of the site, regardless of their editor privileges.</div>");
		printc("<br>");
	} else printc("<input type=hidden name=locked value=$locked>");
			
	printc("</div>");
}

printc("<br><br>");

printc("</form>");

