<?php

global $TWIDOO;

new twidoo_log("LOGOUT");

$mylogin = new twidoo_login('contentbox_userauth', 'contentbox_userauth', 'email', 'password', 'hash');
$mylogin->setHashEncode('sha1');
$mylogin->logout();
header('Location: '.$TWIDOO["baseurl"]);

?>