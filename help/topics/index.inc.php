<? 

/******************************************************************************
 * list of topics - orders by title, fist letter of title should be caps.
 ******************************************************************************/
$topics = array (
	"available" => "Hiding/Don't Hide",
	"daterange" => "Activation/Availability",
	"editors" => "Editors/Permissions",
	"filelibrary" => "Media Library",
	"headerfooter" => "Header and Footer",
	"image" => "Images",
	"listing" => "Public Listing",
	"othersites" => "Other/Old Sites",
	"pagetypes" => "Page Types",
	"sectiontypes" => "Section Types",
	"storytypes" => "Content Types",
	"sites" => "Site Types",
	"template" => "Site Templates",
	"theme" => "Themes",
	"msword" => "Copying From MS Word&reg;"
);

asort($topics);
$alphabet = array ("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");

$index = array();
foreach ($alphabet as $letter) {
	$index[$letter] = array();
	foreach ($topics as $topic => $title) {
		if (substr($title,0,1) == $letter)
			$index[$letter][$topic] = $title;
	}
}

/* print "<hr />"; */
foreach ($index as $letter => $contents) {
	if (count($contents)) {
/* 		print "<h3> $letter </h3>"; */
		print "<ul>";
		foreach ($contents as $topic => $title) {
			print "<li><a href='$_SERVER[PHP_SELF]?&amp;helptopic=$topic'>$title</a>\n";
		}
		print "</ul>";
/* 		print "<hr />"; */
	}
}

$title = "Topic Index"; 

?>