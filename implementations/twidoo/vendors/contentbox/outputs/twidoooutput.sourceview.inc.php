<?php

include_once("twidoooutput.editable.inc.php");

class sourceview extends editable {
	
	
	
	
	
// =============== 
// ! DETAIL VIEW   
// =============== 
	function sub_detail() {
		$this->smarty->assign("rightContent", $this->editCategory());
	}	
	
// ============================= 
// ! MAIN SOURCEVIEW FUNCTIONS   
// ============================= 
	function functionNameLeft() { return "category"; }
	function functionNameRight() { return "item"; }
	function functionNameRightKeyLeft() { return "category_id"; }
	
	function className() {
		return "";
	}
	
	//gibt zurŸck, welche Parameter erlabut sind
	function allowedParameters() {
		
		return array_merge(parent::allowedParameters(), array(
			"detail", 
			"instandAdd", 
			"sortIndex"
		));
	}
	
	function leftData() {
		
		global $TWIDOO;
		
		$pageParameters = $this->getPageParameters();
		
		//wir holen uns die Daten von dem DataSet mit der Funktion: category
		$DATA = $this->dataSetsByFunction[$this->functionNameLeft()]["data"];
		
		if(array_key_exists(1, $pageParameters)) {			
			foreach($DATA as $key => $row) {
				if($row[$this->idName()] == $pageParameters[1])
					$DATA[$key]["active"] = true;
			}
		}
		
		if($this->parentName() != -1)
			$DATA = $TWIDOO["helpers"]->recru(0, $DATA, $this->idName(), $this->parentName());
				
		return $DATA;
	}
	
	function parentName() {
		$tmp_array = $this->dataSetsByFunction[$this->functionNameLeft()]["fieldTypes"];
		$table_key = array_keys($tmp_array);
		$fieldTypes = array_pop($tmp_array);
		
		//wir suchen aus den fieldTypes den Title
		foreach($fieldTypes as $fieldName => $fieldType)
			if($fieldType == "parentid") return $table_key[0]."_".$fieldName;
			
		return -1;
	}
	
	function titleName() {
		
		$tmp_array = $this->dataSetsByFunction[$this->functionNameLeft()]["fieldTypes"];
		$table_key = array_keys($tmp_array);
		$fieldTypes = array_pop($tmp_array);

		
		//wir suchen aus den fieldTypes den Title
		foreach($fieldTypes as $fieldName => $fieldType)
			if($fieldType == "title"){
			
				return $table_key[0]."_".$fieldName;
			} 
		
		//wenn es den nicht gibt, suchen wir nach "title" oder "name"
		$searchWords = array(
			"title", 
			"name", 
			"Title", 
			"Name", 
			"the_title", 
			"the_name"
		);
		
		foreach($searchWords as $sWord) {
			if(array_key_exists($sWord, $fieldTypes)) 
				return $fieldTypes[$sWord];
		}
	
	
	}
	
	function idName() {
		return $this->dataSetsByFunction[$this->functionNameLeft()]["key"];
	}
	
	
	function isTree() {
		if($this->parentName() == -1) return FALSE;
		else return TRUE;
	}
	
