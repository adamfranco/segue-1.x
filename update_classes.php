<? 
//adds classes currently in LDAP to class table in Segue
//this allows for listing of students in a class, adding sites for students that are related to a class
//maintaining a history of classes in Segue

include("functions.inc.php");
include("dbwrapper.inc.php");
include("config.inc.php");
 
// connect to the database
db_connect($dbhost, $dbuser, $dbpass, $dbdb);

//$user = "jfu";
$query = "select * from users where uname='$user'";
$ru = db_query("select * from users");
//print $query."<br>";

$a = db_fetch_assoc($ru);
global $ldap_voadmin_user, $ldap_voadmin_pass,$ldapserver;
$ldap_user = "cn=$ldap_voadmin_user,cn=Recipients,ou=Midd,o=MC";
$ldap_pass = $ldap_voadmin_pass;
$classes = array();
//print $user;

while ($a = db_fetch_assoc($ru)) {
	$nextuser = $a['uname'];
	$nextuser = strtolower($nextuser);	
	//print $nextuser."<br>"; 
	
	$c = ldap_connect($ldapserver); 
	$r = @ldap_bind($c,$ldap_user,$ldap_pass);	
	if ($r) {		// connected & logged in
		$return = array("uid","cn","memberof");
		$base_dn = "ou=Midd,o=MC";
		$filter = "uid=$nextuser";
		//print "ok";
		$sr = ldap_search($c,$base_dn,$filter,$return);
		$res = ldap_get_entries($c,$sr);
//		print "<pre>";print_r($res);print"</pre>";
		$num = ldap_count_entries($c,$sr);
//		print "num: $num<br>";
		ldap_close($c);
		if ($num) {
//			print "memberof num: ".$res[0]['memberof']['count']."<br>";
//			print "or ".count($res[0][memberof])."<br>";
			for ($i = 0; $i<$res[0]['memberof']['count']; $i++) {
				$f = $res[0]['memberof'][$i];
//				print "$f<br>";
				$parts = explode(",",$f);
				foreach ($parts as $p) {
					if (ereg("cn=([a-zA-Z]{2})([0-9]{3})([a-zA-Z]{0,1})-([lsfw]{1})([0-9]{2})",$p,$r)) {
//						print "goood!";
						//$semester = currentsemester ();
						$class = $r[1].$r[2].$r[3]."-".$r[4].$r[5];
						$semester = $r[4];
						$year = "20".$r[5];
						$classes[$class] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
						$classinfo = db_get_line("classes","name='$class'");
						$userinfo = db_get_line("users","uname='$nextuser'");
						$usertype = $userinfo[type];
						$userfname = $userinfo[fname];
						//if (!$classinfo) {
							$query = "insert into classes set name='$class', uname='$nextuser', fname='$userfname', type='$usertype', semester='$semester', year='$year'";
							print $query."<br>";
							db_query($query);
						//}	
					}
				}
			}
		}
	}
}



?>
