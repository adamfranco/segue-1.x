<?

$pagetitle = "Segue: Version 1.0.3 Updater";

/******************************************************************************
 * Make sure that the two new templates exist and add them if they don't.
 ******************************************************************************/
$query = "
	SELECT
		fk_site
	FROM
		slot
	WHERE
		slot_name = 'template4'
";
$r = db_query($query);

// if the template doesn't exist, insert it.
if (!db_num_rows($r)) {

// Insert the site
	$query = "
		INSERT INTO
			site
		SET
			site_title = 'Advanced: Single Section',
			site_theme = 'minimal',
			site_listed = 0
	";
	db_query($query);
	$siteId = lastid();

// Insert the slot
	$query = "
		INSERT INTO
			slot
		SET
			slot_name = 'template4',
			FK_owner = 1,
			FK_assocsite = NULL,
			FK_site = ".$siteId.",
			slot_type = 'system'			
	";
	db_query($query);
	$slotId = lastid();
}