<?php

include_once("twidoooutput.sourceview.inc.php");

class mediagaleries extends sourceview {
	
	function className() {
		return "mediagaleries";
	}
	
	//gibt zurück, welche Parameter erlabut sind
	function allowedParameters() {
		
		return array_merge(parent::allowedParameters(), array(
			"addFotos", 
			"deleteItem", 
			"editItem", 
			"ajaxImageSelect"
		));
	}
	
	
	
//EDITOR AJAX FUNCTIONS
	
	function sub_ajaxImageSelect() {
		
		
		global $TWIDOO;
		
		$mediaListSmarty = new Smarty;		
		
		$getUrl = new twidoo_urlencode;
		$ppm = $getUrl->getPageParameters();
		
		$pathName = "";
		$titleName = "";
		$galeries_idName = "";
		
		$rightTable = array_keys($this->dataSetsByFunction[$this->functionNameRight()]["fieldTypes"]);
		$leftTable = array_keys($this->dataSetsByFunction[$this->functionNameLeft()]["fieldTypes"]);
			
		foreach($this->dataSetsByFunction[$this->functionNameRight()]["fieldTypes"][$rightTable[0]] as $field => $type) {
			if($type == "img") $pathName = $field;
			if($type == "title") $titleName = $field;
			if($type == $this->functionNameRightKeyLeft()) $galeries_idName = $field;
		}
			
		$mediaListSmarty->assign("key", $this->dataSetsByFunction[$this->functionNameRight()]["key"]);
		$mediaListSmarty->assign("titleName", $rightTable[0]."_".$titleName);
		$mediaListSmarty->assign("pathName", $rightTable[0]."_".$pathName);
		$mediaListSmarty->assign("mediaPath", $TWIDOO["site_baseurl"]."media/images/");
		$mediaListSmarty->assign("baseurl", $getUrl->getPage()."/".$getUrl->getPageParameters(0));
		$mediaListSmarty->assign("selectImage", true);	
			
				
		$ALLDATA = "<ul id='allMedia'>";
		//WIR GEHEN JEDE KATEGORIE DURCH
		foreach($this->dataSetsByFunction[$this->functionNameLeft()]["data"] as $category) {
			
			//Append DATA
			$DATA = array();
			foreach($this->dataSetsByFunction[$this->functionNameRight()]["data"] as $row) {
				if($row[$rightTable[0]."_".$galeries_idName] == $category[$this->idName()]) {
					array_push($DATA, $row);
					
					//img resizing
					if(is_file($TWIDOO["site_includepath"]."/media/images/".$row[$rightTable[0]."_".$pathName])) {
						twidoo_cache::cache($rightTable[0], 
						create_function('$DATA', '
						
						global $TWIDOO;
						
						$FILENAME = $DATA[0];
						$SIZE = $DATA[1];
						$thumb = new twidoo_image($FILENAME, $TWIDOO["site_includepath"]."/media/images/", true);
						$thumb->resizeExact($SIZE, $SIZE, $SIZE."_".$FILENAME);
						'), array($row[$rightTable[0]."_".$pathName], 92));
					}
				}
			}
			
			$mediaListSmarty->assign("data", $DATA);
			$mediaListSmarty->assign("title", $category[$this->titleName()]);
			
			$ALLDATA .= '<li>'.$mediaListSmarty->fetch($TWIDOO["includepath"].'/templates/outputs/uielements/elements/medialist.tpl');
		}
		
		
		$this->smarty->assign("rightContent", "
			<div id='ajaxContent'>
				<form>
					<fieldset>
						<legend>Bild</legend>
						
						".$ALLDATA."
						
						<input type='hidden' class='wym_dialog_type' value='Image'/>
						<input type='hidden' class='wym_src' value='' size='40' />
           				<input type='hidden' class='wym_alt' value='' size='40' />
						<input type='hidden' class='wym_title' value='' size='40' />
	               		<button class='wym_submit bigButton' type='button' value='Submit'><span>Einfügen</span></button>
						<button class='wym_cancel smallButtonSmall' type='button' value='Cancel'><span>Oder doch lieber nicht...</span></button>
               		</fieldset>
               	</form>
			</div>
               	");
	}	
	
	
	
// =============== 
// ! DETAIL VIEW   
// =============== 
	
	function sub_editItem() {
		
		global $TWIDOO;
		
		$rightSmarty = new Smarty;
		$url = new twidoo_urlencode;
		$rightSmarty->assign("pagePath", $url->getPage());
		$rightSmarty->assign("ca_key", $url->getPageParameters(0));
		
		$ppm = $url->getPageParameters();
		$tmp = array_keys($this->dataSetsByFunction[$this->functionNameRight()]["fieldTypes"]);
		
		
		$rightSmarty->assign("fromDatamanagement", true);
		$rightSmarty->assign("tablename", $tmp[0]);
		$rightSmarty->assign("optionalParameter", $tmp[0]);
		$rightSmarty->assign("id", $ppm[2]);
		$rightSmarty->assign("mediaPath", $TWIDOO["site_baseurl"]."media/images/");
				
		$rightSmarty->assign("key", $this->dataSetsByFunction[$this->functionNameRight()]["extra_key"]);
		$rightSmarty->assign("baseurl", $url->getPage()."/".$url->getPageParameters(0));
		$rightSmarty->assign("edit_data", $this->dataSetsByFunction[$this->functionNameRight()]["data"][$this->dataSetsByFunction[$this->functionNameRight()]["rowkeys"][$ppm[2]]]);
		
		new log($this->dataSetsByFunction[$this->functionNameRight()]["data"][$this->dataSetsByFunction[$this->functionNameRight()]["rowkeys"][$ppm[2]]]);
		
		$rightSmarty->assign("name", "Bild");
		
		$tmpFildTypes = $this->dataSetsByFunction[$this->functionNameRight()]["fieldTypes"];
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
					'), $this->dataSetsByFunction[$this->functionNameRight()]["data"]);
			}
			
			if($type == "img") {
				$img = $this->dataSetsByFunction[$this->functionNameRight()]["data"][$this->dataSetsByFunction[$this->functionNameRight()]["rowkeys"][$ppm[2]]][$tmp[0]."_".$field];
				
				$newImage = twidoo_cache::cache($tmp[0], 
					create_function('$DATA', '
						global $TWIDOO;
						
						$FILENAME = $DATA[0];
						$SIZE = $DATA[1];
						$thumb = new twidoo_image($FILENAME, $TWIDOO["site_includepath"]."/media/images/", true);
						$thumb->resizeExact($SIZE, $SIZE, $SIZE."_".$FILENAME);
									
						return $newImage;
					'), array($img, 16));
				
			}
		}
		
		$rightSmarty->assign("edit_fields", $tmpFildTypes);
		$rightSmarty->assign("labels", $this->dataSetsByFunction[$this->functionNameRight()]["labels"]);
		
		$this->smarty->assign("rightContent", $rightSmarty->fetch($TWIDOO["includepath"].'/templates/outputs/uielements/elements/edit.tpl'));
	}
	
	
	function sub_deleteItem() {
		
		global $TWIDOO;
		
		$getUrl = new twidoo_urlencode;
		$ppm = $getUrl->getPageParameters();
		
		$ID = $ppm[2];
		
		$rightTable = array_keys($this->dataSetsByFunction[$this->functionNameRight()]["fieldTypes"]);
			
		foreach($this->dataSetsByFunction[$this->functionNameRight()]["fieldTypes"][$rightTable[0]] as $field => $type) {
			if($type == "img") $pathName = $field;
			if($type == "title") $titleName = $field;
			if($type == $this->functionNameRightKeyLeft()) $galeries_idName = $field;
		}

		$PATH = $this->dataSetsByFunction[
			$this->functionNameRight()]["data"][$this->dataSetsByFunction[$this->functionNameRight()]["rowkeys"][$ID]][$rightTable[0]."_".$pathName];
		

		if(file_exists($TWIDOO["site_includepath"]."/media/images/".$PATH) && is_file($TWIDOO["site_includepath"]."/media/images/".$PATH)) {
			//DELETE FILE
			unlink($TWIDOO["site_includepath"]."/media/images/".$PATH);
		}
		
		$this->dataManagementObject->deleteData($rightTable[0], $ID);
		
		return json_encode(array("success" => false, "content" => ""));
	}
	
	
	function addFotos() {
		
		$getUrl = new twidoo_urlencode;
		$ppm = $getUrl->getPageParameters();
		
		return '
			<p style="margin-left:2%; float:left;"><a class="smallButtonBig" href="'.$getUrl->getPage().'/'.$ppm[0].'/addFotos/'.$ppm[2].'"><span>+ Fotos hinzufügen</span></a></p>
		';
	}
	
	function allFotosForThisCategory() {

		global $TWIDOO;
		
		$mediaListSmarty = new Smarty;
		
		$getUrl = new twidoo_urlencode;
		$ppm = $getUrl->getPageParameters();
		
		$pathName = "";
		$titleName = "";
		$galeries_idName = "";
		
		$rightTable = array_keys($this->dataSetsByFunction[$this->functionNameRight()]["fieldTypes"]);
			
		foreach($this->dataSetsByFunction[$this->functionNameRight()]["fieldTypes"][$rightTable[0]] as $field => $type) {
			if($type == "img") $pathName = $field;
			if($type == "title") $titleName = $field;
			if($type == $this->functionNameRightKeyLeft()) $galeries_idName = $field;
		}
		
		//Append DATA
		$DATA = array();
		foreach($this->dataSetsByFunction[$this->functionNameRight()]["data"] as $row) {
			if($row[$rightTable[0]."_".$galeries_idName] == $ppm[2]) {
				array_push($DATA, $row);
				
				//img resizing
				if(is_file($TWIDOO["site_includepath"]."/media/images/".$row[$rightTable[0]."_".$pathName])) {
					twidoo_cache::cache($rightTable[0], 
					create_function('$DATA', '
					
					global $TWIDOO;
					
					$FILENAME = $DATA[0];
					$SIZE = $DATA[1];
					$thumb = new twidoo_image($FILENAME, $TWIDOO["site_includepath"]."/media/images/", true);
					$thumb->resizeExact($SIZE, $SIZE, $SIZE."_".$FILENAME);
					'), array($row[$rightTable[0]."_".$pathName], 92));
				}
			}
		}
		
		$mediaListSmarty->assign("data", $DATA);
		$mediaListSmarty->assign("key", $this->dataSetsByFunction[$this->functionNameRight()]["key"]);
		$mediaListSmarty->assign("titleName", $rightTable[0]."_".$titleName);
		$mediaListSmarty->assign("pathName", $rightTable[0]."_".$pathName);
		$mediaListSmarty->assign("mediaPath", $TWIDOO["site_baseurl"]."media/images/");
		$mediaListSmarty->assign("baseurl", $getUrl->getPage()."/".$getUrl->getPageParameters(0));
		
		
		return $mediaListSmarty->fetch($TWIDOO["includepath"].'/templates/outputs/uielements/elements/medialist.tpl');
	}
	
	
	
	
	
	function sub_detail() {
		$this->smarty->assign("rightContent", $this->editCategory().$this->addFotos().$this->allFotosForThisCategory());
	}
	
	function sub_addFotos() {
		global $TWIDOO;
		$rightSmarty = new Smarty;
		
		$getUrl = new twidoo_urlencode;
		$ppm = $getUrl->getPageParameters();
		
		$rightSmarty->assign("baseurl", $getUrl->getPage()."/".$getUrl->getPageParameters(0));

		
		$rightSmarty->assign("id", $ppm[2]);
		$rightSmarty->assign("category_name", $this->dataSetsByFunction[$this->functionNameLeft()]["data"][$this->dataSetsByFunction[$this->functionNameLeft()]["rowkeys"][$ppm[2]]][$this->titleName()]);
		
		$rightTable = array_keys($this->dataSetsByFunction[$this->functionNameRight()]["fieldTypes"]);
		
		foreach($this->dataSetsByFunction[$this->functionNameRight()]["fieldTypes"][$rightTable[0]] as $field => $type) {
			if($type == "img") $_SESSION["pathName"] = $field;
			if($type == $this->functionNameRightKeyLeft()) $_SESSION["galeries_idName"] = $field;
		}
		
		$_SESSION["tablename"] = $rightTable[0];
		$_SESSION["galeries_id"] = $ppm[2];
		
		$rightSmarty->assign("data", json_encode($_SESSION));
		
		$this->smarty->assign("rightContent", $rightSmarty->fetch($TWIDOO["includepath"].'/templates/outputs/uielements/elements/addFotos.tpl'));
	}
	
	
	
}

return new mediagaleries();

?>