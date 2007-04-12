<?
// output functions

/******************************************************************************
 * Prints out horizontal navigation of sections from nav array
 ******************************************************************************/

function horizontal_nav($navtype,$topnav,$extra, $hide_sidebar = 0) {
	$section = $_REQUEST[section];
	$site = $_REQUEST[site];
	$detail = $_REQUEST[detail];
	$page = $_REQUEST[page];
	$action = $_REQUEST[action];

	$totalnav = count($topnav);
	$next=1;
	print "<div class='sectionnav'>\n";
	//print "<div class='$navlinka'>";
	$nextorder = 0;
	foreach ($topnav as $item) {
		$bold = 0;
		$samepage = (isset($navtype) && ($navtype == $item[id]))?1:0;
		if (!$navtype) $samepage = ($action && ($action == $item[id]))?1:0;
		
		$reorderUrl = $_SERVER['PHP_SELF']."?&amp;action=reorder&amp;site=".$site."&amp;section=".$section."&amp;page=".$page."&amp;reorderSection=".$item['id']."&amp;newPosition=";
		
		// if sidebar hidden, then bold section
		if ($samepage == 1 && $hide_sidebar ==1) {
			$bold = 1;
			$samepage = 0;
		}
		
		// Reorder Links				
		if ($_REQUEST['showorder'] == "section") {
			print "<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
			for ($i=0; $i<count($topnav); $i++) {
				print "<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>\n";
			}
			print "</select> \n";
		}
			
		print makelink($item,$samepage," class='navlink' ",0,$bold);
	//	print makelink($item,$samepage, " class='navlink' ");		

		if ($next != $totalnav) print " | ";		
		$next=$next+1;
		$nextorder++;
	}
	print "\n\t<div class='nav_extras'>$extra</div>\n";
	print "</div>\n";
}

/******************************************************************************
 * Print outs vertical navigation of pages from nav array
 * functions.inc.php/add_link creates nav arrays 
 * navtype = normal (page, link), rss, content, tags
 * nav_items = discreet nav items of navtype
 * extra = editing UI
 ******************************************************************************/

