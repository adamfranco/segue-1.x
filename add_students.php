<? /* $Id$ */

require("objects/objects.inc.php");

ob_start();
session_start();

include("includes.inc.php");

db_connect($dbhost, $dbuser, $dbpass, $dbdb);
//printpre($_REQUEST);

/******************************************************************************
 * Add Associated Site: 
 ******************************************************************************/

if ($action == "addsite" && $addclassid && $adduname) {

	$slotname = $addclassid."-".$adduname;
//	printpre($slotname);
	$obj = &new slot($slotname);
	$obj->owner = strtolower($adduname);
	$obj->assocSite = strtolower($addclassid);
	$obj->type = 'class';
	$obj->uploadlimit = 'NULL';
	
	$obj->insertDB();
	//db_query($query);
}

/******************************************************************************
 * Add student to a class site
 ******************************************************************************/

if ($action == "add" && $addstudent) {
	// make sure the user is in the db
	$valid = 0;
	foreach ($_auth_mods as $_auth) {
		$func = "_valid_".$_auth;
//		print "<BR>AUTH: trying ".$_auth ."..."; //debug
		if ($x = $func($addstudent,"",1)) {
			$valid = 1;
			break;
		}
	}
	
	if ($valid) {
		// Get their db id
		$user_id = db_get_value("user","user_id","user_uname='$addstudent'");
		
		// add them to the ugroup
		$query = "
			INSERT INTO
				ugroup_user
			SET
				FK_user=$user_id,
				FK_ugroup=$ugroup_id			
		";
		db_query($query);
	} else {
		error("invalid username");
	}
}

/******************************************************************************
 * Delete student from a class site
 ******************************************************************************/

if ($action == "delete" && $delstudent) {
	$query = "
		DELETE
		FROM
			ugroup_user
		WHERE
			FK_ugroup = $ugroup_id
				AND
			FK_user = $delstudent	
	";
	db_query($query);
}

/******************************************************************************
 * Lookup a student in userlookup
 ******************************************************************************/

if ($_REQUEST[n]) {
	//include("config.inc.php");
	//include("functions.inc.php");
	$usernames=userlookup($_REQUEST[n],LDAP_BOTH,LDAP_WILD,LDAP_LASTNAME,0);

}	


/******************************************************************************
 * Site Owner add student UI
 ******************************************************************************/

if (isset($_REQUEST[name])) {
	//printpre($name);
	$class_external_id = $_REQUEST[name];
	$ugroup_id = db_get_value("class","FK_ugroup","class_external_id = '$class_external_id'");
	$_REQUEST[ugroup_id] = $ugroup_id;
	//printpre($ugroup_id);
	$participants = getclassstudents($class_external_id);
	//printpre($class_external_id);
	//printpre($participants);

/******************************************************************************
 * Admin add student UI
 ******************************************************************************/

} else {
	$ugroup_id = $_REQUEST[ugroup_id];
	$class_external_id = db_get_value("class","class_external_id","FK_ugroup = $ugroup_id");
	$participants = getclassstudents($class_external_id);

}



/******************************************************************************
 * Sort alphabetically usernames from userlookup and participant names
 ******************************************************************************/
if (count($usernames)) {
	asort($usernames);
	reset($usernames);
}
/* if (count($participants)) { */
/* 	asort($participants); */
/* 	reset($participants); */
/* } */



?>
<html>
<head>
<title>Add Students</title>

<script lang='JavaScript'>
function addStudent(na) {
	f = document.addform;
	if (na == '') {
		alert("You must enter a username, or search for one by pressing 'find'.");
	} else {
		f.action.value = 'add';
		f.addstudent.value = na;
		f.submit();
	}
}

function addStudentSite(id, name) {
	if (confirm("Are you sure you want to create a site associated with " + id + " for " + name + "?")) {
	f = document.addsiteform;
	f.action.value = 'addsite';
	f.addclassid.value = id;
	f.adduname.value = name;
	f.submit();
	}
}


function delStudent(id, name) {
	if (confirm("Are you sure you want to remove " + name + "?")) {
		f = document.delform;
		f.action.value = 'delete';
		f.delstudent.value = id;
		f.submit();
	}
}

</script>

<style type='text/css'>
table {
	border: 1px solid #555;
}

th, td {
	border: 0px;
	background-color: #ddd;
}

th { 
	background-color: #ccc; 
	font-variant: small-caps;
}

body { 
	background-color: white; 
}

body, table, td, th, input {
	font-size: 12px;
	font-family: "Verdana", "sans-serif";
}

input {
	border: 1px solid black;
	background-color: white;
	font-size: 10px;
}

