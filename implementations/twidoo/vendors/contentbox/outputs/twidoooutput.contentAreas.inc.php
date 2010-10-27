<?php

class contentAreas extends twidoo_output
{
	
	
	//gibt zurück, welche Parameter erlabut sind
	function allowedParameters() {
		return array(
			"edit",
			"edit2",
			"delete"
		);
	}
	
	//Löschen
	function sub_delete() {
		global $TWIDOO;
		
		$pageParameters = $this->urlEncode->getParameters();
		
		//verschiedene Tabellen müssen nach einem Löschen durchsucht werden
		$this->dataManagementObject->deleteData("contentbox_contentareas", $pageParameters[2]);
		
		foreach($this->dataSets["contentareas_fieldtypes"]["data"] as $fieldTypeRow) {
			if($fieldTypeRow["contentareas_fieldtypes_ca_id"] == $pageParameters[2])
				$this->dataManagementObject->deleteData("contentareas_fieldtypes", $fieldTypeRow["contentareas_fieldtypes_id"]);
		}
		
		foreach($this->dataSets["contentareas_keys"]["data"] as $keysRow) {
			if($keysRow["contentareas_keys_ca_id"] == $pageParameters[2])
				$this->dataManagementObject->deleteData("contentareas_keys", $keysRow["contentareas_keys_id"]);
		}
		
		foreach($this->dataSets["contentareas_tables"]["data"] as $tablesRow) {
			if($tablesRow["contentareas_tables_ca_id"] == $pageParameters[2])
				$this->dataManagementObject->deleteData("contentareas_tables", $tablesRow["contentareas_tables_id"]);
		}
		
		foreach($this->dataSets["contentareas_userauth_contentareas"]["data"] as $tablesRow) {
			if($tablesRow["contentareas_userauth_contentareas_id_contentarea"] == $pageParameters[2])
				$this->dataManagementObject->deleteData("contentareas_userauth_contentareas", $tablesRow["contentareas_userauth_contentareas_id"]);
		}
		
		$getPage = new twidoo_urlencode;
		header('Location: '.$TWIDOO["baseurl"].$getPage->getPage());
	}


	
	//Editpage2
	function sub_edit() {
		$this->smarty->assign("page", "edit");
		
		$pageParameters = $this->urlEncode->getPageParameters();
			
			
		
		
		//Alle Outputs holen
		$getOutputs = new twidoo_filesystem("outputs");
		$outputs = array();
		foreach($getOutputs->getArray() as $output) {
			//contentAreas soll nicht ausgewählt werden können
			if($output != "twidoooutput.contentAreas.inc.php" && $output != "twidoooutput.persones.inc.php") {
				$outputArray = explode(".", $output);
				array_push($outputs, $outputArray[1]);
			}
		}
		
		$this->smarty->assign("outputs", $outputs);	
			
			
			
				
		
		//BEARBEITEN
		if(is_numeric($pageParameters[1]) && $pageParameters[1] > 0) {
			
			//DAS DATASET
			$editDataSet = $this->dataSets["contentbox_contentareas"]["data"][$this->dataSets["contentbox_contentareas"]["rowkeys"][$pageParameters[1]]];
			
			
			//STANDARDWERTE ÜBERGEBEN
			$this->smarty->assign("edit_id", $editDataSet["contentbox_contentareas_id"]);
			$this->smarty->assign("edit_name", $editDataSet["contentbox_contentareas_name"]);
			$this->smarty->assign("edit_output", $editDataSet["contentbox_contentareas_output"]);
			
			
			//TABELLEN ÜBERGEBEN
			$tableArray = array();
			foreach($this->dataSets["contentareas_tables"]["data"] as $table) {
				if($table["contentareas_tables_ca_id"] == $editDataSet["contentbox_contentareas_id"]) {
					
					foreach($this->dataSets["contentareas_keys"]["data"] as $key) {
						if(
							$table["contentareas_tables_ca_id"] == $key["contentareas_keys_ca_id"] && 
							$key["contentareas_keys_the_table"] == $table["contentareas_tables_the_table"]
						)
							$table["table_key"] = $key["contentareas_keys_the_key"];
					}
					
					$table["table_fieldTypes"] = array();
					
					foreach($this->dataSets["contentareas_fieldtypes"]["data"] as $fieldTypes) {
						if(
							$table["contentareas_tables_ca_id"] == $fieldTypes["contentareas_fieldtypes_ca_id"] && 
							$fieldTypes["contentareas_fieldtypes_the_table"] == $table["contentareas_tables_the_table"]
						)
							array_push($table["table_fieldTypes"], $fieldTypes);
					}
					
					
					array_push($tableArray, $table);
					
					
				}
			}
						
			$this->smarty->assign("tableData", $tableArray);
				
		}
		
		else {
			//NEUANLEGEN
			$editForm = new twidoo_form("edit");
			$editForm->setRequired("name");
					
			//Wenn alles passt, legen wir eine neue Content-Area an
			if($editForm->formDone()) {
				
				$pageParameters = $this->urlEncode->getParameters();
					
	
							
				//wenn die content-ares neu erstellt werden muss
				if(!array_key_exists("id", $editForm->getValues())) {				
					
					//wir schauen uns die position an
					
					//wenn diese 0 ist, bekommt dieser Eintrag sortIndex = 1, alle anderen +1
					if($editForm->getValue("position") == 0) {
						$sortIndex = 0;
						
						foreach($this->dataSets["contentbox_contentareas"]["data"] as $row) {
							$this->dataManagementObject->changeData("contentbox_contentareas", array(
								"contentbox_contentareas_sortIndex" => $row["contentbox_contentareas_sortIndex"]+1, 
								"contentbox_contentareas_id" => $row["contentbox_contentareas_id"] 
							));
						}
					} else {
						
						//Ansonsten bekommen alle ab index+1 + 1, der neue eintrag bekommt index+1
						foreach($this->dataSets["contentbox_contentareas"]["data"] as $dataSetPosition => $row) {
							if($dataSetPosition > $this->dataSets["contentbox_contentareas"]["rowkeys"][$editForm->getValue("position")]) {
								$this->dataManagementObject->changeData("contentbox_contentareas", array(
									"contentbox_contentareas_sortIndex" => $row["contentbox_contentareas_sortIndex"]+1, 
									"contentbox_contentareas_id" => $row["contentbox_contentareas_id"] 
								));
							} elseif($dataSetPosition == $this->dataSets["contentbox_contentareas"]["rowkeys"][$editForm->getValue("position")]) {
								//wenn wir genau den Eintrag haben, holen wir uns den Sortindex
								$sortIndex = $row["contentbox_contentareas_sortIndex"];
							}
						}
					}
					
					
					
					//Dann fügen wir die neue Area ein
					$this->dataManagementObject->changeData("contentbox_contentareas", array(
						"contentbox_contentareas_sortIndex" => $sortIndex+1, 
						"contentbox_contentareas_output" => $editForm->getValue("output"), 
						"contentbox_contentareas_id" => NULL, 
						"contentbox_contentareas_name" => $editForm->getValue("name")
					));
					$this->dataManagementObject->updateData();
					
					//wenn wir gerade eben ein neues content-Area-Objekt erstellt haben, dann müssen wir uns die ID holen, und ausgeben
					$id = 0;
					foreach($this->dataSets["contentbox_contentareas"]["data"] as $newestRow) {
						if($newestRow["contentbox_contentareas_sortIndex"] == $sortIndex+1)
							$id = $newestRow["contentbox_contentareas_id"];
					}
					$this->smarty->assign("edit_id", $id);
					$this->smarty->assign("edit_name", $editForm->getValue("name"));
					$this->smarty->assign("edit_output", $editForm->getValue("output"));			
				}
			}
		}
	}
	
	
	function sub_edit2() {
		$editForm = new twidoo_form("edit");
		$editForm->setRequired("name");
						
		if($editForm->formDone()) {
			
			//UPDATE NAME & OUTPUT
			$this->dataManagementObject->changeData("contentbox_contentareas", array(
				"contentbox_contentareas_output" => $editForm->getValue("output"), 
				"contentbox_contentareas_id" => $editForm->getValue("id"), 
				"contentbox_contentareas_name" => $editForm->getValue("name")
			));
			$this->dataManagementObject->updateData();
			
			//wir gehen die Tabellen durch
			foreach($editForm->getValue("tables") as $tableKey => $table) {
				
				
				//ALLE BESTEHENDEN EINTRÄGE LÖSCHEN
				$deleteTables = new twidoo_sql;
				$deleteTables->setQuery("DELETE FROM contentareas_fieldtypes WHERE ca_id=:cid AND the_table=:table");
				$deleteTables->bindParam(":cid", $editForm->getValue("id"));
				$deleteTables->bindParam(":table", $tableKey);
				$deleteTables->execute();
				
				$deleteTables = new twidoo_sql;
				$deleteTables->setQuery("DELETE FROM contentareas_keys WHERE ca_id=:cid AND the_table=:table");
				$deleteTables->bindParam(":cid", $editForm->getValue("id"));
				$deleteTables->bindParam(":table", $tableKey);
				$deleteTables->execute();
				
				$deleteTables = new twidoo_sql;
				$deleteTables->setQuery("DELETE FROM contentareas_tables WHERE ca_id=:cid AND the_table=:table");
				$deleteTables->bindParam(":cid", $editForm->getValue("id"));
				$deleteTables->bindParam(":table", $tableKey);
				$deleteTables->execute();
				
				
				//TableFields
				foreach($table["fields"] as $fieldID => $field) {
					$this->dataManagementObject->changeData("contentareas_fieldtypes", array(
						"contentareas_fieldtypes_the_table" => $tableKey, 
						"contentareas_fieldtypes_field" => $fieldID, 
						"contentareas_fieldtypes_type" => $field["type"], 
						"contentareas_fieldtypes_ca_id" => $editForm->getValue("id"), 
						"contentareas_fieldtypes_label" => $field["name"], 
						"contentareas_fieldtypes_id" => NULL
					));
				}
				
				//TableKey einfügen, falls vorhanden
				if($table["key"] != "") {
					$this->dataManagementObject->changeData("contentareas_keys", array(
						"contentareas_keys_the_table" => $tableKey, 
						"contentareas_keys_the_key" => $table["key"], 
						"contentareas_keys_ca_id" => $editForm->getValue("id"), 
						"contentareas_keys_id" => NULL
					));
				}
				
				//Table einfügen
				$this->dataManagementObject->changeData("contentareas_tables", array(
					"contentareas_tables_the_table" => $tableKey, 
					"contentareas_tables_ca_id" => $editForm->getValue("id"), 
					"contentareas_tables_the_where" => $table["where"], 
					"contentareas_tables_the_orderby" => $table["orderby"], 
					"contentareas_tables_the_function" => $table["function"], 
					"contentareas_tables_id" => NULL
				));
			}
			
			$this->dataManagementObject->updateData();
		}
		
		
		
		$getPage = new twidoo_urlencode;
		header('Location: '.$TWIDOO["baseurl"].$getPage->getPage());
	}
	
	
	
	
		