	function sub_instandAdd() {
		
		global $TWIDOO;
		
		//wir holen uns mal den Tabellen_namen
		$tmp_array = $this->dataSetsByFunction[$this->functionNameLeft()]["fieldTypes"];
		$table_key = array_keys($tmp_array);
		$fieldTypes = array_pop($tmp_array);
		
		
		//hier holen wir uns das Feld fŸr parentid, und das fŸr sortindex
		$sortIndexField = "";
		$parentIdField = "";
		
		//wir suchen aus den fieldTypes den sortIndex
		foreach($fieldTypes as $fieldName => $fieldType) {
			if($fieldType == "sortindex") $sortIndexField = $fieldName;
			if($fieldType == "parentid") $parentIdField = $fieldName;
		}
		
		
		
		//Ansonsten per POST
		$getInstandData = new twidoo_form("add");
		if($getInstandData->formDone()) {
			
			$sortIndexResult = -1;
			
			//wenn wir einen sortIndex haben, holen wir uns den hšchsten
			if($sortIndexField != "") {
				$selectSortIndex = new twidoo_sql;
				$selectSortIndex->setQuery("SELECT ".$sortIndexField." FROM ".$table_key[0]." WHERE ".$this->parentName()."=0 ORDER BY ".$sortIndexField." DESC LIMIT 1");
				$selectSortIndex->execute();
				$sortIndexResult = $selectSortIndex->getArray();
			}
			
			$this->dataManagementObject->changeData($table_key[0], array(
				$this->titleName() => $getInstandData->getValue("title"), 
				$table_key[0]."_".$sortIndexField => $sortIndexResult[$sortIndexField]+1,
				$this->idName() => NULL
			));
		}
		
		else {
			
			//wenn wir als ein Kind einfŸgen, dann bekommen wir das ganze per GET Ÿbertragen
			$getPage = new twidoo_urlencode();
			$getPageParameters = $getPage->getParameters();
			
			//parentid-name
			$pid = substr($this->parentName(), strlen($table_key[0])+1);	
			
			//get the highest sortIndex for this parent
			$getSortIndex = new twidoo_sql;
			$getSortIndex->setQuery("SELECT ".$sortIndexField." FROM ".$table_key[0]." WHERE ".$pid."=:pid ORDER BY ".$sortIndexField." DESC LIMIT 1");
			$getSortIndex->bindParam(":pid", $getPageParameters[3]);
			$getSortIndex->execute();
			$sortIndexResult = $getSortIndex->getArray();
			
			$this->dataManagementObject->changeData($table_key[0], array(
				$this->titleName() => "Neues Element", 
				$table_key[0]."_".$sortIndexField => $sortIndexResult[$sortIndexField]+1, 
				$table_key[0]."_".$parentIdField => $getPageParameters[3], 
				$this->idName() => NULL
			));
		}

		$getPage = new twidoo_urlencode;
		$pageURL = $getPage->getParameters();
		header('Location: '.$TWIDOO["baseurl"].$pageURL[0]."/".$pageURL[1]);
	}
	
	
	function sub_sortIndex() {
		global $TWIDOO;
		$pageParameters = $this->getPageParameters();
		
		//wir holen uns mal den Tabellen_namen
		$tmp_array = $this->dataSetsByFunction[$this->functionNameLeft()]["fieldTypes"];
		$table_key = array_keys($tmp_array);
		$fieldTypes = array_pop($tmp_array);
		
		
		//hier holen wir uns das Feld fŸr parentid, und das fŸr sortindex
		$sortIndexField = "";
		$parentIdField = "";
		
		//wir suchen aus den fieldTypes den sortIndex
		foreach($fieldTypes as $fieldName => $fieldType) {
			if($fieldType == "sortindex") $sortIndexField = $fieldName;
			if($fieldType == "parentid") $parentIdField = $fieldName;
		}
		
		
		
		//Wenn wir das sortIndex Šndern
		if($pageParameters[1] == "up") {
			foreach($this->dataSets[$table_key[0]]["data"] as $key => $value) {
				if($value[$this->idName()] == $pageParameters[2]) {
					$this->dataManagementObject->changeData($table_key[0], array(
						$this->idName() => $value[$this->idName()],
						$table_key[0]."_".$sortIndexField => $value[$table_key[0]."_".$sortIndexField]-1
					));
					
					$this->dataManagementObject->changeData($table_key[0], array(
						$this->idName() => $this->dataSets[$table_key[0]]["data"][$key-1][$this->idName()],
						$table_key[0]."_".$sortIndexField => $this->dataSets[$table_key[0]]["data"][$key-1][$table_key[0]."_".$sortIndexField]+1
					));
				}
			}
			$this->dataManagementObject->updateData();
			$getPage = new twidoo_urlencode();
			$getPageParameters = $getPage->getParameters();
			header('Location: '.$TWIDOO["baseurl"].$getPageParameters[0]."/".$getPageParameters[1]);
		}
			
		elseif($pageParameters[1] == "down") {
			foreach($this->dataSets[$table_key[0]]["data"] as $key => $value) {
				if($value[$this->idName()] == $pageParameters[2]) {
					$this->dataManagementObject->changeData($table_key[0], array(
						$this->idName() => $value[$this->idName()],
						$sortIndexField => $value[$table_key[0]."_".$sortIndexField]+1
					));
					
					$this->dataManagementObject->changeData($table_key[0], array(
						$this->idName() => $this->dataSets[$table_key[0]]["data"][$key+1][$this->idName()],
						$sortIndexField => $this->dataSets[$table_key[0]]["data"][$key+1][$table_key[0]."_".$sortIndexField]-1
					));
				}
			}
			$this->dataManagementObject->updateData();
			$getPage = new twidoo_urlencode();
			$getPageParameters = $getPage->getParameters();
			header('Location: '.$TWIDOO["baseurl"].$getPageParameters[0]."/".$getPageParameters[1]);
		}
	}
	
	
	
