<? /* $Id$ */

//if ($_SESSION[ltype] == 'admin' && $action=='viewsite') { include("output_modules/other/navbars.inc.php"); return; }

//if ($action == 'viewsite') $topnav_extra = ($thisSite->hasPermission("add"))?" <a href='$PHP_SELF?$sid&$envvars&action=add_section&comingFrom=viewsite' class='".(($topsections)?"btnlink":"small")."' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add section</a>":"";

$isediting = 0;
if ($action == 'viewsite') $isediting=1;

// build topnav items
$_ids = array_keys($thisSite->sections);
/* print_r($_ids); */
/* print "hello?"; */
$link = "$PHP_SELF?$sid&site=$site&action=$action&supplement=listissues";
add_link(topnav,"ISSUES",$link,$extra,'',$target);
add_link(topnav2,"ISSUES",$link,$extra,'',$target);
if (count($_ids)) {
	$s = $_ids[0];$int = &$thisSite->sections;
	$l = $_ids[count($_ids)-1];
	if ($section && $thisSection->getField("title")!="TOP") $last=&$int[$section];
	else $last = &$int[$l];
	$first = &$int[$s];
	
	/* print_r($so->pages); */
	foreach ($first->pages as $p=>$o) {
/* 		print_r($o); */
		$link = "$PHP_SELF?$sid&site=$site&section=$section&page=$p&action=$action";
		add_link(topnav,$o->getField("title"),$link,$extra,'',$target);
		add_link(topnav2,$o->getField("title"),$link,$extra,'',$target);
	}
	add_link(leftnav,"<span class=smaller><i>".strtoupper($last->getField("title"))."</i></span>");
	

	$pdfname = createPdfName($last->getField("title"));
	$filename = "$uploaddir/".$thisSite->name."/$pdfname";
	$fileurl = "$uploadurl/".$thisSite->name."/$pdfname";
	if (file_exists($filename)) {
		$putonlast = "<br><div align=center class='topmargin5 smaller'>".pdflink($filename,$fileurl)."</div>";
	}
	
	$i=0;$total = count($last->pages);
	foreach ($last->pages as $p=>$o) {
		$link = "$PHP_SELF?$sid&site=$site&section=$section&page=$p&action=$action";
		$extra = $list = '';
		if (($author = $o->getField("url")) && $author != "http://") $extra .= "<div class='leftmargin small' align=left>by $author</div>";
		if ($isediting) {
			$list .= ($last->hasPermission("edit"))?"<a href='$PHP_SELF?$sid&action=edit_page&site=$site&section=$section&page=$p&edit_page=$p&comingFrom=$action'>edit</a>\n":"";
			$list .= ($last->hasPermission("delete"))?"<a href='$PHP_SELF?$sid&action=delete_page&site=$site&section=$section&page=$p&delete_page=$p&comingFrom=$action'>del</a>\n":"";
			if ($list != '') $extra .= "<div class=small align=right>".$list."</div>";
		}
		if ($i == $total-1) $extra .= $putonlast;
		add_link(leftnav,$o->getField("title"),$link,$extra,$p,$target);
		$i++;
	}
	add_link(leftnav);
//	add_link(leftnav2);
}


$i=0;
$total=count($thisSite->sections);
if ($thisSite->sections) {
	add_link(leftnav2,"<span class=smaller>ISSUES</span>");
	foreach (array_reverse($thisSite->sections,TRUE) as $s=>$o) {
		if ($o->canview() || $o->hasPermissionDown("add or edit or delete")) {
			if ($i!=$total-1) {
				if ($o->getField("type") == 'section') $link = "$PHP_SELF?$sid&site=$site&section=$s&action=$action&supplement=listarticles";
				if ($o->getField("type") == 'url') { $link = $o->getField("url"); $target="_self";}
				$extra = '';
				$pdfname = $filename=$fileurl = '';
				$pdfname = createPdfName($o->getField("title"));
				$filename = "$uploaddir/".$thisSite->name."/$pdfname";
				$fileurl = "$uploadurl/".$thisSite->name."/$pdfname";
				if (file_exists($filename)) {
					$extra .= "<div align=center class='leftmargin smaller'>".pdflink($filename,$fileurl,2)."</div>";
				}
				if ($isediting) {
					$extra .= ($thisSite->hasPermission("edit"))?"\n<a href='$PHP_SELF?$sid&site=$site&section=$s&action=edit_section&edit_section=$s&comingFrom=viewsite' class='small' title='Edit the title and properties of this section'>edit</a>":"";
					$extra .= ($thisSite->hasPermission("delete"))?"\n<a href='javascript:doconfirm(\"Are absolutely sure you want to PERMANENTLY DELETE this section, including anything that may be held within it?? (you better be SURE!)\",\"$PHP_SELF?$sid&$envvars&action=delete_section&delete_section=$s\")' class='small' title='Delete this section'>del</a>":"";
				}
				add_link(leftnav2,$o->getField("title"),$link,$extra,$s,$target);
			
			}
			$i++;
		}
	}
}

if ($isediting) {
	$leftnav_extra = ($thisSite->hasPermission("add"))?" <a href='$PHP_SELF?$sid&$envvars&action=add_section&comingFrom=viewsite' class='small' title='Add a new Section to this site. A section can hold one or many pages of content. You can also add a Link here instead of a Section.'>+ add issue</a>":"";
}

/******************************************************************************
 * some functions from the old tko site.
 ******************************************************************************/

function filesizestr($filename) {
	if (file_exists($filename)) $file_size = filesize($filename);
	else return "no file";
/*	$bytes = array("KB","MB","GB","TB");
	$units = 'Bytes';
	foreach ($bytes as $unit) {
		if ($size > 1024) {
			$size /= 1024;
			$units = $unit;
		} else end;
	}
	return sprintf("%.02f",$size)." $units";*/
	$j = 0;
	$ext = array("B","KB","MB","GB","TB");
	while ($file_size >= pow(1024,$j)) ++$j;
	$file_size = round($file_size / pow(1024,$j-1) * 100) / 100;
	if ($j <= 2) $file_size = round($file_size);
	$file_size .= $ext[$j-1];
	return $file_size;
}

function pdflink($filename,$fileurl,$sm=0) {
	$size = filesizestr($filename);
	return "<a href='$fileurl'>".(($sm!=2)?"<img src='images/pdficon".(($sm)?"_sm":"").".gif' align=absmiddle border=0 alt='Download PDF'>":"") . (($sm==1)?" ":(($sm==2)?"":"<BR>") . "Download PDF ")."</a>($size)";
}


function createPdfName($title) {
	$parts = explode(" - ",$title);
	$title = $parts[1];
	$title = str_replace("- ","",$title);
	$title = str_replace(" #","-",$title);
	$title = ereg_replace("([',])","",$title);
	$title = str_replace(" ","_",$title);
	return "TKO_$title.pdf";
}