<? /* $Id$ */


// globals
$numerrs = 0;
$errors = array();
$error = 0;
//$errorRevertStep = 0; // step to go back to when there's an error

function printerr() {
 // this prints out all the errors stored in the error array
	global $errors,$numerrs,$message;
	$string = '';
	if ($numerrs) {
		$string .="<div class='error' align=left>The following errors occured:<ul>\n";
		foreach ($errors as $id) {
			$string .="<li class=smaller>" . $id . "\n";
		}
		$string .="</ul></div>";
	}
	if ($message) {
		$string .="<div class=desc><b>$message</b><BR></div>";
	}
	preprintc($string);
}

function printerr2() {
 // this prints out all the errors stored in the error array using "print" instead of "printc"
	global $errors,$numerrs,$message;
	$string = '';
	if ($numerrs) {
		$string .="<div class='error' align=left>The following errors occured:<ul>\n";
		foreach ($errors as $id) {
			$string .="<li class=smaller>" . $id . "\n";
		}
		$string .="</ul></div>";
	}
	if ($message) {
		$string .="<div class=desc><b>$message</b><BR></div>";
	}
	print $string;
}

function error($id, $flagonly=0) {
	global $errors,$numerrs,$error;
	if (!$flagonly) {
		$errors[] = $id;
		$numerrs++;
	}
	$error = 1;
}

function clearerror() {
	global $errors,$numerrs,$error;
	$errors = array();
	$numerrs = $error = 0;
}

// it is also possible to pass errors to scripts through the query string - check if there are any
//if (isset($error)) { error($error);}	

?>
