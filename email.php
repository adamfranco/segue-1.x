<? 
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


/******************************************************************************
 * get search variables and create query
 ******************************************************************************/
		
$storyid = $_REQUEST['story'];

$where = "story_id = $storyid";	


/******************************************************************************
 * query database 
 ******************************************************************************/
 
 if ($storyid) {	
	$query = "
	SELECT 
		distinct user_fname, user_email
	FROM 
		discussion
	INNER JOIN story ON FK_story = story_id
	INNER JOIN page ON FK_page = page_id
	INNER JOIN section ON FK_section = section_id
	INNER JOIN site ON FK_site = site_id
	INNER JOIN user ON FK_author = user_id
	WHERE 
		$where
	";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$numparticipants = db_num_rows($r);
		
	if (!isset($lowerlimit)) $lowerlimit = 0;
	if (!isset($range)) $range = 30;
	if ($lowerlimit < 0) $lowerlimit = 0;
	$limit = " LIMIT $lowerlimit,$range";

	$query = "
	SELECT 
		distinct user_fname, user_email
	FROM 
		discussion
	INNER JOIN story ON FK_story = story_id
	INNER JOIN page ON FK_page = page_id
	INNER JOIN section ON FK_section = section_id
	INNER JOIN site ON FK_site = site_id
	INNER JOIN user ON FK_author = user_id
	WHERE 
		$where $limit
	";		
	$r = db_query($query);
}

printerr();

?>

<html>
<head>
<title>Participants</title>
<? include("themes/common/logs_css.inc.php"); ?>
</head>
<!-- <body onLoad="document.addform.uname.focus()">  -->
<body onLoad="document.searchform.name.focus()">
<div align=right>
<a href=viewlogs.php?$sid&site=<? echo $site ?>>Logs</a>
| <a href=viewsites.php?$sid&site=<? echo $site ?>>Sites</a>
| Participants<br><br>


<?
/* =($_SESSION['ltype']=='admin')? */
/* 	"<a href='username_lookup.php?$sid'>user lookup</a> |  */
/* 		add/edit users |  */
/* 		<a href='classes.php?$sid'>add/edit classes</a> |  */
/* 		<a href='add_slot.php?$sid'>add/edit slots</a> | */
/* 		<a href='update.php?$sid'>segue updates</a> */
/* 	" */
/* :"" */
?>
</div>
<?=$content?>

<table cellspacing=1 width='100%' id='maintable'>
<tr><td>

	<table cellspacing=1 width='100%'>
	<tr><td>
		<form action="<? echo $PHP_SELF ?>" method=get name=searchform>
<!-- 		<input type=submit name='search' value='Find'> -->
<!-- 		<input type=submit name='findall' value='Find All'> -->
		</td>
		<td align=right>
		<?
		$tpages = ceil($numparticipants/$range);
		$curr = ceil(($lowerlimit+$range)/$range);
		$prev = $lowerlimit-$range;
		if ($prev < 0) $prev = 0;
		$next = $lowerlimit+$range;
		if ($next >= $numparticipants) $next = $numparticipants-$range;
		if ($next < 0) $next = 0;
		print "$curr of $tpages ";
	//	print "(prev=$prev lowerlimit=$lowerlimit next=$next )";
		if ($prev != $lowerlimit)
			print "<input type=button value='&lt;&lt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$prev&type=$type&authtype=$authtype&name=$name&order=$order\"'>\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type=button value='&gt;&gt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$next&type=$type&authtype=$authtype&name=$name&order=$order\"'>\n";
		?>

		</form>
	
	</td></tr>
	</table>
	
	<? if (!db_num_rows($r)) {
		print "No matching names found";
		
/******************************************************************************
 * print out 
 ******************************************************************************/

	} else {
		//$numusers = db_num_rows($r);
		//print "Total participants found: ".$numparticipants;
		if ($curraction != 'email') {
			print "<a href=$PHP_SELF?$sid&story=$storyid&action=email>Email</a> | ".$numparticipants." participants<br>";
		} else if ($curraction == 'email') {
			print "<a href=$PHP_SELF?$sid&story=$storyid&action=>List</a> | ".$numparticipants." participants<br><hr>";
			include("htmleditor/editor.inc.php");
			$emaillist = array();
			while ($a2 = db_fetch_assoc($r)) {
				array_push($emaillist, $a2['user_email']);	
			}
			$to = "";
			foreach ($emaillist as $address) {
				$to .= $address.",";
			}
			$to = rtrim($to, ",");
			$html = 1;
			if ($html == 1) {
				$from = $_SESSION['afname']."<".$_SESSION['aemail'].">\nContent-Type: text/html\n";
			} else {
				$from = $_SESSION['afname']."<".$_SESSION['aemail'].">\n";
			}
		
			//$text = "email text here";
			$textarea = "body";
			?>
			
			<form action="<? echo $PHP_SELF ?>" method=post name=emailform>
			<? //addeditor ("body",60,20,$text); ?>
			<table>
			<tr><td align=right>To:</td><td><? echo $to ?></td><td align=right></td></tr>
			<tr><td align=right>From:</td><td><? echo $_SESSION['afname'] ?></td><td align=right></td></tr>
			<tr><td align=right>Subject</td><td><input type=text name='subject' value='' size=50></td><td align=right><input type=submit name='email' value='Send'></td></tr>
			<tr><td></td><td><textarea name=body cols=50 rows=10>
			<? echo $text ?>
			</textarea>
			</td><td><td align=right></td></tr>
			</table>
			<input type=hidden name='action' value='send'>
			<input type=hidden name='story' value='<? echo $storyid ?>'>
			<input type=hidden name='to' value='<? echo $to ?>'>
			<input type=hidden name='from' value='<? echo $from ?>'>
			</form>
			<?
			$r = db_query($query);
			exit();
		} else if ($curraction == 'send') {
			print "<table>";
			print "<tr><td>to:</td><td>".$to."</td></tr>";
			print "<tr><td>from:</td><td>".$_SESSION['afname']."</td></tr>";
			print "<tr><td>subject:</td><td>".$subject."</td></tr>";
			print "<tr><td></td><td>".$body."</td></tr>";
			print "</table>";
			mail($to,$subject,$body,"From: $from");
			$r = db_query($query);
		}

	} 
	
	?>
		
	<table width='100%'>
		<tr>
		<th>full name</th>
		<th>email</th>
		</tr>		
		<? 
			while ($a = db_fetch_assoc($r)) {
				print "<tr>";
				print "<td>".$a['user_fname']."</td>";
				print "<td>".$a['user_email']."</td>";
				print "</tr>";
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
	<td><?=($a[$p.'authtype'] == "db" || !$e)?"<input type=text name='uname' size=10 value=".$a[$p.'uname'].">":$a[$p.'uname']?></td>
	<td><?=($a[$p.'authtype'] == "db" || !$e)?"<input type=text name='fname' size=20 value=".$a[$p.'fname'].">":$a[$p.'fname']?></td>
	<td><?=($a[$p.'authtype'] == "db" || !$e)?"<input type=text name='email' size=30 value=".$a[$p.'email'].">":$a[$p.'email']?></td>
	<td><select name=type>
		<option<?=($a[$p.'type']=='stud')?" selected":""?>>stud
		<option<?=($a[$p.'type']=='prof')?" selected":""?>>prof
		<option<?=($a[$p.'type']=='staff')?" selected":""?>>staff
		<option<?=($a[$p.'type']=='admin')?" selected":""?>>admin
	</select>
	</td>
	<td><?=($e)?$a[$p.'authtype']:"db"?></td>
	<td align=center>
	<input type=hidden name='action' value='<?=($e)?"edit":"add"?>'>
	<?
	if ($e) {
		print "<input type=hidden name='id' value='".$a[$p."id"]."'><input type=hidden name=commit value=1>";
		if ($a[$p.'authtype'] != "db") {
			print "<input type=hidden name='uname' value=".$a[$p.'uname'].">";
			print "<input type=hidden name='fname' value=".$a[$p.'fname'].">";
			print "<input type=hidden name='email' value=".$a[$p.'email'].">";
		}
	} else {
		print "";
	}	
	?>
<!-- 	<a href='#' onClick='document.addform.submit()'><?=($e)?"update":"add user"?></a> -->
	<!-- | <a href='users.php'>cancel</a> -->
	</td>
	</tr>
	</form>
	<?
}

