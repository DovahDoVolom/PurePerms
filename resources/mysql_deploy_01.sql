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

CREATE TABLE IF NOT EXISTS groups(
  id INT(16) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  groupName VARCHAR(64) UNIQUE KEY NOT NULL,
  alias VARCHAR(32) DEFAULT '' NOT NULL,
  isDefault BOOLEAN DEFAULT 0 NOT NULL,
  inheritance TEXT NOT NULL,
  permissions TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS groups_mw(
  id INT(16) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  groupName VARCHAR(64) UNIQUE KEY NOT NULL,
  isDefault BOOLEAN DEFAULT 0 NOT NULL,
  worldName TEXT NOT NULL,
  permissions TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS players(
  id INT(16) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  userName VARCHAR(16) UNIQUE KEY NOT NULL,
  userGroup VARCHAR(32) NOT NULL,
  permissions TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS players_mw(
  id INT(16) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  userName VARCHAR(16) UNIQUE KEY NOT NULL,
  worldName TEXT NOT NULL,
  userGroup VARCHAR(32) NOT NULL,
  permissions TEXT NOT NULL
);