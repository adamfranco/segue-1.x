<?
print "<form action='$PHP_SELF?$sid' method='post'>";
print "<div class='headerbox small' align=center>";

if ($_loggedin) {	//we're already logged in
	if (!$partialstatus) {
		print "$lfname". (($ltype=='admin' && $luser != $auser)?" (acting as $afname)":"").": ";
		print "<a href='$PHP_SELF?login'>logout</a> | ";
		print "<a href='$PHP_SELF?$sid'>home</a>";
		if ($ltype=='admin') {
			print "<input type=hidden name=action value='change_auser'>";
		}
		if ( $auser == $site_owner || $ltype=='admin') {
			print " | <a href='viewsites.php?$sid&site=$site' target='sites' onClick='doWindow(\"sites\",600,600)'>logs</a>";
		}
		if ($ltype=='admin') {
			print " | <a href='username_lookup.php' onClick='doWindow(\"lookup\",300,300)' target='lookup'>users</a>";
			print " | change active user: <input type='text' name='changeauser' size=10 class='textfield small'> <input type='submit' class='button small' value='GO'>";
		}	
		
	} else print $afname;
} else {// print out the login thingy
	//print "Login";
	print " Login: <input type=text class='textfield small' name='name' size=9 value='$name'> password: <input type=password class='textfield small' name='password' size=9> ";
	print "<input type=hidden name='loginform' value=1>";
	print "<input type=hidden name='getquery' value='".urlencode($QUERY_STRING)."'>";
	print "<input type=hidden name='gotourl' value='".urlencode($REQUEST_URI)."'>";
	print "<input type=submit class='button small' name='button' value='GO'>";
}

print "</div>";
print "</form>";

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

?>