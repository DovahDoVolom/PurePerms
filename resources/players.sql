CREATE TABLE IF NOT EXISTS players (
  userName TEXT PRIMARY KEY,
  userGroup TEXT NOT NULL,
  permissions TEXT
);

/* TEST */
INSERT INTO players (userName, userGroup, permissions) VALUES("64FF00", "Guest", NULL);

CREATE TABLE IF NOT EXISTS players_mw (
  userName TEXT PRIMARY KEY,
  world TEXT NOT NULL,
  userGroup TEXT NOT NULL,
  permissions TEXT
);

/* TEST */
INSERT INTO players_mw (userName, world, userGroup, permissions) VALUES("64FF00", "world", "Guest", NULL);