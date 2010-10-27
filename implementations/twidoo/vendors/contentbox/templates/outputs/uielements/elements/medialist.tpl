<h3 style="width:96%; margin:25px 2% 0 2%; float:left;">{if $title}{$title}{else}Fotos in dieser Kategorie{/if}</h3>
<ul class="medialist">
{foreach from=$data item=media}
	<li>
			<a href="{$mediaPath}{$media[$pathName]}" class="fancy">
				<img src="{$mediaPath}92_{$media[$pathName]}" title="{$media[$titleName]}" alt="" />
			</a>
			<p class="details">
				{if $selectImage}
					<a class="edit" href="javascript:;">Übernehmen</a>
				{else}
					<a class="edit" href="{$baseurl}/editItem/{$media[$key]}"><span>Dieses Bild </span>bearbeiten</a>
					<a class="delete" href="javascript:;" onclick="javascript:popup($(this).parent(), $(this).parent().parent().children('p.realydelete'), 'right_above', '', true, 0, 0);">Dieses Bild löschen</a>
				{/if}
			</p>
			<p class="realydelete">
				<a class="big" href="javascript:;" onclick="javascript:myAjax($(this), '{$baseurl}/deleteItem/{$media[$key]}', 'inside', $(this).parent(), '', false, $(this).parent().parent());">Löschen</a>
				Diese Aktion kann nicht rückgängig gemacht werden!
			</p>
	</li>
{/foreach}
</ul>