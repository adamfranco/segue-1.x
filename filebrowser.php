<? // filebrowser.php

$content = '';

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// include all necessary files
include("includes.inc.php");

//if ($ltype != 'admin') exit;

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

if ($delete) {
/*	$query = "select * from media where id='$filetodelete'";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$a[name] = urldecode($a[name]);
	$file_path = $uploaddir."/".$a[site_id]."/".$a[name];
//	$file_path = "../sitesdb_userfiles/afranco/close2.gif";
//	print "file = \"$file_path\" <br>";
	if (file_exists($file_path)) {
//		$exists = file_exists($file_path);
//		print "fileexists = $exists $file_path<br> ";
		$success = unlink($file_path);
//		print "success = $success <br>";
		if ($success) {
			$query = "DELETE FROM media WHERE id='$filetodelete' LIMIT 1";
			db_query($query);
		} else {
			error("File could not be Deleted");
		}
	} else {
		error("File does not exist. Its Entry was deleted");
		$query = "DELETE FROM media WHERE id='$filetodelete' LIMIT 1";
		db_query($query);
	}

*/	deleteuserfile($filetodelete);
	printerr2();
}

if ($upload) {
	$query = "select * from media where site_id='".(($site)?"$site":"$settings[site]")."'";
//	print "$query <br>";
	$r = db_query($query);
	$filename = $_FILES['file']['name'];
	$nameUsed = 0;
	while ($a = db_fetch_assoc($r)) {
		if ($a[name] == $_FILES['file']['name']) $nameUsed = 1;
	}
	if ($_FILES['file']['tmp_name'] == 'none') {
		$upload_results = "<li>No file selected";
	} else if ($nameUsed) {
		$upload_results = "<li>Filename, $filename, is already in use. Please change the filename before uploading.";
	} else {
		$newID = copyuserfile($_FILES['file'],0,0);
		$upload_results = "<li>$filename successfully uploaded to ID $newID";
	}	
}

if ($clear) {
	$user = "";
	$site = "";
	$name = "";
}

if (!isset($order)) $order = "name asc";
$orderby = " order by $order";

$w = array();
if ($ltype == 'admin') {
	if ($site) $w[]="site_id='$site'";
	else if ($all) $w[]="site_id like '%'";
	else $w[]="site_id='$settings[site]'";
} else $w[]="site_id='$settings[site]'";
if ($user) $w[]="addedby like '%$user%'";
if ($name) $w[]="name like '%$name%'";

if (count($w)) $where = " where ".implode(" and ",$w);

$numrows=db_num_rows(db_query("select * from media$where"));
$numperpage = 20;


if (!isset($lowerlimit)) $lowerlimit = 0;
if ($lowerlimit < 0) $lowerlimit = 0;

$limit = " limit $lowerlimit,$numperpage";

$query = "select * from media$where$orderby$limit";

$r = db_query($query);

?>
<html>
<head>
<title>File Browser</title>

<style type='text/css'>
a {
	color: #a33;
	text-decoration: none;
}

a:hover {text-decoration: underline;}

table {
	border: 1px solid #555;
}

th, td {
	border: 0px;
	background-color: #ddd;
	text-align: center;
}

.td1 { 
	background-color: #ccc; 
}

.td0 { 
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
	font-size: 10px;
	font-family: "Verdana", "sans-serif";
}

/* td { font-size: 10px; } */

input,select {
	border: 1px solid black;
	background-color: white;
	font-size: 10px;
}

</style>

<script lang="JavaScript">

function useFile(fileID,fileName) {
	o = opener.document.addform;
	o.libraryfileid.value=fileID;
	o.libraryfilename.value=fileName;
	o.submit();
	window.close();
}

function deleteFile(fileID,fileName) {
	if (confirm("Are you sure that you want to delete "+fileName+"? If this file is in use anywhere in your site, it will no longer appear.")) {
		f = document.deleteform;
		f.filetodelete.value=fileID;
		f.submit();
	}
}

function changeOrder(order) {
	f = document.searchform;
	f.order.value=order;
	f.submit();
}

</script>

