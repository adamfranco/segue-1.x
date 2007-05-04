<? // theme defaults file
	// this file contains defaults for text color, hiliting, etc etc etc
	
$_theme_colors = array(
	"white"=>array(
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
			"contentinfo"=>			"999",
			"imagelocation"=>		"white"
	),
	"yellow"=>array(
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
			"contentinfo"=>			"999",
			"imagelocation"=>		"yellow"
	),
	"black"=>array(
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
			"th-background"=>		"000000",
			"td-color"=>			"333",
			"td0"=>					"e6e6ff",
			"td1"=>					"f6f6ff",
			"header"=>				"fff",
			"topnav"=>				"fff",
			"contentarea"=>			"000000",
			"contentinfo"=>			"999",
			"imagelocation"=>		"black"
	),
		"lightblue"=>array(
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
			"th-background"=>		"FFFFCC",
			"td-color"=>			"333",
			"td0"=>					"e6e6ff",
			"td1"=>					"f6f6ff",
			"header"=>				"b88",
			"topnav"=>				"c99",
			"contentarea"=>			"edd",
			"contentinfo"=>			"990000"
	)
);

$_bordercolor = array(
	"black"=>					"000000",
	"red"=>						"990000",
	"blue"=>					"000033",
	"white"=>					"FFFFFF",
	"yellow"=>					"FFCC00",
);

$_bgcolor = array(
	"gray-white"=>array(
			"bgcolor"=>		"FFFFFF",
			"bgimage"=>		"gray-white.jpg"					
	),
	"yellow-white"=>array(
			"bgcolor"=>		"FFFFFF",
			"bgimage"=>		"yellow-white.jpg"					
	),
	"blue-white"=>array(
			"bgcolor"=>		"FFFFFF",
			"bgimage"=>		"blue-white.jpg"					
	),
	"black-gray"=>array(
			"bgcolor"=>		"000000",
			"bgimage"=>		"black-gray.jpg"					
	)	
);

$_fgcolor = array(
	"black"=>					"000000",
	"red"=>						"990000",
	"blue"=>					"000033",
	"white"=>					"FFFFFF",
	"yellow"=>					"FFCC00",
);

$_textcolor = array(
	"black"=>					"444",
	"red"=>						"990000",
	"blue"=>					"000033",
	"yellow"=>					"FFCC00",
	"white"=>					"FFFFFF"
);

$_linkcolor = array(
	"red"=>						"990000",
	"blue"=>					"003366",
	"green"=>					"006633",
	"yellow"=>					"FFCC00",
	"white"=>					"FFFFFF"
);

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

include("$themesdir/common/nav.inc.php");