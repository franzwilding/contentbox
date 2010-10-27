<?php

global $TWIDOO;

$mediaUrl = new twidoo_urlencode();

$includeUrl = $TWIDOO["mediapath"];

foreach($mediaUrl->getPageparameters() as $step)
	$includeUrl .= "/".$step;


//der letzte Step gibt uns die Datei ohne Pfad
$fileext = explode(".", $step);
$fileext = $fileext[1];
$type = "";
//daraus holen wir uns die ext
switch($fileext) {
	case "html": $type = "text/html"; break;
	case "js": $type = "text/js"; break;
	case "css": $type = "text/css"; break;
	case "jpg": $type = "image/jpeg"; break;
	case "jpeg": $type = "image/jpeg"; break;
	case "png": $type = "image/png"; break;
	case "gif": $type = "image/gif"; break;
}

header('Content-type: '.$type);

return new twidoo_content_return(file_get_contents($includeUrl), 1);

?>