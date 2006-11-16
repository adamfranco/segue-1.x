<? /* $Id$ */

include("objects/objects.inc.php");
$content = '';
$message = '';

ob_start();
session_start();


// include all necessary files
include("includes.inc.php");

if ($_SESSION['ltype'] != 'admin') {
	// take them right to the user lookup page
	header("Location: username_lookup.php");
	exit;
}

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

// what's the action?
$curraction = $_REQUEST['action'];

if ($curraction == 'del') {
	$id = $_REQUEST['id'];
	if ($id > 0) { user::delUser($id); $message = "User $delname (id=$id) deleted successfully."; }
}

// if they want to add a user...
if ($curraction == 'add') {
	// check for errors first
	if (user::userExists($_REQUEST['uname'])) error("A user with that username already exists.");
	if (!$_REQUEST['uname']) error("You must enter a username.");
	if (!$_REQUEST['email']) error("You must enter a valid email address.");
	// all good
	if (!$error) {
		$obj = &new user();
		$obj->uname = strtolower($_REQUEST['uname']);
		$obj->fname = $_REQUEST['fname'];
		$obj->email = $_REQUEST['email'];
		$obj->type = $_REQUEST['type'];
		$obj->authtype = 'db';
		$obj->randpass(5,3);
		$obj->insertDB();
		$obj->sendemail();
		unset($_REQUEST['uname'],$_REQUEST['fname'],$_REQUEST['email'],$_REQUEST['type']);
		$message = "User '".$obj->uname."' added successfully.";
		$name = $_REQUEST['uname'];
	}
}

// if they're editing a user
if ($curraction == 'edit') {
	if ($_REQUEST['commit']==1) {
		if (!$_REQUEST['uname']) error("You must enter a username.");
		if (!$_REQUEST['email']) error("You must enter a valid email address.");
		if (!$error) {
			$obj = &new user();
			$obj->fetchUserID($_REQUEST['id']);
			$obj->uname = strtolower($_REQUEST['uname']);
			$obj->fname = $_REQUEST['fname'];
			$obj->email = $_REQUEST['email'];
			$obj->type = $_REQUEST['type'];
			$obj->updateDB();
			unset($_REQUEST['uname'],$_REQUEST['fname'],$_REQUEST['email'],$_REQUEST['type'],$curraction);
			$message = "User '".$obj->uname."' updated successfully.";
		}
	}
}
		
if ($curraction == 'resetpw') {
	$id = $_REQUEST['id'];
	if ($id > 0) {
		$obj = &new user();
		$obj->fetchUserID($id);
		$obj->randpass(5,3);
		$obj->updateDB();
		$obj->sendemail(1);
		$message = "A random password has been generated for '".$obj->uname."' and an email has been sent to them.";
	}
}

/******************************************************************************
 * get search variables and create query
 ******************************************************************************/
		
$name = $_REQUEST['name'];
$id = $_REQUEST['id'];
$type = $_REQUEST['type'];
$authtype = $_REQUEST['authtype'];
$order = $_REQUEST['authtype'];


$where = "user_uname LIKE '%'";	
if ($name) $where = "(user_uname LIKE '%".addslashes($name)."%' OR user_fname LIKE '%".addslashes($name)."%')";

if ($type == "Any") {
	$where .= " AND user_type LIKE '%'";
} else if ($type) {
	$where .= " AND user_type = '".addslashes($type)."'";
}

if ($authtype == "All") {
	$where .= " AND user_authtype LIKE '%'";
} else if ($authtype) {
	$where .= " AND user_authtype = '".addslashes($authtype)."'";
}
	
if ($findall) {
	$name = '%';
	$type = "Any";
	$authtype = "All";
	$where = "user_uname LIKE '%'";
}




/******************************************************************************
 * query database only if search has been made
 ******************************************************************************/
 
//if $id then editing a user
 if ($id) {	
	$query = "
		SELECT
			user_id,user_uname,user_fname,user_email,user_type,user_authtype
		FROM
			user
		WHERE
			user_id = ".addslashes($id)."
		ORDER BY
			user_uname ASC
	";
	
	$r = db_query($query);

//if not editing then get all users by name or type	or authtype
} else if ($name || $type || $authtype) {
	
	$query = "
	SELECT
		COUNT(*) AS user_count
	FROM
		user
	WHERE
		$where";
		
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$numusers = $a[user_count];
	
	
	if (isset($_REQUEST['range']))
		$range = intval($_REQUEST['range']);
	else
		$range = 30;
	
	
	if (isset($_REQUEST['lowerlimit']))
		$lowerlimit = intval($_REQUEST['lowerlimit']);
	else
		$lowerlimit = 0;
		
		
	
	if ($lowerlimit < 0) 
		$lowerlimit = 0;
	
	$limit = " limit $lowerlimit,$range";


	
	$query = "
	SELECT
		user_id,user_uname,user_fname,user_email,user_type,user_authtype
	FROM
		user
	WHERE
		$where
	ORDER BY
		user_uname ASC
	$limit
	";
	
	$r = db_query($query);
}

