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

/* if ($_SESSION['ltype'] != 'admin') { */
/* 	// take them right to the user lookup page */
/* 	header("Location: username_lookup.php"); */
/* 	exit; */
/* } */

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

// what's the action?
if ($_REQUEST['action']) $curraction = $_REQUEST['action'];
if ($_REQUEST['scope']) $scope = $_REQUEST['scope'];

$sql = $_REQUEST['sql'];
$query_custom = $_REQUEST['newquery'];

if ($_REQUEST['order']) $order = urldecode($_REQUEST['order']);
if (!isset($order)) $order = "user_fname ASC";
$orderby = " ORDER BY $order";

if ($_REQUEST['userfname']) $userfname = urldecode($_REQUEST['userfname']);
if ($_REQUEST['userid']) $userid = $_REQUEST['userid'];

$storyid = $_REQUEST['storyid'];
$siteid = $_REQUEST['siteid'];

/******************************************************************************
 * get search variables and create query
 ******************************************************************************/
		
if ($scope == "site") {
	$where = "site_id = $siteid";
} else {
	$where = "story_id = $storyid";	
}

if ($userid) {
	$where .= " AND user_id = $userid";
}


if ($action == "review") {
	$select = "user_id, user_fname, user_email, discussion_rate, discussion_tstamp, discussion_id, discussion_subject, story_id, page_id, page_title, story_text_short, section_id, site_id";
	if (!isset($order)) $order = "discussion_tstamp ASC";
} else {
	$select = "DISTINCT user_id, user_fname, user_email";
	$order = "user_fname ASC";	
}


/******************************************************************************
 * query database 
 ******************************************************************************/
 
 if (!$query_custom || $query_custom) {	
	$query = "
	SELECT 
		user_id
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
	$numrows = db_num_rows($r);
	

	if (!isset($lowerlimit)) $lowerlimit = 0;
	if (!isset($range)) $range = 30;
	if ($lowerlimit < 0) $lowerlimit = 0;
	$limit = " LIMIT $lowerlimit,$range";
	
	$query = "
	SELECT 
		DISTINCT user_id
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
	//print $numparticipants."<br>";
	
	if ($action == "list") $numrows = $numparticipants;
	//print $numrows."<br>";

	$query = "
	SELECT 
		$select
	FROM 
		discussion
	INNER JOIN story ON FK_story = story_id
	INNER JOIN page ON FK_page = page_id
	INNER JOIN section ON FK_section = section_id
	INNER JOIN site ON FK_site = site_id
	INNER JOIN user ON FK_author = user_id
	WHERE 
		$where $orderby $limit
	";		
	$r = db_query($query);
	
} else {
	//$query = $query_custom;
	//$r = db_query($query);
	//$a = db_fetch_assoc($r);
	//$numparticipants = db_num_rows($r);
}

printerr();

?>

<html>
<head>
<title>Participants</title>
<? include("themes/common/logs_css.inc.php"); ?>

<script lang="JavaScript">

function changeOrder(order) {
	f = document.searchform;
	f.order.value=order;
	f.submit();
}

</script>

</head>
<!-- <body onLoad="document.addform.uname.focus()">  -->
<body onLoad="initEditor()">
<div align=right>
<!-- <a href=viewlogs.php?$sid&site=<? echo $site ?>>Logs</a> -->
<!-- | <a href=viewsites.php?$sid&site=<? echo $site ?>>Sites</a> -->
Participants<br><br>


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
		<input type=hidden name='order' value='<? echo urlencode($order) ?>'>
		<input type=hidden name='action' value='<? echo $action ?>'>
		<input type=hidden name='storyid' value='<? echo $storyid ?>'>
		<input type=hidden name='siteid' value='<? echo $siteid ?>'>
		<input type=hidden name='site' value='<? echo $site ?>'>
		<input type=hidden name='userid' value='<? echo $userid ?>'>
		<input type=hidden name='userfname' value='<? echo urlencode($userfname) ?>'>

