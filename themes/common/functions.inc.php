<?
// output functions

function horizontal_nav($navtype,$topnav,$extra,$navlink="navlink",$navlinka="navlink a") {
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
	//print "</div>";
	print $extra;
	print "</div>";
}

function vertical_nav($navtype,$leftnav,$extra) {

print "<table width=100% cellpadding=2 cellspacing=0>";
	foreach ($leftnav as $item) {
		print "<tr><td>";
		if ($item[type] == 'normal') {
			$samepage = (isset($navtype) && ($navtype == $item[id]))?1:0;
			if (!$navtype) $samepage = ($action && ($action == $item[id]))?1:0;
			print "<div class='nav'>";
			print makelink($item,$samepage," class='navlink' ",1);
			print "</div>";
		}
		if ($item[type] == 'divider') {
			print "$item[extra]<br>";
		}
		if ($item[type] == 'heading') {
			print "<img src='themes/common/images/bullet.gif' border=0 align=absmiddle> $item[name] :";
			if ($item[extra]) print "<div align=right>$item[extra]</div>";
		}
		print "</tr></td>";
	}
	print "</table>";
	print "<br>$extra";
}
	
?>