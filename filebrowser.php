<? /* $Id$ */
 
$content = ''; 
 
ob_start(); 
session_start(); 
 
//output a meta tag 
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
 
// include all necessary files 
include("includes.inc.php"); 
include("sniffer.inc.php");
include("objects/objects.inc.php");
 
//$siteObj = new site ($site);
//if ($ltype != 'admin') exit; 
 
db_connect($dbhost, $dbuser, $dbpass, $dbdb); 
 
if ($delete) { 
	deleteuserfile($filetodelete); 
	printerr2(); 
} 

$sitelist = array();

$w = array(); 
if ($ltype == 'admin') { 
	if ($site) 
		$w[]="slot_name='$site'"; 
	else if ($all) $w[]="slot_name like '%'"; 
	else $w[]="slot_name='$settings[site]'"; 
} else $w[]="slot_name='".(($site)?"$site":"$settings[site]")."'"; 
if (count($w)) $where = " WHERE ".implode(" and ",$w); 

$query = "
	SELECT 
		media_tag,
		media_id,
		media_size,
		media_type,
		slot_name
	FROM 
		media
			INNER JOIN
		slot
			ON
		media.FK_site = slot.FK_site		
	$where
"; 
$r = db_query($query); 

$totalsize = 0;
while ($a = db_fetch_assoc($r)) {
	$totalsize = $totalsize + $a[media_size];
}
 
if ($upload) { 
	$query = "
		SELECT 
			media_tag,
			media_id,
			media_size,
			media_type,
			slot_name,
			user_fname,
			user_uname
		FROM 
			media
				INNER JOIN
			slot
				ON
			media.FK_site = slot.FK_site
				INNER JOIN
			user
				ON
			media.FK_createdby = user_id
		WHERE
			slot_name='".(($site)?"$site":"$settings[site]")."'"; 
//	print "$query <br>"; 
	$r = db_query($query); 
	$filename = ereg_replace("[\x27\x22]",'',trim($_FILES[file][name])); 
	$nameUsed = 0; 
	while ($a = db_fetch_assoc($r)) { 
		if ($a[media_tag] == $filename) {
			$nameUsed = 1;
			$usedId = $a[media_id];
		}
	} 
	if ($_FILES['file']['tmp_name'] == 'none') { 
		$upload_results = "<li>No file selected"; 
	} else if (($_FILES[file][size] + $totalsize) > $userdirlimit) {
		$upload_results = "<li>There is not enough room in your directory for $filename."; 
	} else if ($overwrite && $nameUsed) {
		$newID = copyuserfile($_FILES['file'],(($site)?"$site":"$settings[site]"),1,$usedId,0); 
		$upload_results = "<li>$filename successfully uploaded to ID $newID. <li>The origional file was overwritten. <li>If the your new version does not appear, please reload your page. If the new version still doesn't appear, clear your browser cache."; 
	} else if ($nameUsed) { 
		$upload_results = "<li>Filename, $filename, is already in use. <li>Please change the filename before uploading or check \"overwrite\" to OVERWRITE"; 
	} else { 
		$newID = copyuserfile($_FILES['file'],(($site)?"$site":"$settings[site]"),0,0); 
		$upload_results = "<li>$filename successfully uploaded to ID $newID"; 
	}	 
} 
 
if ($clear) {
	if ($ltype == 'admin') {
		$user = ""; 
		$site = ""; 
		$name = ""; 
	} else {
		$name = "";
	}
} 

$w = array(); 
if ($ltype == 'admin') { 
	if ($site) $w[]="slot_name='$site'"; 
	else if ($all) $w[]="slot_name like '%'"; 
	else $w[]="slot_name='$settings[site]'"; 
} else $w[]="slot_name='".(($site)?"$site":"$settings[site]")."'"; 
if (count($w)) $where = " where ".implode(" and ",$w); 

$query = "
	SELECT 
		media_tag,
		media_id,
		media_size,
		media_type,
		slot_name,
		user_fname,
		user_uname

	FROM 
		media
			INNER JOIN
		slot
			ON
		media.FK_site = slot.FK_site
			INNER JOIN
		user
			ON
		media.FK_createdby = user_id
	$where
