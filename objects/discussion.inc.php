<? /* $Id$ */

class discussion {
	var $storyid,$parentid,$id;
	var $author = array("id"=>0,"uname"=>"","fname"=>"");
	
	var $tstamp,$content,$subject,$order;
	
	var $children=array();
	var $numchildren=0,$pointer=-1,$direction=1;
	
	var $flat=false;
	var $opt = array(
			"showcontent"=>false,
			"showsubject"=>true,
			"showauthor"=>true,
			"showtstamp"=>true,
			"useoptforchildren"=>false
			);
			
	function opt($key,$val=NULL) {
		if ($val!=NULL) { // they're setting the option
			$this->opt[$key] = $val;
			return $val;
		}
		
		if (is_array($key)) {
			$this->opt = $key;
			return true;
		}
		
		return $this->opt[$key];
	}
	
	function discussion($story,$a=NULL,$parent=0) {
		if (is_array($a)) $this->_parseDBline($a);
		if (is_numeric($a)) $this->id = $a;
		if ($story) $this->storyid = $story;
		if ($parent) $this->parentid = $parent;
	}
	
	
	function getNext() {
		$this->pointer+=$this->direction;
		// if we're out of range, return false
		if (($this->direction > 0 && $this->pointer >= $this->numchildren) || ($this->direction < 0 && $this->pointer <= -1)) return false;
		return $this->children[$this->pointer];
	}
	
	function rewind() { $this->pointer = -1; }
	function reverse() { $this->direction*=-1; }
	function setstep($s) { $this->direction=$s; }
	function end() { $this->pointer = $this->numchildren; }
	function startfrombeginning() { $this->rewind(); $this->setstep(1); }
	function startfromend() { $this->end(); $this->setstep(-1); }
	
	function flat() { $this->flat = true; }
	function threaded() { $this->flat = false; }
	
	function count() { return $this->numchildren; }
	
	function fetchID($id) {
		$this->id = $id;
		$this->_fetch();
	}
	
	function fetch() { $this->_fetch(); }
	function fetchchildren() { $this->_fetchchildren(); }
	
	function _parseDBline($a) {
		$_f = array("discussion_subject"=>"subject","LK_parent"=>"parentid","FK_author"=>"authorid","FK_story"=>"storyid","discussion_id"=>"id","discussion_tstamp"=>"tstamp","discussion_content"=>"content","discussion_order"=>"order","user_uname"=>"authoruname","user_fname"=>"authorfname");
		foreach ($_f as $f=>$v) {
			if ($a[$f]) $this->$v = $a[$f];
		}
		if ($this->content) $this->content = urldecode($this->content);
		if ($this->subject) $this->subject = urldecode($this->subject);
	}
	
	function _fetch() {
		if (!$this->id) return false;
		
		$query = "
	SELECT
		discussion_tstamp,discussion_content,discussion_subject,user_uname,user_fname,FK_story,FK_author,LK_parent
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
	
	function _fetchchildren() {
		if (!$this->storyid) return false;
		if ($this->numchildren) return false; // they've already called _fetchchildren();
		
		$query = "
	SELECT
		LK_parent,discussion_id,FK_author,discussion_tstamp,discussion_content,FK_story,discussion_order,user_uname,user_fname
	FROM
		discussion
		INNER JOIN
			user
		ON
			FK_author = user_id
	WHERE
		FK_story = ".$this->storyid.
		// check if we're not top-level - if !flat disc, fetch all children, otherwise fetch all discussions
		(($this->id && !$this->flat)?" and LK_parent=".$this->id:"")
		."
	ORDER BY
		discussion_order ASC";
		
		$r = db_query($query);
		while($a = db_fetch_assoc($r)) {
			$this->children[] = &new discussion($this->storyid,$a);
			$this->numchildren++;
		}
		return true;
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
	
	function insert() { $this->_insert(); }
	function update() { $this->_update(); }
	
	function _generateSQLdata() {
		$query = "FK_author=".$this->authorid;
		if ($this->parentid) $query .= ",LK_parent=".$this->parentid;
		$query .= ",discussion_content='".urlencode($this->content)."'";
		$query .= ",discussion_subject='".urlencode($this->subject)."'";
		$query .= ",FK_story=".$this->storyid;
		$query .= ",discussion_order=".$this->order;
		return $query;
	}
	
	function outputAll($canreply=false,$owner=false) {
		// spider down and output every one
		if ($this->id) $this->_output($cr,$o);
		$this->_outputChildren($cr,$o);
	}
	
	function _outputChildren($cr,$o) {
		$this->_fetchchildren();
		if ($this->numchildren) {
			print "<tr><td><table width=100% style='padding-left:5px'>";
			for ($i=0;$i<$this->numchildren;$i++) {
				if (opt("useoptforchildren")) $this->children[$i]->opt($this->opt);
				if ($this->flat) $this->children[$i]->_output($cr,$o);
				else $this->children[$i]->outputAll($cr,$o);
			}
			print "</table></td></tr>";
		}
	}
	
	function output($canreply=false,$owner=false) {
		// print a small table that will house the discussion
		print "<table width=100%>";
		$this->_output($canreply,$owner);
		print "</table>";
	}
	
	function _output($cr,$o) {
		// output the html and stuff
		print "<tr><td>";
		$s = "<a href='#'>".$this->subject."</a>";
		$a = array();
		if (opt("showauthor")) $a[] = $this->author_fname;
		if (opt("showtstamp")) $a[] = timestamp2usdate($this->tstamp);
		$b = array();
		if ($cr) $b[] = "<a href='#'>[reply]</a>";
		if ($o) $b[] = "<a href='#'>[del]</a>";
		if (count($a) || count($b)) {
			$c = '';
			if (count($a)) $c .= "(".implode(" - ",$a).") ";
			if (count($b)) $c .= implode(" ",$b);
			print "<table width=100%><tr><td align=left>$s</td><td align=right>$c</td></tr></table>";
		} else
			print $s;
		
		// now output the content
		if (opt("showcontent")) {
			print "<tr><td>";
			print $this->content;
			print "</td></tr>";
		}
		// done
	}
}