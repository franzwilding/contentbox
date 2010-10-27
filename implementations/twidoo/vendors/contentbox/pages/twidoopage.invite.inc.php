<?php
	global $TWIDOO;
	$smarty = new Smarty;
	
	
	$invite = new twidoo_form("invite");
	
	
	//wenn das Form noch nicht abgeschickt wurde, zeigen wir die erste Seite an
	if(!$invite->formDone())
	{
		global $TWIDOO;
		
		$page = "1";
		//den Login-Namen aus der DB holen, und ausgeben
		$mylogin = new twidoo_login('loginuser', 'loginuser', 'email', 'password', 'hash');
		$mylogin->setHashEncode('sha1');
		
		$getUsername = new twidoo_sql;
		$getUsername->setQuery("SELECT CONCAT(firstname, \" \", surname) as name FROM cb_loginuser WHERE email=:email");
		$getUsername->bindParam(":email", $mylogin->getUsername());
		$getUsername->execute();
			
		$name = $getUsername->getArray(array(), "table", 1);
		
		$smarty->assign("logedinName", $name["name"]);
	
		$smarty->assign('title', $TWIDOO['title']);
		
		
		
		//Content-Areas holen, und ausgeben
		$getAreas = new twidoo_sql();
		$getAreas->setQuery("SELECT id, name FROM cb_contentareas ORDER BY sortIndex ASC");
		$getAreas->execute();
		
		$smarty->assign("areas", $getAreas->getArray());
	}
	
	//ansonsten wird das Form ausgewertet
	else
	{
		$page = "2";	
	
	
		$newUser = array();
		$newUser = explode(",", $invite->getValue("emails"));
		
		
		$createUserAlias = new twidoo_sql;
		$sendInvitation = new twidoo_mail;
		$sendInvitation->from($TWIDOO['contact']['mail'], $TWIDOO['contact']['name']);
		$sendInvitation->setSubject("Einladung zur gemeinsamen Verwaltung von ".$TWIDOO['title']);
		$sendInvitation->setHTML(true);
		$invitationSmarty = new Smarty;
		$invitationSmarty->assign('message', $invite->getValue('text'));
		
		
		//für jeden neuen User
		foreach($newUser as $user)
		{
			//Whitespace entfernen
			$user = trim($user);
		
			
			//schauen, ob es den User schon gibt
			$checkUser = new twidoo_sql;
			$checkUser->setQuery("SELECT id FROM cb_loginuser WHERE email=:email");
			$checkUser->bindParam(":email", $user);
			$checkUser->execute();
			
			
			if(!$checkUser->rowCount() > 0)
			{
				//user eintragen
				$addUser = new twidoo_sql;
				$addUser->setQuery("INSERT INTO cb_loginuser SET email=:email");
				$addUser->bindParam(":email", $user);
				$addUser->execute();
				
				//getNewUser
				$getNewUser = new twidoo_sql;
				$getNewUser->setQuery("SELECT id FROM cb_loginuser ORDER BY id DESC LIMIT 1");
				$getNewUser->execute();
				$newUser = array();
				$newUser = $getNewUser->getArray();
								
				//userAuth eintragen
				foreach($invite->getValue("allowed") as $ca)
				{
					$addUserAuth = new twidoo_sql;
					$addUserAuth->setQuery("INSERT INTO cb_userauthority SET id_user=:user, id_contentarea=:ca");
					$addUserAuth->bindParam(":user", $newUser["id"]);
					$addUserAuth->bindParam("ca", $ca);
					$addUserAuth->execute();
					
					new twidoo_log($addUserAuth);
				}
				
						
				//email an den User senden
				$randHash = md5(rand(0, 1000000));

				$createUserAlias->setQuery('UPDATE cb_loginuser SET hash =:hash WHERE id=:uid');
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
	
	}

	$smarty->assign("page", $page);
	return new twidoo_content_return($smarty->fetch($TWIDOO['vendors']["contentbox"]["includepath"].'/templates/invite.tpl'));
?>