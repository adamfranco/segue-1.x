<? // add_site_form.inc -- the form that people use to add/edit a site

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");

printc("<input type=hidden name=site value='$settings[site]'>");
//printc("<input type=hidden name=section value=$settings[section]>");
//printc("<input type=hidden name=page value=$settings[page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$settings[page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

printc("<div class=title>Add Item to Section <b>".db_get_value("sections","title","id=$settings[section]")."</b></div>");


if ($settings[type]=='url' || $settings[type]=='page') {
	printc("<div class=title style='font-size: 14px'>Activation & Availability</div>");

	printc("<table>");
	printc("<tr><td align=right>");
	printc("Activate date:</td><td><input type=checkbox name='activatedate' value=1".(($settings[activatedate])?" checked":"")."> <select name='activateday'>");
	for ($i=1;$i<=31;$i++) {
		printc("<option" . (($settings[activateday] == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>\n");
	printc("<select name='activatemonth'>");
	for ($i=0; $i<12; $i++) {
		printc("<option value=$i" . (($settings[activatemonth] == $i)?" selected":"") . ">$months[$i]\n");
	}
	printc("</select>\n<select name='activateyear'>");
	$curryear = date("Y");
	for ($i=$curryear; $i <= ($curryear+5); $i++) {
		printc("<option" . (($settings[activateyear] == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>");
	
	printc("</td></tr>");
	
	printc("<tr><td align=right>");
	printc("Deactivate date:</td><td><input type=checkbox name='deactivatedate' value=1".(($settings[deactivatedate])?" checked":"")."> <select name='deactivateday'>");
	for ($i=1;$i<=31;$i++) {
		printc("<option" . (($settings[deactivateday] == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>\n");
	printc("<select name='deactivatemonth'>");
	for ($i=0; $i<12; $i++) {
		printc("<option value=$i" . (($settings[deactivatemonth] == $i)?" selected":"") . ">$months[$i]\n");
	}
	printc("</select>\n<select name='deactivateyear'>");
	for ($i=$curryear; $i <= ($curryear+5); $i++) {
		printc("<option" . (($settings[deactivateyear] == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>");
	
	printc("</tr></td></table>");

	printc("<br><div class=desc>To prevent the site from being viewed at all, select <b>\"Disabled\"</b> below. This is useful to prevent people from viewing the site while it is still in progress. Selecting <b>\"Disabled\"</b> WILL OVER-RIDE the \"Activate On\" command, above. Selecting \"Enabled\" will not over-ride the \"[De]Activate On\" settings.</div>");

	printc("<input type=radio name=active value=1 " . (($settings[active])?" checked":"") . "> Enabled | ");
	printc("<input type=radio name=active value=0 " . (($settings[active])?"":" checked") . "> Disabled <br>");
}

printc("<br><br>");

//printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid&action=viewsite&site=$site&section=$section'>");
printc("<input type=submit value='&lt;&lt Back' name=goback>\n");
printc("<input type=submit value='");
if ($settings[add])printc("Add");
if ($settings[edit])printc("Update");
printc("'>");

printc("</form>");