	function editCategory() {
		global $TWIDOO;
		
		$rightSmarty = new Smarty;
		$url = new twidoo_urlencode;
		$rightSmarty->assign("pagePath", $url->getPage());
		$rightSmarty->assign("ca_key", $url->getPageParameters(0));
		
		$ppm = $url->getPageParameters();
		$tmp = array_keys($this->dataSetsByFunction[$this->functionNameLeft()]["fieldTypes"]);
		
		
		$rightSmarty->assign("fromDatamanagement", true);
		$rightSmarty->assign("tablename", $tmp[0]);
		$rightSmarty->assign("optionalParameter", $tmp[0]);
		$rightSmarty->assign("id", $ppm[2]);
				
		$rightSmarty->assign("key", $this->dataSetsByFunction[$this->functionNameLeft()]["extra_key"]);
		$rightSmarty->assign("baseurl", $url->getPage()."/".$url->getPageParameters(0));
		$rightSmarty->assign("edit_data", $this->dataSetsByFunction[$this->functionNameLeft()]["data"][$this->dataSetsByFunction[$this->functionNameLeft()]["rowkeys"][$ppm[2]]]);
		
		$rightSmarty->assign("name", "\"".$this->dataSetsByFunction[$this->functionNameLeft()]["data"][$this->dataSetsByFunction[$this->functionNameLeft()]["rowkeys"][$ppm[2]]][$this->titleName()]."\"");
		
		$tmpFildTypes = $this->dataSetsByFunction[$this->functionNameLeft()]["fieldTypes"];
		$tmpFildTypes = array_pop($tmpFildTypes);
		
		foreach($tmpFildTypes as $field => $type) {
			if($type == "title") $tmpFildTypes[$field] = "varchar";
			
			//SELECT PARENT_ID
			if($type == "parentid") {
				
				
				$tmpFildTypes[$field] = twidoo_cache::cache("pages", 
					create_function('$DATA', '
						global $TWIDOO;
												
						$type = array("type" => "selectTree", "content" => array());
						foreach($DATA as $selectRow) {
							if($selectRow["'.$this->idName().'"] != '.$ppm[2].')
								$type["content"][$selectRow["'.$this->idName().'"]] = array(
									"title" => $selectRow["'.$this->titleName().'"], 
									"id" => $selectRow["'.$this->idName().'"], 
									"parent_id" => $selectRow["'.$this->parentName().'"] 
								);
						}
						
						$type["content"] = $TWIDOO["helpers"]->recru(0, $type["content"], "id", "parent_id");
						array_unshift($type["content"], array(
							"title" => "Keine Eltern", 
							"id" => 0, 
							"parent_id" => 0));
						
						return $type;
					'), $this->dataSetsByFunction[$this->functionNameLeft()]["data"]);
			}
		}
		
		$rightSmarty->assign("edit_fields", $tmpFildTypes);
		$rightSmarty->assign("labels", $this->dataSetsByFunction[$this->functionNameLeft()]["labels"]);
		
		return $rightSmarty->fetch($TWIDOO["includepath"].'/templates/outputs/uielements/elements/edit.tpl');
	}
	
	
	
	function execute() {
		global $TWIDOO;
		$url = new twidoo_urlencode;		
		$this->smarty->assign("pagePath", $url->getPage());
		$this->smarty->assign("ca_key", $url->getPageParameters(0));
		$this->smarty->assign("sourceTableName", $this->className());
		$this->smarty->assign("includepath", $TWIDOO["includepath"]);
		$this->smarty->assign("name", $this->dataManagementObject->name);
		$this->smarty->assign("titleName", $this->titleName());
		$this->smarty->assign("idName", $this->idName());
		$this->smarty->assign("leftData", $this->leftData());
		$this->smarty->assign("isTree", $this->isTree());
		
		return $this->smarty->fetch($TWIDOO["includepath"].'/templates/outputs/sourceview.tpl');
	}

}

return new sourceview();

?>