<?php

	global $TWIDOO;
	$smarty = new Smarty;
			
	//Die ID bekommen
	$getID = new twidoo_urlencode;
	$parameter = $getID->getPageParameters();
	$ID = $parameter[0];

	
	//Permission checken
	$mylogin = new twidoo_login('contentbox_userauth', 'contentbox_userauth', 'email', 'password', 'hash');
	$mylogin->setHashEncode('sha1');
		
	//den Login-Namen aus der DB holen, und ausgeben
	$getUsername = new twidoo_sql;
	$getUsername->setQuery("SELECT id, admin FROM contentbox_userauth WHERE email=:email");
	$getUsername->bindParam(":email", $mylogin->getUsername());
	$getUsername->execute();
	$name = $getUsername->getArray(array(), "table", 1);
	
	if(is_numeric($ID) && $ID > 0) {		
		//Die ContentAreas aus der Datenbank holen, und als MenŸ ausgeben
		
		if($name["admin"]) {
			
			$contentAreas = new twidoo_sql;
			$contentAreas->setQuery("SELECT id FROM contentbox_contentareas WHERE id = :id");
			$contentAreas->bindParam(":id", $ID);
			$contentAreas->execute();
		} else {
			
			$contentAreas = new twidoo_sql;
			$contentAreas->setQuery("SELECT 
			id FROM contentareas_userauth_contentareas WHERE id_user = :id AND id_contentarea = :ca");
			$contentAreas->bindParam(":id", $name["id"]);
			$contentAreas->bindParam(":ca", $ID);	
			$contentAreas->execute();	
		}	
				
		if($contentAreas->rowCount() > 0) {
			//Basics holen
			$getTheContentArea = new twidoo_sql;
			$getTheContentArea->setQuery("SELECT output, name FROM contentbox_contentareas WHERE id=:id");
			$getTheContentArea->bindParam(":id", $ID);
			$getTheContentArea->execute();
				$array = $getTheContentArea->getArray();
				if(array_key_exists("output", $array))
					$output = $array["output"];
				else
					$output = "";
					
				if(array_key_exists("name", $array))	
					$ca_name = $array["name"];
				else
					$ca_name = "";
			
			//Tabellen holen
			$getTables = new twidoo_sql;
			$getTables->setQuery("SELECT the_table, the_where, the_orderby, the_function FROM contentareas_tables WHERE ca_id = :id");
			$getTables->bindParam(":id", $ID);
			$getTables->execute();
			$tables = $getTables->getArray();
			
			//FieldTypes holen
			$getFieldTypes = new twidoo_sql;
			$getFieldTypes->setQuery("SELECT the_table, field, type, label FROM contentareas_fieldtypes WHERE ca_id = :id");
			$getFieldTypes->bindParam(":id", $ID);
			$getFieldTypes->execute();
			$fieldTypes = $getFieldTypes->getArray();
			
			//Keys holen
			$getKeys = new twidoo_sql;
			$getKeys->setQuery("SELECT the_table, the_key FROM contentareas_keys WHERE ca_id = :id");
			$getKeys->bindParam(":id", $ID);
			$getKeys->execute();
			
			$keys = array();
			if($getKeys->rowCount() == 1)
				$keys[0] = $getKeys->getArray();
			else $keys = $getKeys->getArray();
					
			//Das DatamanagementObject zusammenbauen
			$myManagement = new twidoo_datamanagement();
		
			//NAME
			if($ca_name != "")
				$myManagement->name = $ca_name;
			
			//OUTPUT
			if($output != "")
				$myManagement->setOutput($output);
				
			//TABLES
			if($getTables->rowCount() == 1) {
				$myManagement->setTable($tables["the_table"], $tables["the_function"]);
				$myManagement->setWhere($tables["the_table"], $tables["the_where"]);
				$myManagement->setOrderBy($tables["the_table"], $tables["the_orderby"]);
			}
			
			else {			
				foreach($tables as $table) {
					$myManagement->setTable($table["the_table"], $table["the_function"]);
					$myManagement->setWhere($table["the_table"], $table["the_where"]);
					$myManagement->setOrderBy($table["the_table"], $table["the_orderby"]);
				}	
			}			
				
				
			//FIELDTYPES	
			foreach($fieldTypes as $fieldType) {		
				$myManagement->addFieldType($fieldType["the_table"], $fieldType["field"], $fieldType["type"]);	

				if($fieldType["label"] != "")
					$myManagement->addLabel($fieldType["the_table"], $fieldType["field"], $fieldType["label"]);
			}

			
			//KEYS	
			foreach($keys as $key)		
				$myManagement->addKey($key["the_key"], $key["the_table"]);
			
			return new twidoo_content_return($myManagement->execute());
		}
	}	
	
	return new twidoo_content_return($smarty->fetch($TWIDOO["includepath"].'/templates/content.tpl'));

?>