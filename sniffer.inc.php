<? /* $Id$ */

	$uagent = $_SERVER["HTTP_USER_AGENT"];
	//print $uagent;
/******************************************************************************
 * get browser OS informatin
 ******************************************************************************/

	//$isMac = (ereg("mac",$uagent) || ereg("Mac",$uagent));
	
	$isWin = eregi("Windows",$uagent);
	$isMac = eregi("Mac*",$uagent);
	
	// Supported PC IE version is greater than 5.5
	// Regex notes:
	// Look for "MSIE " followed by a number/period combo
	// followed by any number of any characters followed by "windows"
	$isWinIE = eregi("MSIE ([0-9.]*).*Windows",$uagent, $parts);
	$winIEVersion = $parts[1];
	
	// Supported Gecko versions: greater than or equal to 20021126
	$isGecko = eregi("(Gecko)/([0-9]{8})",$uagent, $parts);
	$geckoVersion = $parts[2];
	
	// Supported KHTML versions: none
	$isSafari = eregi("((Safari)/([0-9.]*))",$uagent, $parts);
	$safariVersion = $parts[3];

		
 /******************************************************************************
 * Determine supported browsers
 * gecko 1.3 beta or greater, msie 5.5 or greater
 ******************************************************************************/
if ($winIEVersion > 5.5 || $geckoVersion >= 20021126) {
	$supported = 1;
} else {
	$supported = 0;
} 

?>
