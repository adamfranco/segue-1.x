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
$scope = $_REQUEST['scope'];


/******************************************************************************
 * get search variables and create query
 ******************************************************************************/
		
$storyid = $_REQUEST['story'];
$siteid = $_REQUEST['site'];
if ($scope == "site") {
	$where = "site_id = $siteid";
} else {
	$where = "story_id = $storyid";	
}


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
 * depending on action print out either:
 * list of participants
 * email UI
 * sent email confirmation
 ******************************************************************************/

	} else {
		//$numusers = db_num_rows($r);
		//print "Total participants found: ".$numparticipants;
		
		/******************************************************************************
		 * Navigation List | Email participants in discussion or site
		 ******************************************************************************/
		print "<form action=$PHP_SELF method=post name=emailform>";
		print "<table><tr><td><div style='font-size: 12px'>";
		if ($curraction == "list") {
			print "<a href=$PHP_SELF?$sid&story=$storyid&site=$siteid&action=email&scope=$scope>Email</a> | List - ".$numparticipants." participants";
		} else if ($curraction == 'email') {
			print "<a href=$PHP_SELF?$sid&story=$storyid&site=$siteid&action=list&scope=$scope>List</a> | Email - ".$numparticipants." participants";
		} else if ($curraction == 'send') {
			print "<a href=$PHP_SELF?$sid&story=$storyid&site=$siteid&action=email>Email</a> | ";
			print "<a href=$PHP_SELF?$sid&story=$storyid&site=$siteid&action=list>List</a> - ";
			print $numparticipants." participants";
		}
				
		?>
		in this
		<select name=scope>
		<option<?=($scope=='discussion')?" selected":""?>>discussion
		<option<?=($scope=='site')?" selected":""?>>site
		</select>
		<input type=hidden name='action' value='<? echo $action ?>'>
		<input type=hidden name='story' value='<? echo $storyid ?>'>
		<input type=hidden name='site' value='<? echo $siteid ?>'>
		<input type=submit name='update' value='Update'>
		</form>
		</div></td></tr></table>	
		
		<?
		/******************************************************************************
		 * if action is email, then compile to list and print out email UI
		 ******************************************************************************/

		if ($curraction == 'email') {
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
			<table width=100%>
			<tr><td align=right>To:</td><td><? echo $to ?></td><td align=right></td></tr>
			<tr><td align=right>From:</td><td><? echo $_SESSION['afname'] ?></td><td align=right></td></tr>
			<tr><td align=right>Subject</td><td><input type=text name='subject' value='' size=50> <input type=submit name='email' value='Send'></td><td align=left></td></tr>
			<tr><td></td><td align=left><textarea name=body cols=60 rows=20 align=left>
			<? echo //$text ?>
			</textarea>
			</td><td><td align=right></td></tr>
			</table>
			<input type=hidden name='action' value='send'>
			<input type=hidden name='scope' value='<? echo $scope ?>'>
			<input type=hidden name='story' value='<? echo $storyid ?>'>
			<input type=hidden name='site' value='<? echo $siteid ?>'>
			<input type=hidden name='to' value='<? echo $to ?>'>
			<input type=hidden name='from' value='<? echo $from ?>'>
			</form>
			<?
			$r = db_query($query);
			exit();
			
		/******************************************************************************
		 * if action is send then mail subject and body
		 ******************************************************************************/

		} else if ($curraction == 'send') {
			print "<table>";
			print "<tr><td>to:</td><td>".$to."</td></tr><br><hr>";
			print "<tr><td>from:</td><td>".$_SESSION['afname']."</td></tr>";
			print "<tr><td>subject:</td><td>".$subject."</td></tr>";
			print "<tr><td></td><td>".$body."</td></tr>";
			print "</table>";
			print "</div>";
			mail($to,$subject,$body,"From: $from");
			$r = db_query($query);
			exit();
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