<? /* $Id$ */

class ugroup {
	var $id;
/******************************************************************************
 * this class is temporary -- it's a quickie fix
 *
 * 
 ******************************************************************************/
	function getGroupID($name) {
		$query = "
			SELECT
				ugroup_id
			FROM
				ugroup
			WHERE
				ugroup_name='$name'
		";
		echo $query;
		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
			return $a['ugroup_id'];
		}
		return false;
	}
		
}