<!-- 
<table width='100%'>
<tr><td style='text-align: left'>
	<? print $content; ?>
	<? print $numrows . " | " . $query; 
	?>
</td></tr>
</table> -->

<table cellspacing=1 width='100%'>
<?
if ($ltype == 'admin' || $numrows >= $numperpage) {
?>
<tr>
	<td colspan=<? print (($ltype=='admin')?"10":"9"); ?>>
		<table width='100%'>
		<tr><td style='text-align: left'>
		<form action=<?echo "$PHP_SELF?$sid"?> method=post name='searchform'>
		<?
		if ($ltype == 'admin') {
		?>
			filename: <input type=text name=name size=15 value='<?echo $name?>'>
			site: <input type=text name=site size=10 value='<?echo $site?>'>
			user: <input type=text name=user size=10 value='<?echo $user?>'>
		<? } else { ?>
			filename: <input type=text name=name size=10 value='<?echo $name?>'>
		<? } ?>
		<input type=submit value='go'>
		<input type=submit name='clear' value='clear'>
		<? if ($ltype == 'admin') print "Search all sites: <input type=checkbox name='all' value='all sites'".(($all)?" checked":"").">"; ?>
		<input type=hidden name='order' value='<? echo $order ?>'>
		</form>
		</td>
		<td align=right>
		
		<?
		$tpages = ceil($numrows/$numperpage);
		$curr = ceil(($lowerlimit+$numperpage)/$numperpage);
		$prev = $lowerlimit-$numperpage;
		if ($prev < 0) $prev = 0;
		$next = $lowerlimit+$numperpage;
		if ($next >= $numrows) $next = $numrows-$numperpage;
		if ($next < 0) $next = 0;
		print "$curr of $tpages ";
//		print "$prev $lowerlimit $next ";
		if ($prev != $lowerlimit)
			print "<input type=button value='&lt;&lt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$prev&type=$type&user=$user&hideadmin=$hideadmin&site=$site&order=$order&all=$all\"'>\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type=button value='&gt;&gt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$next&type=$type&user=$user&hideadmin=$hideadmin&site=$site&order=$order&all=$all\"'>\n";
		?>
		</td>
		</tr>
		</table>
	</td>
</tr>
<? } ?>
<tr>
	<th> </th>
	<th> </th>
