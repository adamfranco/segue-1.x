<?php

require_once(dirname(__FILE__)."/ArgumentRenderer.class.php");

/**
* Prints a single error.
* @param object Error The Error object to be printed.
* @param boolean $isDetailed If TRUE, will print the error with details.
* @access private
*/
function printError($message = "") {
	print "\n<br />\n<b>ERRORS:</b><br /><br />\n";
	print "<ul>";
	
	
	$description = nl2br($message);
	
	print "<li>\n";
	
	
	print "<b>Description</b>: ".$description."<br /><br />\n";
	

	/* get the call sequence information */
	$traceArray = debug_backtrace();
	
	if (is_array($traceArray)) {
		foreach($traceArray as $trace){
			/* each $traceArray element represents a step in the call hiearchy. Print them from bottom up. */
			$file = basename($trace['file']);
			$line = $trace['line'];
			$function = $trace['function'];
			$class = $trace['class'];
			$type = $trace['type'];
			$args = ArgumentRenderer::renderManyArguments($trace['args'], false, false);
			
			print "in <b>$file:$line</b> $class$type$function($args)<br />\n";
		}
	}
	print "<br />";
	
	print "</ul>";
}


?>