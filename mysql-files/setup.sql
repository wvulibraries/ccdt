# Create the DB
CREATE DATABASE IF NOT EXISTS css
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS css_testing
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

# Give Access to user mountaineer from other dockers
GRANT ALL PRIVILEGES ON *.* TO 'mountaineer'@'%';
