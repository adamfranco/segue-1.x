<? // theme defaults file
	// this file contains defaults for text color, hiliting, etc etc etc
	
$_theme_colors = array(
	"white"=>array(
			"a"=>					"966",
			"a:hover"=>				"b55",
			"font-size"=>			"12px",
			"input-borders"=>		"555",
			"input-size"=>			"12px",
			"box-color"=>			"efefef",
			"box-border-color"=>	"999",
			"btnlink-color"=>		"d33",
			"btnlink-border-color"=>"d55",
			"title-color"=>			"aaa",
			"title-under-color"=>	"aaa",
			"th-color"=>			"777",
			"th-background"=>		"e9e9e9",
			"td-color"=>			"333",
			"td0"=>					"e6e6ff",
			"td1"=>					"f6f6ff",
			"header"=>				"fff",
			"topnav"=>				"fff",
			"contentarea"=>			"fff",
			"contentinfo"=>			"999"
	),
	"yellow"=>array(
			"a"=>					"624",
			"a:hover"=>				"024",
			"font-size"=>			"12px",
			"input-borders"=>		"555",
			"input-size"=>			"12px",
			"box-color"=>			"efefef",
			"box-border-color"=>	"999",
			"btnlink-color"=>		"a33",
			"btnlink-border-color"=>"b55",
			"title-color"=>			"aaa",
			"title-under-color"=>	"aaa",
			"th-color"=>			"777",
			"th-background"=>		"e9e9e9",
			"td-color"=>			"333",
			"td0"=>					"e6e6ff",
			"td1"=>					"f6f6ff",
			"header"=>				"A1AEBE",
			"topnav"=>				"909DAD",
			"contentarea"=>			"FFFFCC",
			"contentinfo"=>			"999"
	),
		"lightblue"=>array(
			"a"=>					"624",
			"a:hover"=>				"024",
			"font-size"=>			"12px",
			"input-borders"=>		"555",
			"input-size"=>			"12px",
			"box-color"=>			"efefef",
			"box-border-color"=>	"999",
			"btnlink-color"=>		"a33",
			"btnlink-border-color"=>"b55",
			"title-color"=>			"aaa",
			"title-under-color"=>	"aaa",
			"th-color"=>			"777",
			"th-background"=>		"e9e9e9",
			"td-color"=>			"333",
			"td0"=>					"e6e6ff",
			"td1"=>					"f6f6ff",
			"header"=>				"A1AEBE",
			"topnav"=>				"909DAD",
			"contentarea"=>			"ebeFf5",
			"contentinfo"=>			"999"
	),
	"olive"=>array(
			"a"=>					"026",
			"a:hover"=>				"060",
			"font-size"=>			"12px",
			"input-borders"=>		"555",
			"input-size"=>			"12px",
			"box-color"=>			"efefef",
			"box-border-color"=>	"999",
			"btnlink-color"=>		"a33",
			"btnlink-border-color"=>"b55",
			"title-color"=>			"888",
			"title-under-color"=>	"888",
			"th-color"=>			"777",
			"th-background"=>		"e9e9e9",
			"td-color"=>			"333",
			"td0"=>					"e6e6ff",
			"td1"=>					"f6f6ff",
			"header"=>				"898",
			"topnav"=>				"aba",
			"contentarea"=>			"bcb",
			"contentinfo"=>			"999"
	),
	"red"=>array(
			"a"=>					"620",
			"a:hover"=>				"048",
			"text"=>				"444",
			"font-size"=>			"12px",
			"input-borders"=>		"555",
			"input-size"=>			"12px",
			"box-color"=>			"efefef",
			"box-border-color"=>	"999",
			"btnlink-color"=>		"a33",
			"btnlink-border-color"=>"b55",
			"title-color"=>			"666",
			"title-under-color"=>	"888",
			"th-color"=>			"777",
			"th-background"=>		"e9e9e9",
			"td-color"=>			"333",
			"td0"=>					"e6e6ff",
			"td1"=>					"f6f6ff",
			"header"=>				"b88",
			"topnav"=>				"c99",
			"contentarea"=>			"edd",
			"contentinfo"=>			"999"
	)
);

$_bordercolor = array(
	"black"=>					"000000",
	"red"=>						"990000",
	"blue"=>					"000033"
);

$_bgcolor = array(
	"blue"=>array(
			"bgshadow"=>		"blue",
			"bg"=>				"5F7082",
			"bglink"=>			"FFFFFF",
			"bgtext"=>			"CCCCCC"
	),
	"gray"=>array(
			"bgshadow"=>		"gray",
			"bg"=>				"999999",
			"bglink"=>			"666666",
			"bgtext"=>			"999999"					
	),
	"white"=>array(
			"bgshadow"=>		"white",
			"bg"=>				"FFFFFF",
			"bglink"=>			"666666",
			"bgtext"=>			"999999"					
	),	
	"yellow"=>array(
			"bgshadow"=>		"yellow",
			"bg"=>				"FFFFCC",
			"bglink"=>			"666666",
			"bgtext"=>			"999999"					
	)	

);

$_textcolor = array(
	"black"=>					"444",
	"red"=>						"990000",
	"blue"=>					"000033"
);

$_linkcolor = array(
	"red"=>						"990000",
	"blue"=>					"003366",
	"green"=>					"006633"
);

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

include("themes/common/nav.inc.php");
