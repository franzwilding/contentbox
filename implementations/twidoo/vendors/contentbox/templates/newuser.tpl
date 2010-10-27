<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	
	
	<base href="{$baseurl}" />
    <title>{$title}</title>
    
    
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="content-language" content="de" />
    <meta name="author" content="Jakob Scholz (typo3),  Franz Wilding (HTML, CSS, Design) - www.franz-wilding.at" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="robots" content="index,follow" />
    <meta name="DC.Title" content="SAME AS TITLE" />
    <meta name="date" content="" />
	<meta name="generator" content="" />
	<meta name="keywords" content="" />
    
    
    
    
    <!-- ##### EXTRA JAVASCRIPT, UM MODERNEN CSS CODE IN ALLEN BROWSERN ZU ERMÖGLICHEN ##### -->
	<!--[if IE]>
		<script src="media/contentbox/js/IE9.js" type="text/javascript"></script>
	<![endif]-->
	
	
	
	
	<!-- ##### MAIN JAVASCRIPT ##### -->
	<script src="media/contentbox/js/functions.js" type="text/javascript"></script>
	



	<!-- ##### MAIN CSS FILE ##### -->
	<link href="media/contentbox/css/main.css" rel="stylesheet" type="text/css" media="screen" />

</head>
<body class="login">


<div class="box" id="newuser">
	<div class="innerTop"></div>
	<div class="innerLeft"></div>
	<div class="innerRight"></div>
	<div class="innerBottom"></div>
	
	<div class="inner">
			{if $userexists}
			{* WENN DER USER EXISTIERT, DARF ER DIE DATEN AUSF†LLEN UND SICH ANMELDEN *}
				
				{if !$formDone}
				
					<h1>Neu Anmelden</h1>
					<form class="editform longLabel" method="post" action="newuser/{$email}/{$hash}">
						<input type="hidden" style="display:none;" name="newuser[id]" value="{$userid}" />
						<fieldset>
							<legend>Profildaten &auml;ndern</legend>
								<ul>
									<li>
										<label for="form_vorname">Vorname:</label>
										<input type="text" value="" name="newuser[vorname]" id="form_vorname" maxlength="50" />
									</li>
									
									<li>
										<label for="form_nachname">Nachname:</label>
										<input type="text" value="" name="newuser[nachname]" id="form_nachname" maxlength="50" />
									</li>
									
									<li>
										<label for="form_email">Email-Adresse:</label>
										<input type="text" value="{$email}" name="newuser[email]" id="form_email" maxlength="45" />
									</li>
									
									<li>
										<label for="form_pw1">Passwort:</label>
										<input type="password" value="" name="newuser[password1]" id="form_pw1" maxlength="40" />
									</li>
									
									<li class="small">
										<label for="form_pw2">Passwort wiederholen:</label>
										<input type="password" value="" name="newuser[password2]" id="form_pw2" maxlength="40" />
									</li>
									
									<li><button type="submit" class="btn green" name="newuser[submit]" value="true"><span><span>Anmelden</span></span></button></li>
									
								</ul>
							
						</fieldset>
					</form><!--- form: taskedit ends here -->
				{else}
					<p class="success"><strong>Danke f&uuml;r die Anmeldung!</strong> Klicke <a href="{$baseurl}">hier</a>, um zur &Uuml;bersichtsseite zu gelangen, wo du dich einloggen kannst.</p>
				{/if}
				
				
				
				
			{else}
			{* WENN NICHT, DARF ER NIX MACHEN! *}
				<p class="error"><strong>Falscher Link!</strong> Bitte die Einladung nocheinmal zuschicken lassen!</p>
			{/if}
	</div>
	
	<span class="lt"></span>
	<span class="rt"></span>
	<span class="lb"></span>
	<span class="rb"></span>
</div>


<p id="createdby">
	<a href="http://franz-wilding.at">contentBox BETA - by Franz Wilding</a>
</p><!-- createdby ends here -->

</body>
</html>