<!-- 		<input type=submit name='search' value='Find'> -->
<!-- 		<input type=submit name='findall' value='Find All'> -->
		</td>
		<td align=right>
		<?
		//$order = urlencode($order);
		$getvariables = "storyid=$storyid&siteid=$siteid&site=$site&scope=$scope";
		if ($userid) {
			$userfname = urlencode($userfname);
			$getusers = "&userid=$userid&userfname=$userfname";
		}


		$tpages = ceil($numrows/$range);
		$curr = ceil(($lowerlimit+$range)/$range);
		$prev = $lowerlimit-$range;
		if ($prev < 0) $prev = 0;
		$next = $lowerlimit+$range;
		if ($next >= $numrows) $next = $numrows-$range;
		if ($next < 0) $next = 0;
		print "$curr of $tpages ";
		//print "(prev=$prev lowerlimit=$lowerlimit next=$next )";
		if ($prev != $lowerlimit)
			print "<input type=button value='&lt;&lt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$prev&$getvariables&action=$curraction\"'>\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type=button value='&gt;&gt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$next&$getvariables&action=$curraction\"'>\n";
		?>

<!-- 		</form> -->
	
	</td></tr>
	</table>
	
	<? if (!db_num_rows($r)) {
		print "No participants found. Try extending the scope to all participants in the site";
		
/******************************************************************************
 * depending on action print out either:
 * list of participants
 * email UI
 * sent email confirmation
 ******************************************************************************/

	} //else {
		//$numusers = db_num_rows($r);
		//print "Total participants found: ".$numparticipants;
		
		/******************************************************************************
		 * Navigation List | Email participants in discussion or site
		 ******************************************************************************/
		//print "<form action=$PHP_SELF method=post name=emailform>";
		print "<table><tr><td><div style='font-size: 12px'>";

		if ($curraction == "list") {
			print "<a href=$PHP_SELF?$sid&action=email&$getvariables$getusers>Email</a> | ";
			print "List | ";
			print "<a href=$PHP_SELF?$sid&action=review&$getvariables$getusers&order=$order>Review</a> - ";
			print $numparticipants." participants";
			
		} else if ($curraction == 'review') {
			print "<a href=$PHP_SELF?$sid&action=email&$getvariables$getusers&order=user_fname>Email</a> | ";
			print "<a href=$PHP_SELF?$sid&action=list&$getvariables&order=user_fname>List</a> | ";
			
			if ($userid) {
				print "<a href=$PHP_SELF?$sid&action=review&$getvariables>Review all</a> | ";
				print $numrows." posts from ".urldecode($userfname);
			} else {
				print "Review - ";
				print $numrows." posts from ".$numparticipants." participants";
			}
			
		} else if ($curraction == 'email') {
			print "Email | ";
			print "<a href=$PHP_SELF?$sid&action=list&$getvariables&order=user_fname>List</a> | ";
			print "<a href=$PHP_SELF?$sid&action=review&$getvariables$getusers>Review</a> - ";
			print $numparticipants." participants";
			
		} else if ($curraction == 'send') {
			print "<a href=$PHP_SELF?$sid&action=email&$getvariables$getusers&order=user_fname>Email</a> | ";
			print "<a href=$PHP_SELF?$sid&action=list&$getvariables&order=user_fname>List</a> | ";
			print "<a href=$PHP_SELF?$sid&action=review&$getvariables$getusers&order=$order>Review</a> - ";
			print $numparticipants." participants";
		}
				
		?>
		in this
		<select name=scope>
		<option<?=($scope=='discussion')?" value='discussion' selected":""?>>discussion/assessment
		<option<?=($scope=='site')?" value='site' selected":""?>>site
		</select>
		<input type=submit name='update' value='Update'>
		
		<? 
		if ($_SESSION['ltype']=='admin') {
/* 			if ($sql) { */
/* 				print "<input type='checkbox' name='sql' checked>"; */
/* 			} else { */
/* 				print "<input type='checkbox' name='sql'>"; */
/* 			} */
			//print "(show sql)<br>";
			//if ($sql) {
			//	print "<textarea name=newquery cols=80 rows=10>";
			//	print $query;
			//	print "</textarea>";
			//}
		}
		?>
		</form>
		</div></td></tr></table>	
		
		<?
		/******************************************************************************
		 * if action is email, then compile to list and print out email UI
		 ******************************************************************************/
		
		if ($curraction == 'email') {
			
			$emaillist = array();
			while ($a2 = db_fetch_assoc($r)) {
				array_push($emaillist, $a2['user_email']);	
			}
			$to = "";
			foreach ($emaillist as $address) {
				$to .= $address.", ";
			}
			$to = rtrim($to, ",");
			
			//compile from and cc into headers
			$from = $_SESSION['afname']."<".$_SESSION['aemail'].">";
			$headers = "From: ".$from."\n";
			$headers .= "Cc: ".$from."\n";
			
			//add content type to header
			$html = 1;
			if ($html == 1) {
				$headers .= "Content-Type: text/html\n";
			} 
		
			//$text = "email text here";
			//$textarea = "email";
			?>
			
			<form action="<? echo $PHP_SELF ?>" method=post name=emailform>
			<table width=100%>
			<tr><td align=right>To:</td><td><? echo $to ?></td><td align=right></td></tr>
			<tr><td align=right>From:</td><td><? echo $_SESSION['afname'] ?></td><td align=right></td></tr>
			<tr><td align=right>Cc:</td><td><? echo $_SESSION['afname'] ?></td><td align=right></td></tr>
			<tr><td align=right>Subject</td><td><input type=text name='subject' value='' size=50> <input type=submit name='email' value='Send'></td><td align=left></td></tr>
			<tr><td></td><td align=left>
			<?
			include("htmleditor/editor.inc.php");
			include("sniffer.inc.php");
			addeditor ("body",60,20,$text,"discuss"); 
			print $content;
			?>
<!-- 			<textarea name=body cols=60 rows=20></textarea> -->
			</td><td><td align=right></td></tr>
			</table>
			<input type=hidden name='action' value='send'>
			<input type=hidden name='scope' value='<? echo $scope ?>'>
			<input type=hidden name='storyid' value='<? echo $storyid ?>'>
			<input type=hidden name='siteid' value='<? echo $siteid ?>'>
			<input type=hidden name='site' value='<? echo $site ?>'>
			<input type=hidden name='to' value='<? echo $to ?>'>
<!-- 			<input type=hidden name='from' value='<? echo $from ?>'> -->
			<input type=hidden name='headers' value='<? echo $headers ?>'>
			</form>
			<?
			$r = db_query($query);
			exit();
			
		/******************************************************************************
		 * if action is send then mail subject and body
		 ******************************************************************************/

		} else if ($curraction == 'send') {
			print "<table>";
			print "<tr><td>To:</td><td>".$to."</td></tr><br><hr>\n";
			print "<tr><td>From:</td><td>".$_SESSION['afname']."</td></tr>\n";
			print "<tr><td>Cc:</td><td>".$_SESSION['afname']."</td></tr>\n";
			print "<tr><td>Subject:</td><td>".$subject."</td></tr>\n";
			print "<tr><td></td><td>".$body."</td></tr>\n";
			//print "<tr><td></td><td>".$headers."</td></tr>\n";  //debug
			print "</table>\n";
			print "</div>\n";
			//mail($to,$subject,$body,"From: $from");
			mail($to, $subject, $body, $headers);
			$r = db_query($query);
			exit();
		}
	// } 
	
	/******************************************************************************
	 * Print out table of participant names
	 ******************************************************************************/
	
	?>
	<table width='100%'>
		<tr>
		<!-- <th>full name</th> -->
		
		<?
		print "<th><a href=# onClick=\"changeOrder('";
		if ($order =='user_fname desc') print "user_fname asc";
		else print "user_fname desc";
		print "')\">Participant Name";
		if ($order =='user_fname asc') print " &or;";
		if ($order =='user_fname desc') print " &and;";	
		print "</a></th>";


		if ($curraction == 'review') {
			print "<th>Page > Topic</th>";
			print "<th>discussion_subject</th>";
/* 			print "<th>discussion_rate</th>"; */
/* 			print "<th>discussion_tstamp</th>"; */
			 			
			print "<th><a href=# onClick=\"changeOrder('";
			if ($order =='discussion_rate asc') print "discussion_rate desc";
			else print "discussion_rate asc";
			print "')\">Rating/Grade";
			if ($order =='discussion_rate asc') print " &or;";
			if ($order =='discussion_rate desc') print " &and;";	
			print "</a></th>";
			
			print "<th><a href=# onClick=\"changeOrder('";
			if ($order =='discussion_tstamp asc') print "discussion_tstamp desc";
			else print "discussion_tstamp asc";
			print "')\">Date Time";
			if ($order =='discussion_tstamp asc') print " &or;";
			if ($order =='discussion_tstamp desc') print " &and;";	
			print "</a></th>";
			
		} else {
			print "<th>Email</th>";
			print "<th># of Posts</th>";
			print "<th>Avg. Rating/Grade</th>";
		}
		?>
		</tr>		
		<? 
		/******************************************************************************
		 * Print out stats and links
		 ******************************************************************************/
			while ($a = db_fetch_assoc($r)) {
			
				/******************************************************************************
				 * If listing participants, get # of posts and avg. rating
				 ******************************************************************************/

				if ($curraction == 'list') {
					$userid = $a[user_id];
					$query2 = "
					SELECT 
						user_id, user_email, discussion_rate, discussion_tstamp
					FROM 
						discussion
					INNER JOIN story ON FK_story = story_id
					INNER JOIN page ON FK_page = page_id
					INNER JOIN section ON FK_section = section_id
					INNER JOIN site ON FK_site = site_id
					INNER JOIN user ON FK_author = user_id
					WHERE 
						$where AND user_id = $userid $orderby $limit
					";		
					$r2 = db_query($query2);
					//$a2 = db_fetch_assoc($r2);
					$postcount = db_num_rows($r2);
					$rating_sum = 0;
					if ($postcount == 1) {
						$avg_rating = $a2['discussion_rate'];
					} else {
						$adjpostcount = $postcount;
						while ($a2 = db_fetch_assoc($r2)) {
							if ($a2['discussion_rate'] == 0) $adjpostcount = $adjpostcount - 1;
							$rating_sum = $rating_sum + $a2['discussion_rate'];
						}
						$avg_rating = round($rating_sum/$adjpostcount, 1);
					}
				}
								
				$discussion_date = $a['discussion_tstamp'];
				$discussion_date = timestamp2usdate($discussion_date);
				$page_link = $_full_uri."/index.php?action=site&site=$site&section=".$a['section_id']."&page=".$a['page_id'];
				$fullstory_link = $_full_uri."/index.php?action=site&site=$site&section=".$a['section_id']."&page=".$a['page_id']."&story=".$a['story_id']."&detail=".$a['story_id'];
				$dicuss_link = $_full_uri."/index.php?action=site&site=$site&section=".$a['section_id']."&page=".$a['page_id']."&story=".$a['story_id']."&detail=".$a['story_id']."#".$a['discussion_id'];
				$shory_text_all = urldecode($a['story_text_short']);
				$shory_text = substr($shory_text_all,0,25)."...";
				print "<tr>";
				
				if ($curraction == 'review') {
					print "<td><a href=$PHP_SELF?$sid&action=review&userid=".$a['user_id']."&userfname=".urlencode($a['user_fname'])."&".$getvariables.">".$a['user_fname']."</a></td>";
					print "<td><a href='#' onClick='opener.window.location=\"$page_link\"'>".$a['page_title']."</a> > <a href='#' onClick='opener.window.location=\"$fullstory_link\"'>".$shory_text."</a></td>";
					print "<td><a href='#' onClick='opener.window.location=\"$dicuss_link\"'>".urldecode($a['discussion_subject'])."</a></td>";
					print "<td>".$a['discussion_rate']."</td>";
					print "<td><a href='#' onClick='opener.window.location=\"$dicuss_link\"'>".$discussion_date."</a></td>";
				} else {
					print "<td><a href=$PHP_SELF?$sid&action=review&userid=".$a['user_id']."&userfname=".urlencode($a['user_fname'])."&".$getvariables.">".$a['user_fname']."</a></td>";
					print "<td>".$a['user_email']."</td>";
					print "<td>".$postcount."</td>";
					print "<td>".$avg_rating."</td>";
				}
								
				print "</tr>";
			}
		?>
	</table>	
</td></tr>
</table>

<BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>
<?

?>