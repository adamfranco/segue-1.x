<? /* $Id$ */

/*--------------------------------------------------------------------------------------*/
/* ---------------------------   DB Wrapper ----------------------------------------- 	*/
/* 		Allows you to easily connect to both MySQL and Oracle 8 database	*/
/*	systems using the same wrapper functions.				      	*/
/*											*/
/*			code by Gabriel Schine						*/
/* ------------------- Copyright 2001 Middlebury College ----------------------------	*/
/*--------------------------------------------------------------------------------------*/
//  Version history:
//    July 4, 2001 - works. simple connect, disconnect, query, and fetch arrays functionality



/*--------------------------------------------------------------------------------------*/
/* --------------------------	GLOBALS ---------------------------------------------	*/
/*--------------------------------------------------------------------------------------*/
// $db_type: set this variable to the type of db you will be connecting to.
//		possible values: "oracle", "mysql"    (values are case-insensitive)
$db_type = "MySQL";


/*--------------------------------------------------------------------------------------*/
/* --------------------------  /end GLOBALS ----------------------------------------	*/
/*--------------------------------------------------------------------------------------*/


/*--------------------------------------------------------------------------------------*/
/* --------------------------  FUNCTIONS -------------------------------------------	
/*--------------------------------------------------------------------------------------

int db_connect(string $host_db,string $username, string $password [, string $db [, int $port=0]]);
	returns connection identifyer

	$host_db = either the hostname (mySQL) or the DB definition (Oracle - specified
			in tnsnames.ora file)
	$username = username to logon as
	$password = oh gee, i forgot what this one is
	$db	= (mySQL only) name of database to use
	$port = (optional, mySQL only) define the tcp/ip port to connect to

int db_query (string $query [, int $cid]);
	returns a result identifyer

	$query = the database query string, such as "SELECT * FROM temp"
	$cid = (optional) the connection ID returned by db_connect()

array db_fetch_assoc(int $res);
	returns an associative array of the next row's data. $res is the result id
	recieved from db_query();

db_disconnect([int $cid]);
	disconnects from the database server

int db_num_rows(int $res);
	counts the number of rows returned by a "SELECT" query. $res is the value returned
	by the db_query() function.

int db_affected_rows(int $res);
	counts the number of rows affected by a "DELETE", "UPDATE", or other SQL query.
	KNOWN ISSUE: since the mysql equiv if db_affected_rows uses the connection id instead of
	the result id, the $res var is never used, and the stored connect_id is used instead.
	This could create problems if users have multiple connections open at the same time.

db_error();
	prints out an error string.
/*--------------------------------------------------------------------------------------*/
/* --------------------------  /end FUNCTIONS ----------------------------------------	*/
/*--------------------------------------------------------------------------------------*/







/*--------------------------------------------------------------------------------------*/
/* 		DO NOT EDIT BELOW THIS LINE!						*/
/*--------------------------------------------------------------------------------------*/

// check if we are already loaded
if (isset($_db_wrapper)) {
  /* if ($debug) print "We're already loaded -- skipping load process.<BR>\n"; */
  return;
}

// $_connect_id: DO NOT EDIT THIS - this variable is used to save the connection ID
//				in case the user does not in their script
$_connect_id = -1;

// First off, we want to make $db_type a lower-case string
$db_type = strtolower($db_type);

// Print out some debug info
if ($debug) {
  print "dbwrapper.php: Starting.<BR>";
}

// these functions requires dbhost, dbuser, dbpass, and dbdb to be set
function next_autoindex($table) {
	global $dbhost, $dbuser, $dbpass, $dbdb;
	$query = "show table status like '$table'";
	db_connect($dbhost, $dbuser, $dbpass, $dbdb);
	$r = db_query($query);
	$info = db_fetch_assoc($r);
	return $info['auto_increment'];
}

function lastid($cid=-1) {
	global $_connect_id;
	if ($cid==-1) $cid = $_connect_id;
	return mysql_insert_id($cid);
}

function db_get_value($table, $what, $where) {
  global $dbhost, $dbuser, $dbpass, $dbdb;
  global $_connect_id;
  if ($cid==-1) $cid = $_connect_id;
  $query = "select $what from $table where $where";
  db_connect($dbhost, $dbuser, $dbpass, $dbdb);
  $r = db_query($query);
   if (!$r) db_error();
  if (db_num_rows($r)) {
    $a = db_fetch_array($r);
    $v = $a[0];
    return $v;
  }
  return null;
}

function db_get_line($table,$where) {
	global $dbhost, $dbuser, $dbpass, $dbdb;
	global $debug;
	db_connect($dbhost, $dbuser, $dbpass,$dbdb);
	$query = "select * from $table where $where";
	if ($debug) print "$query<BR>";
	$line = db_fetch_assoc(db_query($query));
	return $line;
}

function db_line_exists($table, $where) {
	global $dbhost, $dbuser, $dbpass, $dbdb;
	db_connect($dbhost, $dbuser, $dbpass,$dbdb);
	$query = "select * from $table where $where";
	return db_num_rows(db_query($query));
}

