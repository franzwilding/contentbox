<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	
	
	<base href="{$baseurl}" />
    <title></title>
    
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-language" content="de" />
    <meta name="author" content="Jakob Scholz (typo3),  Franz Wilding (HTML, CSS, Design) - www.franz-wilding.at" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="robots" content="index,follow" />
    <meta name="DC.Title" content="SAME AS TITLE" />
    <meta name="date" content="" />
	<meta name="generator" content="" />
	<meta name="keywords" content="" />
    
	<!--[if lt IE 9]>
		<script src="js/IEFIXES.js" type="text/javascript"></script>
	<![endif]-->
	

	
	<!-- ##### MAIN JAVASCRIPT ##### -->
	<script src="{$mediapath}/js/jquery.js" type="text/javascript"></script>
	<script src="{$mediapath}/js/functions.js" type="text/javascript"></script>
	

	<!-- ##### MAIN CSS FILE ##### -->
	<link href="{$mediapath}/css/main.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="{$mediapath}/css/install.css" rel="stylesheet" type="text/css" media="screen" />

</head>
<body id="publicView" class="install">

<div class="mainView">
	<h1><span>contentBox</span></h1>
	
	{if !$INSTALLSUCCESS}
		
		<h2>Installation</h2>
		
		<div class="split" style="width:48%;">
			<h3>1. Datenbank einrichten</h3>
			<div class="box">
				{if $statuses|@count > 0}
					{assign value=0 var="errorCount"}
					<ol>
						{foreach from=$statuses item=status}
							{if $status.type == "error"}{assign value=$errorCount+1 var="errorCount"}{/if}
							<li class="{$status.type}">{$status.message}</li>
						{/foreach}
					</ol>
				{/if}
				
				{if $errorCount == 0}
					<p class="success">Die Datenbank kann eingerichtet werden!</p>
				{else}
					<p class="error">Es sind Fehler aufgetreten, die Datenbank kann nicht eingerichtet werden!</p>
				{/if}
			</div>
		</div>
		
			<div class="split" style="width:48%;">
			<h3>2. Administrator_in anlegen</h3>
			
			<form action="" method="post">
				<fieldset>
					<legend>Administrator_in anlegen</legend>
					<ul>
						<li><label for="input_email"{if $formErrors.email|@count > 0} class="error"{/if}>Emailadresse: </label><input id="input_email" type="text" name="install[email]" value="{$formData.email}" /></li>
						<li><label for="input_password"{if $formErrors.password|@count > 0} class="error"{/if}>Passwort: </label><input id="input_password" type="text" name="install[password]" value="{$formData.password}" /></li>
						<li><label for="input_password2"{if $formErrors.password2|@count > 0} class="error"{/if}>Passwort wiederholen: </label><input id="input_password2" type="text"  name="install[password2]" value="{$formData.password2}" /></li>
						<li><label for="input_vorname"{if $formErrors.vorname|@count > 0} class="error"{/if}>Vorname: </label><input id="input_vorname" type="text"  name="install[vorname]" value="{$formData.vorname}" /></li>
						<li><label for="input_nachname"{if $formErrors.nachname|@count > 0} class="error"{/if}>Nachname: </label><input id="input_nachname" type="text"  name="install[nachname]" value="{$formData.nachname}" /></li>
						<li class="submit"><button class="bigButton"  name="install[submit]" value="true"><span>Installieren</span></button></li>
					</ul>
				</fieldset>
			</form>
		</div>
	{else}
		<h2 style="text-align:center; font-size:40px; color:green;">ICH HABE CONTENTBOX ERFOLGREICH INSTALLIERT!!!!</h2>
		<p style="text-align:center;"><a class="smallButtonBig" href="">Einloggen & Loslegen</a></p>
	{/if}
	
</div><!-- mainView ends here -->

</body>
</html>