<!--
	Layout: SourceView
	Parameter: 
		- includepath (Pfad bis zu dem Verzeichnis "uielements") 
		- baseurl (inkl. PageID)
		- titel (Wird als Titel angezeigt, und als ID )
		- id= (Die ID des aktuellen Elements, wichtig um zu sehen ob wir "add" haben)
		- left (Content-Array links)
		- right (Content-HTLM rechts)
	Implementierung:
		- Array kann verschachtelt sein
		- Attribute (id, title, active, children)
		- sub_"titel"_add2 muss als Funktion implementiert werden
		- right sollte die Informationen oder Details für das aktuell ausgewählte Element enthalten
-->
<div class="sourceview {$classname}">
	<div class="left">
		
		<h2>{$titel}<a href="" class="smallButtonBig">+ Hinzufügen</a></h2>
		<form class="addRootElement" action="{$baseurl}/instandAdd" method="post">
			<fieldset>
				<legend>Ein neues Element hinzufügen</legend>
				<input type="text" name="add[title]" /><button name="add[submit]" value="true" class="bigButton"><span>Hinzufügen</span></button>
			</fieldset>
		</form>
		
		{include 
			file="$includepath/helpers/recursiv_list.tpl" 
			includepath=$includepath  
			baseurl="$baseurl" 
			idName=$idName 
			titleName=$titleName 
			tree=$tree
			array=$left}
		<p class="c"></p>		
	</div>
	<div class="right">
		{$right}
	</div>
</div>