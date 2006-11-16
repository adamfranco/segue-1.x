<? /* $Id$ */

function _valid_db($name,$pass,$admin_auser=0) {
	$name = strtolower($name);
	global $dbhost, $dbuser,$dbpass, $dbdb;
	db_connect($dbhost,$dbuser,$dbpass,$dbdb);
	$query = "SELECT * FROM user WHERE user_uname='".addslashes($name)."'".(($admin_auser)?"":" AND user_pass='".addslashes($pass)."' AND user_authtype='db'");
	$r = db_query($query);
//	$a = db_fetch_assoc($r);
	
//	if (db_num_rows($r)  && $a['pass'] == $pass) {
	if (db_num_rows($r)) {
		$a = db_fetch_assoc($r);
		$x = array(); // array for returned info
		$x[fullname] = $a[user_fname];
		$x[user] = $name;
		$x[pass] = $pass;
		$x[email] = $a[user_email];
		$x[type] = $a[user_type];
		$x[method] = 'db';
		$x[id] = $a[user_id];
		return $x;
	} /*else {
	    $query = "select * from users where email='$name' and pass='$pass' and status='open'";
	    $r = db_query($query);
	    if (db_num_rows($r)) {
	        $logmethod = "open";
	        return $r;
	    }
	}*/
	return 0;
}

?>
