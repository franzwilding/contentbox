<?php

class editable extends twidoo_output {
	
	//gibt zurŸck, welche Parameter erlabut sind
	function allowedParameters() {
		
		return array_merge(parent::allowedParameters(), array(
			"edit", 
			"edit2", 
			"delete", 
			"multidelete"
		));
	}
	
	
	function getPageParameters() {
		global $TWIDOO;
		$getId = new twidoo_urlencode;
		$tmp_array = $getId->getPageParameters();
		$return_array = array();
		foreach($tmp_array as $key=>$value) {
			if($key > 0) array_push($return_array, $value);
		}
		return $return_array;
	}

	
	
	//EDIT FUNCTIONS
	
	function edit_name() {
		return $this->dataManagementObject->name;
	}

	
	
	function sub_edit() {
		$pageParameters = $this->getPageParameters();
		
		$this->smarty->assign("page", "edit");
		$this->smarty->assign("edit_name", $this->edit_name());
		
		$allSets = $this->dataSets;
		$setItem = array();
		$setName = "";
		
		//wenn wir den Tabellennamen Ÿbergeben bekommen
		if(array_key_exists(strtolower($pageParameters[1]), $this->dataSets)) {
			$setItem = $allSets[strtolower($pageParameters[1])];
			$setName = strtolower($pageParameters[1]);
		}
		//wenn der Name nicht angegeben wurde
		else {
			$setItem = array_pop($allSets);
			$table = array_keys($this->dataSets);
		}

		$this->smarty->assign("edit_tablename", $setName);
		$this->smarty->assign("labels", $setItem["labels"]);
		
		//DATA
		$DATA = array();		
		
		$id = 0;
		if(count($pageParameters) == 3) $id = $pageParameters[2];
		if(count($pageParameters) == 2) $id = $pageParameters[1];
		
		//wenn wir eine ID Ÿbergeben bekommen haben, dann lesen wir die Daten aus
		if(is_numeric($id) && $id > 0) {
			
			$this->smarty->assign("edit_id", $id);
			
			foreach($setItem["data"] as $key => $value) {
				if($value[$setItem["key"]] == $id)
					$DATA = $setItem["data"][$key];				
			}
		} else {
			$this->smarty->assign("edit_id", "-1");
		}
		
		
		//FIELDS
		$fields = array();
		foreach($setItem["fieldTypes"][$setName] as $label => $fieldType) {			
		
			if($fieldType == "editor") {
				$DATA[$setName."_".$label] = stripslashes($DATA[$setName."_".$label]);
			}
		
		
			//special_felder erkennt mensch daran, dass sie durch : unterteilt sind
			$test4SpecialFields = explode(":", $fieldType);
			if(count($test4SpecialFields) > 1) {
				
				
				//SELECT
				if($test4SpecialFields[0] == "select") {
					//wir holen uns bei einem select die Tabelle, die als Source angegeben wurde
					$sn = $test4SpecialFields[1];
					$selectData = $this->dataSets[$sn]["data"];
					//jetzt holen wir uns das ID-Feld
					$selectKey = $test4SpecialFields[2];
					//wenn angegebn wurde, welche Felder in der select-box stehen sollen, nehem wir diese, ansonsten title oder name
					if(count($test4SpecialFields) > 3) $selectLabels = explode(",", $test4SpecialFields[3]);
					else $selectLabels = array("title", "name");
					//jetzt bauen wir uns den Content zusammen
					$selectContent = array();
					$selectContent[0] = "Keine Auswahl";
					foreach($selectData as $row) {
						$labelString = "";
						foreach($selectLabels as $labelKey) {
							if(array_key_exists($sn."_".$labelKey, $row))
								$labelString .= $row[$sn."_".$labelKey]." ";
						}
						$selectContent[$row[$sn."_".$selectKey]] = $labelString;
					}
					//zum schluss ersetzten wir noch den $fieldWert
					$fields[$label] = array("type" => "select", "content" => $selectContent);
				}
			}
			
			//wenn wir kein special_field haben
			else $fields[$label] = $fieldType;				
		}
		$this->smarty->assign("edit_fields", $fields);
		$this->smarty->assign("edit_data", $DATA);
	}
	
