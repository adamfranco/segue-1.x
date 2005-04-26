<? /* $Id$ */
	// holds permissions functions and checks for people's permissions in accessing certain pages

define("SITE",0);
define("SECTION",1);
define("PAGE",2);
define("STORY",3);

define("ADD",0);
define("EDIT",1);
define("DELETE",2);
define("VIEW",3);
define("DISCUSS",4);

/******************************************************************************
 * PERMISSIONS OBJECT
 ******************************************************************************/

class permissions {

	// defines
	function ADD() {return 0;}
	function EDIT() {return 1;}
	function DELETE() {return 2;}
	function VIEW() {return 3;}
	function DISCUSS() {return 4;}
	
	// for this function to work, the form within which this is called MUST
	// be named 'addform'
	function outputForm(&$o,$d=0,$canAddEditors=true) {
		global $cfg;
		$sitename = $o->owning_site;
		if ($_SESSION[settings][edit] && !$o->builtPermissions) $o->buildPermissionsArray();
	
		// ---- Editor actions ----
		if ($_REQUEST[edaction] == 'add') {
			if (isgroup($_REQUEST[edname])) {
				$classes = group::getClassesFromName($_REQUEST[edname]);
				foreach ($classes as $class) {
					$o->addEditor($class);
				}
			} else {
				$o->addEditor($_REQUEST[edname]);
			}
		}
		
		if ($_REQUEST[edaction] == 'del') {
			$o->delEditor($_REQUEST[edname]);
		}
		
		printc("\n<input type=hidden name=edaction value=''>");
		printc("\n<input type=hidden name=edname value=''>");
		
		if ($className = getNameOfClassForSite($sitename)) {
			print "<script lang='javascript'>";
			print "function addClassEditor() {";
			print "	f = document.addform;";
			print "	f.edaction.value='add';";
			print "	f.edname.value='$className';";
			print "	f.submit();";
			print "}";
			print "</script>";
		}
		
		$a = array(0=>4,1=>1);
		
		printc("\n<style type='text/css'>th, .td0, .td1 {font-size: 10px;}</style>");
		printc("\n<table width=100% style='border: 1px solid gray'>");
		printc("\n<tr><th width=50%>name</th>	<th colspan=".($a[$d])." width=30%>permissions</th>");
		if ($canAddEditors) {
			printc("<th>del</th>");
		}
		printc("</tr>");
		printc("\n<tr><th>&nbsp;</th>".(($d)?"<th>discuss</th>":"<th>add</th><th>edit</th><th>delete</th><th>view</th>"));
		if ($canAddEditors) {
			printc("<th>&nbsp;</th>");
		}
		printc("</tr>");
		if (($edlist = $o->getEditors())) {
			$permissions = $o->getPermissions();
			if (count($edlist)) {
				$color = 0;
				foreach ($edlist as $e) {
					// :: hack ::
					// this is needed until "anonymous" discussion is enabled. could be v 2.0
					//if ($e == 'everyone') continue;
					// :: /hack ::
					
					printc("\n<tr><td class=td$color align='left'>");
					if ($e == "everyone")
						printc("Everyone (will override other entries)</td>");
					else if ($e == "institute")
						printc($cfg[inst_name]." Users</td>");
					else
						printc(ldapfname($e)." ($e)</td>");
					
					for ($i = 0; $i<5; $i++) {
						$skip = 0;$nob=0;
						if ($d && $i<4) $skip = 1;
						if (!$d && $i==4) $skip = 1;
						if (!$d && (($e == 'everyone' || $e == 'institute') && $i!=3)) $nob=1;
						if (!$skip) {
							printc("\n<td class=td$color align='center'>");
							if ($nob) printc("&nbsp;");
							else printc("\n<input type=checkbox name='permissions[$e][$i]' value=1".(($permissions[$e][$i])?" checked":"").">");
							printc("</td>");
						}
						if ($skip || $nob) {
							printc("\n<input type=hidden name='permissions[$e][$i]' value=".$permissions[$e][$i].">");
						}
					}
					
					printc("</td>");
					if ($canAddEditors) {
						printc("\n<td class=td$color align='center'>");
						if ($e == 'everyone' || $e == 'institute') printc("&nbsp;");
						else printc("<a href='#' onClick='delEditor(\"$e\");'>remove</a>");
						printc("</td>");
					}
					printc("</tr>");
					$color = 1-$color;
				}
				
			}
		} else printc("\n<tr><td class=td1 > &nbsp; </td><td class=td1 colspan=".($a[$d]+1).">no editors added</td></tr>");
		
		if ($canAddEditors) {
			printc("\n<tr><th colspan=".($a[$d]+1).">");


			$className= array();
			if (isgroup($sitename)) {
				$className = group::getClassesFromName($sitename);
			} else {
				$className = getNameOfClassForSite($sitename);
			}


			//$className = getNameOfClassForSite($sitename);
			//printpre($className);
			
			foreach ($className as $class) {
				if (!in_array($class,$edlist)) {
					printc("<a href='#' onClick='addClassEditor();'>Add students in ".$sitename."</a><br>");
					break;
				} else {
					printc("&nbsp;");
				}
			}
			
			printc("</th><th><a href='add_editor.php?$sid' target='addeditor' onClick='doWindow(\"addeditor\",400,250);'>add editor</a></th></tr>");
		}
		
		printc("\n</table>");
		
//		if ($_SESSION[settings][edit]) printc("<a href='editor_access.php?$sid&site=".$sitename."' onClick='doWindow(\"permissions\",600,400)' target='permissions'>Permissions as of last save</a>");
		
	}
	
}
