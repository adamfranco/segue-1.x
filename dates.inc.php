<? /* $Id$ */

/**
 * Break a date-string into parts
 * 
 * @param string $date
 * @return integer array OR FALSE on invalid format
 * @access public
 * @date 12/13/04
 */
function getDateArray ($date) {
	// Trim off any whitespace
	$date = trim($date);
	
	// Check for two forms of dates (with and without delimiters):
	//		2003-06-17 17:10:18
	//		20030617171018
	// As well, dates may or may not include times
	//
	$regex = "/
			^								# Match the start of the line to 
											# ensure no prior charachters
	
			([0-9]{4})						# Match the Year - #1
			-?								# Possibly followed by a dash
			
			(0?[0-9]|1[0-2])?				# Match the Month - #2
			
			-?								# Possibly followed by a dash
			(0?[0-9]|1[0-9]|2[0-9]|3[0-1])?	# Match the Day - #3
	
			(								# Begin the possible time match - #4
				\s?							# Possibly starting with a space
				(0?[0-9]|1[0-9]|2[0-3])		# Match the Hour - #5
			
				:?							# Possibly separated by a colon
				([0-5][0-9])				# Match the Minute - #6
			
				:?							# Possibly separated by a colon
				([0-5][0-9])?				# Match the second if it exists - #7
			)?								# End the time match
	
			$								# Ensure no other characters at the
											# end of the line
	/x";	// Ignore whitespace
	
	if (!preg_match ($regex, $date, $regs)) {
		return FALSE;
	}
	
	// If we have a match, build our array
	$dateArray = array(
		"Year" => intval($regs[1]),
		"Month" => intval($regs[2]),
		"Day" => intval($regs[3]),
		"Hour" => intval($regs[5]),
		"Minute" => intval($regs[6]),
		"Second" => intval($regs[7]),
	);
	
	// Make sure that we don't have a Month or Day	
	return $dateArray;
}


/**
 * Check whether or not a date string is a valid string
 * 
 * @param string $date
 * @return boolean
 * @access public
 * @date 12/13/04
 */
function valid_date($date) {	
	// first off, if they're blank, return true
	if (!$date || trim($date) == '') 
		return TRUE;
	
	// Get an array of the date parts
	if (!$dateArray = getDateArray($date))
		return FALSE;
		
	// If all parts are zero, then this date is valid:
	//		0000-00-00 00:00:00
	//		00000000000000
	if ($dateArray['Year'] == 0 
		&& $dateArray['Month'] == 0 
		&& $dateArray['Day'] == 0
		&& $dateArray['Hour'] == 0
		&& $dateArray['Minute'] == 0
		&& $dateArray['Second'] == 0)
	{
		 return TRUE;
	} 
	// If the dates are zero, but the time isn't, 
	// then this is invalid
	else if ($dateArray['Year'] == 0 
		&& $dateArray['Month'] == 0 
		&& $dateArray['Day'] == 0)
	{
		return FALSE;
	}
	
	// Check that we do not have non-zero values
	// following zero values.
	if ($dateArray['Year'] == 0
		&& ($dateArray['Month'] != 0
			|| $dateArray['Day'] != 0))
	{
		return FALSE;
	}
	
	// Check that we do not have non-zero values
	// following zero values.
	if ($dateArray['Month'] == 0
		&& $dateArray['Day'] != 0)
	{
		return FALSE;
	}
	
	// Validate that the date is a valid Gregorian date
	$month = ($dateArray['Month'])?$dateArray['Month']:1;
	$day = ($dateArray['Day'])?$dateArray['Day']:1;
	if (!checkdate($month, 
				$day,
				$dateArray['Year']))
	{
		return FALSE;
	}
		
	return TRUE;
}

/**
 * Check whether or not a date string is real, non-zero date
 * 
 * @param string $date
 * @return boolean
 * @access public
 * @date 12/13/04
 */