function db_connect ($host_db, $username, $password, $db='', $port=0) {
  global $_connect_id;
  global $db_type; global $debug;
  if ($debug) {
/*     print "<br><br>db_connect: starting function with arguments:<br>"; */
/*     print "host_db = $host_db, username = $username, password = $password"; */
/*     print ", db = $db, port = $port<br>"; */
/*     print "db_connect: db_type = $db_type<br>"; */
  }
  if ($db_type == "mysql") {
    if ($port != 0) $host_db .= ":$port";
    $cid = mysql_pconnect($host_db,$username,$password) OR die("db_connect: Could not connect to host $host_db: " . mysql_error());
    $_connect_id = $cid;		// save the cid var for later use
    // now that we have a good connection, let's set the database to use
    mysql_select_db($db) or die ("db_connect: Could not select database $db: " . mysql_error());
    return $cid; // done
  } else if ($db_type == "oracle") {
    $cid = OCILogon($username, $password, $host_db) or
		ocidie("db_connect: Could not connect to database $host_db");
    $_connect_id = $cid;		// save the cid var for later use
    return $cid; // done
  }
}

function db_error() {
  global $db_type;
  if ($db_type == "mysql") {
    print mysql_error();
  }
  if ($db_type == "oracle") {
    $a = OCIError();
    print $a['message'];
  }
}

function ocidie ($t) {
  global $db_type; global $debug;
  $a = OCIError();
  $s = ": ". $a['code'] . ": " . $a['message'];
  $t .= $s;
  die ($t);
}

/******************************************************************************
 * for counting the number of queries done.
 ******************************************************************************/
$_totalQueries = 0;

function db_query ($query, $cid=-1) {
	// for counting the total number of queries
	global $_totalQueries;
	$_totalQueries++;
	
  global $db_type; global $debug;
  global $_connect_id;
  if ($debug) {
  	// The $debug variable is set at the top of this script
	// The $debug variable also prints a lot of other crap that clutters the screen and I don't want to see ;)
    echo "\n\n<br><br>QUERY:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n".$query;
  }
  if ($cid==-1) $cid = $_connect_id;
  if ($db_type == "mysql") {
    $res = mysql_query($query, $cid);
	if ($debug) {
	 	// The $debug variable is set at the top of this script	
		// The $debug variable also prints a lot of other crap that clutters the screen and I don't want to see ;)
		echo "\n\n<br><b>RESULT:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n".$res."</b>";
	}
 	if (mysql_error() && $debug)
 		echo "\n<b>".mysql_error()."</b>";
    return $res;
  } else if ($db_type == "oracle") {
    $stmt = OCIParse($cid, $query) or ocidie ("db_query: could not query the server with $query");
    OCIExecute($stmt) or ocidie("db_query: could not execute the OCI statement");
    return $stmt;
  }
}

function db_fetch_assoc($res) {
  global $db_type; global $debug;
  
  if ($db_type=="mysql") {
    $ar = mysql_fetch_assoc($res);
    //$ar = mysql_fetch_array($res);
/*     if ($ar) { */
/*       foreach ($ar as $name=>$value)  */
/*         $arn[strtolower($name)] = $arn[strtoupper($name)] = $value; */
/*     } */
    return $ar;
  } else if ($db_type == "oracle") {
    $ar = array();
    OCIFetchInto($res, &$ar, OCI_ASSOC+OCI_RETURN_LOBS);
/*     if ($ar) { */
/*       foreach($ar as $name=>$value) */
/*         $arn[strtolower($name)] = $arn[strtoupper($name)] = $value; */
/*     } */
    return $ar;
  }
}
    
function db_fetch_array($res) {
  global $db_type; global $debug;
  
  if ($db_type=="mysql") {
    $ar = mysql_fetch_array($res,MYSQL_NUM);
    return $ar;
  } else if ($db_type == "oracle") {
    $ar = array();
    OCIFetchInto($res, &$ar, OCI_NUM+OCI_RETURN_LOBS);
    return $ar;
  }
}
    
function db_disconnect($cid = -1) {
  global $db_type; global $debug; global $_connect_id;
  if ($cid==-1) $cid = $_connect_id;
  if ($db_type == "mysql") {
    return mysql_close($cid);
  } else if ($db_type == "oracle") {
    return OCILogOff($cid);
  }
}

function db_num_rows($res) {
  global $db_type; global $debug;
  if ($db_type == "mysql") {
    if (!$res) return 0;
    return mysql_num_rows($res);
  } else if ($db_type == "oracle") {
    $num = OCIFetchStatement($res);
    OCIExecute($res);			// since ocifetchstatement advances the results one row
					// we need to reset that by executing the query again
    return $num;
  }
}

// KNOWN ISSUE: since the mysql equiv if db_affected_rows uses the connection id instead of
// the result id, the $res var is never used, and the stored connect_id is used instead.
// This could create problems if users have multiple connections open at the same time.

function db_affected_rows($res) {
  global $db_type; global $debug; global $_connect_id;
  if ($db_type == "mysql") {
    return mysql_affected_rows($_connect_id);
  } else if ($db_type == "oracle") {
    return OCIRowCount($res);
  }
}

  
// We are all loaded
$_db_wrapper = 1;

