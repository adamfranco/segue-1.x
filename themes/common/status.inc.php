<?

// Small definitions for the extended tooltips
// added by Reinhold Lange December 06, 2002
// Questions? rlange@middlebury.edu
print "<script language=\"JavaScript\">\n";

print " var ol_textfont = \"Arial,Geneva,Helvetica,sans-serif\";\n";

print " var ol_fgcolor =  \"#FFFFCC\";\n";

print " var ol_bgcolor = \"#FAE952\";\n";

print " var ol_textcolor = \"#000000\";\n";
print " var ol_captionfont = \"Arial,Geneva,Helvetica,sans-serif\";\n";

print " var ol_capcolor = \"#000000\";\n";

print " var ol_textsize = \"2\";\n";

print " var ol_captionsize = \"2\";\n";

print " var ol_closesize = \"2\";\n";

print "</script>\n";

print "<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>\n";

print "<script language=\"JavaScript\" src=\"themes/common/overlib.js\"></script>\n";
// end tooltips


if (!$_REQUEST[nostatus]) {
	print "<form action='$PHP_SELF?$sid";
	foreach ($_GET as $key => $val) {
		print "&".$key."=".$val;
	}
	print "' method='post'>";
	print "<div class='headerbox small' align=center>";
	
	if ($_loggedin) {	//we're already logged in
		$_userTypes = array(
			"stud"=>"student",
			"prof"=>"professor",
			"staff"=>"staff",
			"visitor"=>"visitor",
			"guest"=>"guest",
			"admin"=>"administrator");
		if (!$_REQUEST[partialstatus]) {
			print "$_SESSION[lfname]". (($_SESSION[ltype]=='admin'
&& $_SESSION[luser] != $_SESSION[auser])?" (acting as $_SESSION[afname])":"")."
(".$_userTypes[$_SESSION[atype]]."): " ;
			if ($_SESSION[ltype]=='admin') {
				print "\n<br />";
				print " change active user: <input type='text' name='changeauser' size=10 class='textfield small'> <input type='submit' class='button small' value='GO'>";
				print "\n<br />";
			}
			print "<a href='$PHP_SELF?login=logout&$sid";
			foreach ($_GET as $key => $val) {
				print "&".$key."=".$val;
			}
			print "' class='navlink'>logout</a>";
			print " | <a href='username_lookup.php?$sid' onClick='doWindow(\"lookup\",300,300)' target='lookup' class='navlink'>directory</a>";
			if ($_SESSION[ltype]=='admin') {
				print "<input type=hidden name=action value='change_auser'>";
			}
			if ( $_SESSION[auser] == $site_owner || $_SESSION[ltype]=='admin') {
				print " | <a href='viewlogs.php?$sid".((is_object($site))?"":"&site=$site")."' target='sites' onClick='doWindow(\"sites\",600,600)' class='navlink'>tracking</a>";
			}
			if ($_SESSION[ltype]=='admin') {
				print " | <a href='users.php?$sid' target='sites' onClick='doWindow(\"sites\",600,600)' class='navlink'>admin tools</a>";
			}
			
			print " | <a href='$PHP_SELF?".$sid."' class='navlink'>home</a>     ";
		} else 
			print $_SESSION[afname];
	} else {// print out the login thingy
		//print "Login";
		print " Login: <input type=text class='textfield small' name='name' size=9 value='$name'> password: <input type=password class='textfield small' name='password' size=9> ";
		print "<input type=hidden name='loginform' value=1>";
		print "<input type=hidden name='getquery' value='".urlencode($QUERY_STRING)."'>";
		print "<input type=hidden name='gotourl' value='".urlencode($REQUEST_URI)."'>";
		print "<input type=submit class='button small' name='button' value='GO'><br>";
		if ($cfg[auth_reset_on] == TRUE) {
			print "<a href='passwd.php?action=reset' target='password' onClick='doWindow(\"password\",400,300)'>Forgot your passsword?</a>";
			//print "<a href='passwd.php?action=change' target='password' onClick='doWindow(\"password\",400,300)'>Change?</a>)";
		}
		if ($cfg[auth_register_on] == TRUE) {
			print " | <a href='passwd.php?action=register' target='password' onClick='doWindow(\"password\",400,300)'>Visitor Registration</a>";
			//print "<a href='passwd.php?action=change' target='password' onClick='doWindow(\"password\",400,300)'>Change?</a>)";
		}
	}
	
	
	
	print "</div>";
	print "</form>";
}
?>