	function changeSortIndex() {
		global $TWIDOO;
		$pageParameters = $this->getPageParameters();
		
		//Wenn wir das sortIndex ändern
		if($pageParameters[0] == "up") {
			foreach($this->dataSets["contentbox_contentareas"]["data"] as $key => $value) {
				if($value["contentbox_contentareas_id"] == $pageParameters[1]) {
					$this->dataManagementObject->changeData("contentbox_contentareas", array(
						"contentbox_contentareas_id" => $value["contentbox_contentareas_id"],
						"contentbox_contentareas_sortIndex" => $value["contentbox_contentareas_sortIndex"]-1
					));
					
					$this->dataManagementObject->changeData("contentbox_contentareas", array(
						"contentbox_contentareas_id" => $this->dataSets["contentbox_contentareas"]["data"][$key-1]["contentbox_contentareas_id"],
						"contentbox_contentareas_sortIndex" => $this->dataSets["contentbox_contentareas"]["data"][$key-1]["contentbox_contentareas_sortIndex"]+1
					));
				}
			}
			$this->dataManagementObject->updateData();
			$getPage = new twidoo_urlencode;
			header('Location: '.$TWIDOO["baseurl"].$getPage->getPage());
		}
			
		elseif($pageParameters[0] == "down") {
			foreach($this->dataSets["contentbox_contentareas"]["data"] as $key => $value) {
				if($value["contentbox_contentareas_id"] == $pageParameters[1]) {
					$this->dataManagementObject->changeData("contentbox_contentareas", array(
						"contentbox_contentareas_id" => $value["contentbox_contentareas_id"],
						"contentbox_contentareas_sortIndex" => $value["contentbox_contentareas_sortIndex"]+1
					));
					
					$this->dataManagementObject->changeData("contentbox_contentareas", array(
						"contentbox_contentareas_id" => $this->dataSets["contentbox_contentareas"]["data"][$key+1]["contentbox_contentareas_id"],
						"contentbox_contentareas_sortIndex" => $this->dataSets["contentbox_contentareas"]["data"][$key+1]["contentbox_contentareas_sortIndex"]-1
					));
				}
			}
			$this->dataManagementObject->updateData();
			$getPage = new twidoo_urlencode;
			header('Location: '.$TWIDOO["baseurl"].$getPage->getPage());
		}
	}	
	

