<? // segue common functions for sites, sections, pages, stories

class segue {
	function setActivateDate($year,$month,$day) {
		// test to see if it's a valid date
		if (!checkdate($month,$day,$year)) {
			error("The activate date you entered is invalid. It has not been set.");
			return false;
		}
		$this->setField("activatedate",$year."-".$month."-".$day);
		return true;
	}
	
	function setDeactivateDate($year,$month,$day) {
		// test to see if it's a valid date
		if (!checkdate($month,$day,$year)) {
			error("The deactivate date you entered is invalid. It has not been set.");
			return false;
		}
		$this->setField("deactivatedate",$year."-".$month."-".$day);
		return true;
	}
	
	function parseMediaTextForEdit($field) {
		$this->data[$field] = ereg_replace("src=('{0,1})####('{0,1})","####",$this->data[$field]);
		$textarray1 = explode("####", $this->data[$field]);
		if (count($textarray1) > 1) {
			for ($i=1; $i<count($textarray1); $i+=2) {
				$id = $textarray1[$i];
				$filename = db_get_value("media","name","id=$id");
				$dir = db_get_value("media","site_id","id=$id");
				$filepath = $uploadurl."/".$dir."/".$filename;
				$textarray1[$i] = "&&&& src='".$filepath."' @@@@".$id."@@@@ &&&&";
			}		
			$this->data[$field] = implode("",$textarray1);
		}
	}
	
	function parseMediaTextForDB($field) {
		$textarray1 = explode("&&&&", $this->data[$field]);
		if (count($textarray1) > 1) {
			for ($i=1; $i<count($textarray1); $i=$i+2) {
				$textarray2 = explode("@@@@", $textarray1[$i]);
				$id = $textarray2[1];
				$textarray1[$i] = "src='####".$id."####'";
			}		
			$this->data[$field] = implode("",$textarray1);
		}
	}
}