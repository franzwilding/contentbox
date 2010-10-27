<ul>
{foreach from=$array item=element key=key name=recursiv_list}
	<li{if $element.active} class="active"{/if}>
		{if $element.$idName > 0 || $element.$idName != ""}
			<a class="edit" {if $ajaxSelector}rel="{$element.$idName}" {/if}href="{if $ajaxSelector}javascript:;{else}{$baseurl}/detail/{$element.$idName}{/if}">{$element.$titleName|truncate:25:"...":true}</a>

			{if $element.active}
				{if $tree}<a class="add" href="{$baseurl}/instandAdd/{$element.$idName}">+</a>{/if}
				<a class="delete" href="javascript:;" onclick="javascript:popup($(this).parent(), $(this).parent().children('p'), 'right_above', '', false, -16, 0);">-</a>
			{/if}
			{if $ajaxSelector} 
			{else}
				<a class="up" href="{$baseurl}/sortIndex/up/{$element.$idName}">Rauf</a>
				<a class="down" href="{$baseurl}/sortIndex/down/{$element.$idName}">Runter</a>
			{/if}
			<!--span class="loading">Laden...</span-->
		{else}			
			{$element.$titleName}
		{/if}
		
		{if !empty($element.children)}
			{assign var="curID" value=$element.id}
			{include 
				file="$includepath/helpers/recursiv_list.tpl" 
				idName=$idName 
				titleName=$titleName 
				includepath=$includepath 
				baseurl="$baseurl" 
				array=$element.children}
		{/if}
		
		<p>
			<a class="big" href="{$baseurl}/delete/{$element.$idName}">Löschen</a>
			Diese Aktion kann nicht rückgängig gemacht werden!
		</p>
	</li>
{/foreach}
</ul>