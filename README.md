# FRIENDS MODULE

### [INSTALLATION]
- Upload all the contents of "upload" directory to the root directory of your NamelessMC installation.
- Then login to your staff panel, and head over to templates section and click "Install" button.
- Then enable the newly appeared module named "Friends", also make it default.
- Thats it!

### [NOTE]
- This module only contains the support for **DefaultRevamp** Template.
- If you have made any changes to `/custom/templates/DefaultRevamp/profile.tpl` file, make sure to back it up. Installing this module will overwrite this file.

### [FOR TEMPLATE AUTHORS]

#### If any template author wants to add the support for this module:
- You can use `{if isset($FRIENDS)} ... {/if}` to check if the Friends module is enabled.
- This module's functions and variables only work on Profile page.

##### Adding the friend button:
```
<form action="" method="post">
    <input type="hidden" name="token" value="{$TOKEN}">
    <input type="hidden" name="action" value="{$FRIEND.action}">
    <button type="submit" class="btn btn-danger btn-block my-3">{$FRIEND.icon} {$FRIEND.text}</button>
</form>
```
- The variable `{$FRIEND.action}` produces one of the four actions: `addFriend`, `removeFriend`, `acceptRequest`, `cancelRequest` depeding upon the case.
- The variable `{$FRIEND.icon}` produces the icon of the action.
- The variable `{$FRIEND.text}` produces the name of the action and can change its value depeding on the language.
- You must use the `{if !isset($SELF)} ... {/if}` condition to make sure the user is not on his own profile.
- You must use the `{if isset($LOGGED_IN_USER)} ... {/if}` condition to make sure the user is not a guest.

##### Printing the friends list:
```
<div class="card card-default">
    <div class="card-header">
        {$FRIENDS}
    </div>
    <div class="card-body text-center">	
        {if count($FRIENDS_LIST)}
            {foreach from=$FRIENDS_LIST item=$item}
                <a href="{$item.profile}">
                    <img class="rounded float-none" src="{$item.avatar}" data-toggle="tooltip" title="{$item.nickname}">
                </a>
            {/foreach}
        {else}
            {$NO_FRIENDS}
        {/if}
    </div>
</div>
```
- The variable `{$FRIENDS}` is the title of the Friends list and can change its value depeding on the language.
- The condition `{if count($FRIENDS_LIST)} ... {/if}` checks if the user's friends list is not empty, otherwise print the `{$NO_FRIENDS}` variable.
- The loop `{foreach from=$FRIENDS_LIST item=$item} ... {/foreach}` cycles all the friends of the user.
- The loop can have these subscripts: `$item.id`, `$item.avatar`, `$item.username`, `$item.nickname` and `$item.profile`.

### [CONTACT]
- Discord: https://xemah.com/support
- Email: contact@xemah.com
