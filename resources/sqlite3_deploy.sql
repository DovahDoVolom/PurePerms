CREATE TABLE IF NOT EXISTS groups (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  groupName TEXT,
  isDefault INTEGER NOT NULL DEFAULT 0,
  inheritance TEXT,
  permissions TEXT
);

INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES("Guest", 1, NULL, "-pocketmine.command.me,pocketmine.command.list,pperms.command.ppinfo");
INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES("Admin", 0, "Guest", "pocketmine.broadcast,pocketmine.command.gamemode,pocketmine.command.give,pocketmine.command.kick,pocketmine.command.teleport,pocketmine.command.time");
INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES("Owner", 0, "Guest,Admin", "pocketmine.command,pperms.command");

CREATE TABLE IF NOT EXISTS groups_mw (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  groupName TEXT,
  worldName TEXT NOT NULL,
  permissions TEXT
);

CREATE TABLE IF NOT EXISTS players (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  userName TEXT,
  userGroup TEXT NOT NULL,
  permissions TEXT
);

CREATE TABLE IF NOT EXISTS players_mw (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  userName TEXT,
  worldName TEXT NOT NULL,
  userGroup TEXT NOT NULL,
  permissions TEXT
);
