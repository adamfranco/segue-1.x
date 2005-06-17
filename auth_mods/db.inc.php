<? /* $Id$ */

function _valid_db($name,$pass,$admin_auser=0) {
	$name = strtolower($name);
	global $dbhost, $dbuser,$dbpass, $dbdb;
	db_connect($dbhost,$dbuser,$dbpass,$dbdb);
	$query = "SELECT * FROM users WHERE username='$name' AND password=md5('$pass')";
	($r = db_query($query)) || die ("Couldn't check table: ".mysql_error());

//	if (db_num_rows($r)  && $a['pass'] == $pass) {
	if (db_num_rows($r)) {
		$a = db_fetch_assoc($r);
		$x = array(); // array for returned info
		$x[fullname] = $a[user_fname];
		$x[user] = $name;
		$x[pass] = $pass;
		$x[type] = $a[user_type];
		$x[id] = $a[user_id];
		$x[itunes_id] = $a[itunes_id];
		$x[email] = $a[user_email];
		$x[method] = "db";
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
