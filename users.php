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
	if ($id > 0) { user::delUser($id); $message = "User ID $id deleted successfully."; }
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
		$obj->uname = $_REQUEST['uname'];
		$obj->fname = $_REQUEST['fname'];
		$obj->email = $_REQUEST['email'];
		$obj->type = $_REQUEST['type'];
		$obj->randpass(5,3);
		$obj->insertDB();
		$obj->sendemail();
		unset($_REQUEST['uname'],$_REQUEST['fname'],$_REQUEST['email'],$_REQUEST['type']);
		$message = "User '".$obj->uname."' added successfully.";
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
			$obj->uname = $_REQUEST['uname'];
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
		
		
$query = "
	SELECT
		user_id,user_uname,user_fname,user_email,user_type
	FROM
		user
	WHERE
		user_authtype='db'
	ORDER BY
		user_uname ASC";

$r = db_query($query);

printerr();

?>

<html>
<head>
<title>Users</title>
<? include("themes/common/logs_css.inc.php"); ?>
</head>
<body onLoad="document.addform.uname.focus()">
<?=($_SESSION['ltype']=='admin')?"<div align=right><a href='username_lookup.php?$sid'>user lookup</a> | add/edit users | <a href='classes.php?$sid'>add/edit classes</a></div>":""?>

<?=$content?>

<table cellspacing=1 width='100%' id='maintable'>
<tr>
	<td>
		<table width='100%'>
			<tr>
			<th>id</th>
			<th>user</th>
			<th>full name</th>
			<th>email</th>
			<th>type</th>
			<th>options</th>
			</tr>
			
			<? // now output all the users
			while ($a = db_fetch_assoc($r)) {
				if ($curraction == 'edit' && $_REQUEST['id'] == $a['user_id'])
					doUserForm($a,'user_',1);
				else {
					print "<tr>";
					print "<td align=center>".$a['user_id']."</td>";
					print "<td>".$a['user_uname']."</td>";
					print "<td>".$a['user_fname']."</td>";
					print "<td>".$a['user_email']."</td>";
					print "<td>".$a['user_type']."</td>";
					print "<td align=center><nobr>";
					print "<a href='users.php?$sid&action=del&id=".$a['user_id']."'>del</a> | \n";
					print "<a href='users.php?$sid&action=edit&id=".$a['user_id']."'>edit</a> | \n";
					print "<a href='users.php?$sid&action=resetpw&id=".$a['user_id']."'>reset pwd</a>\n";
					print "</nobr></td>";
					print "</tr>";
				}
			}
			?>
			
			<? if ($curraction != 'edit') { doUserForm($_REQUEST); } ?>
			
		</table>
	</td>
</tr>
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