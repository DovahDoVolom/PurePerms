/* PurePerms by 64FF00 (xktiverz@gmail.com, @64ff00 for Twitter) */

CREATE TABLE IF NOT EXISTS groups
(
  groupName VARCHAR(64) PRIMARY KEY,
  isDefault BOOLEAN DEFAULT 0,
  inheritance TEXT,
  permissions TEXT
);