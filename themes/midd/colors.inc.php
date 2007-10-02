<? // theme defaults file
	// this file contains defaults for text color, hiliting, etc etc etc
	
$_theme_colors = array(
	"blue"=>array(
			"font-size"=>			"12px",
			"input-borders"=>		"555",
			"input-size"=>			"12px",
			"box-color"=>			"efefef",
			"box-border-color"=>	"999",
			"btnlink-color"=>		"d33",
			"btnlink-border-color"=>"d55",
			"title-color"=>			"B03E22",
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
			"navlinkcolor"=>		"003366",
			"navrowcolor"=>			"D1DBE7",
			"subnavrowcolor"=>		"E0EBEB",
			"navlinkdivider"=>		"B5C6D8",
			"imagelocation"=>		"blue"
	),
	"green"=>array(
			"font-size"=>			"12px",
			"input-borders"=>		"555",
			"input-size"=>			"12px",
			"box-color"=>			"efefef",
			"box-border-color"=>	"999",
			"btnlink-color"=>		"d33",
			"btnlink-border-color"=>"d55",
			"title-color"=>			"319A9C",
			"title-under-color"=>	"aaa",
			"th-color"=>			"777",
			"th-background"=>		"000000",
			"td-color"=>			"333",
			"td0"=>					"e6e6ff",
			"td1"=>					"f6f6ff",
			"header"=>				"fff",
			"topnav"=>				"fff",
			"contentarea"=>			"fff",
			"contentinfo"=>			"999",
			"navlinkcolor"=>		"003366",
			"navrowcolor"=>			"BAD1D1",
			"subnavrowcolor"=>		"E0EBEB",
			"navlinkdivider"=>		"88B0B0",
			"navlinktitle"=>		"FFFFFF",
			"imagelocation"=>		"green"
	),
	"yellow"=>array(
			"font-size"=>			"12px",
			"input-borders"=>		"555",
			"input-size"=>			"12px",
			"box-color"=>			"efefef",
			"box-border-color"=>	"999",
			"btnlink-color"=>		"a33",
			"btnlink-border-color"=>"b55",
			"title-color"=>			"CC9900",
			"title-under-color"=>	"aaa",
			"th-color"=>			"777",
			"th-background"=>		"e9e9e9",
			"td-color"=>			"333",
			"td0"=>					"e6e6ff",
			"td1"=>					"f6f6ff",
			"header"=>				"A1AEBE",
			"topnav"=>				"909DAD",
			"contentarea"=>			"fff",
			"contentinfo"=>			"999",
			"navlinkcolor"=>		"A16600",
			"navrowcolor"=>			"FEE185",
			"subnavrowcolor"=>		"FEEBB0",
			"navlinkdivider"=>		"F3D167",
			"imagelocation"=>		"yellow"
	),
	"red"=>array(
			"font-size"=>			"12px",
			"input-borders"=>		"555",
			"input-size"=>			"12px",
			"box-color"=>			"efefef",
			"box-border-color"=>	"999",
			"btnlink-color"=>		"a33",
			"btnlink-border-color"=>"b55",
			"title-color"=>			"CC9900",
			"title-under-color"=>	"aaa",
			"th-color"=>			"777",
			"th-background"=>		"e9e9e9",
			"td-color"=>			"333",
			"td0"=>					"e6e6ff",
			"td1"=>					"f6f6ff",
			"header"=>				"A1AEBE",
			"topnav"=>				"909DAD",
			"contentarea"=>			"fff",
			"contentinfo"=>			"999",
			"navlinkcolor"=>		"990000",
			"navrowcolor"=>			"DF5C34",
			"subnavrowcolor"=>		"DF825A",
			"navlinkdivider"=>		"990000",
			"imagelocation"=>		"red"
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
	"blue"=>array(
			"bgshadow"=>		"blue",
			"bg"=>				"003366",
			"bglink"=>			"FFFFFF",
			"bgtext"=>			"CCCCCC"					
	),
	"yellow"=>array(
			"bgshadow"=>		"yellow",
			"bg"=>				"FEE185",
			"bglink"=>			"666666",
			"bgtext"=>			"999999"					
	)
);

$_textcolor = array(
	"black"=>					"444",
	"red"=>						"990000",
	"blue"=>					"000033",
	"yellow"=>					"FFCC00",
	"white"=>					"FFFFFF"
);

$_linkcolor = array(
	"blue"=>					"336699",
	"blue"=>					"003366",
	"green"=>					"006633",
	"yellow"=>					"FFCC00",
	"white"=>					"FFFFFF"
);

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

include("themes/common/nav.inc.php");