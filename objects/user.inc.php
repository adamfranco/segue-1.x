<? /* $Id$ */

class user {
	var $id,$uname,$fname,$pass,$email,$type,$authtype;
	var $randpassgen = false;
	
	function user() {
		$this->authtype = 'db';
		$this->type = 'stud';
	}
	
	function fetchUserID($id) {
		$this->id = $id;
		$this->_fetch();
	}
	
	function _fetch() {
		$query = "
SELECT
	user_uname,user_fname,user_email,user_type,user_authtype
FROM user WHERE user_id=".$this->id;
		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
		} else return false;
		
		$this->uname = $a['user_uname'];
		$this->email = $a['user_email'];
		$this->type = $a['user_type'];
		$this->authtype = $a['user_authtype'];
	}
	
	function _insert() {
		if ($this->authtype != 'db') return false; // we only edit db-type users
		$data = "user_uname='".$this->uname."'";
		$data .= ",user_fname='".$this->fname."'";
		$data .= ",user_email='".$this->email."'";
		$data .= ",user_type='".$this->type."'";
		$data .= ",user_pass='".$this->pass."'";
		$data .= ",user_authtype='db'";
		
		if ($this->id) { // are we updating?
			$query = "
UPDATE
	user
	SET $data
	WHERE user_id=".$this->id;
		} else $query = "
INSERT
	INTO user
	SET $data";
		
		return db_query($query);
	}
	
	function updateDB() { $this->_insert(); }
	function insertDB() { $this->_insert(); }
	
	function randpass($chars,$nums) {
		// generate a random password with $chars characters followed by $nums digits
		$thepass = '';
		for ($i = 0; $i < $chars; $i++) {
			$ch = 'a';
			$ch = chr(rand(ord('a'),ord('z')));
			$thepass .= $ch;
		}
		
		for ($i = 0; $i < $nums; $i++) {
			$n = 0;
			$n = rand(0,9);
			$thepass .= $n;
		}
		
		$this->pass = $thepass;
		$this->randpassgen = true;
		return $thepass;
	}
	
	function sendemail() {
		if (!$this->randpassgen || $this->authtype != 'db') return false;
		
		// send an email to the user with their new random password!!
		
	}
		
}