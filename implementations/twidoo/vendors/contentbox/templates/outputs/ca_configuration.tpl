{* Startpage - Übersicht *}
{if $page == "start"}
	
	<div class="split" style="width:48%;">
		<h2>contentbereiche</h2>
		
		<ol class="list" id="contentareas">
			{foreach name=uebersicht from=$data item=dataRow}
				<li class="box">
					<h3><a href="{$pagePath}edit/{$dataRow.contentbox_contentareas_id}">{$dataRow.contentbox_contentareas_name}</a></h3>
					<p class="subinfo"><strong>Tabelle(n):</strong> {foreach from=$dataRow.tables item=table name=areaTables}{$table}{if !$smarty.foreach.areaTables.last},{/if}{/foreach} | <strong>Darstellung:</strong> {$dataRow.contentbox_contentareas_output}</p>
					<a class="delete" href="javascript:;" onclick="javascript:popup($(this), $(this).next(), 'right_above');">Diese contentBox löschen</a>
					<p><a class="big" href="{$pagePath}delete/{$dataRow.contentbox_contentareas_id}">Löschen</a>Diese Aktion kann nicht rückgängig gemacht werden!</p>
					{if !$smarty.foreach.uebersicht.first}
						<a class="up" href="{$pagePath}up/{$dataRow.contentbox_contentareas_id}">Eine Position nach oben verschieben</a>
					{/if}
					{if !$smarty.foreach.uebersicht.last}
						<a class="down" href="{$pagePath}down/{$dataRow.contentbox_contentareas_id}">Eine Position nach unten verschieben</a>
					{/if}
				</li>
			{/foreach}
		</ol><!-- list ends here -->
	</div>
	
	<div class="split" style="width:48%;">
		<h2>einen neuen Bereich erstellen</h2>
		<form class="box" id="newcontentarea" action="{$pagePath}edit" method="post">
			<fieldset>
				<legend>einen neuen Bereich erstellen</legend>
				<label>Name</label>
				<input type="text" name="edit[name]" />
				<label>Darstellung</label>
				<select name="edit[output]">
					{foreach from=$outputs item=curOutput}
						<option value="{$curOutput}">{$curOutput}</option>
					{/foreach}
				</select>
				
				<label>Position</label>
				<select name="edit[position]">
						<option value="0">An erste Stelle</option>
					{foreach name=uebersicht from=$data item=dataRow}
						<option value="{$dataRow.contentbox_contentareas_id}">Nach {$dataRow.contentbox_contentareas_name}</option>
					{/foreach}
				</select>
				
				<button class="bigButton" name="edit[submit]" value="true"><span>Erstellen</span></button>
			</fieldset>
		</form><!-- box ends here -->
	</div>
	
			
{* Editpage 1 *}
{elseif $page == "edit"}

	<h1>Bereich: {$edit_name}</h1>
	
	<form id="contentboxdetails" action="{$pagePath}edit2" method="post">
		<fieldset>
			<legend>Diese ContentBox bearbeiten</legend>
			<input type="hidden" name="edit[id]" value="{$edit_id}" />
			<ul>
				<li><label>Name</label><input type="text" name="edit[name]" value="{$edit_name}" /></li>
				<li>
					<label>Darstellung</label>
					<select name="edit[output]">
					{foreach from=$outputs item=curOutput}
						<option value="{$curOutput}"{if $curOutput == $edit_output} selected="selected"{/if}>{$curOutput}</option>
					{/foreach}
					</select>
				</li>
				
				<li class="tables">
					<h2>Tabellen</h2>
					<ul>
						<li style="display:none" class="box">
							<label>Tabelle</label>
							<input class="table_input" type="text" onblur="javascript:myAjax($(this), 'ajax/loadTableData', 'inside', $(this).parent(), 'tablename='+$(this).val(), true);"/>
						</li>
						
						{foreach from=$tableData item=oneTable}
							<li class="box">
								<label>Tabelle</label>
								<input class="table_input" type="text" value="{$oneTable.contentareas_tables_the_table}" onblur="javascript:myAjax($(this), 'ajax/loadTableData', 'inside', $(this).parent(), 'tablename='+$(this).val()+'&contentarea={$oneTable.contentareas_tables_ca_id}', true);" />
							</li>
						{/foreach}
						
						<li class="add"><a href="javascript:;" onclick="javascript:settings_addTable();" class="smallButtonBig">+ eine Tabelle hinzufügen</a></li>
					</ul>
				</li>
				
				<li class="submit"><button class="bigButton" name="edit[submit]" value="true"><span>Speichern</span></button></li>
			</ul>
			
		</fieldset>
	</form>
{/if}
