<?

/**
 * Check the Segue config file for missing/improper values.
 * 
 * @return void
 * @access public
 * @date 9/29/04
 */
function checkConfig () {
	global $cfg;

	if (!is_array($cfg))
		die ("<h4>ERROR! Non-existant or Mal-formed config file</h4>
			You must create a config file before <b>Segue</b> can run.
			<br /><br />
			Copy the 'config_sample.inc.php' in your segue directory to 'config.inc.php' 
			and edit the values there to point to your directories, url, and database.");
	
	$errors = array();
	
	$requiredParams = array(
							"inst_name",
							"full_uri",
							"uploaddir",
							"uploadurl",
							"defaulttheme",
							"dbhost",
							"dbuser",
							"dbdb",
							"network",
							"programtheme",
							"themesdir",
						);
	
	$requiredArrays = array(
							"vhosts",
							"inst_ips",
							"auth_mods",
							"templates",
							"months",
							"semesters"
						);
						
	foreach ($requiredParams as $param) {
		if ($cfg[$param] == "")
			$errors[] = "You must specify a value for '".$param."'.";
	}
	
	foreach ($requiredArrays as $param) {
		if ($cfg[$param] == "")
			$errors[] = "'".$param."' does not need to have values, but must be an array. Use '\$cfg['".$param."'] = array();' to specify no values.";
	}
	
	if (!in_array("db", $cfg['auth_mods']))
		$errors[] = "'db' must be one of the 'auth_mods' specified.";
		
	if (!is_writable($cfg['uploaddir']))
		$errors[] = "The upload directory, '".$cfg['uploaddir']."' does not exist or is not writable by the webserver.";
	
	
	// Print out the errors
	if (count($errors)) {
		ob_start();
		print "<h3>ERROR! You must specify values in your configuration before <b>Segue</b> can run.</h3>
			Edit the 'config.inc.php' file in your segue directory and enter values for the
			following parameters:";
		print "\n<ul>";
		foreach ($errors as $errorString) {
			print "\n<li>".$errorString."</li>";
		}
		print "\n</ul>";
		ob_end_flush();
		exit;
	}
}
?>