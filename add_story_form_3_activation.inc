<? /* $Id$ */

printc("<form action='$PHP_SELF?$sid' method=post name=addform>");
printc("<input type=hidden name='step'>");

//printc("<input type=hidden name=site value='$_SESSION[settings][site]'>");
//printc("<input type=hidden name=section value=$_SESSION[settings][section]>");
//printc("<input type=hidden name=page value=$_SESSION[settings][page]>");
//if ($edit) printc("<input type=hidden name=edit_page value=$_SESSION[settings][page]>");
printc("<input type=hidden name=action value='$action'>");

//printc("<input type=hidden name=mode value=$mode>");
printc("<input type=hidden name=typeswitch value=0>");

if ($_SESSION[settings][add]) printc("<div class=title>Add Content to Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");
else  printc("<div class=title>Edit Content on Page \"".$thisPage->getField("title")."\" in Section \"".$thisSection->getField("title")."\"</div>");

//----Cancel/Save/Next/Previous Buttons -----
printc("<table width=100%><tr><td width=50%>");
	printc("<input type=button value='Cancel' onClick='cancelForm()'>\n");
printc("</td><td width=50% align='right'>");
	printc("<input type=button value='&larr; Previous' onClick='submitPrevButton()'>\n");
	printc("<input type=button value='Continue &rarr;' onClick='submitNextButton()'>\n");
printc("</td></tr><tr><td>");
printc("</td><td align='right'>");
	printc("<input type=submit name=save value='Save Now'>\n");
printc("</td></tr></table>");
printc("<input type=hidden value='0' name='cancel'>");
printc("<input type=hidden value='0' name='prevbutton'>");
printc("<input type=hidden value='0' name='nextbutton'>");
printc("<input type=hidden value='' name='tag_update'>");

printc("<div class=title style='font-size: 14px'>Activation & Availability ".helplink("daterange")."</div>");

$_SESSION[storyObj]->outputDateForm();

printc("<br /><br /><div class=title style='font-size: 14px'>Category</div>");


if (!isset($_SESSION[settings][story_categories])) {
	$record_tags = get_record_tags($_SESSION[settings][site],$_SESSION[storyObj]->id, $_SESSION[aid], "story");
	if (is_array($record_tags)) {
		foreach($record_tags as $tag) {
			$_SESSION[settings][story_categories][] = urldecode($tag);	
		}
	}
}

//printpre($_SESSION[settings][story_categories]);

//printc("Category: <input type='text' name=newcategory size=20 value='".$_SESSION[settings][newcategory]."'>");
//	printc("<input type='hidden' name=category value='".(($_SESSION[settings][category]=='yes')?"yes":"")."'>");
//	printc("<select name=category onChange='document.storyform.newcategory.value = document.storyform.category.value'>");
//printc("<option value=''".(($_SESSION[storyObj]->getField("category")=='')?" selected":"").">none");


$story_tags = "";
//printpre($_SESSION[settings][story_categories]);
if (is_array($_SESSION[settings][story_categories])) {	
	foreach ($_SESSION[settings][story_categories] as $c) {
		//printc("<option value='$c'".(($_SESSION[storyObj]->getField("category")==$c)?" selected":"").">$c");
		if ($c != " ") {
			$c = urldecode($c);
			$c = htmlspecialchars($c, ENT_QUOTES);
			$story_tags .= $c." ";
		}
	}
}

printc("Category: <input type='text' name=story_tags size=50 value='".$story_tags."'>");
printc("<br \><div class=desc>(separate categories by a space (for multiple word categories, use an underscore (e.g. Open_Source)</div>");
$site_tags = array();
$site_tags = get_tags($_REQUEST[site], $_REQUEST[section], $_REQUEST[page]);
printc("<br /><br />Site Tags<br />");
$currenttags = $_SESSION[settings][story_categories];


foreach ($site_tags as $key => $value) {
	$tag = urldecode($key);
	$urltag = urlencode($key);
	$tagcount = $value;

	if ($tagcount > 10) {
		$class = 'level_four';
	} else if ($tagcount > 5) {
		$class = 'level_three';
	} else if ($tagcount > 1) {
		$class = 'level_two';
	} else if ($tagcount = 1) {
		$class = 'level_one';
	} else {
		$class = '';
	}
	$tagcount = $value;
	if (is_array($currenttags) && (in_array($tag, $currenttags) || in_array(urlencode($tag), $currenttags))) {
		$taglist .= "<a href='#' class='tagused' title='".$tagcount." posts' onClick=\"deleteTag('".$tag."')\">$tag</a> ";
	} else {
		$taglist .= "<a href='#' class='$class' title='".$tagcount." posts' onClick=\"addTag('".$tag."')\">$tag</a> ";
	}
	
	if ($tag_update == 1) {
		$newcurrenttags = array();
		//$_SESSION[settings][story_categories_delete] = array();
		foreach ($currenttags as $currenttag) {
			if ($currenttag != $tag) {
				$newcurrenttags[] = $currenttag;
			} else {
				//$_SESSION[settings][story_categories_delete][] = $tag;
			}
			
		}
		$_SESSION[settings][story_categories] = $newcurrenttags;		
		//$tag_update = 0;
	}
}
printc("<div class='tagcloud'>");
printc($taglist);
printc("</div>");
	
printc("<br /><br />");


printc("</form>");

