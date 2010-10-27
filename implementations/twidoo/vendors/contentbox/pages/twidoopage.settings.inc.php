<?php
	global $TWIDOO;

	$myManagement = new twidoo_datamanagement();
	$myManagement->setTables(array(
		"contentbox_contentareas", 
		"contentareas_tables", 
		"contentareas_joins", 
		"contentareas_keys", 
		"contentareas_fieldtypes", 
		"contentbox_userauth", 
		"contentareas_userauth_contentareas"
		));
	$myManagement->setOrderBy("contentbox_contentareas", "sortIndex");
	$myManagement->setOutput("contentAreas");
	
	return new twidoo_content_return($myManagement->execute());
?>