<?php
	global $TWIDOO;
	$smarty = new Smarty;

	return new twidoo_content_return($smarty->fetch($TWIDOO['vendors']["contentbox"]["includepath"].'/templates/register.tpl'), true);
?>