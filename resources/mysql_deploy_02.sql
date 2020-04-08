/*
    PurePerms by 64FF00 (Twitter: @64FF00)

      888  888    .d8888b.      d8888  8888888888 8888888888 .d8888b.   .d8888b.
      888  888   d88P  Y88b    d8P888  888        888       d88P  Y88b d88P  Y88b
    888888888888 888          d8P 888  888        888       888    888 888    888
      888  888   888d888b.   d8P  888  8888888    8888888   888    888 888    888
      888  888   888P "Y88b d88   888  888        888       888    888 888    888
    888888888888 888    888 8888888888 888        888       888    888 888    888
      888  888   Y88b  d88P       888  888        888       Y88b  d88P Y88b  d88P
      888  888    "Y8888P"        888  888        888        "Y8888P"   "Y8888P"
*/

INSERT IGNORE INTO groups (groupName, alias, isDefault, inheritance, permissions) VALUES ('Guest', 'gst', 1, '', '-essentials.kit,-essentials.kit.other,-pocketmine.command.me,pchat.colored.format,pchat.colored.nametag,pocketmine.command.list,pperms.command.ppinfo');
INSERT IGNORE INTO groups (groupName, alias, isDefault, inheritance, permissions) VALUES ('Admin', 'adm', 0, 'Guest', 'essentials.gamemode,pocketmine.broadcast,pocketmine.command.gamemode,pocketmine.command.give,pocketmine.command.kick,pocketmine.command.teleport,pocketmine.command.time');
INSERT IGNORE INTO groups (groupName, alias, isDefault, inheritance, permissions) VALUES ('Owner', 'owr', 0, 'Admin', 'essentials,pocketmine.command,pperms.command');
INSERT IGNORE INTO groups (groupName, alias, isDefault, inheritance, permissions) VALUES ('OP', 'op', 0, '', '*');