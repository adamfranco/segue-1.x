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
			if ($item[extra]) print "<div align=right>$item[extra]</div>";
		}
		print "</tr></td>";
	}
	print "</table>";
	print "<br>$extra";
}

function side_nav($navtype,$leftnav,$subnav,$extra, $subextra) {
	$detail = $_REQUEST[detail];
	$page = $_REQUEST[page];
	
	print "<table width=100% cellpadding=2 cellspacing=0>";
	
	/******************************************************************************
	 * main left nav = sections
	 ******************************************************************************/

	foreach ($leftnav as $item) {
		$bold=0;
		print "<tr><td>";
	//	print $page."<br>";
	//	print $item[id];
	
		/******************************************************************************
		 * new section
		 ******************************************************************************/
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
			
			/******************************************************************************
			 * print out all subnav items (ie pages of section)
			 ******************************************************************************/
			if (($samepage == 1 && count($subnav) > 1) || action == "viewsite") {
				print "<table width=100% cellpadding=2 cellspacing=0>";
				foreach ($subnav as $item) {
					$bold=0;
					print "<tr><td>";
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
						}
						print "<div class='subnav'>  ";
						print makelink($item,$samepage," class='navlink' ",1,$bold);
						print "</div>";
						if ($samepage == 1) {
							
						}
					}
					if ($item[type] == 'divider') {
						print "$item[extra]<br>";
					}
					if ($item[type] == 'heading') {
						print "<div class='subnav'>  ";
						print "<img src='themes/common/images/bullet.gif' border=0 align=top>$item[name]:";
						if ($item[extra]) print "<div align=right>$item[extra]</div>";
						print "</div>";
					}
					print "</tr></td>";
				}
				print "</table>";
				print "$subextra";		
			}
			if ($samepage == 1) print "$subextra";	
		}
		/******************************************************************************
		 * divider
		 ******************************************************************************/
		if ($item[type] == 'divider') {
			print "$item[extra]<br>";
		}
		/******************************************************************************
		 * heading
		 ******************************************************************************/
		if ($item[type] == 'heading') {
			print "<div class='nav'>";
			print "<img src='themes/common/images/bullet.gif' border=0 align=top>$item[name]:";
			if ($item[extra]) print "<div align=right>$item[extra]</div>";
			print "</div>";
		}
		print "</tr></td>";
	}
	print "</table>";
	print "<div align=right>$extra</div>";
}

	
?>
