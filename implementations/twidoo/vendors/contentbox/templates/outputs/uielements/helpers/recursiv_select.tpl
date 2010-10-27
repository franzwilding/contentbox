{foreach from=$array item=element key=key name=recursiv_select}
<option style="padding-left:{$startlevel*10}px;" value="{$element.id}"{if $selectId == $element.id} selected="selected"{/if}>{$element.title}</option>

{if !empty($element.children)}
	{assign var="curID" value=$element.id}
	{include 
		file="$includepath/helpers/recursiv_select.tpl" 
		includepath=$includepath 
		startlevel=$startlevel+1 
		selectId=$selectId 
		array=$element.children}
{/if}
{/foreach}