printerr();


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Users</title>
<? include("themes/common/logs_css.inc.php"); ?>
</head>
<!-- <body onload="document.addform.uname.focus()">  -->
<body onload="document.searchform.name.focus()">

<?
/******************************************************************************
 * Get site id for links to participation section
 ******************************************************************************/
	
	$siteObj =&new site($site);
	$siteid = $siteObj->id;

if ($_SESSION['ltype']=='admin') {
	print "<table width='100%'  class='bg'><tr><td class='bg'>
	Logs: <a href='viewsites.php?$sid&amp;site=$site'>sites</a> 
	 | <a href='viewlogs.php?$sid&amp;site=$site'>users</a>
	</td><td align='right' class='bg'>
	add/edit users | 
	<a href='classes.php?$sid&amp;site=$site'>add/edit classes</a> | 
	<a href='add_slot.php?$sid&amp;site=$site'>add/edit slots</a> |
	<a href='update.php?$sid&amp;site=$site'>segue updates</a>
	</td></tr></table>";
}

if ($site) {
	print "<div align='right'>";
	print "<a href='add_students.php?$sid&amp;name=$site'>Roster</a>";
	print " | <a href='email.php?$sid&amp;siteid=$siteid&amp;site=$site&amp;action=list&amp;scope=site'>Participation</a>";
	print " | <a href='viewusers.php?$sid&amp;site=$site'>Logs</a>";
	print "</div><br />";
}


?>

<?=$content?>

<table cellspacing='1' width='100%' id='maintable'>
<tr><td>

	<table cellspacing='1' width='100%'>
	<tr><td>
		<form action="<? echo $PHP_SELF ?>" method='get' name='searchform'>
		Name: <input type='text' name='name' size='20' value='<?echo $name?>' /> 
		User Type:
		<select name='type'>
			<option<?=($type=='Any')?" selected='selected'":""?>>Any</option>
			<option<?=($type=='stud')?" selected='selected'":""?>>stud</option>
			<option<?=($type=='prof')?" selected='selected'":""?>>prof</option>
			<option<?=($type=='staff')?" selected='selected'":""?>>staff</option>
			<option<?=($type=='visitor')?" selected='selected'":""?>>visitor</option>
			<option<?=($type=='guest')?" selected='selected'":""?>>guest</option>
			<option<?=($type=='admin')?" selected='selected'":""?>>admin</option>
		</select>
		<?
		$query = "
			SELECT
				DISTINCT user_authtype
			FROM
				user
		";	
		
		$r2 = db_query($query);
		?>
		Auth Type:
		<select name='authtype'>
			<option<?=($authtype=='All')?" selected='selected'":""?>>All</option>
		<?
		while ($a = db_fetch_assoc($r2)) {
			print "\n\t\t\t\t<option ";
			($authtype==$a['user_authtype'])? print "selected='selected'": print "";
			print ">".$a['user_authtype']."</option>";
		}	
		?>

		</select>
		<input type='submit' name='search' value='Find' />
		<input type='submit' name='findall' value='Find All' />
		</form>
		</td>
		<td align='right'>
		<?
		if ($range) {
			$tpages = ceil($numusers/$range);			
			$curr = ceil(($lowerlimit+$range)/$range);
		} else {
			$tpages = 1;			
			$curr = 1;
		}
		$prev = $lowerlimit-$range;
		if ($prev < 0) $prev = 0;
		$next = $lowerlimit+$range;
		if ($next >= $numusers) $next = $numusers-$range;
		if ($next < 0) $next = 0;
		print "$curr of $tpages ";
	//	print "(prev=$prev lowerlimit=$lowerlimit next=$next )";
		if ($prev != $lowerlimit)
			print "<input type='button' value='&lt;&lt;' onclick='window.location=\"$PHP_SELF?$sid&lowerlimit=$prev&type=$type&authtype=$authtype&name=$name&order=$order\"' />\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type='button' value='&gt;&gt;' onclick='window.location=\"$PHP_SELF?$sid&lowerlimit=$next&type=$type&authtype=$authtype&name=$name&order=$order\"' />\n";
		?>
	
	</td></tr>
	</table>
	
	<? if (!db_num_rows($r)) {
		print "No matching names found";
	} else {
		//$numusers = db_num_rows($r);
		print "Total users found: ".$numusers;
	} 
	
	?>
