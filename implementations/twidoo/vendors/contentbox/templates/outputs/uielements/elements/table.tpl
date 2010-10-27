
{foreach from=$tableData item=curTableData key=tableName}

<h2 class="add">
{if $tableData|@count > 1}
	<a class="toggleTable" href="javascript:;">Tabelle ein/ausblenden</a>
{/if}
{$tableName} <a href="{$baseurl}/edit/{$tableName}/" class="addButtonBig">+ eine weitere Zeile einfügen</a></h2>
	
	{if $curTableData[0]|@count > 0}
	<form action="{$baseurl}/multidelete" method="post">
		<fieldset>
			<legend>Löschen</legend>
			<input type="hidden" name="delete[setname]" value="{$tableName}" />
			<table>
				<tr class="headline">
					{foreach from=$curTableData[0] key=th item=tc name=headerNames}
						
						{if $fields[$tableName][$th] != "hidden" || $smarty.foreach.headerNames.first}
							<th>
								{if $smarty.foreach.headerNames.first}<span class="id">{/if}
								
								{* Wenn wir Labels haben, nehmen wir diese, anonsten die field_names*}
								{if $labels[$tableName][$th] != ""}
								
									{$labels[$tableName][$th]}
								
								{else}
			
									{if $fromDatamanagement} {assign var=name value="_"|explode:$th} {$name[1]}
									{else} {$th} {/if}
								
								{/if}
		
								{if $smarty.foreach.headerNames.first}</span>{/if}
							</th>
						{/if}
					{/foreach}
						<th><span class="delete">Löschen</span></th>
				</tr>
		
			{foreach from=$curTableData item=datarow name=datasettable}
				<tr>
					{foreach from=$datarow key=cellname item=datacell}
						{* KEY *}
						{if $cellname == $key || $cellname == $key[$tableName]}
							<td>
								<a class="id" href="{$baseurl}/edit/{$tableName}/{$datacell}">{assign value=$datacell var="curID"}{$datacell}</a>
							</td>
						
						{* IMG *}
						{elseif $fields[$tableName][$cellname] == "img"}
							<td>
								<a class="smallimage fancy" href="{$mediaPath}{$datacell}"><img src="{$mediaPath}16_{$datacell}" title="" alt="" /></a>
							</td>
							
						{* CHECKBOX *}
						{elseif $fields[$tableName][$cellname] == "tinyint"}
							<td>
								{if $datacell == 1}
									<span class="checkbox">{$datacell}</span>
								{/if}
							</td>
							
						{* date *}
						{elseif $fields[$tableName][$cellname] == "date"}
							<td class="date">
								{$datacell}
							</td>
						
						{* date *}
						{elseif $fields[$tableName][$cellname] == "hidden"}
						
						{else}
							<td>
								{$datacell}
							</td>
						{/if}
					{/foreach}
						<td class="delete"><input name="delete[rows][{$curID}]" type="checkbox"/></td>
				</tr>	
			{/foreach}
	</table>
	<p class="table_delete">
		<button>Löschen</button>
		<span>Möchtest du wirklich alle selektierten Einträge löschen?</span>
	</p>
	</fieldset>
	</form>
	{/if}
	
	
	{if $tableData|@count > 1}
		<hr />
	{/if}
	
	
{/foreach}