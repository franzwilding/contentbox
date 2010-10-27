<?php

class contentAreas extends twidoo_output
{
	
	
	//gibt zurück, welche Parameter erlabut sind
	function allowedParameters() {
		return array(
			"user_edit", 
			"user_edit2", 
			"user_delete", 
			"invite"
		);
	}
	
	
	
	
	/**
	 * Subpage, um User-Rechte zu bearbeiten
	 *
	 * @author Franz Wilding
	 */
	function sub_user_edit() {
		$this->smarty->assign("page", "user_edit");
		$pageParameters = $this->urlEncode->getParameters();
		$authArray = array();
		
		//Contentbereiche holen, und User-Auth eintragen
		foreach($this->dataSets["contentbox_contentareas"]["data"] as $dataRow) {
			$insertArray = array(
				"ca_id" => $dataRow["contentbox_contentareas_id"], 
				"ca_name" => $dataRow["contentbox_contentareas_name"], 
			);
			
			//userAuth für diese Area holen
			foreach($this->dataSets["contentareas_userauth_contentareas"]["data"] as $userauth) {
				if(	$userauth["contentareas_userauth_contentareas_id_user"] == $pageParameters[2] && 
					$userauth["contentareas_userauth_contentareas_id_contentarea"] == $dataRow["contentbox_contentareas_id"]) {
					$insertArray["userauth"] = true;
				}		
			}
			array_push($authArray, $insertArray);
		}
		
		//getuserbyID
		$username = "";
		foreach($this->dataSets["contentbox_userauth"]["data"] as $user) {
			if($user["contentbox_userauth_id"] == $pageParameters[2])
				$username = $user["contentbox_userauth_firstname"]." ".$user["contentbox_userauth_surname"];
		}
				
		$this->smarty->assign("userauth", $authArray);
		$this->smarty->assign("user_id", $pageParameters[2]);
		$this->smarty->assign("user_name", $username);
	}
	
	
	
	
	
	/**
	 * User Edit Abschließen
	 *
	 * @author Franz Wilding
	 */
	function sub_user_edit2() {
		global $TWIDOO;
		$editForm = new twidoo_form("edit");
		$allFields = $editForm->values;
		
		foreach($editForm->values as $ca_id => $status) {
			if($ca_id != "id" && $ca_id != "submit") {

				//zuerst alle des users löschen, 
				foreach($this->dataSets["contentareas_userauth_contentareas"]["data"] as $key => $value) {				
					if($value["contentareas_userauth_contentareas_id_user"] == $editForm->getValue("id"))
						$this->dataManagementObject->deleteData("contentareas_userauth_contentareas", $value["contentareas_userauth_contentareas_id"]);
				}

				//dann die neuen anlegen
				if($status == "on") {
					$this->dataManagementObject->changeData("contentareas_userauth_contentareas", array(
						"contentareas_userauth_contentareas_id_user" => $editForm->getValue("id"),
						"contentareas_userauth_contentareas_id_contentarea" => $ca_id, 
						"contentareas_userauth_contentareas_id" => NULL
					));
				}
			}
		}
		
		$getPage = new twidoo_urlencode();
		header('Location: '.$TWIDOO["baseurl"].$getPage->getPage());
	}

		
	/**
	 * Subpage, um User zu löschen
	 *
	 * @author Franz Wilding
	 */
	function sub_user_delete() {
		global $TWIDOO;
		
		$params = $this->urlEncode->getParameters();
		$USERID = $params[2];
		
		$deleteUser = new twidoo_sql;
		$deleteUser->setQuery("DELETE FROM contentbox_userauth WHERE id=:id");
		$deleteUser->bindParam(":id", $USERID);
		$deleteUser->execute();
		
		$getPage = new twidoo_urlencode();
		header('Location: '.$TWIDOO["baseurl"].$getPage->getPage());
	}
	

	function execute() {
		global $TWIDOO;
		
		//Die Startausgabe
		if(!$this->withParameter) {	
			$this->smarty->assign("page", "start");
			$this->smarty->assign("invitePage", 1);
			$this->smarty->assign("pageTitle", $TWIDOO["site_title"]);
			$this->smarty->assign("user", $this->dataSets["contentbox_userauth"]["data"]);
			$this->smarty->assign("areas", $this->dataSets["contentbox_contentareas"]["data"]);
			
			$userAuthArray = array();
			foreach($this->dataSets["contentareas_userauth_contentareas"]["data"] as $userAuch) {
				$userAuthArray[(int)$userAuch["contentareas_userauth_contentareas_id_user"]][(int)$userAuch["contentareas_userauth_contentareas_id_contentarea"]] = true;
			}

			$this->smarty->assign("userauth", $userAuthArray);
			
			//get Username
			$mylogin = new twidoo_login('contentbox_userauth', 'contentbox_userauth', 'email', 'password', 'hash');
			$mylogin->setHashEncode('sha1');
			$getName = new twidoo_sql;
			$getName->setQuery("SELECT CONCAT(firstname, ' ', surname) as name FROM contentbox_userauth WHERE email=:email LIMIT 1");
			$getName->bindParam(":email", $mylogin->getUsername());
			$getName->execute();
			$name = $getName->getArray();
			$this->smarty->assign("username", $name["name"]);
		}
		
		$getPage = new twidoo_urlencode();
		$this->smarty->assign("pagePath", $getPage->getPage()."/");
		$this->smarty->assign("includePath", $TWIDOO["includepath"].'/templates/outputs');
		return $this->smarty->fetch($TWIDOO["includepath"].'/templates/outputs/ca_persones.tpl');
	}
}

return new contentAreas();

?>