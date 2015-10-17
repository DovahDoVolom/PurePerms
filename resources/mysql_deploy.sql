CREATE TABLE IF NOT EXISTS groups(
  groupName VARCHAR(64) PRIMARY KEY NOT NULL,
  isDefault BOOLEAN DEFAULT 0 NOT NULL,
  inheritance TEXT NOT NULL,
  permissions TEXT NOT NULL
);

INSERT IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ('Guest', 1, '', '-essentials.kit,-essentials.kit.other,-pocketmine.command.me,pchat.colored.format,pchat.colored.nametag,pocketmine.command.list,pperms.command.ppinfo');
INSERT IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ('Admin', 0, 'Guest', 'essentials.gamemode,pocketmine.broadcast,pocketmine.command.gamemode,pocketmine.command.give,pocketmine.command.kick,pocketmine.command.teleport,pocketmine.command.time');
INSERT IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ('Owner', 0, 'Guest,Admin', 'essentials,pocketmine.command,pperms.command');
INSERT IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ('OP', 0, '', '*');

CREATE TABLE IF NOT EXISTS groups_mw(
  groupName VARCHAR(64) PRIMARY KEY NOT NULL,
  worldName TEXT NOT NULL,
  permissions TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS players(
  userName VARCHAR(16) PRIMARY KEY NOT NULL,
  userGroup VARCHAR(32) NOT NULL,
  permissions TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS players_mw(
  userName VARCHAR(16) PRIMARY KEY NOT NULL,
  worldName TEXT NOT NULL,
  userGroup VARCHAR(32) NOT NULL,
  permissions TEXT NOT NULL
);