<? // some functions for working with dates

function valid_date($date) {
  // first off, if they're blank, return true
  if (!$date || $date=='') return true;
  if (!ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date, $regs)) {
    return false;
  }
  if ($regs[1]==0 && $regs[2]==0 && $regs[3]==0) return true;
  if ($regs[2]>12 || $regs[3]>31) return false;
  if (!checkdate((integer)$regs[2],(integer)$regs[3],(integer)$regs[1])) return false;
  return true;
}

function real_date($date) {
  // first off, if they're blank, return false
  if (!$date || $date=='') return false;
  if (!ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date, $regs)) {
    return false;
  }
  if ($regs[2]>12 || $regs[3]>31) return false;
  if ($regs[3]==0 || $regs[2]==0) return false;
  if (!checkdate((integer)$regs[2],(integer)$regs[3],(integer)$regs[1])) return false;
  return true;
}

function usdate($date) {
  ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date, $regs);
  $newdate='';
  if ($regs[1]>1900) $regs[1]-=1900;
  if ($regs[1]>=100) $regs[1]-=100;
  $newdate = ((integer)$regs[2])."/".((integer)$regs[3])."/".sprintf("%02d",$regs[1]);
  return $newdate;
}

function datetime2usdate($dt,$include_s=0) {
	ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})",$dt,$regs);
	return regs2usdate($regs,$include_s);
}

function timestamp2usdate($t,$include_s=0) {
//	return date("n/d/y g:i".(($include_s)?":s":"")." A",$t);
	ereg("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})",$t,$regs);
	return regs2usdate($regs,$include_s);
}

function regs2usdate($regs,$include_s=0) {
	$year = (integer)$regs[1];
	$month = (integer)$regs[2];
	$day = (integer)$regs[3];
	if ($year > 1900) $year-=1900;
	if ($year >=100) $year-=100;
	$date = $month."/".sprintf("%02d",$day)."/".sprintf("%02d",$year);
	
	$hours = (integer)$regs[4];
	$mins = (integer)$regs[5];
	$sec = (integer)$regs[6];
	$ampm = "AM";
	if ($hours > 12) { $hours -=12; $ampm = "PM"; }
	if ($hours == 12) $ampm = "PM";
	if ($hours == 0) $hours = 12;
	$time = "$hours:".sprintf("%02d",$mins).(($include_s)?":".sprintf("%02d",$sec):"")." $ampm";
	return $date . " " . $time;
}

function valid_time($time) {
  if (!$time || $time=='') return true;
  if (!ereg("([0-9]{1,2}):([0-9]{1,2})", $time, $regs)) return false;
  if ($regs[2]>59) return false;
  return true;
}

function time2long($time) {
  ereg("([0-9]{1,2}):([0-9]{1,2})", $time, $regs);
  $t = $regs[1]*60 + $regs[2];
  return $t;
}

function long2time($time) {
  $hrs = $time/60;
  for ($i=0;$i<$hrs;$i++);
  if ($i != $hrs) $i--;
  $mins= $time % 60;
  return sprintf("%02d:%02d",$i,$mins);
}

// checks if we are currently in this range of dates
function indaterange($date1, $date2) {
	if (real_date($date1)) {
		ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date1, $regs);
//		print "m: ".$regs[2]." d: ".$regs[3]." y: ".$regs[1];
		$unix1=mktime(0,0,0,$regs[2],$regs[3],$regs[1]);
//		print "<BR>time1: $unix1<BR>";
//		print strftime("%D",$unix1)."<BR>";
	} else $unix1=0;
	
	if (real_date($date2)) {
		ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date2, $regs);
//		print "m: ".$regs[2]." d: ".$regs[3]." y: ".$regs[1];
		$unix2=mktime(23,59,59,$regs[2],$regs[3],$regs[1]);
//		print "<BR>time1: $unix2<BR>";
//		print strftime("%D",$unix2)."<BR>";
	} else $unix2=0;
//	print "ctime: ".time()."<BR>";
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
	if ($date == '0000-00-00') return 1;
	return 0;
}

?>