function email($emaillist=0) {
	global $sid,$error;
	global $_full_uri;
	
	//$script = $_SERVER['SCRIPT_NAME'];
/* 		$site =& new site($_REQUEST[site]); */
/* 		$siteowneremail = $site->owneremail; */
/* 		$siteownerfname = $site->ownerfname; */
/* 		$sitetitle = $site->title; */
/* 		 */
/* 		$pageObj =& new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[page], &$sectionObj); */
/* 		$pagetitle = $pageObj->getField('title');		 */
/* 		$storyObj =& new story($_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story], &$pageObj); */
/* 		$storytext = $storyObj->getField('shorttext'); */
	
	// send an email to the siteowner
	$html = 1;
	$emaillist = array();
	
	
	if ($emaillist!=0) {
		$to = $siteownerfname."<".$siteowneremail.">\n";
		//$to = $siteowneremail;
		if ($html == 1) {
			$from = $_SESSION['afname']."<".$_SESSION['aemail'].">\nContent-Type: text/html\n";
		} else {
			$from = $_SESSION['afname']."<".$_SESSION['aemail'].">\n";
		}
		$discussurl = "/index.php?$sid&action=site&site=".$_REQUEST['site']."&section=".$_REQUEST['section']."&page=".$_REQUEST['page']."&story=".$_REQUEST['story']."&detail=".$_REQUEST['detail']."#".$newid;

		if ($html == 1) {
			$body = $siteownerfname.", There has been a discussion posting from the following Segue site:<br>\n";			
			$body .= "<a href='".$_full_uri.$discussurl."'>".$sitetitle." > ".$pagetitle."</a><br><br>\n";			
			$body .= "<table cellpadding=0 cellspacing=0 border=0>";
			$body .= "<tr><td>subject: </td><td>".$_REQUEST['subject']."</td></tr>\n";
			$body .= "<tr><td>author: </td><td>".$_SESSION['afname']."</td></tr></table><br>\n";
			$body .= $_REQUEST['content']."<br><br>\n";
			$body .= "For complete discussion, see:<br>";
			$body .= "<a href='".$_full_uri.$discussurl."'>".$sitetitle." > ".$pagetitle."</a><br><br>\n";			
		} else {
			$body = "site: ".$sitetitle."\n";
			//$body .= "topic: ".$this->story."\n";	
			$body .= "subject: ".$_REQUEST['subject']."\n";		
			$body .= "author: ".$_SESSION['afname']."\n";
			$body .= $_REQUEST['content']."\n\n";
			$body .= "For complete discussion, see:\n";
			$discussurl2 = "/index.php?$sid&action=site&site=".$_REQUEST['site']."&section=".$_REQUEST['section']."&page=".$_REQUEST['page']."&story=".$_REQUEST['story']."&detail=".$_REQUEST['detail']."#".$newid;
			$body .= $_full_uri.$discussurl2."\n";
		}
	} else {
		$from = $siteowneremail;
	
	
	}
	
	// send it!
	mail($to,$subject,$body,"From: $from");
}

?>