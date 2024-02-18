![](https://www.expresstechsoftwares.com/wp-content/uploads/paidmembershippro_discord_addon_banner.png)

# [Connect Paid Memberships Pro to Discord](https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-pmpro-and-discord-server-using-discord-addon/) #
![](https://img.shields.io/badge/build-passing-green) ![License](https://img.shields.io/badge/license-GPL--2.0%2B-red.svg)

### Welcome to the PMPRO Discord Add On GitHub Repository

This add-on enables connecting your PMPRO enabled website to your discord server. Now you can add/remove PMPRO members directly to your discord server, assign roles according to the membership levels, unassign roles when member expire or cancel, change role when member change membership.

# [Step By Step guide on how to set-up plugin](https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-pmpro-and-discord-server-using-discord-addon/)


## Installation
- You can find the plugin inside the PMPRO settings Add-ons and click install from there
- OR Upload the `pmpro-discord` folder to the `/wp-content/plugins/` directory.
- Activate the plugin through the 'Installed Plugins' page in WordPress admin.

## Connecting the plugin to your Discord Server.
- Inside WP Admin, you will find Discord Settings sub-menu under top-level PMPRRO Memberships menu in the left hand side.
- Login to your dsicord account and open this url: https://discord.com/developers/applications
- Click Top right button "New Appliaction", and name your Application.
- New screen will load, you need to look at left hand side and see "oAuth".
- See right hand side, you will see "CLIENT ID and CLIENT SECRET" values copy them.
- Open the discord settings page.
- Paste the copied ClientID and ClientSecret.
- If the PMPRO is already setup you will see redirect URL inside plugin settings. Just copy it and paste into Discord "Redirect URL" then save settings in Discord.
- Now again see inside discord left hand side menu, you will see "Bot" page link.
- This is very important, you need to name your bot and click generate, this will generate "Bot Token".
- Copy the "Bot Token" and paste into "Bot Token" setting of Discord aa-on Plugin.
- Now the last and most important setting, "Server ID".
- - Open https://discord.com/ and go inside your server.
- - Enable Developer mode by going into Advanced setting of your account.
- - Then you should right click on your server name and you will see "Copy ID"
- - Copy and paste into "Guild ID" Settings
- Now you will see "Connect your bot" button on your plugin settings page.
- Click Connect your bot button and this will take you to the Discord authorisation page.
- Here you need to select the Server of which Guild ID you just did copy in above steps.
- Once successfully connect you should see Bot Authorized screen.
- Open again the discord server settings and see Roles menu.
- Please make sure your bot role has the highest priority among all other roles in your discord server roles settings otherwise you will see 5000:Missing Access Error in your plugin error logs.

## Some features
- Allow any member to connect their discord account with your PaidMebershipPro membership website.
- Members will be assigned roles in discord as per their membership level.
- Members roles can be changed/remove from the admin of the site.
- Members roles will be updated when membership expires.
- Members roles will be updated when membership cancelled.
- Admin can decide what default role to be given to all members upon connecting their discord to their membership account.
- Admin can decide if membership should stay in their discord server when membership expires or cancelled.
- Admin can decide what default role to be assigned when membership cancelled or expire.
- Admin can change role by changing the membership by editng user insider WP Manage user.
- Send a Direct message to discord members when their membership has expired. (Only work when allow none member is set to YES and Direct Message advanced setting is set ENABLED)
- Send a Direct message to discord members when their membership is cancelled. (Only work when allow none member is set to YES and Direct Message advanced setting is set ENABLED)
- Send membership expiration warnings Direct Message when membership is about to expire (Default 7 days before)
- Short code [discord_connect_button] can be used on any page to display connect/disconnect button.
- Using the shortcode [discord_connect_button] on any page, anyone can join the website discord server by authentication via member discord account. New members will get `default` role if selected in the setting.
- Button styling feature under the plugin settings.
- Support of Paid Memberships Pro - Cancel on Next Payment Date. So the member role wont get removed immediately upon cancel.
- Support for forced discord authentication before checkout.

- Hide the connect button using the simple filter: ets_pmpro_show_connect_button_on_profile
`add_filter('ets_pmpro_show_connect_button_on_profile', '__return_false' );`
Adding above code line in functions.php of theme or using code snippet plugin.

## Solution of Missing Access Error
- Inside the log tab you will see "50001:Missing Access", which is happening because the new BOT role need to the TOP priroty among the other roles.
- - The new created BOT will add a ROLE with the same name as it is given to the BOT itself.
- So, Go inside the "Server Settings" from the TOP left menu.
- Go inside the "Roles" and Drag and Drop the new BOT over to the TOP all other roles.
- Do not for forget to save the roles settings

# Fequently Asked Questions
- I'm getting an error in error Log 'Missing Access'
- - Please make sure your bot role has the highest priority among all other roles in your discord server roles settings. Watch this video https://youtu.be/v7lxB_Bvlv4?t=363
- Role Settings is not appearing.
- - Clear browser cache, to uninstall and install again.
- - Try the disabling cache
- - Try Disabling other plugins, there may be any conflict with another plugin.
- Members are not being added spontaneously. 
- - Due to the nature of Discord API, we have to use schedules to precisely control API calls, that why actions are delayed. 
- Member roles are not being assigned spontaneously.
- - Due to the nature of Discord API, we have to use schedules to precisely control API calls, that why actions are delayed. 
- Some members are not getting their role and there is no error in the log.
- - Sometimes discord API behaves weirdly, It is suggested to TRY again OR use another discord account.
- After expiry or member cancellation the roles are not being removed
- - It is seen in discord API that it return SUCCESS but does not work sometimes. It is suggested to manually adjust roles via PMPPRO->Members table.
