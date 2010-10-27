<?php


function loadTableData($data) {
	
	$describeTable = new twidoo_sql;
	$describeTable->setQuery("DESCRIBE ".$data["tablename"]);
	$describeTable->execute();
	
	if($describeTable->getError() == "")
	{
		
		if(is_numeric($data["contentarea"]) && $data["contentarea"] > 0) {
			
			//SELECT BASIC TABLEDATA
			$getTable = new twidoo_sql;
			$getTable->setQuery("SELECT id, the_where, the_orderby, the_function FROM contentareas_tables WHERE the_table=:table AND ca_id=:cid LIMIT 1");
			$getTable->bindParam(":table", $data["tablename"]);
			$getTable->bindParam(":cid", $data["contentarea"]);
			$getTable->execute();
			$tableInfo = $getTable->getArray();
			
			$WHERE = "";
			$ORDERBY = "";
			$FUNCTION = "";
			
			$WHERE = $tableInfo["the_where"];
			$ORDERBY = $tableInfo["the_orderby"];
			$FUNCTION = $tableInfo["the_function"];
			
			
			//SELECT KEY
			$key = "";
			
			$getKey = new twidoo_sql;
			$getKey->setQuery("SELECT the_key FROM contentareas_keys WHERE the_table=:table AND ca_id=:cid LIMIT 1");
			$getKey->bindParam(":table", $data["tablename"]);
			$getKey->bindParam(":cid", $data["contentarea"]);
			$getKey->execute();
			$keyArray = $getKey->getArray();
			$key = $keyArray["the_key"];
			
			
			//GET TABLEFIELDDATA
			$getTableFields = new twidoo_sql;
			$getTableFields->setQuery("SELECT field, type, label FROM contentareas_fieldtypes WHERE the_table=:table AND ca_id=:cid");
			$getTableFields->bindParam(":table", $data["tablename"]);
			$getTableFields->bindParam(":cid", $data["contentarea"]);
			$getTableFields->execute();
			
			$fieldArray = array();
			
			foreach($getTableFields->getArray() as $field) {
				$fieldArray[$field["field"]]["type"] = $field["type"];
				$fieldArray[$field["field"]]["label"] = $field["label"];
			}
			
		}
		
		$resultArray = array();
		
		if($describeTable->rowCount() == 1)	array_push($resultArray, $describeTable->getArray());
		else $resultArray = $describeTable->getArray();
	
		
		$returnString = "<label>Key</label><select name=\"edit[tables][".$data["tablename"]."][key]\">";
		
		foreach($resultArray as $field) {
			$selectedString = "";
			if($field["Field"] == $key) $selectedString = ' selected="selected"';
			
			$returnString .= "<option name=\"".$field["Field"]."\"".$selectedString.">".$field["Field"]."</option>";
		}
		
		$returnString .=	"</select>
							<label>Order By</label>
							<input name=\"edit[tables][".$data["tablename"]."][orderby]\" type=\"text\" value=\"".$ORDERBY."\" />
							
							<label>Funktion</label>
							<input name=\"edit[tables][".$data["tablename"]."][function]\" type=\"text\" value=\"".$FUNCTION."\"/><hr />";
		$returnString .= "<table class=\"small\">
									<tr>
										<th></th>
										<th>Type</th>
										<th>Name</th>
									</tr>
						";
	
			
		foreach($resultArray as $field) {
			$typeArray = explode("(", $field["Type"]);
			
			//TYPEDATA
			if($fieldArray[$field["Field"]]["type"] != "")
				$typeString = ' value="'.$fieldArray[$field["Field"]]["type"].'"';
			else
				$typeString = ' value="'.$typeArray[0].'"';
			
			//LABELDATA
			$labelString = ' value="'.$fieldArray[$field["Field"]]["label"].'"';
			
			
			$returnString .= "<tr>
								<td>".$field["Field"]."</td>
								<td><input name=\"edit[tables][".$data["tablename"]."][fields][".$field["Field"]."][type]\" type=\"text\"".$typeString." /></td>
								<td><input name=\"edit[tables][".$data["tablename"]."][fields][".$field["Field"]."][name]\" type=\"text\"".$labelString." /></td>
							</tr>";
		}
		
		$returnString .= "</table><label>Where</label>
								<textarea name=\"edit[tables][".$data["tablename"]."][where]\">".$WHERE."</textarea>";
		
		return json_encode(array("success" => true, "content" => $returnString));
	}

	else return json_encode(array("success" => false, "content" => ""));
}