	function sub_edit2() {
		global $TWIDOO;
				
		$getData = new twidoo_form("edit");
		$pageParameters = $this->getPageParameters();
		$allSets = $this->dataSets;
		$setItem = array();
		$setName = "";
				
		//wenn wir den Tabellennamen Ÿbergeben bekommen
		if(array_key_exists(strtolower($pageParameters[1]), $this->dataSets)) {
			$setItem = $allSets[strtolower($pageParameters[1])];
			$setName = strtolower($pageParameters[1]);
		}
		//wenn der Name nicht angegeben wurde
		else {
			$setItem = array_pop($allSets);
			$table = array_keys($this->dataSets);
			$setName = $table[0];
		}
		

		if($getData->formDone()) {
			//Neu anlegen
			if($getData->getValue("the_incredible_page_id") == "-1") {
				$fieldTypes = $setItem["fieldTypes"][$setName];
				$newDataRow = array();
				foreach($getData->getValues() as $key => $value) {
					if($key != "the_incredible_page_id" && $value != "" && $key != "submit") {
						switch($fieldTypes[$key]) {
							
							case "date": 
									$day = substr($value, 0, 2);
									$month = substr($value, 3, 2);
									$year = substr($value, 6, 4);
									$MYSQLTimeStamp = $year."-".$month."-".$day." 00:00:00";
									$newDataRow[$setName."_".$key] = $MYSQLTimeStamp;
								break;
							
							case "tinyint": 
									if($value == "on")
										$newDataRow[$setName."_".$key] = 1;
									else
										$newDataRow[$setName."_".$key] = 0;
								break;
							
							
							default: $newDataRow[$setName."_".$key] = $value;
								break;
						}
					
					}
				}
				//ID als NULL hinzufŸgen		
				$newDataRow[$setItem["key"]] = NULL;

				//FILES
				if(array_key_exists("edit", $_FILES)) {
					foreach($_FILES["edit"]["name"] as $key => $file) {
						if($file != "") {
							
							$folder = "";
							if(substr($_FILES["edit"]["type"][$key], 0, 5) == "image") $folder = "/images";
							
							$uploadFile = new twidoo_filesystem;
							$newDataRow[$setName."_".$key] = $uploadFile->uploadFile("edit", $key, "", $TWIDOO["site_includepath"]."/media".$folder);
						}
						else {
							$newDataRow[$setName."_".$key] = "";
						}
					}
				}
				
				$this->dataManagementObject->changeData($setName, $newDataRow);
			}
			
			//Bearbeiten
			else {
				$fieldTypes = $setItem["fieldTypes"][$setName];
				$newDataRow = array();
				foreach($getData->getValues() as $key => $value) {
										
					if($key != "the_incredible_page_id" && $value != "" && $key != "submit") {

						switch($fieldTypes[$key]) {
							
							case "date": 
									$day = substr($value, 0, 2);
									$month = substr($value, 3, 2);
									$year = substr($value, 6, 4);
									$MYSQLTimeStamp = $year."-".$month."-".$day." 00:00:00";
									$newDataRow[$setName."_".$key] = $MYSQLTimeStamp;
								break;
							
							case "tinyint": 
									if($value == "on")
										$newDataRow[$setName."_".$key] = 1;
									else
										$newDataRow[$setName."_".$key] = 0;
								break;							
							
							default: $newDataRow[$setName."_".$key] = $value;
								break;
						}
					}
				}
								
				//FILES
				if(array_key_exists("edit", $_FILES)) {
					foreach($_FILES["edit"]["name"] as $key => $file) {
						if($file != "") {
							
							$folder = "";
							if(substr($_FILES["edit"]["type"][$key], 0, 5) == "image") $folder = "/images";
							
							$uploadFile = new twidoo_filesystem;
							$newDataRow[$setName."_".$key] = $uploadFile->uploadFile("edit", $key, "", $TWIDOO["site_includepath"]."/media".$folder);
						}
					}
				}		
				$this->dataManagementObject->changeData($setName, $newDataRow);
			}
		}
		
		$getPage = new twidoo_urlencode;
		$pageURL = $getPage->getParameters();
		header('Location: '.$TWIDOO["baseurl"].$pageURL[0]."/".$pageURL[1]);
	}
	
	
	
	function sub_delete() {
		global $TWIDOO;
		
		$pageParameters = $this->getPageParameters();
		$allSets = $this->dataSets;
		$setItem = array();
		$setName = "";
		$itemID = -1;
				
		//wenn wir den Tabellennamen Ÿbergeben bekommen
		if(array_key_exists(strtolower($pageParameters[1]), $this->dataSets)) {
			$setName = strtolower($pageParameters[1]);
			$itemID = $pageParameters[2];
		}
		//wenn der Name nicht angegeben wurde
		else {
			$table = array_keys($this->dataSets);
			$setName = $table[0];
			$itemID = $pageParameters[1];
		}
		
		$this->dataManagementObject->deleteData($setName, $itemID);
		$getPage = new twidoo_urlencode;
		$pageURL = $getPage->getParameters();
		header('Location: '.$TWIDOO["baseurl"].$pageURL[0]."/".$pageURL[1]);
		
	}
	
	function sub_multidelete() {
		
		$delForm = new twidoo_form("delete");
		
		foreach($delForm->values["rows"] as $id => $status) {
			
			if($status == "on")
				$this->dataManagementObject->deleteData($delForm->values["setname"], $id);
		}
		
		$getPage = new twidoo_urlencode;
		$pageURL = $getPage->getParameters();
		header('Location: '.$TWIDOO["baseurl"].$pageURL[0]."/".$pageURL[1]);

	}
	
	
	function execute() {
		global $TWIDOO;
		$url = new twidoo_urlencode;		
		$this->smarty->assign("pagePath", $url->getPage());
		$this->smarty->assign("includepath", $TWIDOO["includepath"]);
	}
	
}

return new editable();

?>