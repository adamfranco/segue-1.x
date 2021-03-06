<?

// Small definitions for the extended tooltips
// added by Reinhold Lange December 06, 2002
// Questions? rlange@middlebury.edu
print "<script type=\"JavaScript\">\n";

print " var ol_textfont = \"Arial,Geneva,Helvetica,sans-serif\";\n\t\t\t\t";

print " var ol_fgcolor =  \"#FFFFCC\";\n\t\t\t\t";

print " var ol_bgcolor = \"#FAE952\";\n\t\t\t\t";

print " var ol_textcolor = \"#000000\";\n\t\t\t\t";
print " var ol_captionfont = \"Arial,Geneva,Helvetica,sans-serif\";\n\t\t\t\t";

print " var ol_capcolor = \"#000000\";\n\t\t\t\t";

print " var ol_textsize = \"2\";\n\t\t\t\t";

print " var ol_captionsize = \"2\";\n\t\t\t\t";

print " var ol_closesize = \"2\";\n\t\t\t";

print "</script>\n\t\t\t";

print "<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>\n\t\t\t";

print "<script type=\"JavaScript\" src=\"themes/common/overlib.js\"></script>\n\t\t\t";
// end tooltips


if (!$_REQUEST[nostatus]) {

	/******************************************************************************
	 * Form action 
	 ******************************************************************************/

	print "<form action='$PHP_SELF?$sid";
	foreach ($_GET as $key => $val) {
		if (!in_array($key, array('theme', 'themesettings')))
			print "&amp;".$key."=".$val;
	}
	print "' id='loginform' name='loginform' method='post'>\n";
		
	/******************************************************************************
	 * if logged in
	 ******************************************************************************/
	
	if ($_loggedin) {
	
		// array of user types
		$_userTypes = array(
			"stud"=>"student",
			"prof"=>"professor",
			"staff"=>"staff",
			"visitor"=>"visitor",
			"guest"=>"guest",
			"admin"=>"administrator");
			
		// not sure when partial status is used...
		
		if (!$_REQUEST[partialstatus]) {
		
			print "<table width='100%' cellspacing='0' cellpadding='0'>";
			print "<tr><td align='left' class='small'>";

			// home link
			print "<a href='$PHP_SELF?".$sid."' class='navlink'>home</a> \n";
		
			// directory link
			print " | <a href='username_lookup.php?$sid' onclick='doWindow(\"lookup\",300,300)' target='lookup' class='navlink'>directory</a>";

			// tracking link
			if ( $_SESSION[auser] == $site_owner || $_SESSION[ltype]=='admin') {
				print " | <a href='viewlogs.php?$sid".((is_object($site))?"":"&amp;site=$site")."' target='sites' onclick='doWindow(\"sites\",600,600)' class='navlink'>tracking</a>\n";
			}
			
			// admin tools link
			if ($_SESSION[ltype]=='admin') {
				print " | <a href='users.php?$sid' target='sites' onclick='doWindow(\"sites\",700,600)' class='navlink'>admin tools</a>\n";
			}

			
			print "</td>";
			
			print "<td align='right' class='headerbox small'>";		
		
			// username (+ acting as username for admins)
			print "$_SESSION[lfname]". (($_SESSION[ltype]=='admin'&& $_SESSION[luser] != $_SESSION[auser])?" (acting as ".$_SESSION[afname].")":"")." (".$_userTypes[$_SESSION[atype]].") " ;
			
			// logout ?
			print " | <a href='$PHP_SELF?login=logout$sid";
			foreach ($_GET as $key => $val) {
				if (!in_array($key, array('theme', 'themesettings')))
					print "&amp;".$key."=".$val;
			}
			print "' class='navlink'>logout</a>";
			

			//change active user form
			if ($_SESSION[ltype]=='admin') {
				print "\n<br />";
				print " change active user: <input type='text' name='changeauser' size='10' class='textfield small'/> <input type='submit' class='button small' value='GO'/>\n";

				print "<input type='hidden' name='action' value='change_auser' />";
			}
			
			print "</td></tr>";
			print "</table>";
			
		} else {
			print $_SESSION[afname];
		}
			
	/******************************************************************************
	 * if not logged in print out login fields
	 ******************************************************************************/
			
	} else {// print out the login thingy
		//print "Login";
		//printpre($name);
		print "<div class='headerbox small' align='center'>\n";
		print " Login: <input type='text' class='textfield small' name='name' size='9' value=''/> password: <input type='password' class='textfield small' name='password' size='9'/> \n";
		print "<input type='hidden' name='loginform' value='1'/>\n";
		print "<input type='hidden' name='getquery' value='".urlencode(getenv("QUERY_STRING"))."'/>\n";
		print "<input type='hidden' name='gotourl' value='".urlencode($_SERVER['REQUEST_URI'])."'/>\n";
		print "<input type='submit' class='button small' name='button' value='GO'/><br />\n";
		
		if ($cfg[auth_reset_on] == TRUE) {
			print "<a href='passwd.php?action=reset' target='password' onclick='doWindow(\"password\",400,300)'>Forgot your password?</a>";
			//print "<a href='passwd.php?action=change' target='password' onclick='doWindow(\"password\",400,300)'>Change?</a>)";
		}
		if ($cfg[auth_register_on] == TRUE) {
			print " | <a href='passwd.php?action=register' target='password' onclick='doWindow(\"password\",400,300)'>Visitor Registration</a>";
			//print "<a href='passwd.php?action=change' target='password' onclick='doWindow(\"password\",400,300)'>Change?</a>)";
		}
		print "</div>\n";	
	}
	
	
	print "</form>\n";
	

	
} else {

	print "&nbsp;";
	
}
?>