"; 
$r = db_query($query); 

$totalsize = 0;
while ($a = db_fetch_assoc($r)) {
	$totalsize = $totalsize + $a[media_size];
}

if (!isset($order)) $order = "media_tag asc"; 
$orderby = " ORDER BY $order"; 
 
$w = array(); 
if ($ltype == 'admin') { 
	if ($site) $w[]="slot_name='$site'"; 
	else if ($all) $w[]="slot_name like '%'"; 
	else $w[]="slot_name='$settings[site]'"; 
} else $w[]="slot_name='".(($site)?"$site":"$settings[site]")."'"; 
if ($user) $w[]="user_uname LIKE '%$user%'"; 
if ($name) $w[]="media_tag LIKE '%$name%'"; 
 
if (count($w)) $where = " WHERE ".implode(" AND ",$w); 
 
$r=db_query("
	SELECT 
		COUNT(*) AS media_count
	FROM 
		media
			INNER JOIN
		slot
			ON
		media.FK_site = slot.FK_site
			INNER JOIN
		user
			ON
		media.FK_createdby = user_id
	$where"); 
$a = db_fetch_assoc($r);
$numrows = $a[media_count];
$numperpage = 20; 
 
 
if (!isset($lowerlimit)) $lowerlimit = 0; 
if ($lowerlimit < 0) $lowerlimit = 0; 
 
$limit = " LIMIT $lowerlimit,$numperpage"; 
 
$query = "
	SELECT 
		media_tag,
		media_id,
		media_size,
		date_format(media_updated_tstamp, '%M %e, %Y %k:%i') AS media_updated_tstamp_text,
		media_updated_tstamp,
		media_type,
		slot_name,
		user_fname,
		user_uname

	FROM 
		media
			INNER JOIN
		slot
			ON
		media.FK_site = slot.FK_site
			INNER JOIN
		user
			ON
		media.FK_createdby = user_id
	$where
	$orderby
	$limit
"; 
 
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

.sizebox1 {
	text-align: left;
	padding-right: 5px;
}

.sizebox2 {
	text-align: right;
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
 
<? if ($editor == 'none') { ?> 
	function useFile(fileID,fileName) { 
		o = opener.document.addform; 
		o.libraryfileid.value=fileID; 
		o.libraryfilename.value=fileName; 
		o.submit(); 
		window.close(); 
	} 

<? } else if ($editor == 'text') { 
	// if using the non-IE text editor 
?> 

<? } else { 
	// if using the activeX editor
		$site = $site; 
?> 
	function useFile(siteName,fileName,fileID) { 
		opener = window.dialogArguments;
		var _editor_url = opener._editor_url;
		var objname     = location.search.substring(1,location.search.length);
		var config      = opener.document.all[objname].config;
		var editor_obj  = opener.document.all["_" +objname+  "_editor"];
		var editdoc     = editor_obj.contentWindow.document;
  		var image 	= '<img src="<?echo $uploadurl ?>/' +siteName+ '/' +fileName+ '" imageID=\"' +fileID+ '\">\n';
  		opener.editor_insertHTML(objname, image);
		window.close();
	} 
<? } ?> 
 
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

function changePage(lolim) {
	f = document.searchform;
	f.lowerlimit.value=lolim;
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
 
	<tr> 
		<td colspan=<? print (($ltype=='admin')?"10":"9"); ?>> 
			<table width='100%' > 
				<tr> 
				<td style='text-align: center; padding-top: 5px; border: 0px solid #FFF' valign=top> 
					<form action="filebrowser.php" name='addform' method="POST" enctype="multipart/form-data"> 
					<input type=hidden name='comingfrom' value='<? echo $comingfrom ?>'>
					<input type=hidden name='site' value='<? echo $site ?>'> 
					<input type="hidden" name="MAX_FILE_SIZE" value="'.$_max_upload.'"> 
					<input type=hidden name='upload' value='1'> 
					<input type=hidden name='order' value='<? echo $order ?>'> 
					<input type=hidden name='editor' value='<? echo $editor ?>'> 
					Overwrite old version: <input type=checkbox name='overwrite' value=1>
					<?
					if ($browser_os == "pcie5+" || $browser_os == "pcie4") {
						print "<input type=file name='file' class=textfield style='color: #FFF'>";
						print "<input type=submit value='Upload'>";
					} else {
						print "<input type=file name='file' class=textfield onClick=\"document.addform.submit()\">";
					}
					?>
					</form> 
				</td> 
				<td rowspan=2 valign=top style='text-align: right; border: 0px solid #FFF'>
					<?
					$dirtotal = convertfilesize($totalsize);
					if ($all) {
						$res = db_query("SELECT COUNT(*) FROM site");
						$b = db_fetch_assoc($res);
						$dirlimit_B = $b['COUNT(*)']*$userdirlimit;
					} else
						$dirlimit_B = $userdirlimit;
					$dirlimit = convertfilesize($dirlimit_B);
					$percentused = round($totalsize/$dirlimit_B,"4")*100;
					$percentfree = 100-$percentused;
					$space = $dirlimit_B - $totalsize;
					$space = convertfilesize($space);
					print "<table cellspacing=0 cellpadding=0 align=right>";
					print "<tr><td class='sizebox1'>Total media allowed: </td><td class='sizebox2'> $dirlimit</td></tr>";
					print "<tr><td class='sizebox1'>Total size of your media: </td><td class='sizebox2'> $dirtotal</td></tr>";
					print "<tr><td class='sizebox1'>Space available: </td><td class='sizebox2' style='border-top: 1px solid #000'> $space</td></tr>";
					print "<tr><td colspan=2><table width=100%><tr>";
					if ($percentused == 0)
						print "<td style='background-color: #00C; height: 5px;' width=100%> </td>";
					else if ($percentused == 100)
						print "<td style='background-color: #F00; height: 5px;' width=100%> </td>";
					else
						print "<td style='background-color: #F00; height: 5px;' width=$percentused%> </td><td style='background-color: #00C;' width=$percentfree%> </td>";
					print "</tr></table></td></tr>";
					print "</table><br>";
					?>
				</td>
				</tr> 
				<tr>
				<td style='text-align: left; border: 0px solid #FFF' valign=top>
					<div class=desc>Select the file or image you would like to upload by clicking the 'Browse...' button above.</div> 
				</td>
				</tr>
				<tr>
				<td style='text-align: left; height: 40px; border: 0px solid #FFF' valign=top> 
					<? 					
					if ($upload) { 
						print "Upload Results: <div style='margin-left: 25px'>"; 
						print $upload_results; 
						print "</div>"; 
					} else {
						print " &nbsp; ";
					}
					?> 
				</td> 
				<td>
					<?
					print "<div style='text-align: center;'>";
					print helplink("filelibrary");
					print "</div>";
					?>
				</td>
				</tr>
			</table> 
		</td> 
	</tr> 
 
<? 
if (1) { 
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
			<input type=hidden name=site value='<?echo $site?>'>
		<? } ?> 
		<input type=submit value='search'> 
		<input type=submit name='clear' value='clear'> 
		<? if ($ltype == 'admin') print "Search all sites: <input type=checkbox name='all' value='all sites'".(($all)?" checked":"").">"; ?> 
		<input type=hidden name='order' value='<? echo $order ?>'> 
		<input type=hidden name='editor' value='<? echo $editor ?>'> 
		<input type=hidden name='comingfrom' value='<? echo $comingfrom ?>'> 
		<input type=hidden name='lowerlimit' value=0>
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
			print "<input type=button value='&lt;&lt' onClick=\"changePage('$prev')\">\n"; 
		if ($next != $lowerlimit && $next > $lowerlimit) 
			print "<input type=button value='&gt;&gt' onClick=\"changePage('$next')\">\n"; 
		?> 
		</td> 
		</tr> 
		</table> 
	</td> 
</tr> 
<? } else { ?> 
	<form action=<?echo "$PHP_SELF?$sid"?> method=post name='searchform'> 
	<input type=hidden name='order' value='<? echo $order ?>'> 
	<input type=hidden name='editor' value='<? echo $editor ?>'> 
	<input type=hidden name='comingfrom' value='<? echo $comingfrom ?>'>
	<input type=hidden name='site' value='<? echo $site ?>'>
	</form>
<? } ?>


<tr> 
	<th> </th> 
	<th> </th> 
<? 
	print "<th><a href=# onClick=\"changeOrder('"; 
	if ($order =='media_id asc') print "media_id desc"; 
	else print "media_id asc"; 
	print "')\" style='color: #000'>ID"; 
	if ($order =='media_id asc') print " &or;"; 
	if ($order =='media_id desc') print " &and;";	 
	print "</a></th>"; 
	 
	print "<th><a href=# onClick=\"changeOrder('"; 
	if ($order =='media_tag asc') print "media_tag desc"; 
	else print "media_tag asc"; 
	print "')\" style='color: #000'>File Name"; 
	if ($order =='media_tag asc') print " &or;"; 
	if ($order =='media_tag desc') print " &and;";	 
	print "</a></th>"; 
	 
	print "<th><a href=# onClick=\"changeOrder('"; 
	if ($order =='media_type asc') print "media_type desc"; 
	else print "media_type asc"; 
	print "')\" style='color: #000'>Type"; 
	if ($order =='media_type asc') print " &or;"; 
	if ($order =='media_type desc') print " &and;";	 
	print "</a></th>"; 
	 
	print "<th><a href=# onClick=\"changeOrder('"; 
	if ($order =='media_size asc') print "media_size desc"; 
	else print "media_size asc"; 
	print "')\" style='color: #000'>Size"; 
	if ($order =='media_size asc') print " &or;"; 
	if ($order =='media_size desc') print " &and;";	 
	print "</a></th>"; 
	 
	if ($ltype == 'admin') { 
		print "<th><a href=# onClick=\"changeOrder('"; 
		if ($order =='slot_name asc') print "slot_name desc"; 
		else print "slot_name asc"; 
		print "')\" style='color: #000'>Site"; 
		if ($order =='slot_name asc') print " &or;"; 
		if ($order =='slot_name desc') print " &and;";	 
		print "</a></th>"; 
	} 
 
	print "<th><a href=# onClick=\"changeOrder('"; 
	if ($order =='media_updated_tstamp asc') print "media_updated_tstamp desc"; 
	else print "media_updated_tstamp asc"; 
	print "')\" style='color: #000'>Date Modified"; 
	if ($order =='media_updated_tstamp asc') print " &or;"; 
	if ($order =='media_updated_tstamp desc') print " &and;";	 
	print "</a></th>"; 
	 
	print "<th><a href=# onClick=\"changeOrder('"; 
	if ($order =='user_uname asc') print "user_uname desc"; 
	else print "user_uname asc"; 
	print "')\" style='color: #000'>Added by User:"; 
	if ($order =='user_uname asc') print " &or;"; 
	if ($order =='user_uname desc') print " &and;";	 
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
		$a[media_tag] = urldecode($a[media_tag]); 
		$a[media_size] = convertfilesize($a[media_size]); 
		
		$url = $uploadurl."/".$a[slot_name]."/".rawurlencode($a[media_tag]); 
		if ($a[media_type] == 'image') { 
			$img_path = $uploaddir."/".$a[slot_name]."/".$a[media_tag];
			$img_url = $url;
		} else { 
			$img_path = "images/file.gif";
			$img_url = $img_path;
		}  
		if (file_exists($img_path)) {
			$thumb_size = get_sizes($img_path,'50');
			$img_size = get_size($img_path); 
		} else {
			$img_url = "images/nofile.gif";
			$thumb_size = get_sizes($img_path);
			$img_size = get_size($img_path); 
		}
		
/* 		$img_size = get_size($url);  */
		 
		print "<tr>"; 
 
		print "<td class=td$color>";
			if ($comingfrom != "viewsite") {
                if ($editor == 'none') {
					print "<input type=button name='use' value='use' onClick=\"useFile('".$a[media_id]."','".$a[media_tag]."')\">";
                }
                else if ($editor == 'text') {
                        print "<input type=button name='use' value='use' onClick=\"useFile('".$a[media_id]."','".$a[media_tag]."')\">";
                }
                else {
                        print "<input type=button name='use' value='use' onClick=\"useFile('".$a[slot_name]."','".$a[media_tag]."','".$a[media_id]."')\">";
                }   
            } else print " &nbsp; ";
//			print "<input type=button name='use' value='use' onClick=\"useFile()\">";  
		print "</td>"; 
 
		print "<td class=td$color>"; 
			if ($a[media_type]=='image') { 
				$windowSize[x] = $img_size[x]+15; 
				$windowSize[y] = $img_size[y]+15; 
//				print "<a href=# onClick=\"window.open('$url','imagewindow',config='width=$img_size[x],height=$img_size[y],resizeable=1,scrollbars=0')\">"; 
				print "<a href=JavaScript:window.open('$url','imagewindow',config='width=$windowSize[x],height=$windowSize[y],resizeable=1,scrollbars=0');void('');>"; 
			} else 
				print "<a href='$url'>"; 
			print "<img src='$img_url' height=$thumb_size[y] width=$thumb_size[x] border=0>"; 
			print "</a>"; 
		print "</td>"; 
 
		print "<td class=td$color>"; 
			print "$a[media_id]"; 
		print "</td>"; 
		 
		print "<td class=td$color style='text-align: left'>"; 
			print "$a[media_tag]"; 
		print "</td>"; 
 
		print "<td class=td$color>"; 
			print "$a[media_type]"; 
		print "</td>"; 
 
		print "<td class=td$color>"; 
			print "$a[media_size]"; 
		print "</td>"; 
		 
		if ($ltype == 'admin') { 
			print "<td class=td$color>"; 
			print "$a[slot_name]"; 
		print "</td>"; 
		} 
 
		print "<td class=td$color><nobr>"; 
			if (strncmp($today, $a[media_updated_tstamp], 8) == 0 || strncmp($yesterday, $a[media_updated_tstamp], 8) == 0) print "<b>"; 
			print $a[media_updated_tstamp_text]; 
			if (strncmp($today, $a[media_updated_tstamp], 8) == 0 || strncmp($yesterday, $a[media_updated_tstamp], 8) == 0) print "</b>"; 
		print "</nobr></td>"; 
		 
		print "<td class=td$color>"; 
			print "$a[user_fname] ($a[user_uname])"; 
		print "</td>"; 
		 
		print "<td class=td$color>"; 
			print "<input type=button value='delete' onClick=\"deleteFile('".$a[media_id]."','".$a[media_tag]."')\">";  
//			print "<input type=button name='delete' value='delete'>";  
		print "</td>"; 
 
		print "</tr>"; 
		$color = 1-$color; 
	} 
} else { 
	print "<tr><td colspan=".(($ltype=='admin')?"10":"9")." style='text-align: left'>No media.</td></tr>"; 
} 
?> 
 
