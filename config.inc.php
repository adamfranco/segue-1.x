<?
// set the $sid var for easy use
$sid = SID;

// what kind of error reporting do we want? (how cluttered do we want our site to be?)
//error_reporting(0);				// none
//error_reporting(E_ERROR);		// only fatal errors
error_reporting(E_ERROR | E_WARNING);	// more errors
//error_reporting(E_ERROR | E_WARNING | E_NOTICE); 		// lots of errors
//error_reporting(E_ALL);			// all errors !!
// NOTE: the last two options here enable so much error reporting that they will
// seriously screw up MOTS's functionality with HTML forms. Just don't use them.

$this_computer = "etdev";
#$this_computer = "et";
#$this_computer = "chunky"; 

include("config_".$this_computer.".inc.php");

?>
