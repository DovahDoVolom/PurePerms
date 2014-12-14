CREATE TABLE IF NOT EXISTS groups (
  groupName TEXT PRIMARY KEY,
  isDefault INTEGER NOT NULL DEFAULT 0,
  inheritance TEXT,
  permissions TEXT
);

/* TEST */
INSERT INTO groups (groupName, isDefault, inheritance, permissions) VALUES("Guest", 1, NULL, "-pocketmine.command.me;pocketmine.command.list;pperms.command.ppinfo");
INSERT INTO groups (groupName, isDefault, inheritance, permissions) VALUES("Admin", 0, "Guest", "pocketmine.broadcast;pocketmine.command.gamemode;pocketmine.command.give;pocketmine.command.kick;pocketmine.command.teleport;pocketmine.command.time");
INSERT INTO groups (groupName, isDefault, inheritance, permissions) VALUES("Owner", 0, "Guest;Admin", "pocketmine.command;pperms.command");

CREATE TABLE IF NOT EXISTS groups_mw (
  groupName TEXT PRIMARY KEY,
  world TEXT,
  permissions TEXT
);

/* TEST */
INSERT INTO groups_mw (groupName, world, permissions) VALUES("Guest", NULL, NULL);
INSERT INTO groups_mw (groupName, world, permissions) VALUES("Admin", NULL, NULL);
INSERT INTO groups_mw (groupName, world, permissions) VALUES("Owner", NULL, NULL);