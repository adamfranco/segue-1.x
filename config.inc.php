<? /* $Id$ */
// set the $sid var for easy use
$sid = SID;

// what kind of error reporting do we want? (how cluttered do we want our site to be?)
//error_reporting(0);				// none
//error_reporting(E_ERROR);		// only fatal errors
error_reporting(E_ERROR | E_WARNING);	// more errors
//error_reporting(E_ERROR | E_WARNING | E_NOTICE); 		// lots of errors
//error_reporting(E_ALL);			// all errors !!
/* NOTE: the last two options here enable so much error reporting that they will */
/* seriously screw up Segue's functionality with HTML forms. Just don't use them */

//defines - DO NOT CHANGE THESE!!!
define("leftnav","leftnav",TRUE);
define("rightnav","rightnav",TRUE);
define("topnav","topnav",TRUE);

$file = file("machine");
$this_computer = trim($file[0]);
//print $this_computer;
if (!$this_computer) $this_computer = "devo";

include("configs/config_".$this_computer.".inc.php");
unset($this_computer,$file);

?>
