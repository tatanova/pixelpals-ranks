
# [PixelPals/Ranks]

> ⚠️ **Status: Archived / Unmaintained**  
> This is a legacy PHP project made for <a href="https://github.com/pmmp/PocketMine-MP">PMMP 3.0.0</a> (MCPE 0.15.10) and preserved for historical purposes only. It is no longer actively developed, updated, or monitored for security vulnerabilities.

</br>

<h2 align="center"><span>About</span></h2>
<p>This is a simple and a compact role management plugin inspired from PurePerms and PureChat plugins.</br>You can edit each role manualy by editing the config file and this plugin does not support any Economy integration, however such a feature can be added quite easily.</p>

</br>

<h2 align="center"><span>Commands</span></h2>

<h4>Rank</h4>

###### This command requires rank.use permission and is an admin command by default

```
/rank
```

> Shows help menu for this command

</br>

```
/rank set <player> <rank>
/rank add <player> <rank>
```

> Can be used to change a player's rank/role, \<player\> must be valid and online player name

</br>

```
/rank list
/rank ls
```

> Shows the list of loaded valid ranks/roles

</br>

<h2 align="center"><span>Permissions</span></h2>

- **`rank.use`**
  - Admin permission

</br>

<h2 align="center"><span>Config</span></h2>


```YAML
# NOTE: those permissions are from PixelPals' other system plugins
# and admin role does not exist in the released plugin
---
player:
  perms:
  - default.player
  - help.cmd.use
  chat: "§7* §fPlayer §7{username} §8» §f{msg}" # {username} is the literal username of the player
  nametag: "Player §7* {nick}"
owner:
  perms:
  - op # just adding op will grand all the permissions
  chat: "§6* §eOwner §7{username} §8» §e{msg}" # {msg} is the message context that is about to be sent by the player
  nametag: "§eOwner §6* §7{nick}"
admin:
  perms:
  - instant.cc
  - pp.main.ban
  - pp.main.mute
  - pp.spec
  - pp.shop.vip
  chat: "§e* §cAdmin §7{username} §8» §c{msg}"
  nametag: "§cAdmin §e* §7{nick}" # {nick} is the nickname which is not username (nickname can be changed but username is static)
...
```

</br>

<h2 align="center"><span>File activity</span></h2>

When enabled, the plugin will create:
<br>

- A YAML file: __~/PP_Ranks/p/ranks.pp__
- A YAML file: __~/PP_Ranks/p/users.dpp__

</br>

<h2 align="center"><span>Affected events</span></h2>

- PlayerJoinEvent
- PlayerQuitEvent
- PlayerChatEvent

</br>
</br>
</br>

<p align="center"><img src="https://media.tenor.com/o9rNU1uX_R0AAAAj/cat-campfire.gif" alt="cats" width="200"/></p>
<p align="center">
  <sub>Made with <b>0.0% AI</b> / <b>100.0% Human Code & Passion</b></sub><br>
  <sub><i>Preserved with pride from the PixelPals era.</i></sub>
</p>