function real_date($date) {
	// first off, if they're blank, return false
	if (!$date || $date=='') 
		return FALSE;
	
	// Make sure that the date is good format
	if (!valid_date($date))
		return FALSE;
	
	// Get an array of the date parts
	if (!$dateArray = getDateArray($date))
		return FALSE;
	
	// Validate that the date is a valid Gregorian date
	$month = ($dateArray['Month'])?$dateArray['Month']:1;
	$day = ($dateArray['Day'])?$dateArray['Day']:1;
	if (!checkdate($month, 
				$day,
				$dateArray['Year']))
	{
		return FALSE;
	}
		
	return TRUE;
}

function usdate($date) {
	// Get an array of the date parts
	return dateArrayToUSDate(getDateArray($date));
}

function datetime2usdate($dt,$include_s=0) {
	// Get an array of the date parts
	return dateArrayToUSDate(getDateArray($dt), TRUE, $include_s);
}

function timestamp2usdate($t,$include_s=0) {
	// Get an array of the date parts
	return dateArrayToUSDate(getDateArray($t), TRUE, $include_s);
}

function dateArrayToUSDate($dateArray, $include_time=0, $include_s=0) {
	global $cfg;
	
// 	print $dateArray['Hour']." ".$dateArray['Minute']." ".$dateArray['Second']." ".
// 			(($dateArray['Year'] && !$dateArray['Month'])?1:$dateArray['Month'])." ".$dateArray['Day']." ".$dateArray['Year']."\n";
	
	$timestamp = mktime(
			$dateArray['Hour'], 
			$dateArray['Minute'], 
			$dateArray['Second'], 
			($dateArray['Year'] && !$dateArray['Month'])?1:$dateArray['Month'], // Must be non-zero
			$dateArray['Day'], 
			$dateArray['Year']);
			
	// Set our date format
	if ($cfg['date_format']) {
		$dateFormat = $cfg['date_format'];
	} else {
		$dateFormat = "n/j/Y";
	}
	
	if ($include_time) {
		// Set our time format
		if ($cfg['time_format']) {
			$timeFormat = $cfg['time_format'];
		} else {
			$timeFormat = "g:i A";
		}
		
		if ($include_s) {
				$timeFormat = ereg_replace("([^gGhH]?)(i)", "\\1\\2\\1s", $timeFormat);
		}
		
		return date($dateFormat, $timestamp)." ".date($timeFormat, $timestamp);
	} else {
		return date($dateFormat, $timestamp);
	}
}

// checks if we are currently in this range of dates
function indaterange($date1, $date2) {
	if (real_date($date1)) {
		$dateArray = getDateArray($date1);
		$unix1 = mktime(
			$dateArray['Hour'], $dateArray['Minute'], $dateArray['Second'], 
			$dateArray['Month'], $dateArray['Day'], $dateArray['Year']);
	} else 
		$unix1=0;
	
	if (real_date($date2)) {
		$dateArray = getDateArray($date2);
		$unix2 = mktime(
			$dateArray['Hour'], $dateArray['Minute'], $dateArray['Second'], 
			$dateArray['Month'], $dateArray['Day'], $dateArray['Year']);
	} else 
		$unix2=0;
		
	$ctime = time();

	return (($unix1==0 || $unix1<=$ctime) && ($unix2==0 || $unix2>=$ctime));
}

function txtdaterange($date1,$date2) {
	if (nulldate($date1) && nulldate($date2)) return "always";
	if (nulldate($date1) && !nulldate($date2)) return "before ".usdate($date2);
	if (!nulldate($date1) && nulldate($date2)) return "after ".usdate($date1);
	if (!nulldate($date1) && !nulldate($date2)) return "between ".usdate($date1)." and ".usdate($date2);
	return "????";
}

function nulldate($date) {
	if ($date == '0000-00-00') 
		return 1;
	else
		return 0;
}

?>
