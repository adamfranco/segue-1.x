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
 * Delete Associated Site:
 ******************************************************************************/

if ($action == "deletesite" && $delstudentsite) {
	$slot_name = $delstudentsite;
	$id = db_get_value("slot","slot_id","slot_name='$slot_name'");	
	if ($id > 0) {
		// delete a slot
		$slotObj = new slot("","","","",$id);
		$slotObj->delete();
	}
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
	$site = $class_external_id;
	$_REQUEST[ugroup_id] = $ugroup_id;
	$participants = getclassstudents($class_external_id);
	//printpre($class_external_id);
	//printpre($participants);

/******************************************************************************
 * Admin add student UI
 ******************************************************************************/

} else {
	$ugroup_id = $_REQUEST[ugroup_id];
	$class_external_id = db_get_value("class","class_external_id","FK_ugroup = $ugroup_id");
	$site = $class_external_id;
	$participants = getclassstudents($class_external_id);
}

/******************************************************************************
 * Get site id for links to participation section
 ******************************************************************************/
	
	$siteObj =&new site($class_external_id);
	$siteid = $siteObj->id;

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
<? include("themes/common/logs_css.inc.php"); ?>
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

function delStudentSite(id, name) {
	if (confirm("Are you sure you want to delete the site for " + name + " that is associated with this class?")) {
		f = document.delsiteform;
		f.action.value = 'deletesite';
		f.delstudentsite.value = id;
		f.deluname.value = name;
		f.submit();
	}
}


</script>

</style>
<form action="<? echo $PHP_SELF ?>" method=get name='lookup'>
<input type=hidden name='ugroup_id' value='<?=$_REQUEST[ugroup_id]?>'>
<input type=hidden name='participants' value='<?=$_REQUEST[name]?>'>
<!-- <input type=hidden name='ugroup_id' value='<? $ugroup_id ?>'> -->

<?
printerr2();
if ($_SESSION['ltype']=='admin') {
	print "<table width=100%  class='bg'><tr><td class='bg'>
	Logs: <a href='viewsites.php?$sid&site=$site'>sites</a>
	 | <a href='viewlogs.php?$sid&site=$site'>users</a>
	</td><td align=right class='bg'>
	<a href='users.php?$sid&site=$site'>add/edit users</a> | 
	<a href='classes.php?$sid&site=$site'>add/edit classes</a> | 
	<a href='add_slot.php?$sid&site=$site'>add/edit slots</a> |
	<a href='update.php?$sid&site=$site'>segue updates</a>
	</td></tr></table>";
}

print "<div align=right>";
print "Roster";
print " | <a href='email.php?$sid&siteid=$siteid&site=$class_external_id&action=list&scope=site'>Participation</a>";
print " | <a href='viewlogs.php?$sid&site=$class_external_id'>Logs</a>";
print "</div><br>";
?>
<!-- <div align=right>Students | Participants</div><br> -->
<table cellspacing=1 width='100%' id='maintable'>

<tr>
 	<th width=20%>Add</th>
	<th width=60%>New Students</th>
	<th width=20%></th>
</tr>
<tr>
	<td align=center>
	<? 
	if ($_SESSION[ltype]=='admin') 
		print "<input type=button name='use' value='add' onClick='addStudent(document.lookup.n.value)'>"; 
	else 
		print "&nbsp;  ";
	?>
	</td>
	<td>Name: <input type=text name='n' size=20 value='<?echo $_REQUEST[n]?>'> <input type=submit value='find'></td>
	<td></td>	
</tr>
<?
if (count($usernames)) {
	$c = 1;
	foreach ($usernames as $u=>$f) {
		if (!$u || $u=='') next;
		if (!ereg("[a-z]",$u)) next;
		$u = strtolower($u);
		print "<tr>";
		print "<td class=td$color align=center><input type=button name='use' value='add' onClick='addStudent(\"$u\")'></td>";
		print "<td class=td$color>$f ($u)</td>";
		print "<td class=td$color></td>";
		print "</tr>";
		$c++;
		$color = 1-$color;
	}
} else {
	print "<tr><td></td><td colspan=2>No usernames. Enter a name or part of a name above.</td></tr>";
}
?>
</table>

</form>

<p>
<div align=center><b>Class Roster</b></div>
Below is a current list of all the students in your class. You have the option of creating a site
for each of your students that is <i>associated</i> with your class site.  An associated site can be
used for class projects that involve extensive web publication such as weblogs.
<br><br>

<table cellspacing=1 width='100%' id='maintable'>
<tr>
	<th width=20%>Remove</th>
	<th width=60%>Current Students</th>
	<th width=20%>Associated Site</th>
</tr>
<?

/******************************************************************************
 * Lists all students who are members of the class:
 ******************************************************************************/
$owner_uname = db_get_value("class","FK_owner","FK_ugroup = $ugroup_id");

foreach (array_keys($participants) as $key) {
	//printpre($participants[$key][uname]);

	print "<tr>";
	/******************************************************************************
	 * If not site owner and participant not from LDAP
	 * print out delete button
	 ******************************************************************************/
	print "<td align=center  class=td$color>";
	

	if ($owner_uname != $participants[$key][uname] && $participants[$key][memberlist] != "external") {
		print "<input type=button name='use' value='remove' onClick=\"delStudent('".$participants[$key][id]."','".$participants[$key][fname]." (".$participants[$key][uname].")')\">";
	}
	print "</td>";
	
	/******************************************************************************
	 * Print out participant/student names
	 ******************************************************************************/

	print "<td class=td$color>";
	print $participants[$key][fname]." (".$participants[$key][uname].")";
	if ($participants[$key][type] == "prof") print " - ".$participants[$key][type]."";
	print "</td>";
	
	/******************************************************************************
	 * Print out buttons for creating, deleting and viewing associated sites
	 ******************************************************************************/

	print "<td align=center class=td$color>";
	if ($participants[$key][type] != "prof") {
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
		
		// if associated site slot doesn't exit, print out add site button
		if (!db_num_rows($r)) {
			print "<input type=button name='classsite' value='add site' onClick=\"addStudentSite('".$class_external_id."','".$participants[$key][uname]."')\">";
		// if associated site slot does exist
		// if site is created in associated site slot, then print link
		// if site has not yet been created, print delete site button
		} else {
			if ($a['inuse']) {
				print "<a href=$cfg[full_uri]/sites/$slotname target=new_window>View Site</a>";
			} else {
				print "<input type=button name='delclasssite' value='delete site' onClick=\"delStudentSite('".$slotname."','".$participants[$key][uname]."')\">";
			}
		}
	}
	print "</td>";
	print "</tr>";
	$color = 1-$color;
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

<form name='delsiteform'>
<input type=hidden name='ugroup_id' value='<?=$_REQUEST[ugroup_id]?>'>
<input type=hidden name='delstudentsite'>
<input type=hidden name='deluname'>
<input type=hidden name="action">
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