<? /* $Id$ */
include("objects/objects.inc.php");
$content = '';
$message = '';

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

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
 * get search variables and query only if search form has been submitted
 ******************************************************************************/
		
$name = $_REQUEST['name'];
$id = $_REQUEST['id'];
$type = $_REQUEST['type'];

if ($findall) {
	$name = '%';
	$type = "Any";
}

/******************************************************************************
 * query database only if search has been made
 ******************************************************************************/
 
//$id is set when user is edited
 if ($id) {	
	$query = "
	SELECT
		user_id,user_uname,user_fname,user_email,user_type
	FROM
		user
	WHERE
		user_authtype='db'
	AND
		user_id = $id
	ORDER BY
		user_uname ASC";
	$r = db_query($query);

//users can be searched by name or type	
} else if ($name || $type) {
	$where = "user_uname LIKE '%'";	
	if ($name) $where = "(user_uname LIKE '%$name%' OR user_fname LIKE '%$name%')";
	
	//$name = '%';
	if ($type == "Any") {
		$where .= " AND user_type LIKE '%'";
	} else if ($type) {
		$where .= " AND user_type = '$type'";
	}
	
	$query = "
	SELECT
		COUNT(*) AS user_count
	FROM
		user
	WHERE
		user_authtype='db'
	AND
		$where";
		
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$numusers = $a[user_count];
	
	if (!isset($lowerlimit)) $lowerlimit = 0;
	if ($lowerlimit < 0) $lowerlimit = 0;
	$limit = " LIMIT $lowerlimit,30";

	
	$query = "
	SELECT
		user_id,user_uname,user_fname,user_email,user_type
	FROM
		user
	WHERE
		user_authtype='db'
	AND
		$where
	ORDER BY
		user_uname ASC
	$limit";
	
	$r = db_query($query);
}

printerr();


?>

<html>
<head>
<title>Users</title>
<? include("themes/common/logs_css.inc.php"); ?>
</head>
<!-- <body onLoad="document.addform.uname.focus()">  -->
<body onLoad="document.searchform.name.focus()">

<?=($_SESSION['ltype']=='admin')?
	"<div align=right>
		<a href='username_lookup.php?$sid'>user lookup</a> | 
		add/edit users | 
		<a href='classes.php?$sid'>add/edit classes</a> | 
		<a href='add_slot.php?$sid'>add/edit slots</a> |
		<a href='update.php?$sid'>segue updates</a>
	</div>"
:""?>

<?=$content?>

<table cellspacing=1 width='100%' id='maintable'>
<tr><td>

	<table cellspacing=1 width='100%'>
	<tr><td>
		<form action="<? echo $PHP_SELF ?>" method=get name=searchform>
		Name: <input type=text name='name' size=20 value='<?echo $name?>'> 
		Type:
		<select name=type>
		<option<?=($type=='Any')?" selected":""?>>Any
		<option<?=($type=='stud')?" selected":""?>>stud
		<option<?=($type=='prof')?" selected":""?>>prof
		<option<?=($type=='staff')?" selected":""?>>staff
		<option<?=($type=='admin')?" selected":""?>>admin
		</select>
		<input type=submit name='search' value='Find'>
		<input type=submit name='findall' value='Find All'>
		(Segue database users only)
		</td>
		<td align=right>
		<?
		$tpages = ceil($numusers/30);
		$curr = ceil(($lowerlimit+30)/30);
		$prev = $lowerlimit-30;
		if ($prev < 0) $prev = 0;
		$next = $lowerlimit+30;
		if ($next >= $numusers) $next = $numusers-30;
		if ($next < 0) $next = 0;
		print "$curr of $tpages ";
//		print "$prev $lowerlimit $next ";
		if ($prev != $lowerlimit)
			print "<input type=button value='&lt;&lt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$prev&type=$type&name=$name&order=$order\"'>\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type=button value='&gt;&gt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$next&type=$type&name=$name&order=$order\"'>\n";
		?>

		</form>
	
	</td></tr>
	</table>
	
	<? if (!db_num_rows($r)) {
		print "No matching names found";
	} else {
		//$numusers = db_num_rows($r);
		print "Total users found: ".$numusers;
	} 
	
	?>
		
	<table width='100%'>
		<tr>
		<th>id</th>
		<th>user</th>
		<th>full name</th>
		<th>email</th>
		<th>type</th>
		<th>options</th>
		</tr>
		
		<? if ($curraction != 'edit') { doUserForm($_REQUEST); } 
		if ($curraction == 'edit') {
			$a = db_fetch_assoc($r);
			doUserForm($a,'user_',1);
		
		// output found users				
		} else if ($name || $id) {	
				
			while ($a = db_fetch_assoc($r)) {
				print "<tr>";
				print "<td align=center>".$a['user_id']."</td>";
				print "<td>".$a['user_uname']."</td>";
				print "<td>".$a['user_fname']."</td>";
				print "<td>".$a['user_email']."</td>";
				print "<td>".$a['user_type']."</td>";
				print "<td align=center><nobr>";
				print "<a href='users.php?$sid&name=$name&type=$type&action=del&id=".$a['user_id']."&delname=".$a['user_uname']."'>del</a> | \n";
				print "<a href='users.php?$sid&name=$name&type=$type&action=edit&id=".$a['user_id']."'>edit</a> | \n";
				print "<a href='users.php?$sid&name=$name&type=$type&action=resetpw&id=".$a['user_id']."'>reset pwd</a>\n";
				print "</nobr></td>";
				print "</tr>";
			}
		}
		?>
	</table>	
</td></tr>
</table>

<BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>
<?
function doUserForm($a,$p='',$e=0) {
	?>
	<form method='post' name='addform'>
	<tr>
	<td><?=($e)?$a[$p.'id']:"&nbsp"?></td>
	<td><input type=text name='uname' size=10 value="<?=$a[$p.'uname']?>"></td>
	<td><input type=text name='fname' size=20 value="<?=$a[$p.'fname']?>"></td>
	<td><input type=text name='email' size=30 value="<?=$a[$p.'email']?>"></td>
	<td><select name=type>
		<option<?=($a[$p.'type']=='stud')?" selected":""?>>stud
		<option<?=($a[$p.'type']=='prof')?" selected":""?>>prof
		<option<?=($a[$p.'type']=='staff')?" selected":""?>>staff
		<option<?=($a[$p.'type']=='admin')?" selected":""?>>admin
	</select>
	</td>
	<td align=center>
	<input type=hidden name='action' value='<?=($e)?"edit":"add"?>'>
	<?=($e)?"<input type=hidden name='id' value='".$a[$p."id"]."'><input type=hidden name=commit value=1>":""?>
	<a href='#' onClick='document.addform.submit()'><?=($e)?"update":"add user"?></a> | <a href='users.php'>cancel</a>
	</td>
	</tr>
	</form>
	<?
}
?>