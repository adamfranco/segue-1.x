<? /* $Id$ */

function _valid_pam($name,$pass,$admin_auser=0) {
	global $pam_email_suffix;

	$exists = 0;
	if ($admin_auser) {
		$exists = 1;
	}
		
	
	if ($exists || pam_auth($name,$pass,&$error)) {
		$x = array();
		$x[user] = $name;
		$x[pass] = $pass;
		$x[type] = "stud";
		$x[email] = $name . '@' . $pam_email_suffix;
		$x[method] = 'pam';
		$x[fullname] = $name;
		
		$x = _auth_check_db($x,1);
		
		return $x;
	} else return 0;
}

?>