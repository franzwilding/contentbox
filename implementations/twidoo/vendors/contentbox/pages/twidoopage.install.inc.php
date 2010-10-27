<?php
	
	global $TWIDOO;
	$smarty = new Smarty;
	
	$smarty->assign("mediapath", $TWIDOO["mediapath"]);
	$smarty->assign("title", $TWIDOO["title"]);
	$smarty->assign("baseurl", $TWIDOO["baseurl"]);	
	$smarty->assign("sitepath", $TWIDOO["sitepath"]);	
	$smarty->assign("version", "1.0");
	
	
	
	$getInstallForm = new twidoo_form("install");
	$getInstallForm->setRequired('vorname');
	$getInstallForm->setRequired('nachname');
	$getInstallForm->setRequired('email');
	$getInstallForm->setRequired('password');
	$getInstallForm->setRequired('password2');		
	$getInstallForm->setRule('email', 'valid', 'email');
	$getInstallForm->setRule('password', 'sameas', 'password2');
	
	$smarty->assign("formErrors", $getInstallForm->getErrors());
	$smarty->assign("formData", $getInstallForm->getValues());
	
	/* WENN DAS ANMELDEFORM ABGESENDET WURDE */
	if($getInstallForm->formDone()) {
		
		$smarty->assign("INSTALLSUCCESS", true);
		
		
		
		//TABELLEN ANLEGEN
		$createTables = new twidoo_sql;
		$createTables->setQuery('
			# Dump of table contentareas_fieldtypes
			# ------------------------------------------------------------
			
			DROP TABLE IF EXISTS `contentareas_fieldtypes`;
			
			CREATE TABLE `contentareas_fieldtypes` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ca_id` int(11) NOT NULL,
			  `the_table` varchar(100) NOT NULL,
			  `field` varchar(100) NOT NULL,
			  `type` varchar(100) NOT NULL,
			  `label` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=latin1;
			
			
			
			# Dump of table contentareas_joins
			# ------------------------------------------------------------
			
			DROP TABLE IF EXISTS `contentareas_joins`;
			
			CREATE TABLE `contentareas_joins` (
			  `id` int(11) DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;
			
			
			
			# Dump of table contentareas_keys
			# ------------------------------------------------------------
			
			CREATE TABLE `contentareas_keys` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ca_id` int(11) NOT NULL,
			  `the_key` varchar(100) NOT NULL,
			  `the_table` varchar(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
			
			
			
			# Dump of table contentareas_tables
			# ------------------------------------------------------------
			
			DROP TABLE IF EXISTS `contentareas_tables`;
			
			CREATE TABLE `contentareas_tables` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ca_id` int(11) NOT NULL,
			  `the_table` varchar(100) NOT NULL,
			  `the_where` text,
			  `the_orderby` text,
			  `the_function` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
			
			
			
			# Dump of table contentareas_userauth_contentareas
			# ------------------------------------------------------------
			
			DROP TABLE IF EXISTS `contentareas_userauth_contentareas`;
			
			CREATE TABLE `contentareas_userauth_contentareas` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `id_user` int(11) DEFAULT NULL,
			  `id_contentarea` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
			
			
			
			# Dump of table contentbox_contentareas
			# ------------------------------------------------------------
			
			DROP TABLE IF EXISTS `contentbox_contentareas`;
			
			CREATE TABLE `contentbox_contentareas` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `output` varchar(100) NOT NULL,
			  `sortIndex` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
			
			
			
			# Dump of table contentbox_userauth
			# ------------------------------------------------------------
			
			DROP TABLE IF EXISTS `contentbox_userauth`;
			
			CREATE TABLE `contentbox_userauth` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `email` varchar(100) NOT NULL,
			  `firstname` varchar(100) NOT NULL,
			  `surname` varchar(100) DEFAULT NULL,
			  `password` varchar(100) NOT NULL,
			  `hash` varchar(100) DEFAULT NULL,
			  `admin` tinyint(1) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
			
			
			
			INSERT INTO contentbox_userauth SET email=:email, firstname=:firstname, surname=:surname, password=:password, admin=1;
			');
		
		$createTables->bindParam(":email", $getInstallForm->getValue("email"));
		$createTables->bindParam(":firstname", $getInstallForm->getValue("vorname"));
		$createTables->bindParam(":surname", $getInstallForm->getValue("nachname"));
		$createTables->bindParam(":password", sha1($getInstallForm->getValue("password")));
		$createTables->execute();
		new log($createTables->getError());
		
		
	} else {
	
		$smarty->assign("INSTALLSUCCESS", false);
	
		$statuses = array();
		
		//try to access the Database AND usr/password
		$sql = new twidoo_sql;
		$sql->setQuery("");
		$sql->execute();
		
		if($sql->getError() == "SQLSTATE[HY000] [2002] No such file or directory")
			array_push($statuses, array("type" => "error", "message" => "Ich konnte die Datenbank nicht erreichen. Ist die Verbindung in <strong>twidoo/configuration/twidoo.config.inc.php</strong> richtig angegeben?"));
			
		else if( substr($sql->getError(), 0, 36) == "SQLSTATE[42000] [1044] Access denied")
			array_push($statuses, array("type" => "error", "message" => "Ich konnte die Datenbank zwar erreichen, mich aber nicht einloggen. Check nochmal den Username und das Passwort in <strong>twidoo/configuration/twidoo.config.inc.php</strong>."));
		
		
		//WENN DAS CONNECTEN KLAPPT
		else {
			array_push($statuses, array("type" => "success", "message" => "Ich konnte mich zur Datenbank verbinden, und habe mich auch erfolgreich eingeloggt!"));
			
			
			
			
			
			
			$tables = array(
				"contentbox_userauth", 
				"contentbox_contentareas", 
				"contentareas_userauth_contentareas", 
				"contentareas_tables", 
				"contentareas_keys", 
				"contentareas_joins", 
				"contentareas_fieldtypes" 
			);
			
			foreach($tables as $table) {
			
				$checkForEachTable = new twidoo_sql;
				$checkForEachTable->setQuery("SELECT * FROM ".$table);
				$checkForEachTable->execute();
				
				//wenn die Tabelle nicht existiert
				if($checkForEachTable->getError() != "") {
					array_push($statuses, array("type" => "success", "message" => "Die Tabelle: ".$table." existiert noch nicht, ich werde sie anlegen"));
				}
				
				//wenn die Tabelle schon existiert, muss sie leer sein
				else {
					if($checkForEachTable->rowCount() == 0)
						array_push($statuses, array("type" => "success", "message" => "Die Tabelle: ".$table." existiert und ist leer"));
					else
						array_push($statuses, array("type" => "error", "message" => "Die Tabelle: ".$table." existiert, ist aber nicht leer. Ich möchte keine Daten überschreiben, bitte erstelle eine neue Datenbank, oder exportiere die Daten."));
				}
				
			}
		}
	
		$smarty->assign("statuses", $statuses);
		
	}
	
	
		
	return new twidoo_content_return($smarty->fetch($TWIDOO["includepath"].'/templates/install.tpl'), true);

?>