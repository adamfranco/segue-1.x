<?
print "<form action='$PHP_SELF?$sid' method='post'>";
print "<div class='headerbox small' align=center>";

if ($_loggedin) {	//we're already logged in
	if (!$partialstatus) {
		print "$lfname". (($ltype=='admin' && $luser != $auser)?" (acting as $afname)":"").": ";
		print "<a href='$PHP_SELF?login'>[ logout ]</a> ";
		print "<a href='$PHP_SELF?$sid'>[ home ]</a> ";
		if ($ltype=='admin') {
			print "<input type=hidden name=action value='change_auser'>";
			print "[ change active user: <input type='text' name='changeauser' size=15 class='textfield'> <input type='submit' class='button' value='GO'> ]";
			print " <a href='viewlogs.php?$sid' target='logs' onClick='doWindow(\"logs\",600,500)'>[view logs]</a>";
			print " <a href='username_lookup.php' onClick='doWindow(\"lookup\",300,300)' target='lookup'>[user lookup]</a>";
		}
	} else print $afname;
} else {	// print out the login thingy
	print "Login";
	print " name: <input type=text class='textfield small' name='name' size=15 value='$name'> password: <input type=password class='textfield small' name='password' size=15> ";
	print "<input type=hidden name='loginform' value=1>";
	print "<input type=hidden name='getquery' value='".urlencode($QUERY_STRING)."'>";
	print "<input type=hidden name='gotourl' value='".urlencode($REQUEST_URI)."'>";
	print "<input type=submit class='button small' name='button' value='GO'>";
}

print "</div>";
print "</form>";
?>