function vertical_nav($navtype, $nav_items, $extra, $bordercolor='000000', $hide_sidebar = 0) {
	global $thisSection;
	
	$section = $_REQUEST[section];
	$site = $_REQUEST[site];
	$detail = $_REQUEST[detail];
	$page = $_REQUEST[page];
	$action = $_REQUEST[action];
	if ($thisSection->getField('pageorder') == "editeddesc") {
		$pageorder = "Recently Edited First";
	} else if ($thisSection->getField('pageorder') == "editedasc") {
		$pageorder = "Recently Edited Last";
	} else if ($thisSection->getField('pageorder') == "addeddesc") {
		$pageorder = "Recently First";
	} else if ($thisSection->getField('pageorder') == "addedasc") {
		$pageorder = "Recently Last";
	} else if ($thisSection->getField('pageorder') == "titleasc") {
		$pageorder = "Alphabetic Display";
	} else if ($thisSection->getField('pageorder') == "custom") {
		$pageorder = "Custom Order";
	}

	
	
	$numContentPages = 0;
	foreach ($thisSection->pages as $pageObj) {
		if ($pageObj->getField('type') == 'page')
			$numContentPages++;
	}
	
	//printpre($nav_items);
		print "\n\t\t\t\t<table width='100%' cellpadding='2' cellspacing='0'>";
		print "\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>\n";

		/******************************************************************************
		 * Print display options message
		 ******************************************************************************/
		
		if ($hide_sidebar && $hide_sidebar != 2) {
			print "<table cellpadding='1' style='border: 1px solid;'><tr><td>";
			print "<div style='font-size: 9px;'>";
			print "<strong>".$pageorder."</strong><br />";
			print "This sidebar will be hidden when viewing this section. To change, see: ";
			print "[<a href='$PHP_SELF?$sid&amp;site=$site&amp;action=edit_section&amp;step=3&amp;edit_section=$section&amp;comingFrom=viewsite'>";
			print "display options</a>]";
			print "</div>";
			print "</td></tr></table>";
		} else if ($numContentPages == 1 && $action == 'viewsite' && $hide_sidebar != 2) {
			print "<table cellpadding='1' style='border: 1px solid;'><tr><td>";
			print "<div style='font-size: 9px;'>";
			print "This section has only one page.  If you want to hide this sidebar when viewing this section see: ";
			print "<a href='$PHP_SELF?$sid&amp;site=$site&amp;action=edit_section&amp;step=3&amp;edit_section=$section&amp;comingFrom=viewsite'>";
			print "display options</a>";
			print "</div>";
			print "</td></tr></table>";
				
		} else if ($action == 'viewsite') {
			print "<table cellpadding='1' width='95%' style='border: 1px solid;'><tr><td>";
			print "<div style='font-size: 9px;'>";
			print "<strong>".$pageorder."</strong><br />";
			print "[<a href='$PHP_SELF?$sid&amp;site=$site&amp;action=edit_section&amp;step=3&amp;edit_section=$section&amp;comingFrom=viewsite'>";
			print "display options</a>]";
			print "</div>";
			print "</td></tr></table>";
		
		
		}
		print "\n\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>\n";

		/******************************************************************************
		 * Loop through nav items and print out
		 ******************************************************************************/
		$nextorder = 0;
		
		foreach ($nav_items as $item) {
			print "\n<!-- Start Nav Item ".$item['id']." -->";
			
			$bold=0;
			print "\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>\n";
			

			/******************************************************************************
			 * print out links for new and link type pages
			 ******************************************************************************/
			
			$reorderUrl = $_SERVER['PHP_SELF']."?&amp;action=reorder&amp;site=".$site."&amp;section=".$section."&amp;page=".$page."&amp;reorderPage=".$item['id']."&amp;newPosition=";
			
			if ($item[type] == 'normal') {
			//if ($item[type] == "page" || $item[type] == "link") {

				$samepage = (isset($navtype) && ($navtype == $item[id]))?1:0;
				if (!$navtype) $samepage = ($action && ($action == $item[id]))?1:0;
				
				//if story detail then keep link to page and bold
				if ($samepage == 1 && $detail && $page==$item[id]) {
					$samepage = 0;
					$bold = 1;
				}
				
				if ($_REQUEST["tag"] ) {
					$samepage = 0;
				}

				print "\n\t\t\t\t\t\t\t<div class='nav'>";
				
				// Reorder Links
				
				if ($_REQUEST['showorder'] == "page") {
					//print "<select name='reorder2' style = 'font-size: 9px; display: none;' class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
					print "\n\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
					for ($i=0; $i<count($nav_items); $i++) {
						print "\n\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
					}
					print "\n\t\t\t\t\t\t\t\t</select>";
				}
				
				print makelink($item,$samepage," class='navlink' ",1,$bold);
				$bold=0;
				print "\n\t\t\t\t\t\t\t</div>";
			}
			
			
			/******************************************************************************
			 * print out content, rss, tag and participant type pages
			 ******************************************************************************/
			
			if ($item[type] == 'content' || $item[type] == 'rss' || $item[type] == 'tags' || $item[type] == 'participants') {
				if ($action == "viewsite") {
					print "\n\t\t\t\t\t\t\t<table width='100%' cellspacing='0' cellpadding='1' class='page_sidebar_content'>";
				} else {
					print "\n\t\t\t\t\t\t\t<table width='100%' cellspacing='0' cellpadding='0' class='page_sidebar_content'>";
				}
							
				if ($item[name]) {
					print "\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td class='heading'>";
				} else {
					print "\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td>";
				}
				
				// Reorder Links
				
				if ($_REQUEST['showorder'] == "page") {
					print "\n\t\t\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
					for ($i=0; $i<count($nav_items); $i++) {
						print "\n\t\t\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
					}
					print "\n\t\t\t\t\t\t\t\t\t\t</select>";
				}
				
				if ($item[name]) 
					print "\n\t\t\t\t\t\t\t\t\t\t".$item[name];
				
				print "\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>";
				if ($item[type] == 'content') {
					print "\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td>";
					print "\n\t\t\t\t\t\t\t\t\t\t".$item['content']."<br />";
					print "\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>";	
				} else {
					print "\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td>";
					print "\n\t\t\t\t\t\t\t\t\t\t".$item['content'];
					print "\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>";				
				}
// 				print "\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td style='border-bottom: 1px solid #$bordercolor; padding-bottom: 2px; text-align: right;'>";
				
				if ($action == "viewsite") {
					print "\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td class='nav_extras'>";
					print "\n\t\t\t\t\t\t\t".$item['extra']."<br />";
					print "\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>";
				}
				
				print "\n\t\t\t\t\t\t\t</table>";
			}
			/******************************************************************************
			 * divider
			 ******************************************************************************/
			if ($item[type] == 'divider') {
				// Reorder Links				
				if ($_REQUEST['showorder'] == "page") {
					print "\n\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
					for ($i=0; $i<count($nav_items); $i++) {
						print "\n\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
					}
					print "\n\t\t\t\t\t\t\t</select>";
				}

				print "\n\t\t\t\t\t\t\t$item[extra]<br />\n";
			}
			/******************************************************************************
			 * header
			 ******************************************************************************/
			if ($item[type] == 'heading') {

				print "\n\t\t\t\t\t\t\t<div class='heading'>";
				// Reorder Links				
				if ($_REQUEST['showorder'] == "page") {
					print "\n\t\t\t\t\t\t\t\t<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>'";
					for ($i=0; $i<count($nav_items); $i++) {
						print "\n\t\t\t\t\t\t\t\t\t<option value='".$i."'".(($i==$nextorder)?" selected":"").">".($i+1)."</option>";
					}
					print "\n\t\t\t\t\t\t\t\t</select>";
				}
				
				print "$item[name]\n";
				if ($item[extra]) 
					print "\n\t\t\t\t\t\t\t<div class='nav_extras'>$item[extra]</div>\n";
				
				print "\n\t\t\t\t\t\t\t</div>";
			}
			$nextorder++;
			
			print "\n\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>";
			
			print "\n<!-- End Nav Item -->";
		}		
	print "\n\t\t\t\t</table>\n";
	print "<div class='nav_extras'>$extra</div>\n";
}

