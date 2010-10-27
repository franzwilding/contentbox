<h2>Fotos in das Album "{$category_name}" hinzufügen</h2>

<form class="addFotos" style="width:94%;" action="{$baseurl}/" method="post">
	<fieldset>
		<legend>Fotos hinzufügen</legend>
		<div id="fileQueue"></div>
		<input type="file" id="uploadify" name="addFotos[media]" />
		<!--button name="add[submit]" value="true" class="bigButton"><span>Hinzufügen</span></button-->

	</fieldset>
</form>

{literal}
<script type="text/javascript">
	//UPLOADIFY
	$("#uploadify").uploadify({
		'uploader'       : '/media/contentbox/js/uploadify/scripts/uploadify.swf',
		'script'         : 'processFotos',
		'cancelImg'      : '/media/contentbox/css/img/error.gif',
		'folder'         : '/media/images',
		'queueID'        : 'fileQueue',
		'auto'           : true, 
		'scriptData': {/literal}{$data}{literal},
		'multi'          : true, 
		'onComplite'	 : function uploadifyComplete(event, queueID, fileObj, reposnse, data) {
			console.log(reposnse); 
			console.log(data); 
			console.log(fileObj); 
		},
		'onAllComplete'     : function uploadifyComplete(){
        }
	});
</script>
{/literal}

<p style="margin-left:2%;"><a href="{$baseurl}/detail/{$id}" class="smallButtonBig">Zurück zur Übersicht</a></p>