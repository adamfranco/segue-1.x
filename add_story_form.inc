<? // add_story_form.inc -- the form that people use to add/edit a story

// what kind of editor will we use?
// now included in editor.inc.php
/*
$uagent = $HTTP_SERVER_VARS["HTTP_USER_AGENT"];
$isMac = ereg("Mac",$uagent);
$uagent = explode("; ",$uagent);
$uagent = explode(" ",$uagent[1]);
$bname = strtoupper($uagent[0]);
$bvers = $uagent[1];
if(($bname == "MSIE") && (intval($bvers) >= 5) && (!$isMac) ) {
	$editorType = "activex";
} else {
	$editorType = "txt";
}
*/

printc("<form action='$PHP_SELF?$sid' method=post name=storyform".(($type != 'story')?" enctype='multipart/form-data'":"").">");

printc("<input type=hidden name=site value=$site>");
printc("<input type=hidden name=section value=$section>");
printc("<input type=hidden name=page value=$page>");
if ($edit) printc("<input type=hidden name=edit_story value='$edit_story'>");
printc("<input type=hidden name=action value='$action'>");
printc("<input type=hidden name=step value=2>");
printc("<input type=hidden name=submitted value=1>");
printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

/* printc("\n<div align=right>"); */
/* printc("<input type=submit name=switchmode value='Switch to ".(($mode)?"Basic":"Advanced")." view'>"); */
/* printc("\n</div>"); */

printc("<div class=title>Add Content to Page <b>".db_get_value("pages","title","id=$page")."</b></div>");

// top bar w/ mode switch
printc("\n<table width=100%><tr>");
/* printc("<td align=left>Type: <select name=type onChange='typeChange()'>"); */
/* printc("<option value='story'".(($type=='story')?" selected":"").">Content Block"); */
/* printc("<option value='image'".(($type=='image')?" selected":"").">Image"); */
/* printc("<option value='file'".(($type=='file')?" selected":"").">File for Download"); */
/* printc("<option value='link'".(($type=='link')?" selected":"").">Link"); */
/* printc("</select></td>"); */
printc("<td align=left>Type: ".helplink("storytypes"));
printc("</td>");

printc("<td align=right><input type=submit name=switchmode value='Switch to ".(($mode)?"Basic":"Advanced")." view'></td>");
printc("\n</tr></table>");
printc("<div class=desc>Please choose what type of content you would like to add to this page. The choices are:</div>");
// end
printc("<div class=desc><b><input type=radio name='type' onClick='typeChange()' value='story'".(($type=='story')?" checked":"")."> Text Block</b>".(($type=='story')?" - allows you to enter a block of content on this page.":"")."</div>");
printc("<div class=desc><b><input type=radio name='type' onClick='typeChange()' value='image'".(($type=='image')?" checked":"")."> Image</b>".(($type=='image')?" - displays an image of your choice on the page with optional caption or description text.":"")."</div>");
printc("<div class=desc><b><input type=radio name='type' onClick='typeChange()' value='file'".(($type=='file')?" checked":"")."> File for Download</b>".(($type=='file')?" - displays a download link to a file of your choice, such as a Word document containing syllabus information.":"")."</div>");
printc("<div class=desc><b><input type=radio name='type' onClick='typeChange()' value='link'".(($type=='link')?" checked":"")."> Link</b>".(($type=='link')?" - displays a link with optional title and description.":"")."</div>");

if ($type == 'story') {
	printc("<div class=title>Text Block</div>");
	printc("<div class=desc>Enter the content for your story below. Title and full content are optional. If you enter full content, the shorter content (or Abstract) will be displayed on your page with a link to the full content, in a new window.</div>");
}

if ($type == 'image') {
	printc("<div class=title>Image</div>");
	printc("<div class=desc>Select an image file to use, and enter optional title and caption text.</div>");
}