</style>
<form action="<? echo $PHP_SELF ?>" method=get name='lookup'>
<input type=hidden name='ugroup_id' value='<?=$_REQUEST[ugroup_id]?>'>
<input type=hidden name='participants' value='<?=$_REQUEST[name]?>'>
<!-- <input type=hidden name='ugroup_id' value='<? $ugroup_id ?>'> -->

<?
printerr2();
?>

<table cellspacing=1 width='100%'>

<tr>
	<th>Use</th>
	<th>Full Name</th>
	<th></th>
</tr>
<tr>
	<td align=center>
	<? 
		if ($_SESSION[ltype]=='admin') 
			print "<input type=button name='use' value='add' onClick='addStudent(document.lookup.n.value)'>"; 
		else 
			print "&nbsp;";
	?>
	</td>
	<td>
		Name: <input type=text name='n' size=20 value='<?echo $_REQUEST[n]?>'> 
	</td>
	<td align=center>
		<input type=submit value='find'>
	</td>
</tr>
<?
if (count($usernames)) {
	$c = 1;
	foreach ($usernames as $u=>$f) {
		if (!$u || $u=='') next;
		if (!ereg("[a-z]",$u)) next;
		$u = strtolower($u);
		print "<tr>";
		print "<td align=center><input type=button name='use' value='add' onClick='addStudent(\"$u\")'></td>";
		print "<td>$f</td><td>$u</td>";
		print "</tr>";
		$c++;
	}
} else {
	print "<tr><td colspan=3>No usernames. Enter a name or part of a name above.</td></tr>";
}
?>
</table>
</form>

<p>

<table width=100%>
<tr>
	<th> </th>
	<th>Name</th>
	<th></th>
</tr>
<?

/******************************************************************************
 * adds all students who are members of the class EXCEPT:
 * -those who have logged in
 * need to check if these students are in the fetched array above...
 ******************************************************************************/
$owner_uname = db_get_value("class","FK_owner","FK_ugroup = $ugroup_id");

foreach (array_keys($participants) as $key) {
	//printpre($participants[$key][uname]);
	
		print "<tr>";
			print "<td align=center>";
			if ($owner_uname != $participants[$key][uname] && $participants[$key][memberlist] != "external") {
				print "<input type=button name='use' value='remove' onClick=\"delStudent('".$participants[$key][id]."','".$participants[$key][fname]." (".$participants[$key][uname].")')\">";
			}
			print "</td>";
			print "<td>";
			print $participants[$key][fname]." (".$participants[$key][uname].")";
			if ($participants[$key][type] == "prof") print " - ".$participants[$key][type]."";
			print "</td>";
			print "<td align=center>";
			if ($_SESSION[ltype]=='admin' && $participants[$key][type] != "prof") {
				$slotname = $class_external_id."-".$participants[$key][uname];
				$query = "
				SELECT 
					slot.slot_name AS name,
					user.user_uname AS owner,
					slot.slot_type AS type,
					assocsite.slot_name AS assocsite_name,
					slot.FK_site as inuse,
					slot.slot_uploadlimit AS uploadlimit
				FROM 
					slot
						LEFT JOIN
					user
						ON
							slot.FK_owner = user_id
						LEFT JOIN
							slot AS assocsite
						ON
							slot.FK_assocsite = assocsite.slot_id
				WHERE
					slot.slot_name = '$slotname'
				";
				$r = db_query($query);
				$a = db_fetch_assoc($r);
				if (!db_num_rows($r)) {
					print "<input type=button name='classsite' value='add site' onClick=\"addStudentSite('".$class_external_id."','".$participants[$key][uname]."')\">";
				} else {
					if ($a['inuse']) {
						print "in use";
					} else {
						print "not in use";
					}
				}
			}
			print "</td>";
		print "</tr>";
	//}


}

?>
</table>

<form name='addsiteform'>
<input type=hidden name='ugroup_id' value='<?=$_REQUEST[ugroup_id]?>'>
<input type=hidden name='addclassid'>
<input type=hidden name='adduname'>
<input type=hidden name="action">
</form>

<form name='addform'>
<input type=hidden name='ugroup_id' value='<?=$_REQUEST[ugroup_id]?>'>
<!-- <input type=hidden name='ugroup_id' value='<? $ugroup_id ?>'> -->
<input type=hidden name='addstudent'>
<input type=hidden name="action">
</form>


<form name='delform'>
<input type=hidden name='ugroup_id' value='<?=$_REQUEST[ugroup_id]?>'>
<input type=hidden name='delstudent'>
<input type=hidden name="action">
<input type=hidden name="comingfrom" value="<? echo $comingfrom ?>">
</form>

<div align=right>
<!-- <input type=button value='Add Editor' onClick='addEditor()'> -->
<input type=button value='Done' onClick='window.close()'></div>

<?

// debug output -- handy :)
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "\n\n"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n";  */
/* print "</pre>"; */