# Create the DB
CREATE DATABASE css;

# Give Access to Vagrant Box
GRANT ALL PRIVILEGES ON *.* TO 'mountaineer'@'localhost';

# Set the password
SET PASSWORD for 'mountaineer'@'localhost' =PASSWORD('mountaineer');
