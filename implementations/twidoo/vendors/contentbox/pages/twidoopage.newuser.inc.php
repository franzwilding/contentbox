<?php
	$smarty = new Smarty;
	
	//wir holen uns die Emailadresse, und den Hashcode, der Ÿber die Url Ÿbergeben wurde
	$getParams = new twidoo_urlencode();
	$EMAIL = $getParams->getParameter(1);
	$HASH = $getParams->getParameter(2);
	
	//jetzt schauen wir, ob der User auch wirklich eingeladen wurde
	$getUserID = new twidoo_sql;
	$getUserID->setQuery('SELECT id FROM contentbox_userauth WHERE hash=:hash AND email=:mail');
	$getUserID->bindParam('hash', $HASH);
	$getUserID->bindParam('mail', $EMAIL);
	$getUserID->execute();
	
	$USER = $getUserID->getArray();
	
	new twidoo_log($USER);
	
	//WENN DER USER EXISTIERT
	if($USER['id'] > 0 && is_numeric($USER['id']))
	{
		$smarty->assign('userexists', true);
		
		$form = new twidoo_form('newuser');
			$form->setRequired('vorname');
			$form->setRequired('nachname');
			$form->setRequired('email');
			$form->setRequired('password1');
			$form->setRequired('password2');
			
			$form->setRule('email', 'valid', 'email');
			$form->setRule('password1', 'sameas', 'password2');
		
		/* WENN DAS ANMELDEFORM ABGESENDET WURDE */
		if($form->formDone())
		{
			$smarty->assign('formDone', true);
			
			//user updaten
			$updateUser = new twidoo_sql;
			$updateUser->setQuery('UPDATE contentbox_userauth SET firstname=:firstname, surname=:surname, email=:mail, password=:password WHERE id=:ID');
			$updateUser->bindParam('firstname', $form->getValue('vorname'));
			$updateUser->bindParam('surname', $form->getValue('nachname'));
			$updateUser->bindParam('mail', $form->getValue('email'));
			$updateUser->bindParam('password', sha1($form->getValue('password1')));
			$updateUser->bindParam('ID', $USER['id']);
			$updateUser->execute();
		
			//und auch gleich einloggen
			$mylogin = new twidoo_login('userlogin', 'cb_loginuser', 'email', 'password', 'hash');
			$mylogin->setHashEncode('sha1');
			$mylogin->login($form->getValue('email'), $form->getValue('password1'));
			
		}
		
		/* WENN NOCH NICHT */
		else
		{
			$smarty->assign('formDone', false);
			
			$smarty->assign('email', $EMAIL);
			$smarty->assign('hash', $HASH);
			$smarty->assign('userid', $USER['id']);
			
			
			//logout, damit wir spŠter keine schwierigkeiten bekommen
			$mylogin = new twidoo_login('userlogin');
			$mylogin->logout();
		}
	}
	
	//WENN NICHT
	else
	{
		$smarty->assign('userexists', false);
	}
		
	
	$smarty->assign("baseurl", $TWIDOO["baseurl"]);
	
	return new twidoo_content_return($smarty->fetch($TWIDOO['includepath'].'/templates/newuser.tpl'), 'content', true);
	
	
	
?>