<? /* $Id$ */
include("objects/objects.inc.php");
$content = '';
$message = '';

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

/* // debug output -- handy :) */
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "\n\n"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */
/* print "</pre>"; */

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
$id = $_REQUEST['id'];

if ($curraction == 'del') {
	$id = $_REQUEST['id'];
	if ($id > 0) {
		course::delCourse($id);
		$message = "Class ID $id deleted successfully."; 
	}
}

// if they want to add a class...
if ($curraction == 'add') {
	// check for errors first
	if (course::courseExists(generateCodeFromData($_REQUEST['department'],$_REQUEST['number'],$_REQUEST['section'],$_REQUEST['semester'],$_REQUEST['year']))) error("A class with that code already exists.");
	if (!$_REQUEST['department']) error("You must enter a department.");
	if (!$_REQUEST['number']) error("You must enter a number.");
	if (!$_REQUEST['semester']) error("You must enter a semester.");
	if (!ereg("^[0-9]{4}$",$_REQUEST['year'])) error("You must enter a valid 4-digit year.");
	// all good
	if (!$error) {
		$owner_id = db_get_value("user","user_id","user_uname='".$_REQUEST['owner']."'");
		
		$query = "
			INSERT INTO
				ugroup
			SET
				ugroup_name = '".generateCodeFromData($_REQUEST['department'],$_REQUEST['number'],$_REQUEST['section'],$_REQUEST['semester'],$_REQUEST['year'])."',
				ugroup_type = 'class'
		";
		db_query($query);
		$ugroup_id = lastid();
		
		if ($owner_id) {
			$query = "
				INSERT INTO
					ugroup_user
				SET
					FK_ugroup = $ugroup_id,
					FK_user = $owner_id
			";
			db_query($query);
		}
		
		$obj = &new course();
		$obj->external_id = $_REQUEST['external_id'];
		$obj->department = $_REQUEST['department'];
		$obj->number = $_REQUEST['number'];
		$obj->section = $_REQUEST['section'];
		$obj->semester = $_REQUEST['semester'];
		$obj->year = $_REQUEST['year'];
		$obj->name = $_REQUEST['name'];
		$obj->owner = $owner_id;
		$obj->ugroup = $ugroup_id;
//		$obj->classgroup = $_REQUEST['classgroup'];
		$obj->insertDB();
		
		$message = "Class '".generateCodeFromData($_REQUEST['department'],$_REQUEST['number'],$_REQUEST['section'],$_REQUEST['semester'],$_REQUEST['year'])."' added successfully.";
		unset($_REQUEST['external_id'],$_REQUEST['name'],$_REQUEST['department'],$_REQUEST['number'],$_REQUEST['section'],$_REQUEST['semester'],$_REQUEST['year'],$_REQUEST['owner'],$_REQUEST['ugroup']);
	}
}

// if they're editing a course
if ($curraction == 'edit') {
	if ($_REQUEST['commit']==1) {
		if (!$_REQUEST['department']) error("You must enter a department.");
		if (!$_REQUEST['number']) error("You must enter a number.");
		if (!$_REQUEST['semester']) error("You must enter a semester.");
		if (!ereg("^[0-9]{4}$",$_REQUEST['year'])) error("You must enter a valid 4-digit year.");
		if (!$error) {
			$owner_id = db_get_value("user","user_id","user_uname='".$_REQUEST['owner']."'");
			
			$obj = &new course();
			$obj->fetchCourseID($_REQUEST['id']);
			$obj->external_id = $_REQUEST['external_id'];
			$obj->department = $_REQUEST['department'];
			$obj->number = $_REQUEST['number'];
			$obj->section = $_REQUEST['section'];
			$obj->semester = $_REQUEST['semester'];
			$obj->year = $_REQUEST['year'];
			$obj->name = $_REQUEST['name'];
			$obj->owner = $owner_id;
	//		$obj->ugroup = $ugroup_id;
	//		$obj->classgroup = $_REQUEST['classgroup'];
			$obj->updateDB();
			
			$query = "
				UPDATE
					ugroup
				SET
					ugroup_name='".generateCodeFromData($_REQUEST['department'],$_REQUEST['number'],$_REQUEST['section'],$_REQUEST['semester'],$_REQUEST['year'])."'
				WHERE
					ugroup_id='".$obj->ugroup."'
			";
			db_query($query);
			
			if ($owner_id && !db_get_line("ugroup_user","FK_user=$owner_id AND FK_ugroup = ".$obj->ugroup)) {
				$query = "
					INSERT INTO
						ugroup_user
					SET
						FK_ugroup = ".$obj->ugroup.",
						FK_user = $owner_id
				";
				db_query($query);
			}
			
			$message = "Class '".generateCodeFromData($_REQUEST['department'],$_REQUEST['number'],$_REQUEST['section'],$_REQUEST['semester'],$_REQUEST['year'])."' updated successfully.";
			unset($_REQUEST['external_id'],$_REQUEST['name'],$_REQUEST['department'],$_REQUEST['number'],$_REQUEST['section'],$_REQUEST['semester'],$_REQUEST['year'],$_REQUEST['owner'],$_REQUEST['ugroup']);
		}
	}
}
		
