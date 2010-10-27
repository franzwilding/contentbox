<h2>{if $edit_id == -1}Neue(n) {$name} erstellen{else}{$name} Bearbeiten{/if}</h2>
<form class="edit" action="{$pagePath}/{$ca_key}/edit2/{$optionalParameter}" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>{$name} bearbeiten/erstellen</legend>
			<input type="hidden" name="edit[the_incredible_page_id]" value="{$id}" />
			<ul>
				{foreach from=$edit_fields item=field key=label name=editForm}
					{assign var="varname" value="`$tablename`_`$label`"} 
					{if $labels[$label] != ""}
						{assign var="shownLabel" value=$labels[$label]} 
					{else}
						{assign var="shownLabel" value=$label} 
					{/if}
					
					{assign var="includepath" value=$smarty.template}
								{assign var="includepathCount" value=$includepath|count_characters:true}
								{assign var="includepath" value=$includepath|truncate:$includepathCount-18:"":true}
								{assign var="curIndex" value=$smarty.foreach.editForm.index}
								

					
					
					{* Wenn wir special Felder haben*}
					{assign var="testField" value="("|explode:$field}
					{if $testField|@count == 2}
						
						{* ENUM *}
						{if $testField[0] == "ENUM"}
							{assign var="enumfelder1" value=")"|explode:$testField[1]}
							{assign var="enumfelder" value=";"|explode:$enumfelder1[0]}							
							<li class="select">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								<select id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]">
									{foreach from=$enumfelder item=option}
										<option value="{$option}"{if $edit_data[$varname] == $option} selected="selected"{/if}>{$option}</option>
									{/foreach}
								</select>
							</li>
						{/if}
					{/if}
						
						
					{if $field == "hidden"}
						<input id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]" type="hidden" value="{$edit_data[$varname]}" />	
					{else}
						{if $field == "varchar" || $field == "int" || $field == "password" || $field == "float"}
							<li class="text">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								<input id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]" type="text" value="{$edit_data[$varname]}" />
							</li>
						{elseif $field == "noedit"}
							<li class="noedit">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								<input id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]" disabled="disabled" type="text" value="{$edit_data[$varname]}" />
							</li>
						{elseif $field == "file"}
							<li class="file">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								<input id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]" size="13" type="file"/>
							</li>
						{elseif $field == "img"}
							<li class="file img">
								<label for="f_{$smarty.foreach.editForm.index}" style="background:url()">
									{if $edit_data[$varname] != ""}
										<a class="smallimage fancy" href="{$mediaPath}{$edit_data[$varname]}"><img src="{$mediaPath}16_{$edit_data[$varname]}" title="" alt="" /></a>
									{/if}
									{$shownLabel}: 
								</label>
								<input id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]" size="25" type="file"/>
							</li>								
						{elseif $field == "text"}
							<li class="textarea">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								<textarea id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]">{$edit_data[$varname]}</textarea>
							</li>
						{elseif $field == "timestamp"}
							<li class="text">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								<input id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]" type="text" value="{$edit_data[$varname]}" />
						{elseif $field == "date"}
							<li class="date">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								<input id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]" type="text" value="{$edit_data[$varname]|date_format:"%d.%m.%Y"}" />
							</li>
						
						{elseif $field == "datetime"}
							<li class="datetime">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								<input id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]" type="text" value="{$edit_data[$varname]|date_format:"%d.%m.%Y"}" />
								<select name="edit[hour][{$label}]">
									{section loop=24 start=0 step=1 name="hour"}
										<option value="{$smarty.section.hour.index}"{if $edit_data[$varname]|date_format:"%H" == "%02d"|sprintf:$smarty.section.hour.index} selected="selected"{/if}>{"%02d"|sprintf:$smarty.section.hour.index}</option>
									{/section}
								</select>
								
								<select name="edit[minute][{$label}]">
									{section loop=60 start=0 step=15 name="minute"}
										<option value="{$smarty.section.minute.index}"{if $edit_data[$varname]|date_format:"%M" == "%02d"|sprintf:$smarty.section.minute.index} selected="selected"{/if}>{"%02d"|sprintf:$smarty.section.minute.index}</option>
									{/section}
								</select>
							</li>
						
						{elseif $field == "tinyint"}
							<li class="checkbox">
								<input type="checkbox" id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]"{if $edit_data[$varname] == 1} checked="checked"{/if}/>	
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel} </label>
							</li>
						{elseif $field == "editor"}
							<li class="editor">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								{include 
									file="$includepath/elements/editor.tpl" 
									value=$edit_data[$varname] 
									name="edit[$label]" 
									id="f_$curIndex" 
									}
							</li>
						{elseif is_array($field) && $field.type == "select"}
							<li class="select">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								<select id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]">
									{foreach from=$field.content item=option key=optionKey}
										<option value="{$optionKey}"{if $edit_data[$varname] == $optionKey} selected="selected"{/if}>{$option}</option>
									{/foreach}
								</select>
							</li>
							
						{elseif is_array($field) && $field.type == "selectTree"}
							<li class="select">
								<label for="f_{$smarty.foreach.editForm.index}">{$shownLabel}: </label>
								<select id="f_{$smarty.foreach.editForm.index}" name="edit[{$label}]">
									{include 
									file="$includepath/helpers/recursiv_select.tpl" 
									array=$field.content 
									selectId=$edit_data[$varname] 
									startlevel=0}
								</select>
							</li>
						{/if}
					{/if}
				{/foreach}
					<li class="submit">
						<button class="bigButton" type="submit" tabindex="3" name="edit[submit]" value="true"><span>Speichern</span></button>
						<a class="smallButtonSmall" href="{$baseurl}">Oder doch lieber nicht</a>
					</li>
			</ul>
		</fieldset>
	</form>
	{if $info}
		<div class="info">{$info}</div>
	{/if}
	<p class="c"></p>