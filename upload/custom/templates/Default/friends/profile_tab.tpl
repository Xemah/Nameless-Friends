{if isset($FRIENDS)}

	{if isset($FRIENDS.button)}
		{if ($FRIENDS.button.action == 'add_friend')}
			{assign var='FRIENDS_BUTTON_CLASS' value='btn btn-sm btn-success'}
			{assign var='FRIENDS_BUTTON_ICON' value='<i class="fas fa-user-plus mr-1"></i>'}
		{else if ($FRIENDS.button.action == 'remove_friend')}
			{assign var='FRIENDS_BUTTON_CLASS' value='btn btn-sm btn-danger'}
			{assign var='FRIENDS_BUTTON_ICON' value='<i class="fas fa-user-times mr-1"></i>'}
		{else if ($FRIENDS.button.action == 'accept_request')}
			{assign var='FRIENDS_BUTTON_CLASS' value='btn btn-sm btn-success'}
			{assign var='FRIENDS_BUTTON_ICON' value='<i class="fas fa-user-check mr-1"></i>'}
		{else if ($FRIENDS.button.action == 'cancel_request')}
			{assign var='FRIENDS_BUTTON_CLASS' value='btn btn-sm btn-warning'}
			{assign var='FRIENDS_BUTTON_ICON' value='<i class="fas fa-user-minus mr-1"></i>'}
		{/if}
	{/if}

	<h3>
		{$FRIENDS.title}
		{if isset($FRIENDS.button)}
			<form action="" method="post" id="form-friend" style="float: right;">
				<input type="hidden" name="token" value="{$TOKEN}">
				<input type="hidden" name="action" value="{$FRIENDS.button.action}">
				<button type="submit" class="{$FRIENDS_BUTTON_CLASS}">{$FRIENDS_BUTTON_ICON} {$FRIENDS.button.text}</button>
			</form>
		{/if}
	</h3>

	{if !empty($FRIENDS.list)}
		<div class="row" style="margin-top: 1.5rem; margin-bottom: -1rem;">
			{foreach from=$FRIENDS.list item=$friend}
				<div class="col-md-3 text-center mb-3">
					<img src="{$friend.avatar}" style="display: block; margin: 0 auto .5rem; width: 60px; height: 60px; border-radius: 50%;">
					<a style="{$friend.style}" href="{$friend.profile}">{$friend.nickname}</a>
				</div>
			{/foreach}
		</div>
	{else}
		<p>{$FRIENDS.no_friends}</p>
	{/if}

{/if}