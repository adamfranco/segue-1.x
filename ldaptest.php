<? /* $Id$ */ ?>
<html>
<head>
<title>LDAP test</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<div align="left">
  <form name="form1" method="get" action="<? echo $PHP_SELF ?>">
     <p> username: 
      <input type="text" name="userid" value=""> <input type="submit" name="ldap" value="lookup">
    </p>
  </form>
  <hr>
</div>
</body>
</html>

<?php
// basic sequence with LDAP is connect, bind, search, interpret search
// result, close connection

if ($userid) {

	echo "<h3>LDAP query test for: $userid</h3>";
	echo "Connecting ...";
	$ds=ldap_connect("tiger.middlebury.edu");  // must be a valid LDAP server!
	echo "connect result is ".$ds."<br>";

	if ($ds) { 
	    echo "Binding ..."; 
	    $r=ldap_bind($ds,"cn=fjones,cn=midd","lk87df");
	//    $r=ldap_bind($ds);
	    echo "Bind result is ".$r."<br>";

	    echo "Searching for (uid=$userid) ...";
	    // Search uid
	//    $ret = array("memberOf","cn","uid","mail","extension-attribute-1","extension-attribute-2","extension-attribute-3","extension-attribute-4","extension-attribute-5","extension-attribute-6","extension-attribute-7","extension-attribute-8","extension-attribute-9","extension-attribute-10");
	    $ret = array("cn","extension-attribute-1","mail","memberOf");

	//    $sr=ldap_search($ds,"ou=Midd,o=MC", "extension-attribute-1=108861",$ret);
	    $sr=ldap_search($ds,"ou=Midd,o=MC", "uid=$userid",$ret);
	//    $sr=ldap_search($ds,"ou=Midd,o=MC", "memberOf=*all*",$ret);
	//    $sr=ldap_search($ds,"ou=Midd,o=MC", "cn=all faculty,objectClass=groupOfNames",$ret);
	    echo "Search result is ".$sr."<br>";

	    if (!$sr) {print ldap_error($ds);}

	    echo "Number of entires returned is ".ldap_count_entries($ds,$sr)."<br>";

	    echo "Getting entries ...<br>";
	    $info = ldap_get_entries($ds, $sr);
	    echo "Data for ".$info["count"]." items returned:<p>";

		print "<pre>";
		print_r($info);
		print "</pre>";



	    echo "Closing connection";
	    ldap_close($ds);

	} else {
	    echo "<h4>Unable to connect to LDAP server</h4>";
	}
}
?>
<hr>
</body>
</html>