/******************************************************************************
 * Print outs side navigation of pages from nav array
 * functions.inc.php/add_link creates nav arrays 
 * navtype = normal (page, link), rss, content, tags
 * nav_items = discreet nav items of navtype
 * extra = editing UI
 ******************************************************************************/

function side_nav($navtype,$leftnav,$subnav,$extra, $subextra, $bordercolor='000000') {
//	global $action;
	$section = $_REQUEST[section];
	$site = $_REQUEST[site];
	$detail = $_REQUEST[detail];
	$page = $_REQUEST[page];
	$action = $_REQUEST[action];
	
//	printpre($subnav);
	
	print "\n\n\n<!--   Side navigation   -->";
	print "\n<table width='100%' cellpadding='2' cellspacing='0'>\n";
	
	/******************************************************************************
	 * main left nav = sections
	 ******************************************************************************/
	$nextorder_section = 0;
	foreach ($leftnav as $item) {
		print "\n<!--   Section: ".$item[id]."   -->";
		$bold=0;
		print "\n\t<tr><td>";
	//	print $page."<br />";
	//	print $item[id];
	
		/******************************************************************************
		 * new section
		 ******************************************************************************/
		 $reorderUrl = $_SERVER['PHP_SELF']."?&amp;action=reorder&amp;site=".$site."&amp;section=".$section."&amp;page=".$page."&amp;reorderSection=".$item['id']."&amp;newPosition=";
		 
		if ($item[type] == 'normal') {
		//if ($item[type] == "page" || $item[type] == "link") {

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
			
			print "\n\t<div class='sidesectionnav'>";

			// Reorder Links				
			if ($_REQUEST['showorder'] == "section") {
				print "<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
				for ($i=0; $i<count($leftnav); $i++) {
					print "<option value='".$i."'".(($i==$nextorder_section)?" selected":"").">".($i+1)."</option>\n";
				}
				print "</select> \n";
			}

			print "\n\t\t".makelink($item,$samepage," class='navlink' ",1,$bold);
			print "\n\t</div>";
			
			/******************************************************************************
			 * check if any pages in section are of link type
			 * (checks to see if url contains "http")
			 ******************************************************************************/
			 $link_pagetype = 0;
			 $nextorder_page = 0;
			 
			 /******************************************************************************
			 * Page links (subnav)
			 ******************************************************************************/
			 
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
				print "\n\t<table width='100%' cellpadding='2' cellspacing='0'>";
				foreach ($subnav as $item) {
					print "\n<!--   Page: ".$item[id]."   -->";
					$bold=0;
					print "\n\t\t<tr>\n\t\t<td>";
					
					/******************************************************************************
					 * print out links for new page and link/url types
					 ******************************************************************************/
					$reorderUrl = $_SERVER['PHP_SELF']."?&amp;action=reorder&amp;site=".$site."&amp;section=".$section."&amp;page=".$page."&amp;reorderPage=".$item['id']."&amp;newPosition=";
					
					if ($item[type] == 'normal') {
					//if ($item[type] == "page" || $item[type] == "link") {
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
						
						print "\n\t\t<div class='subnav'>";
						
						// Reorder Links
						if ($_REQUEST['showorder'] == "page") {
							print "<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
							for ($i=0; $i<count($subnav); $i++) {
								print "<option value='".$i."'".(($i==$nextorder_page)?" selected":"").">".($i+1)."</option>\n";
							}
							print "</select>\n";
						}

						print "\n\t\t\t".makelink($item,$samepage," class='navlink' ",1,$bold);
						print "\n\t\t</div>";
						if ($samepage == 1) {
							
						}
					}
					
					/******************************************************************************
					 * print out content, rss, tag, and participant type pages
					 ******************************************************************************/

					if ($item[type] == 'content'  || $item[type] == 'rss' || $item[type] == 'tags' || $item[type] == 'participants') {
						print "\n\t\t<div class='subnav'>";
						if ($action == "viewsite") {
							print "\n\t\t\t<table width='100%' cellspacing='0' cellpadding='1' class='page_sidebar_content'>";
						} else {
							print "\n\t\t\t<table width='100%' cellspacing='0' cellpadding=>";
						}
						if ($item[name]) {
							print "\n\t\t\t\t<tr><td class='heading'>";
							
							// Reorder Links
							if ($_REQUEST['showorder'] == "page") {
								print "<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
								for ($i=0; $i<count($subnav); $i++) {
									print "<option value='".$i."'".(($i==$nextorder_page)?" selected":"").">".($i+1)."</option>\n";
								}
								print "</select>\n";
							}							
							print "$item[name]</td></tr>";
						}

						if ($item[type] == 'content') {
							print "\n\t\t\t\t<tr><td>";
							
							// Reorder Links
							if ($_REQUEST['showorder'] == "page") {
								print "<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
								for ($i=0; $i<count($subnav); $i++) {
									print "<option value='".$i."'".(($i==$nextorder_page)?" selected":"").">".($i+1)."</option>\n";
								}
								print "</select>\n";
							}							
							
							print "$item[content]<br />";
						} else {
							
							// Reorder Links
//							if ($_REQUEST['showorder'] == "page") {
//								print "<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
//								for ($i=0; $i<count($subnav); $i++) {
//									print "<option value='".$i."'".(($i==$nextorder_page)?" selected":"").">".($i+1)."</option>\n";
//								}
//								print "</select>\n";
//							}							

							print "\n\t\t\t\t<tr><td>$item[content]";				
						}
						print "\n\t\t\t\t\t<div class='nav_extras'>$item[extra]</div>";
						print "\n\t\t\t\t</td></tr>\n\t\t\t</table>";
						print "\n\t\t</div>";
					}

					if ($item[type] == 'divider') {
						print "\n\t\t<div class='subnav'>";
						// Reorder Links
						if ($_REQUEST['showorder'] == "page") {
							print "<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
							for ($i=0; $i<count($subnav); $i++) {
								print "<option value='".$i."'".(($i==$nextorder_page)?" selected":"").">".($i+1)."</option>\n";
							}
							print "</select>\n";
						}							
						print "\n\t\t$item[extra]<br />";
						print "\n\t\t</div>";
					}
					if ($item[type] == 'heading') {
						print "\n\t\t<div class='subnav'>";
						print "<div class='heading'>";
						// Reorder Links
						if ($_REQUEST['showorder'] == "page") {
							print "<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
							for ($i=0; $i<count($subnav); $i++) {
								print "<option value='".$i."'".(($i==$nextorder_page)?" selected":"").">".($i+1)."</option>\n";
							}
							print "</select>\n";
							}							
						
						print "$item[name]";
						if ($item[extra]) print "\n\t\t<div class='nav_extras'>$item[extra]</div>";
						print "\n\t\t</div>";
					}
					print "\n\t\t</td></tr>";
					$nextorder_page++;
				}
				print "\n\t</table>";
				print "\n\t<div class='nav_extras'>$subextra</div>";	
				
			}
		}
		/******************************************************************************
		 * divider
		 ******************************************************************************/
		if ($item[type] == 'divider') {
			// Reorder Links
			if ($_REQUEST['showorder'] == "page") {
				print "<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
				for ($i=0; $i<count($subnav); $i++) {
					print "<option value='".$i."'".(($i==$nextorder_page)?" selected":"").">".($i+1)."</option>\n";
				}
				print "</select>\n";
			}							

			print "\n\t<div >$item[extra]<br />";
		}
		/******************************************************************************
		 * heading
		 ******************************************************************************/
		if ($item[type] == 'heading') {
			print "\n\t<div class='nav'>";
			
			// Reorder Links
			if ($_REQUEST['showorder'] == "page") {
				print "<select name='reorder2' style = 'font-size: 9px; class='pageOrder' onchange='window.location = \"".$reorderUrl."\" + this.value;'>\n'";
				for ($i=0; $i<count($subnav); $i++) {
					print "<option value='".$i."'".(($i==$nextorder_page)?" selected":"").">".($i+1)."</option>\n";
				}
				print "</select>\n";
			}							

			print "\n\t\t$item[name]:";
			if ($item[extra]) print "\n\t\t<div class='nav_extras'>$item[extra]</div>";
			print "\n\t</div>";
		}
		print "\n\t</td></tr>";
		$nextorder_section++;
	}
	print "\n</table>";
	print "\n<div class='nav_extras'>$extra</div>";
	
}

	
?>
