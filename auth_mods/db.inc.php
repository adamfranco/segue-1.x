<? /* $Id$ */

function _valid_db($name,$pass,$admin_auser=0) {
	$name = strtolower($name);
	global $dbhost, $dbuser,$dbpass, $dbdb;
	db_connect($dbhost,$dbuser,$dbpass,$dbdb);
	$query = "select * from users where uname='$name'".(($admin_auser)?"":" and pass='$pass' and status='db'");
//	print $query; // debug
//	$query = "select * from users where uname='$name'and pass='$pass' and status!='ldap'";
//	$query = "select * from users where uname='$name'";
	$r = db_query($query);
//	$a = db_fetch_assoc($r);
	
//	if (db_num_rows($r)  && $a['pass'] == $pass) {
	if (db_num_rows($r)) {
		$a = db_fetch_assoc($r);
		$x = array(); // array for returned info
		$x[fullname] = $a[fname];
		$x[user] = $name;
		$x[pass] = $pass;
		$x[email] = $a[email];
		$x[type] = $a[type];
		$x[method] = 'db';
		$x[id] = $a[id];
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