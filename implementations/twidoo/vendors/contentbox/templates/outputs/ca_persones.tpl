<h1>Benutzer_inneneinstellungen</h1>

<div class="split" style="width:48%;">
	<h2>userrechte</h2>
	<ul class="list" id="user">
		{foreach from=$user item=cur_user}
			{if $cur_user.contentbox_userauth_password != ""}
				<li class="box">
					<h3>{$cur_user.contentbox_userauth_firstname} {$cur_user.contentbox_userauth_surname} {if $cur_user.contentbox_userauth_admin}<span>Admin</span>{/if}</h3>
					{if !$cur_user.contentbox_userauth_admin}
						<form action="javascript:;" method="post">
							<fieldset>
								<legend>Userrechte von {$cur_user.contentbox_userauth_firstname} {$cur_user.contentbox_userauth_surname} bearbeiten</legend>
									<ul>
										{foreach from=$areas item=area}
											<li><input type="checkbox"{if $userauth[$cur_user.contentbox_userauth_id][$area.contentbox_contentareas_id]} checked="checked"{/if} name="{$area.contentbox_contentareas_id}" id="areas_{$cur_user.contentbox_userauth_id}_{$area.contentbox_contentareas_id}"/><label for="areas_{$cur_user.contentbox_userauth_id}_{$area.contentbox_contentareas_id}">{$area.contentbox_contentareas_name}</label></li>
										{/foreach}
									</ul>
									<button class="smallButtonSmall" onclick="javascript:myAjax($(this), 'ajax/changeUserAuth', 'inline', $(this).parent(), 'data='+returnCheckedBoxes($(this).prev())+'&user={$cur_user.contentbox_userauth_id}', false);">Änderungen speichern</button>
							</fieldset>
						</form>
						<a class="delete" href="javascript:;" onclick="javascript:popup($(this), $(this).next(), 'right_above');">Diesen User löschen</a>
						<p><a class="big" href="{$pagePath}user_delete/{$cur_user.contentbox_userauth_id}">Löschen</a>Diese Aktion kann nicht rückgängig gemacht werden!</p>
					{/if}
				</li>
			{/if}
		{/foreach}
	</ul>
</div>



<div class="split" style="width:48%;">
	<h2>user_innen einladen</h2>
	<form class="box" id="inviteform" action="javascript:;" method="post">
		<fieldset>
			<legend>user_innen einladen</legend>
			<label for="invite_emails">Emailadressen <span>(mit Beistrich getrennt)</span></label>
			<textarea id="invite_emails" name="invite[emails]" onfocus="if(this.value=='mail@franz-wilding.at, anna@beispiel.at')this.value=''" onblur="if(this.value=='')this.value='mail@franz-wilding.at, anna@beispiel.at'">mail@franz-wilding.at, anna@beispiel.at</textarea>
			
			<label for="invite_message">Nachricht</label>
			<textarea id="invite_message" name="invite[text]" onfocus="if(this.value=='{$username} möchte dich einladen, gemeinsam die Website {$pageTitle} zu verwalten.')this.value=''" onblur="if(this.value=='')this.value='{$username} möchte dich einladen, gemeinsam die Website {$pageTitle} zu verwalten.'">{$username} möchte dich einladen, gemeinsam die Website {$pageTitle} zu verwalten.</textarea>
			<button class="bigButton" name="invite[submit]" onclick="javascript:myAjax($(this), 'ajax/email_invite', 'inline', $(this).parent(), 'emails='+$('#invite_emails').val()+'&message='+$('#invite_message').val(), true);" value="true"><span>Einladungen versenden</span></button>
		</fieldset>
	</form><!-- box ends here -->
</div>