<?
// output functions

function horizontal_nav($navtype,$topnav,$extra) {
	$totalnav = count($topnav);
	$next=1;
	print "<div class='sectionnav'>";
	//print "<div class=$navlinka>";
	foreach ($topnav as $item) {
		$samepage = (isset($navtype) && ($navtype == $item[id]))?1:0;
		if (!$navtype) $samepage = ($action && ($action == $item[id]))?1:0;
		print makelink($item,$samepage, " class='navlink' ");		
		if ($next != $totalnav) print " | ";		
		$next=$next+1;
	}
	print $extra;
	print "</div>";
}

function vertical_nav($navtype,$leftnav,$extra) {
	$detail = $_REQUEST[detail];
	$page = $_REQUEST[page];
	
	print "<table width=100% cellpadding=2 cellspacing=0>";
	foreach ($leftnav as $item) {
		$bold=0;
		print "<tr><td>";
	//	print $page."<br>";
	//	print $item[id];
		if ($item[type] == 'normal') {
			$samepage = (isset($navtype) && ($navtype == $item[id]))?1:0;
			if (!$navtype) $samepage = ($action && ($action == $item[id]))?1:0;
			
			//if story detail then keep link to page and bold
			if ($samepage == 1 && $detail && $page==$item[id]) {
				$samepage = 0;
				$bold = 1;
			}
			print "<div class='nav'>";
			print makelink($item,$samepage," class='navlink' ",1,$bold);
			print "</div>";
		}
		if ($item[type] == 'divider') {
			print "$item[extra]<br>";
		}
		if ($item[type] == 'heading') {
			print "<img src='themes/common/images/bullet.gif' border=0 align=top>$item[name]:";
			if ($item[extra]) print "<div align='right'>$item[extra]</div>";
		}
		print "</tr></td>";
	}
	print "</table>";
	print "<br>$extra";
}

function side_nav($navtype,$leftnav,$subnav,$extra, $subextra) {
	global $action;
	$detail = $_REQUEST[detail];
	$page = $_REQUEST[page];
//	printpre($subnav);
	
	print "\n\n\n<!--   Side navigation   -->";
	print "\n<table width=100% cellpadding=2 cellspacing=0>";
	
	/******************************************************************************
	 * main left nav = sections
	 ******************************************************************************/

	foreach ($leftnav as $item) {
		print "\n<!--   Section: ".$item[id]."   -->";
		$bold=0;
		print "\n\t<tr><td>";
	//	print $page."<br>";
	//	print $item[id];
	
		/******************************************************************************
		 * new section
		 ******************************************************************************/
		if ($item[type] == 'normal') {
			if ($navtype) {
				$samepage = ($navtype == $item[id])?1:0;
			} else {
				$samepage = ($action && ($action == $item[id]))?1:0;
			}
			
			//if story detail then keep link to page and bold
			if ($samepage == 1 && $detail && $page==$item[id]) {
				$samepage = 0;
				$bold = 1;
			}
			print "\n\t<div class='nav'>";
			print "\n\t\t".makelink($item,$samepage," class='navlink' ",1,$bold);
			print "\n\t</div>";
			
			/******************************************************************************
			 * check if any pages in section are of link type
			 * (checks to see if url contains "http")
			 ******************************************************************************/
			 $link_pagetype = 0;
			 foreach ($subnav as $item) {
			 	if (ereg("http", $item[url])) $link_pagetype = 1;
			 }

			/******************************************************************************
			 * print out all subnav items (ie pages of section)
			 * do not print out subnav if:
			 * 1. there are no link type pages and
			 * 2. there is only 1 page and 
			 * 3. action is not viewsite
			 ******************************************************************************/
			 
			if ($samepage == 1 && (count($subnav) > 1 || $action == "viewsite" || $link_pagetype == 1)) {
				print "\n\t<table width=100% cellpadding=2 cellspacing=0>";
				foreach ($subnav as $item) {
					print "\n<!--   Page: ".$item[id]."   -->";
					$bold=0;
					print "\n\t\t<tr>\n\t\t<td>";
				//	print $page."<br>";
				//	print $item[id];
					if ($item[type] == 'normal') {
						if (!isset($page)) {
							$page = $item[id];
							$samepage = ($page == $item[id])?1:0;
						} else {
							$samepage = (isset($page) && ($page == $item[id]))?1:0;
						}
						
						//if story detail then keep link to page and bold
						if ($samepage == 1 && $detail && $page==$item[id]) {
							$samepage = 0;
							$bold = 1;
						//if page is link type then not samepage (i.e. needs to be link...)
						} else if (ereg("http", $item[url])) {
							$samepage = 0;
						}
						
						print "\n\t\t<div class='subnav'>  ";
						print "\n\t\t\t".makelink($item,$samepage," class='navlink' ",1,$bold);
						print "\n\t\t</div>";
						if ($samepage == 1) {
							
						}
					}
					if ($item[type] == 'divider') {
						print "\n\t\t$item[extra]<br>";
					}
					if ($item[type] == 'heading') {
						print "\n\t\t<div class='subnav'>  ";
						print "\n\t\t\t<img src='themes/common/images/bullet.gif' border=0 align=top>$item[name]:";
						if ($item[extra]) print "\\n\t\t<div align='right'>$item[extra]</div>";
						print "\n\t\t</div>";
					}
					print "\n\t\t</td></tr>";
				}
				print "\n\t</table>";
				print "\n\t$subextra";		
			}
		}
		/******************************************************************************
		 * divider
		 ******************************************************************************/
		if ($item[type] == 'divider') {
			print "\n\t$item[extra]<br>";
		}
		/******************************************************************************
		 * heading
		 ******************************************************************************/
		if ($item[type] == 'heading') {
			print "\n\t<div class='nav'>";
			print "\n\t\t<img src='themes/common/images/bullet.gif' border=0 align=top>$item[name]:";
			if ($item[extra]) print "\n\t\t<div align='right'>$item[extra]</div>";
			print "\n\t</div>";
		}
		print "\n\t</tr></td>";
	}
	print "\n</table>";
	print "\n<div align='right'>$extra</div>";
}

	
?>
