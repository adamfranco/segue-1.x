<? /* $Id$ */

class discussions {
	var $storyid;
	var $darray;
	var $pointer = -1;
	var $total = 0;
	
	function discussions($id=-1) {
		if ($id != -1) $this->storyid = $id;
	}
	
	function fetchFromDB() {
		if (!$this->storyid) return false;
		$query = "
	SELECT
		discussion_id,FK_author,discussion_tstamp,discussion_content,FK_story,discussion_order,user_uname,user_fname
	FROM
		discussion
		INNER JOIN
			user
		ON
			FK_author = user_id
	WHERE
		FK_story = ".$this->storyid."
	ORDER BY
		discussion_order DESC";
		$r = db_query($query);
		$this->darray = array();
		while ($a = db_fetch_assoc($r)) {
			$this->darray[] = &new discussion($a);
			$this->total++;
		}
		return true;
	}
	
	function getNext() {
		$this->pointer++;
		if ($this->pointer == $this->total) return false;
		return $this->darray[$this->pointer];
	}
	
	function rewind() { $this->pointer = -1; }
}
		
		
class discussion {
	var $id;
	var $tstamp;
	var $authorid,$authorfname,$authoruname;
	var $content;
	var $storyid;
	var $order;
	
	function discussion($a="none") {
		if (is_array($a)) $this->_parseDBline($a);
		if (is_numeric($a)) $this->fetchID($a);
	}
	
	function fetchID($id) {
		$this->id = $id;
		$this->_fetch();
	}
	
	function _fetch() {
		if (!$this->id) return false;
		
		$query = "
	SELECT
		discussion_tstamp,discussion_content,user_uname,user_fname,FK_story,FK_author
	FROM
		discussion
		INNER JOIN
			user
		ON
			FK_author = user_id
	WHERE
		discussion_id=".$this->id;
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		$this->_parseDBline($a);
		return true;
	}
	
	function _parseDBline($a) {
		$_f = array("FK_author"=>"authorid","FK_story"=>"storyid","discussion_id"=>"id","discussion_tstamp"=>"tstamp","discussion_content"=>"content","discussion_order"=>"order","user_uname"=>"authoruname","user_fname"=>"authorfname");
		foreach ($_f as $f=>$v) {
			if ($a[$f]) $this->$v = $a[$f];
		}
		if ($this->content) $this->content = urldecode($this->content);
	}
	
	function _insert() {
		$query = "
	SELECT
		COUNT(*) as count
	FROM
		discussion
	WHERE
		FK_story=".$this->storyid;
		$a = db_fetch_assoc(db_query($query));
		$this->order = $a['count'];
		
		
		$query = "
	INSERT INTO
		discussion
	SET
		".$this->_generateSQLdata();
		db_query($query);
		return true;
	}
	
	function _update() {
		if (!$this->id) return false;
		$query = "
	UPDATE
		discussion
	SET
		".$this->_generateSQLdata()."
	WHERE
		discussion_id=".$this->id;
		db_query($query);
		return true;
	}
	
	function insertDB() { $this->_insert(); }
	function updateDB() { $this->_update(); }
		
		
	function _generateSQLdata() {
		$query = "FK_author=".$this->authorid;
		$query .= ",discussion_content='".urlencode($this->content)."'";
		$query .= ",FK_story=".$this->storyid;
		$query .= ",discussion_order=".$this->order;
		return $query;
	}
}