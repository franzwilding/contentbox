<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	
	
	<base href="{$baseurl}" />
    <title>{$title}</title>
    
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-language" content="de" />
    <meta name="author" content="Franz Wilding - www.franz-wilding.at" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="robots" content="index,follow" />

    <meta name="DC.Title" content="SAME AS TITLE" />
    <meta name="date" content="" />
	<meta name="generator" content="" />
	<meta name="keywords" content="" />
    
	<!--[if lt IE 9]>
		<script src="media/js/IEFIXES.js" type="text/javascript"></script>
	<![endif]-->
	

	
	<!-- ##### MAIN JAVASCRIPT ##### -->
	<script src="{$mediapath}/js/jquery.js" type="text/javascript"></script>
	<script src="{$mediapath}/js/functions.js" type="text/javascript"></script>
	



	<!-- ##### MAIN CSS FILE ##### -->
	<link href="{$mediapath}/css/main.css" rel="stylesheet" type="text/css" media="screen" />

</head>

<body id="publicView">

<div class="mainView">
	<form id="login" action="login" method="post">
		<fieldset>
			<legend>In diese contentBox einloggen</legend>
			<h1>contentBox</h1>
			<label for="email">Emailadresse: </label><input onblur="if(this.value=='')this.value='emailadresse'" onfocus="if(this.value=='emailadresse')this.value=''" id="email" type="text" name="login[email]" value="emailadresse" />
			<label for="password">Emailadresse: </label><input id="password" onblur="if(this.value=='')this.value='passwort'" onfocus="if(this.value=='passwort')this.value=''" type="password"  name="login[password]" value="passwort" />
			<input type="hidden"  name="login[password2]" value="" />
			<button class="bigButton" name="login[submit]" value="true"><span>login</span></button>
		</fieldset>
		
	</form>
</div><!-- mainView ends here -->

<div id="subinfo">
	<p>
		contentBox ist eine neue Art, Websiten zu verwalten. Gemacht hat das Franz Wilding, auf <br /><a target="_blank" href="http://franz-wilding.at">seiner Website</a> kannst du mehr über das Projekt erfahren.
	</p>
</div><!-- subinfo ends here -->

</body>

</html>