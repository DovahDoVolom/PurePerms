CREATE TABLE IF NOT EXISTS groups (
  groupName TEXT NOT NULL PRIMARY KEY,
  isDefault INTEGER NOT NULL,
  permissions TEXT
);