</table><BR> 
 
<form action='filebrowser.php' name='deleteform' method=post> 
<input type=hidden name='filetodelete'> 
<input type=hidden name='delete' value=1> 
<input type=hidden name='order' value='<? echo $order ?>'> 
<input type=hidden name='all' value='<? echo $all ?>'> 
<input type=hidden name='editor' value='<? echo $editor ?>'>
<input type=hidden name='site' value='<? echo $site ?>'>
<input type=hidden name='comingfrom' value='<? echo $comingfrom ?>'> 
</form> 
 
<div align=right><input type=button value='Close Window' onClick='window.close()'></div> 

<?

// debug output -- handy :)
print "<pre>";
print "request:\n";
print_r($_REQUEST);
print "\n\n";
print "session:\n";
print_r($_SESSION);
print "\n\n";

/* if (is_object($thisPage)) { */
/* 	print "\n\n"; */
/* 	print "thisPage:\n"; */
/* 	print_r($thisPage); */
/* } else if (is_object($thisSection)) { */
/* 	print "\n\n"; */
/* 	print "thisSection:\n"; */
/* 	print_r($thisSection); */
/* } else if (is_object($thisSite)) { */
/* 	print "\n\n"; */
/* 	print "thisSite:\n"; */
/* 	print_r($thisSite); */
/* } */
print "</pre>";
?>