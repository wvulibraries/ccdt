<?php
    // Setup for Vagrant Users - Vagrant Box is the following credentials for login
    $databaseOptions = array(
        'username' => 'username',
        'password' => 'password'
    );

    // Pull in the login stuff for production value
    require_once '/home/www.libraries.wvu.edu/phpincludes/databaseConnectors/database.lib.wvu.edu.remote.php';

   // DB Name and login steup for the rest of the app
    $databaseOptions['dbName'] = 'websiteGeneral';
    $db                        = db::create('mysql', $databaseOptions, 'appDB');


    if (EngineAPI::VERSION >= "4.0") {
        $localvars  = localvars::getInstance();
        $localvarsFunction = array($localvars,'set');
    }
    else {
        $localvarsFunction = array("localvars","add");
    }
?>