<form method='post' name='addform' action='<? echo $PHP_SELF ?>'>
	<table width='100%'>
		<tr>
		<th>id</th>
		<th>user</th>
		<th>full name</th>
		<th>email</th>
		<th>type</th>
		<th>AuthN</th>
		<th>options</th>
		</tr>
		
		<? if ($curraction != 'edit') { doUserForm($_REQUEST); } 
		if ($curraction == 'edit') {
			$a = db_fetch_assoc($r);
			doUserForm($a,'user_',1);
		
		// output found users				
		} else if (db_num_rows($r)){
			while ($a = db_fetch_assoc($r)) {
				print "<tr>";
				print "<td align='center'>".$a['user_id']."</td>";
				print "<td>".$a['user_uname']."</td>";
				print "<td>".$a['user_fname']."</td>";
				print "<td>".$a['user_email']."</td>";
				print "<td>".$a['user_type']."</td>";
				print "<td>".$a['user_authtype']."</td>";
				print "<td align='center'><span style='white-space: nowrap;'>";
				if ($a['user_authtype'] == "db") {
					print "<a href='users.php?$sid&amp;name=$name&amp;type=$type&amp;action=del&amp;id=".$a['user_id']."&amp;delname=".$a['user_uname']."'>del</a> | \n";
				} else {
					print "del | \n";
				}
				print "<a href='users.php?$sid&amp;name=$name&amp;type=$type&amp;action=edit&amp;id=".$a['user_id']."'>edit</a>\n";
				if ($a['user_authtype'] == "db") {
					print " | <a href='users.php?$sid&amp;name=$name&amp;type=$type&amp;action=resetpw&amp;id=".$a['user_id']."'>reset pwd</a>\n";
				} else {
					print " | reset pwd\n";
				}
				print "</span></td>";
				print "</tr>";
			}
		}
		?>
	</table>
</form>
</td></tr>
</table>

<br />
<div align='right'><input type='button' value='Close Window' onclick='window.close()' /></div>
<?
function doUserForm($a,$p='',$e=0) {
	?>
	
	<tr>
	<td><?=($e)?$a[$p.'id']:"&nbsp;"?></td>
	<td><?=($a[$p.'authtype'] == "db" || !$e)?"<input type='text' name='uname' size='10' value=\"".$a[$p.'uname']."\" />":$a[$p.'uname']?></td>
	<td><?=($a[$p.'authtype'] == "db" || !$e)?"<input type='text' name='fname' size='20' value=\"".$a[$p.'fname']."\" />":$a[$p.'fname']?></td>
	<td><?=($a[$p.'authtype'] == "db" || !$e)?"<input type='text' name='email' size='30' value=\"".$a[$p.'email']."\" />":$a[$p.'email']?></td>
	<td><select name='type'>
		<option<?=($a[$p.'type']=='stud')?" selected='selected'":""?>>stud</option>
		<option<?=($a[$p.'type']=='prof')?" selected='selected'":""?>>prof</option>
		<option<?=($a[$p.'type']=='staff')?" selected='selected'":""?>>staff</option>
		<option<?=($a[$p.'type']=='visitor')?" selected='selected'":""?>>visitor</option>
		<option<?=($a[$p.'type']=='guest')?" selected='selected'":""?>>guest</option>
		<option<?=($a[$p.'type']=='admin')?" selected='selected'":""?>>admin</option>
	</select>
	</td>
	<td><?=($e)?$a[$p.'authtype']:"db"?></td>
	<td align='center'>
	<input type='hidden' name='action' value='<?=($e)?"edit":"add"?>' />
	<?
	if ($e) {
		print "<input type='hidden' name='id' value='".$a[$p."id"]."' /><input type='hidden' name='commit' value='1' />";
		if ($a[$p.'authtype'] != "db") {
			print "<input type='hidden' name='uname' value=\"".$a[$p.'uname']."\" />";
			print "<input type='hidden' name='fname' value=\"".$a[$p.'fname']."\" />";
			print "<input type='hidden' name='email' value=\"".$a[$p.'email']."\" />";
		}
	} else {
		print "";
	}	
	?>
	<a href='#' onclick='document.addform.submit()'><?=($e)?"update":"add user"?></a>
	<!-- | <a href='users.php'>cancel</a> -->
	</td>
	</tr>
	<?
}
?>

</body>
</html>