/* if ($curraction == 'resetpw') { */
/* 	$id = $_REQUEST['id']; */
/* 	if ($id > 0) { */
/* 		$obj = &new user(); */
/* 		$obj->fetchUserID($id); */
/* 		$obj->randpass(5,3); */
/* 		$obj->updateDB(); */
/* 		$obj->sendemail(1); */
/* 		$message = "A random password has been generated for '".$obj->uname."' and an email has been sent to them."; */
/* 	} */
/* } */
		
		
$query = "
	SELECT
		class_id,
		class_external_id,
		class_name,
		class_department,
		class_number,
		class_section,
		class_semester,
		class_year,
		classowner.user_id AS classowner_id,
		classowner.user_uname AS classowner_uname,
		classowner.user_fname AS classowner_fname,
		classgroup_id,
		classgroup_name,
		ugroup_id
	FROM
		class
			LEFT JOIN
		user AS classowner
			ON
		class.FK_owner = user_id
			LEFT JOIN
		classgroup
			ON
		FK_classgroup = classgroup_id
			LEFT JOIN
		ugroup
			ON
		FK_ugroup = ugroup_id
	ORDER BY
		class_year DESC, class_department ASC, class_number ASC, class_section ASC";

$r = db_query($query);

printerr();

?>

<html>
<head>
<title>Classes</title>
<? 
include("themes/common/logs_css.inc.php"); 
include("themes/common/header.inc.php");
?>
</head>

<?=($_SESSION['ltype']=='admin')?"<div align=right><a href='username_lookup.php?$sid'>user lookup</a> | <a href='users.php?$sid'>add/edit users</a> | add/edit classes</div>":""?>

<?=$content?>

<table cellspacing=1 width='100%' id='maintable'>
<tr>
	<td>
		<table width='100%'>
			<tr>
			<th>id</th>
			<th>code</th>
			<th>external id</th>
			<th>name</th>
			<th>department</th>
			<th>number</th>
			<th>section</th>
			<th>semester</th>
			<th>year</th>
			<th>owner</th>
			<th>group</th>
			<th>options</th>
			</tr>
			
			<? // now output all the users
			while ($a = db_fetch_assoc($r)) {
				if ($curraction == 'edit' && $_REQUEST['id'] == $a['class_id'])
					doUserForm($a,'class_',1);
				else {
					print "<tr>";
					print "<td align=center>".$a['class_id']."</td>";
					print "<td>".generateCourseCode($a['class_id'])."</td>";
					print "<td>".$a['class_external_id']."</td>";
					print "<td>".$a['class_name']."</td>";
					print "<td>".$a['class_department']."</td>";
					print "<td>".$a['class_number']."</td>";
					print "<td>".$a['class_section']."</td>";
					print "<td>".$_semesters[$a['class_semester']]."</td>";
					print "<td>".$a['class_year']."</td>";
					print "<td>".(($a['classowner_id'])?$a['classowner_fname']." (".$a['classowner_uname'].")":"")."</td>";
					print "<td>".$a['classgroup_name']."</td>";
					print "<td align=center><nobr>";
					print "<a href='classes.php?$sid&action=del&id=".$a['class_id']."'>del</a> | \n";
					print "<a href='classes.php?$sid&action=edit&id=".$a['class_id']."'>edit</a> | \n";
					print "<a href=\"Javascript:sendWindow('addstudents',400,250,'add_students.php?$sid&ugroup_id=".$a['ugroup_id']."')\">students</a>\n";
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
	global $_semesters;
	?>
			<form method='post' name='addform'>
			<tr>
			<td><?=($e)?$a[$p.'id']:"&nbsp"?></td>
			<td><?=($e)?generateCourseCode($a['class_id']):""?></td>
			<td><input type=text name='external_id' size=10 value="<?=$a[$p.'external_id']?>"></td>
			<td><input type=text name='name' size=20 value="<?=$a[$p.'name']?>"></td>
			<td><input type=text name='department' size=3 value="<?=$a[$p.'department']?>"></td>
			<td><input type=text name='number' size=3 value="<?=$a[$p.'number']?>"></td>
			<td><input type=text name='section' size=1 value="<?=$a[$p.'section']?>"></td>
			<td><select name=semester>
				<option<?=($a[$p.'semester']=='f')?" selected":""?> value='f'><?=$_semesters[f]?>
				<option<?=($a[$p.'semester']=='w')?" selected":""?> value='w'><?=$_semesters[w]?>
				<option<?=($a[$p.'semester']=='s')?" selected":""?> value='s'><?=$_semesters[s]?>
				<option<?=($a[$p.'semester']=='l')?" selected":""?> value='l'><?=$_semesters[l]?>
			</select>
			</td>
			<td><input type=text name='year' size=4 value="<?=$a[$p.'year']?>"></td>
			<td><input type=text name='owner' size=8 value="<?=$a['classowner_uname']?>" readonly> <a href="Javascript:sendWindow('addeditor',400,250,'add_editor.php?$sid&comingfrom=classes')">choose</a></td>
			<td><?=$a[classgroup_name]?></td>
			<td align=center>
			<input type=hidden name='action' value='<?=($e)?"edit":"add"?>'>
			<?=($e)?"<input type=hidden name='id' value='".$a[$p."id"]."'><input type=hidden name=commit value=1>":""?>
			<a href='#' onClick='document.addform.submit()'><?=($e)?"update":"add class"?></a> | <a href='classes.php'>cancel</a>
			</td>
			</tr>
			</form>
	<?
}

/* // debug output -- handy :) */
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "\n\n"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */
/* print "</pre>"; */
?>