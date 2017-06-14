# Create the DB
CREATE DATABASE css character set UTF8mb4 collate utf8mb4_bin;

# Give Access to Vagrant Box
GRANT ALL PRIVILEGES ON *.* TO 'mountaineer'@'localhost';

# Set the password
SET PASSWORD for 'mountaineer'@'localhost' =PASSWORD('mountaineer');
