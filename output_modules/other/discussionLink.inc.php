<?

// Print out the discussion link

	if ($action == 'viewsite')
		$discussAction = 'site';
	else if (ereg("preview_edit_as|preview_as", $action))
		$discussAction = ereg_replace("preview_edit_as", "preview_as", $action);
	else
		$discussAction = 'site';
		
	$link = "index.php?$sid&amp;action=".$discussAction."&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$o->id."&amp;detail=".$o->id;
	
	printc("<div class='contentinfo'>");
	$l = array();
	//if ($o->getField("longertext")) $l[] = $link."Full Text</a>";
	if ($o->getField("discuss")) {
		$discusslabel = $o->getField("discusslabel");
		// check if discuss label exists for backward compatibility
		if ($discusslabel) {
			printc("<a href='".$link."'>".$discusslabel."</a>");
		} else {
			printc("<a href='".$link."'>Discuss</a>");
		}
		printc(" (".discussion::generateStatistics($o->id).")");	
	}
	printc(implode(" | ",$l));
	
	printc("</div>");
?>