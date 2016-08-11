# Rockefeller CSS
> Not this CSS:

![CSS](http://i.giphy.com/nArBQosm5nXdm.gif)

This is [Recokefeller](https://en.wikipedia.org/wiki/Jay_Rockefeller) Constituent Services System (CSS) archival repository. The repository hosts the project to load and process flat files into MySQL. Also, the repository presents a searchable interface for processed information.

## Requirements
1. [Vagrant](https://www.vagrantup.com/downloads.html)
1. [Luck](http://i.giphy.com/9m9wvjeu3K5c4.gif)

## To-do
- [x] Create a virtual environment with Vagrant
- [x] Create scripts to load and process the flat files and store data into MySQL
- [ ] Create public interface for users to search through the files and user records
- [ ] Create an admin interface to manage the archival system

## Installation
Although effort has been made to automate most of the tasks, some functionality is intentionally kept manual. If you want to own this project for your own flat file then simply do the following:
  1. Change the `$folder` and `$fileName` in [LoadFlatFiles.php](LoadCrsFlatFiles/LoadCrsFlatFiles.php)
  1. Change the schema of your database in [base.sql](sqlFiles/base.sql) and [setup.sql](sqlFiles/setup.sql) depending on your data(Will allow you to customize your database schema)
  1. Update the columns(at multiple locations) in insrtToDb at [LoadFlatFiles.php](LoadCrsFlatFiles/LoadCrsFlatFiles.php)

## Author
1. [Ajay Kavuri](http://pseudoaj.com)

## Refernces
1. [explode vs strtok](http://stackoverflow.com/questions/2528168/whats-the-use-of-function-strtokin-php-how-is-better-than-other-string-functi)
1. [PDO](http://markonphp.com/insert-pdo-prepared-statement/)
1. [Preventing injection with PDO](http://stackoverflow.com/questions/4364686/how-do-i-sanitize-input-with-pdo)
