/* PurePerms by 64FF00 (xktiverz@gmail.com, @64ff00 for Twitter) / UTF-8 */

CREATE TABLE IF NOT EXISTS `groups`(
  `groupName` VARCHAR(32) NOT NULL,
  `isDefault` BOOLEAN DEFAULT 0 NOT NULL,
  `inheritance` TEXT NOT NULL,
  `permissions` TEXT NOT NULL,
  PRIMARY KEY(`groupName`)
);

INSERT IGNORE INTO `groups` VALUES ('Guest', 1, '', '-essentials.kit,-essentials.kit.other,-pocketmine.command.me,pocketmine.command.list,pperms.command.ppinfo');
INSERT IGNORE INTO `groups` VALUES ('Admin', 0, 'Guest', 'essentials.gamemode,pocketmine.broadcast,pocketmine.command.gamemode,pocketmine.command.give,pocketmine.command.kick,pocketmine.command.teleport,pocketmine.command.time');
INSERT IGNORE INTO `groups` VALUES ('Owner', 0, 'Guest,Admin', 'essentials,pocketmine.command,pperms.command');
INSERT IGNORE INTO `groups` VALUES ('OP', 0, '', '*');

CREATE TABLE IF NOT EXISTS `groups_mw`(
  `groupName` VARCHAR(64) NOT NULL,
  `worldName` TEXT NOT NULL,
  `permissions` TEXT NOT NULL,
  PRIMARY KEY(`groupName`)
);

CREATE TABLE IF NOT EXISTS `players`(
  `userName` VARCHAR(16) NOT NULL,
  `userGroup` VARCHAR(32) NOT NULL,
  `permissions` TEXT NOT NULL,
  PRIMARY KEY(`userName`)
);

CREATE TABLE IF NOT EXISTS `players_mw`(
  `userName` VARCHAR(16) NOT NULL,
  `worldName` TEXT NOT NULL,
  `userGroup` VARCHAR(32) NOT NULL,
  `permissions` TEXT NOT NULL,
  PRIMARY KEY(`userName`)
);