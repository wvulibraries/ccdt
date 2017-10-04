# Create the DB
CREATE DATABASE IF NOT EXISTS css
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS css_testing 
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

# Give Access to Vagrant Box
GRANT ALL PRIVILEGES ON *.* TO 'mountaineer'@'localhost';

# Set the password
SET PASSWORD for 'mountaineer'@'localhost' =PASSWORD('mountaineer');