function changeUserAuth($data) {

	$success = true;
	
	$USER = $data["user"];
	$DATA = explode(";", substr($data["data"], 1));	
	
	new log($DATA);
	
	//ZUERST ALLE L…SCHEN
	$changeUserAuth = new twidoo_sql;
	$changeUserAuth->setQuery("DELETE FROM contentareas_userauth_contentareas WHERE id_user=:USER");
	$changeUserAuth->bindParam(":USER", $USER);
	$changeUserAuth->execute();
	
	if($changeUserAuth->getError() != "") $success = false;

	
	
	
	//DANN NEU EINF†GEN
	foreach($DATA as $row) {
		if($row > 0) {
			$changeUserAuth = new twidoo_sql;
			$changeUserAuth->setQuery("INSERT INTO contentareas_userauth_contentareas SET id_user=:USER, id_contentarea=:CA");
			$changeUserAuth->bindParam(":USER", $USER);
			$changeUserAuth->bindParam(":CA", $row);
			$changeUserAuth->execute();
			if($changeUserAuth->getError() != "") $success = false;
		}
	}
	
	return json_encode(array("success" => $success, "content" => ""));
	
}



function email_invite($data) {
	
	$returnString = json_encode(array("success" => false, "content" => "<p>Emails konnten nicht versendet werden</p>"));
	
	$EMAILS = $data["emails"];
	$MESSAGE = $data["message"];
	
	global $TWIDOO;

	$newUser = array();
	$newUser = explode(",", $EMAILS);			
	$createUserAlias = new twidoo_sql;
	$sendInvitation = new twidoo_mail;
	$sendInvitation->from($TWIDOO['contact']['mail'], $TWIDOO['contact']['name']);
	$sendInvitation->setSubject("Einladung zur gemeinsamen Verwaltung von ".$TWIDOO['title']);
	$sendInvitation->setHTML(true);
	$invitationSmarty = new Smarty;
	$invitationSmarty->assign('message', $MESSAGE);
					
	//fŸr jeden neuen User
	foreach($newUser as $user) {
		$user = trim($user);				//Whitespace entfernen
		$checkUser = new twidoo_sql;		//schauen, ob es den User schon gibt
		$checkUser->setQuery("SELECT id FROM contentbox_userauth WHERE email=:email");
		$checkUser->bindParam(":email", $user);
		$checkUser->execute();
		
		if(!$checkUser->rowCount() > 0) {
			$addUser = new twidoo_sql;		//user eintragen
			$addUser->setQuery("INSERT INTO contentbox_userauth SET email=:email");
			$addUser->bindParam(":email", $user);
			$addUser->execute();
			
			$getNewUser = new twidoo_sql;	//getNewUser
			$getNewUser->setQuery("SELECT id FROM contentbox_userauth ORDER BY id DESC LIMIT 1");
			$getNewUser->execute();
			$newUser = $getNewUser->getArray();
							
			foreach($invite->getValue("allowed") as $ca) {
				$addUserAuth = new twidoo_sql; //userAuth eintragen
				$addUserAuth->setQuery("INSERT INTO contentareas_userauth_contentareas SET id_user=:user, id_contentarea=:ca");
				$addUserAuth->bindParam(":user", $newUser["id"]);
				$addUserAuth->bindParam(":ca", $ca);
				$addUserAuth->execute();
			}
					
			$randHash = md5(rand(0, 1000000));	//email an den User senden
			$createUserAlias->setQuery('UPDATE contentbox_userauth SET hash =:hash WHERE id=:uid');
			$createUserAlias->bindParam(':hash', $randHash);
			$createUserAlias->bindParam(':uid', $newUser["id"]);
			$createUserAlias->execute();
			$sendInvitation->addAddress($user);
			$invitationSmarty->assign('email', $user);
			$invitationSmarty->assign('baseurl', $TWIDOO['baseurl']);
			$invitationSmarty->assign('name', $TWIDOO['title']);
			$invitationSmarty->assign('hash', $randHash);
			$sendInvitation->setBody($invitationSmarty->fetch($TWIDOO['includepath'].'/templates/invite_template.tpl'));
			$sendInvitation->send();
		}
	}
	
	//SUCCESS
	if(count($newUser) > 0) {
		$returnString = json_encode(array("success" => true, "content" => "<p>Eine Einladung wurde an ".count($newUser)." Personen versendet</p>"));
	}
	
	return $returnString;
}
















$getFunctionName = new twidoo_urlencode;
$tmp = $getFunctionName->getPageParameters();
$getFunctionName = $tmp[0];
return new twidoo_content_return(call_user_func($getFunctionName, $_POST), true);


?>