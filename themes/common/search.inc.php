<?



if (!$_REQUEST[nostatus]) {
	print "<form action='$PHP_SELF?$sid";
	foreach ($_GET as $key => $val) {
		print "&amp;".$key."=".$val;
	}
	print "' id='search' name='search' method='post'>\n";
	print "<div class='headerbox small' align='center'>\n";

	//print "Login";
	//printpre($name);
	print " <input type='text' class='textfield small' name='search' size='9' value='".(($_REQUEST['search'])?$_REQUEST['search']:'')."'/> \n";
//	print "<input type='hidden' name='search' value='1'/>\n";
	print "<input type='hidden' name='getquery' value='".urlencode($QUERY_STRING)."'/>\n";
	print "<input type='hidden' name='gotourl' value='".urlencode($REQUEST_URI)."'/>\n";
//	print "<input type='hidden' name='action' value='site'/>\n";
	print "<input type='submit' class='button small' name='button' value='Find'/><br />\n";
	print "</div>\n";
	print "</form>\n";
}
?>