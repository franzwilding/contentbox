<?php

global $TWIDOO;
$smarty = new Smarty;
$smarty->assign("title", $TWIDOO["title"]);
$smarty->assign("baseurl", $TWIDOO["baseurl"]);
$smarty->assign("mediapath", $TWIDOO["mediapath"]);


// ======================== 
// ! LOGIN-FORM AUSWERTEN   
// ======================== 
$myForm = new twidoo_form("login");
$myForm->setRequired("email");
$myForm->setRequired("password");
$myForm->setHoneyPot("password2");

// ================================================ 
// ! WENN DAS FORM PASST, LOGGEN WIR DEN USER EIN   
// ================================================ 
if($myForm->formDone()) {	

	$mylogin = new twidoo_login('contentbox_userauth', 'contentbox_userauth', 'email', 'password', 'hash');
	$mylogin->setHashEncode('sha1');
	$mylogin->login($myForm->getValue("email"), $myForm->getValue("password"));
	header('Location: '.$TWIDOO["baseurl"]);
}



return new twidoo_content_return($smarty->fetch($TWIDOO["includepath"].'/templates/login.tpl'));

?>