<? // output modules common code

// check if we are in admin or not
if ($action == "site") { $areadmin=0; $areuser=1;}
if ($action == "viewsite") { $areadmin=1; $areuser=0;}