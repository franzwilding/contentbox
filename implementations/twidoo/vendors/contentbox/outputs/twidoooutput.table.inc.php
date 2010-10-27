<?php

include_once("twidoooutput.editable.inc.php");

class table extends editable {
	
	function execute() {
		
		global $TWIDOO;
		
		parent::execute();

		//current ContentArea ID
		$getId = new twidoo_urlencode;
		$pageParameters = $getId->getPageParameters();
		$this->smarty->assign("ca_key", $pageParameters[0]);

		//Die Startausgabe
		if(!$this->withParameter) {
			//Dataset an das Template Ÿbergeben
			
			$DATA = array();
			$KEY =  array();
			$FIELDS = array();
			$LABELS = array();
			
			foreach($this->dataSets as $key => $dataSet) {
				$KEY[$key] = $dataSet["key"];
				$DATA[$key] = $dataSet["data"];
				$LABELS[$key] = $dataSet["labels"];
				$FIELDS[$key] = $dataSet["fieldTypes"][$key];
				
				foreach($LABELS[$key] as $fkey => $fvalue)
					$LABELS[$key][$key."_".$fkey] = $fvalue;
				
				foreach($FIELDS[$key] as $fkey => $fvalue)
					$FIELDS[$key][$key."_".$fkey] = $fvalue;
			}

			$DATA = twidoo_cache::cache("contentbox_contentareas", 
					create_function('$DATAARRAY', '
				
				global $TWIDOO;
				
				$DATA = $DATAARRAY[0];
				$FIELDS = $DATAARRAY[1];
				$SIZE = $DATAARRAY[2];
				
				foreach($DATA as $tableID => $table) {
					foreach($table as $dkey => $dvalue) {
						foreach($dvalue as $columnKey => $columnValue) {
							$columnValue = str_replace("\n", "", $columnValue);
							$columnValue = str_replace("\t", "", $columnValue);
							
							// HTML-Tags  rausfischen
						    preg_match_all(\'|</?[^>]*>|\', $columnValue, $tags);
						    $tags = array_unique($tags[0]);
						
						    // Platzhalter setzen
						    foreach($tags as $key => $tag) {
						        $columnValue = str_replace($tag, "", $columnValue);
						    }
							
							if($FIELDS[$tableID][$columnKey] != "img" && $FIELDS[$tableID][$columnKey] != "date" && $FIELDS[$tableID][$columnKey] != "datetime")
							$DATA[$tableID][$dkey][$columnKey] = substr($columnValue, 0, 19);
							
							if($FIELDS[$tableID][$columnKey] == "img") {
								
								if(is_file($TWIDOO["site_includepath"]."/media/images/".$columnValue)) {
									//$thumb = new twidoo_image($columnValue, $TWIDOO["site_includepath"]."/media/images/", true);
									//$thumb->resizeExact($SIZE, $SIZE, $SIZE."_".$columnValue);
								}
							}
						}
					}
				}

				return $DATA;
				
			'), array($DATA, $FIELDS, 16));
						
			$this->smarty->assign("page", "start");
			$this->smarty->assign("data", $DATA);
			$this->smarty->assign("key", $KEY);
			$this->smarty->assign("fields", $FIELDS);
			$this->smarty->assign("labels", $LABELS);
		}
		
		$this->smarty->assign("mediaPath", $TWIDOO["site_baseurl"]."media/images/");
		return $this->smarty->fetch($TWIDOO["includepath"].'/templates/outputs/table.tpl');
	}

}

return new table();

?>