	function execute() {
		global $TWIDOO;
		$this->changeSortIndex();
		
		//Die Startausgabe
		if(!$this->withParameter) {	
			$this->smarty->assign("page", "start");
			$this->smarty->assign("data", $this->dataSets["contentbox_contentareas"]["data"]);
			
			
			foreach($this->dataSets["contentbox_contentareas"]["data"] as $key => $dataSet) {
				
				$tableArray = array();
				foreach($this->dataSets["contentareas_tables"]["data"] as $table) {
					if($table["contentareas_tables_ca_id"] == $dataSet["contentbox_contentareas_id"])
						array_push($tableArray, $table["contentareas_tables_the_table"]);
				}
				
				$this->dataSets["contentbox_contentareas"]["data"][$key]["tables"] = $tableArray;
			}
			
			$this->smarty->assign("data", $this->dataSets["contentbox_contentareas"]["data"]);
			
			//Alle Outputs holen
			$getOutputs = new twidoo_filesystem("outputs");
			$outputs = array();
			foreach($getOutputs->getArray() as $output) {
				//contentAreas soll nicht ausgewählt werden können
				if($output != "twidoooutput.contentAreas.inc.php" && $output != "twidoooutput.persones.inc.php") {
					$outputArray = explode(".", $output);
					array_push($outputs, $outputArray[1]);
				}
			}
			
			$this->smarty->assign("outputs", $outputs);
		}
		
		$getPage = new twidoo_urlencode;
		$this->smarty->assign("includepath", $TWIDOO["includepath"]);
		$this->smarty->assign("pagePath", $getPage->getPage()."/");
		return $this->smarty->fetch($TWIDOO["includepath"].'/templates/outputs/ca_configuration.tpl');
	}
}

return new contentAreas();

?>