if ($type == 'file') {
	printc("<div class=title>File for Download</div>");
	printc("<div class=desc>Select the file you would like to make available, and enter optional title and description text.</div>");
}

if ($type == 'link') {
	printc("<div class=title>Link</div>");
	printc("<div class=desc>Enter the URL (internet address) to the page you would like to link to below, and enter optional title and description.</div>");
}


printc("<div class=leftmargin>");

printc("Title (optional): <input type=text size=35 name=title value='".spchars($title)."'>");


if ($type == 'image' || $type == 'file') {
	printc("<br><br>");
	printc('<input type="hidden" name="MAX_FILE_SIZE" value="'.$_max_upload.'">');
	if ($edit) printc("New ");
	if ($type == 'image') printc("Image: ");
	else printc("File: ");
	printc("<input type=file name=file class=textfield>");
	if ($type=='image') printc("<div class=desc>Select the image you would like to use by clicking the 'Browse...' button above. Accepted image types are GIF, JPEG and PNG.</div>");
	else printc("<div class=desc>Select the file you would like to make available by clicking the 'Browse...' button above.</div>");
	printc("<br>");
	if ($type=='image') printc("Caption/Description (optional):");
	else printc("Description (optional):");
	printc("<br>");
	printc("<textarea name=shorttext class=textarea rows=4 cols=80>".spchars($shorttext)."</textarea>");
	
}

if ($type == 'link') {
	printc("<br>");
	printc("URL: <input type=text name='url' value='$url' size=50><BR>");
	printc("Description (optional):<br>");
	printc("<textarea name='shorttext' cols=80 rows=3>");
	printc(spchars($shorttext));
	printc("</textarea>");
}


if ($type == 'story') {
	//	Begin modification 1 - 11.1.02 - achapin
	// removed active-x editor code to editor_activex.php
	// editor.inc.php determines browser/platform and choses appropriate editor
	include("htmleditor/editor.inc.php");
		
	printc("<br>");
	printc("<table width=80%><tr><td style='padding-left: 15px'>");
	printc("Content (or Abstract):");
	printc("<br>");
		
	//	print out text areas with editor (determined by addeditor function)
	//  addeditor function (defined in editor.inc.php)
	//  addeditor function variables (textarea, cols, rows, text)
	printc(addeditor ("shorttext",80,16,$text));
	printc("</td></tr></table>");
		
	printc("<br>");
	
	printc("<table width=80%><tr><td style='padding-left: 15px'>");
	printc("Full Content: (optional)");
	printc("<br>");
	
	printc(addeditor ("longertext",80,16,$text));
	printc("</td></tr></table>");
			
	printc("<div align=right>");
	printc("What type of text did you enter? <select name=texttype>");
	printc("<option value=text".(($texttype=='text')?" selected":"").">Plain text");
	printc("<option value=html".(($texttype=='html')?" selected":"").">Preformatted HTML");
	printc("</select>");
	printc("</div>");
	printc("<div class=desc>Choose above if the text you entered is formatted HTML (the formatting commands used by the WWW). If you are unsure, select <b>Plain text</b>.</div>");
	printc("<div class=desc><b>NOTE</b>: <b>Plain text</b> may still contain HTML commands. By choosing <b>Preformatted HTML</b>, SitesDB will not convert newline characters to the HTML equivalent: &lt;br&gt;</div>");
	//	end modification 1 - 11.1.02 - achapin
}

printc("</div>");


printc("<br>");

