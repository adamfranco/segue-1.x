<? /* $Id$ */$possible_themes = array( "bevelbox"=>"Bevel Box","minimal"=>"Minimal Colors","shadowbox"=>"Shadow Box","beveledge"=>"Bevel Edge","tornpaper"=>"Torn Paper","tornpieces"=>"Torn Pieces","default"=>"Tabs","customcss"=>"Custom CSS <br/>(For users with advanced knowledge of CSS and XHTML)"/* "white"=>"White", *//* "borders"=>"Alex's Borders", *//* "breadloaf"=>"BreadLoaf" */);if (isset($additional_themes)) {	foreach($additional_themes as $dir => $title) {		$possible_themes[$dir] = $title;	}}?>