<? // PAM auth module

function _valid_pam($name,$pass,$admin_auser=0) {
	global $pam_email_suffix,$_network;
	if ($_network == 'kenyon')
		global $supdbhost, $supdbuser, $supdbpass, $supdbdb, $dbhost, $dbuser, $dbpass, $dbdb;
	
	if (pam_auth($name,$pass,&$error)) {
		$x = array();
		$x[user] = $name;
		$x[pass] = $pass;
		$x[type] = "stud";
		$x[email] = $name . '@' . $pam_email_suffix;
		$x[method] = 'pam';
		if ($_network == 'kenyon') {	
			db_connect($supdbhost, $supdbuser, $supdbpass, $supdbdb);
			$query = "select * from people where email like '$name%' limit 1";
			$r = db_query($query);
//			print (db_num_rows($r));
			$a = db_fetch_assoc($r);
//			print_r($a);
			$x[fullname] = $a[first_name] . " " . $a[last_name];
			db_connect($dbhost, $dbuser, $dbpass, $dbdb);
		} else {
			$x[fullname] = $name;
		}
		
		$x = _auth_check_db($x,1);
		
		return $x;
	} else return 0;
}

?>