if ($mode) {
	printc("<div class=title>Activation & Availability</div>");
	printc("<div class=leftmargin>");
	
	printc("Activate date: <input type=checkbox name='activatedate' value=1".(($activatedate)?" checked":"")."> <select name='activateday'>");
	for ($i=1;$i<=31;$i++) {
		printc("<option" . (($activateday == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>\n");
	printc("<select name='activatemonth'>");
	for ($i=0; $i<12; $i++)
		printc("<option value=$i" . (($activatemonth == $i)?" selected":"") . ">$months[$i]\n");
	
	printc("</select>\n<select name='activateyear'>");
	$curryear = date("Y");
	for ($i=$curryear; $i <= ($curryear+5); $i++) {
		printc("<option" . (($activateyear == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>");
	
	printc("<br>");
	
	
	printc("Deactivate date: <input type=checkbox name='deactivatedate' value=1".(($deactivatedate)?" checked":"")."> <select name='deactivateday'>");
	for ($i=1;$i<=31;$i++) {
		printc("<option" . (($deactivateday == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>\n");
	printc("<select name='deactivatemonth'>");
	for ($i=0; $i<12; $i++) {
		printc("<option value=$i" . (($deactivatemonth == $i)?" selected":"") . ">$months[$i]\n");
	}
	printc("</select>\n<select name='deactivateyear'>");
	for ($i=$curryear; $i <= ($curryear+5); $i++) {
		printc("<option" . (($deactivateyear == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>");
	
	printc("</div>");
	
	printc("<br><br>");
	
	if ($type == 'story') {
		// only if they're allowed to
		if ($auser == $site_owner || db_get_value("pages","ediscussion","id=$page")==1) {
			printc("<div class=title>Discussion</div>");
			printc("<div class=desc>You have the option of allowing visitors to your site discuss certain content contained in it. You can restrict discussion access to students in the class (if this is a class website), only Middlebury users, or anyone (allowing for anonymous discussion).</div>");
			printc("<div class=leftmargin>");
			printc("Enable discussion for this story: <input type=checkbox name=discuss".(($discuss)?" checked":"")."><br>");
			printc("Who should be able to discuss this story? <select name=discusspermissions>");
			printc("<option value='anyone'".(($discusspermissions=='anyone')?" selected":"")."> anyone");
			printc("<option value='midd'".(($discusspermissions=='midd')?" selected":"")."> Middlebury College users");
			if (isclass($site)) printc("<option value='class'".(($discusspermissions=='class')?" selected":"").">students in class");
			printc("</select>");
			printc("</div>");
			printc("<br><br>");
		} else {
			printc("<input type=hidden name=discuss value=$discuss>");
			printc("<input type=hidden name=discusspermissions value='$discusspermissions'>");
		}
		
		$categories=array();
		if (db_num_rows(($r=db_query("select distinct category,id from stories")))) {
			while ($a=db_fetch_assoc($r)) {
				if ($a[category]!='' && insite($site,$section,$page,$a[id])) {
					$categories[] = $a[category];
		/* 			$strs = decode_array(db_get_value("pages","stories","id=$page")); */
		/* 			print_r($strs); */
		/* 			print "$a[id] => ".in_array($a[id],$strs)."<BR>"; */
					
				}
			}
			sort($categories);
		}
		printc("Category: <input type=text name=newcategory size=20 value='$newcategory'> <select name=category onChange='document.storyform.newcategory.value = document.storyform.category.value'>");
		printc("<option value=''".(($category=='')?" selected":"").">none");
		foreach ($categories as $c) {
			printc("<option value='$c'".(($category==$c)?" selected":"").">$c");
		}
		printc("</select><br><br>");
	}
	
	if ($auser == $site_owner)
		printc("Locked: <input type=checkbox name='locked' value=1" . (($locked)?" checked":"") . "><br><br>");
	else printc("<input type=hidden name=locked value=$locked>");
} else {
	$array = array("category","locked","discuss","discusspermissions");
	foreach ($array as $ar) {
		printc("<input type=hidden name='$ar' value='".$$ar."'>");
	}
}

printc("<input type=hidden name=gobackurl value='$PHP_SELF?$sid&action=viewsite&site=$site&section=$section&page=$page'>");
printc("<input type=submit name=goback value='&lt;&lt Back'>\n");
printc("<input type=submit value='");
if ($add) printc("Add");
if ($edit) printc("Update");
printc("'>");

printc("</form>");