<?
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='id asc') print "id desc";
	else print "id asc";
	print "')\" style='color: #000'>ID";
	if ($order =='id asc') print " &or;";
	if ($order =='id desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='name asc') print "name desc";
	else print "name asc";
	print "')\" style='color: #000'>File Name";
	if ($order =='name asc') print " &or;";
	if ($order =='name desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='type asc') print "type desc";
	else print "type asc";
	print "')\" style='color: #000'>Type";
	if ($order =='type asc') print " &or;";
	if ($order =='type desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='size asc') print "size desc";
	else print "size asc";
	print "')\" style='color: #000'>Size";
	if ($order =='size asc') print " &or;";
	if ($order =='size desc') print " &and;";	
	print "</a></th>";
	
	if ($ltype == 'admin') {
		print "<th><a href=# onClick=\"changeOrder('";
		if ($order =='site_id asc') print "site_id desc";
		else print "site_id asc";
		print "')\" style='color: #000'>Site";
		if ($order =='site_id asc') print " &or;";
		if ($order =='site_id desc') print " &and;";	
		print "</a></th>";
	}

	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='addedtimestamp asc') print "addedtimestamp desc";
	else print "addedtimestamp asc";
	print "')\" style='color: #000'>Date Created";
	if ($order =='addedtimestamp asc') print " &or;";
	if ($order =='addedtimestamp desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='addedby asc') print "addedby desc";
	else print "addedby asc";
	print "')\" style='color: #000'>Added by User:";
	if ($order =='addedby asc') print " &or;";
	if ($order =='addedby desc') print " &and;";	
	print "</a></th>";
?>	
	<th> </th>
</tr>
<?
$color = 0;
$today = date(Ymd);
$yesterday = date(Ymd)-1;

if (db_num_rows($r)) {
	while ($a=db_fetch_assoc($r)) {
		$a[name] = urldecode($a[name]);
		$a[size] = convertfilesize($a[size]);
		
		if ($a[type] == 'image') {
			$img_path = $uploadurl."/".$a[site_id]."/".$a[name];
		} else {
			$img_path = "images/file.gif";
		}
		$url = $uploadurl."/".$a[site_id]."/".rawurlencode($a[name]);
		$thumb_size = get_sizes($img_path,'50');
		$img_size = get_size($img_path);
		
		print "<tr>";

		print "<td class=td$color>";
			print "<input type=button name='use' value='use' onClick=\"useFile('".$a[id]."','".$a[name]."')\">"; 
//			print "<input type=button name='use' value='use' onClick=\"useFile()\">"; 
		print "</td>";

		print "<td class=td$color>";
			if ($a[type]=='image') {
				$windowSize[x] = $img_size[x]+15;
				$windowSize[y] = $img_size[y]+15;
//				print "<a href=# onClick=\"window.open('$url','imagewindow',config='width=$img_size[x],height=$img_size[y],resizeable=1,scrollbars=0')\">";
				print "<a href=JavaScript:window.open('$url','imagewindow',config='width=$windowSize[x],height=$windowSize[y],resizeable=1,scrollbars=0');void('');>";
			} else
				print "<a href='$url'>";
			print "<img src='$img_path' height=$thumb_size[y] width=$thumb_size[x] border=0>";
			print "</a>";
		print "</td>";

		print "<td class=td$color>";
			print "$a[id]";
		print "</td>";
		
		print "<td class=td$color style='text-align: left'>";
			print "$a[name]";
		print "</td>";

		print "<td class=td$color>";
			print "$a[type]";
		print "</td>";

		print "<td class=td$color>";
			print "$a[size]";
		print "</td>";
		
		if ($ltype == 'admin') {
			print "<td class=td$color>";
			print "$a[site_id]";
		print "</td>";
		}

		print "<td class=td$color><nobr>";
			if (strncmp($today, $a[addedtimestamp], 8) == 0 || strncmp($yesterday, $a[addedtimestamp], 8) == 0) print "<b>";
			print $a[addedtimestamp];
			if (strncmp($today, $a[addedtimestamp], 8) == 0 || strncmp($yesterday, $a[addedtimestamp], 8) == 0) print "</b>";
		print "</nobr></td>";
		
		print "<td class=td$color>";
			print "$a[addedby]";
		print "</td>";
		
		print "<td class=td$color>";
			print "<input type=button value='delete' onClick=\"deleteFile('".$a[id]."','".$a[name]."')\">"; 
//			print "<input type=button name='delete' value='delete'>"; 
		print "</td>";

		print "</tr>";
		$color = 1-$color;
	}
} else {
	print "<tr><td colspan=".(($ltype=='admin')?"10":"9")." style='text-align: left'>No media.</td></tr>";
}
?>
	<tr>
		<td colspan=<? print (($ltype=='admin')?"10":"9"); ?>>
			<table width='100%'>
				<tr>
				<td style='text-align: left; padding-top:10px;' valign=top>
					<?
					if ($upload) {
						print "Upload Results: <div style='margin-left: 25px'>";
						print $upload_results;
						print "</div>";
					}
					?>
				</td>
				<td style='text-align: left; padding-top:10px;' width=50% valign=top>
					<form action="filebrowser.php" name='addform' method="POST" enctype="multipart/form-data">
					<input type="hidden" name="MAX_FILE_SIZE" value="'.$_max_upload.'">
					<input type=file name='file' class=textfield onClick="document.addform.submit()">  
					<input type=hidden name='upload' value='1'>
					<div class=desc>Select the file or image you would like to upload<br>by clicking the 'Browse...' button above.</div>
					</form>
				</td>
				</tr>
			</table>
		</td>
	</tr>
</table><BR>

<form action='filebrowser.php' name='deleteform' method=post>
<input type=hidden name='filetodelete'>
<input type=hidden name='delete' value=1>
</form>

<div align=right><input type=button value='Close Window' onClick='window.close()'></div>
