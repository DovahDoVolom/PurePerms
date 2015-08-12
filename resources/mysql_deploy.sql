/* PurePerms by 64FF00 (xktiverz@gmail.com, @64ff00 for Twitter) */

CREATE TABLE IF NOT EXISTS groups (
  groupName CHAR(255) PRIMARY KEY NOT NULL,
  isDefault INTEGER NOT NULL DEFAULT 0,
  inheritance CHAR(255) NOT NULL,
  permissions VARCHAR(65535) NOT NULL
);

INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ("Guest", 1, "", "-essentials.kit,-essentials.kit.other,-pocketmine.command.me,pocketmine.command.list,pperms.command.ppinfo");
INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ("Admin", 0, "Guest", "essentials.gamemode,pocketmine.broadcast,pocketmine.command.gamemode,pocketmine.command.give,pocketmine.command.kick,pocketmine.command.teleport,pocketmine.command.time");
INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ("Owner", 0, "Guest,Admin", "essentials,pocketmine.command,pperms.command");
INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ("OP", 0, "", "*");

CREATE TABLE IF NOT EXISTS groups_mw (
  groupName CHAR(255) PRIMARY KEY NOT NULL,
  worldName CHAR(255) NOT NULL,
  permissions VARCHAR(65535) NOT NULL
);

CREATE TABLE IF NOT EXISTS players (
  userName VARCHAR(16) PRIMARY KEY NOT NULL,
  userGroup CHAR(255) NOT NULL,
  permissions VARCHAR(65535) NOT NULL
);

CREATE TABLE IF NOT EXISTS players_mw (
  userName VARCHAR(16) PRIMARY KEY NOT NULL,
  worldName CHAR(255) NOT NULL,
  userGroup CHAR(255) NOT NULL,
  permissions VARCHAR(65535) NOT NULL
);
