<?php

// ========================= 
// ! CONFIGURE THIS VENDOR   
// ========================= 
	global $TWIDOO;
	$smarty = new Smarty;
		
	$TWIDOO["sitepath"] = $TWIDOO["baseurl"];
	$TWIDOO["site_includepath"] = $TWIDOO["includepath"];
	$TWIDOO["mediapath"] = $TWIDOO["baseurl"]."media/contentbox";
	$TWIDOO["includepath"] = $TWIDOO["includepath"].'/vendors/contentbox';
	$TWIDOO["site_baseurl"] = $TWIDOO["baseurl"];
	$TWIDOO["baseurl"] = $TWIDOO["baseurl"].'contentbox/';
	$TWIDOO["site_title"] = $TWIDOO["title"];
	$TWIDOO["title"] = "A new ContentBox";
	
	$smarty->assign("mediapath", $TWIDOO["mediapath"]);
	$smarty->assign("title", $TWIDOO["title"]);
	$smarty->assign("baseurl", $TWIDOO["baseurl"]);	
	$smarty->assign("sitepath", $TWIDOO["sitepath"]);	
	$smarty->assign("version", "1.0");	
	
	
// ========================== 
// ! GET THE SUBPAGE-CONTENT   
// ========================== 	
	$contentBoxUrl = new twidoo_urlencode;
	$contentBoxContent = new twidoo_content;
	$contentBoxContent->setStartPage("content");
	$contentBoxContent->setErrorPage("content");
	$contentBoxContent->setContent($contentBoxUrl->getPage());
	
	if(!$contentBoxContent->contentOnly()) {
	
	
// ============================= 
// ! CHECK USER AUTHENTICATION   
// ============================= 
		$mylogin = new twidoo_login('contentbox_userauth', 'contentbox_userauth', 'email', 'password', 'hash');
		$mylogin->setHashEncode('sha1');
		
		if($mylogin->checklogin()) {
		//If User loggedin successfully
			

			$isUserAdmin = new twidoo_sql;
			$isUserAdmin->setQuery("SELECT id, admin FROM contentbox_userauth WHERE email=:email");
			$isUserAdmin->bindParam(":email", $mylogin->getUsername());
			$isUserAdmin->execute();
			$admin1 = $isUserAdmin->getArray();
			$admin = $admin1["admin"];
			
			
			if(!$admin) {
				//Die ContentAreas aus der Datenbank holen, und als MenŸ ausgeben
				$contentAreas = new twidoo_sql;
				$contentAreas->setQuery("SELECT 
					contentbox_contentareas.id as id, 
					contentbox_contentareas.name as name 
					FROM contentareas_userauth_contentareas 
					LEFT JOIN contentbox_contentareas ON(contentareas_userauth_contentareas.id_contentarea = contentbox_contentareas.id) 
					WHERE contentareas_userauth_contentareas.id_user = :id OR 
					ORDER BY contentbox_contentareas.sortIndex ASC");
				$contentAreas->bindParam(":id", $admin1["id"]); 
				$contentAreas->execute();			
			} else {
				//Die ContentAreas aus der Datenbank holen, und als MenŸ ausgeben
				$contentAreas = new twidoo_sql;
				$contentAreas->setQuery("SELECT 
					contentbox_contentareas.id as id, 
					contentbox_contentareas.name as name 
					FROM contentbox_contentareas 
					ORDER BY contentbox_contentareas.sortIndex ASC");
				$contentAreas->bindParam(":id", $admin1["id"]); 
				$contentAreas->execute();
			}
				
			$contentAreasArray = array();
			
			//Die ID bekommen
			$getID = new twidoo_urlencode;
			$parameter = $getID->getPageParameters();
			$ID = $parameter[0];
			
			
			if($contentAreas->rowCount() > 1)
				$contentAreasArray = $contentAreas->getArray();
			elseif(count($contentAreas->getArray()) > 0)
				array_push($contentAreasArray, $contentAreas->getArray());
			
			//die aktive contentarea makieren
			foreach($contentAreasArray as $key => $element) {
				if($element["id"] == $ID) $contentAreasArray[$key]["active"] = true;
			}		
			
			$smarty->assign("contentareas", $contentAreasArray);
			
			
			
			
			
			
			
			
			if(($contentBoxUrl->getPage() == "settings" || $contentBoxUrl->getPage() == "persones") && !$admin)
				$smarty->assign("content", 'Authorization failed. Du must Administrator_in sein, um diese Seite aufrufen zu können');
			else	
				$smarty->assign("content", $contentBoxContent->getContent(0));
			
			$smarty->assign("active", $contentBoxUrl->getPage());
			$smarty->assign("admin", $admin);
			
			return new twidoo_content_return($smarty->fetch($TWIDOO["includepath"].'/templates/index.tpl'), 1);
		} else {
			
			//FILE UPLOAD
			if(array_key_exists("contentbox_userauth", $_POST) && array_key_exists("Filename", $_POST) && array_key_exists("folder", $_POST) && array_key_exists("Upload", $_POST)) {
				
				//LOGIN
				$loginUserInformaitons = explode(",", $_POST["contentbox_userauth"]);
				
				//TEST IF USER EXISTS
				$checkSQL = new twidoo_sql;
				$checkSQL->setQuery("SELECT id FROM contentbox_userauth WHERE email=:email AND password=:password LIMIT 1");
				$checkSQL->bindParam(":email", $loginUserInformaitons[1]);
				$checkSQL->bindParam(":password", $loginUserInformaitons[2]);
				$checkSQL->execute();	
				
				//wenn ich eingeloggt bin
				if($checkSQL->rowCount() == 1) {

					//wenn der Dateiupload nicht funktioniert hat...
					if($_FILES["Filedata"]["tmp_name"] == "") return new twidoo_content_return("", true);

					//da die uploadFile Methode nicht ganz so flexibel ist, müssen wir das $_FILES Array umstellen 
					$_FILES["Filedata"]["name"] = array("0" => $_FILES["Filedata"]["name"]);
					$_FILES["Filedata"]["tmp_name"] = array("0" => $_FILES["Filedata"]["tmp_name"]);
					
					$uploadFile = new twidoo_filesystem;
					$fileName = $uploadFile->uploadFile("Filedata", 0, "", $TWIDOO["site_includepath"].$_POST["folder"]);
					
					//das ganze muss dann noch in die Datenbank eingetragen werden. Den Tabellennamen und Feldnamen bekommen wir mitgesendet
				
					$tableName = $_POST["tablename"];
					$pathName = $_POST["pathName"];
					$galeries_idName = $_POST["galeries_idName"];
					
					$insertIMG = new twidoo_sql;
					
					$insertIMG->setQuery("INSERT INTO 
							".$tableName." 
						SET ".$pathName."=:path, 
							".$galeries_idName."=:galeries_id");
					
					$insertIMG->bindParam(":path", $fileName);
					$insertIMG->bindParam(":galeries_id", $_POST["galeries_id"]);
					$insertIMG->execute();
					
					return new twidoo_content_return("lalalala1", true);
					
				} else return new twidoo_content_return("", true);

			}
			
			else {
				$contentBoxContent->setContent("login");
				return new twidoo_content_return($contentBoxContent->getContent(0), true);
			}
		}
	}
	
	
// ============================================================================================ 
// ! WENN DIE SUBPAGE DIE CONTENT-AUSGABE SELBER †BERNIMMT, BRAUCHEN WIR KEINE AUTHETICATION   
// ============================================================================================ 
	else {
		return new twidoo_content_return($contentBoxContent->getContent(0), true);
	}


?>