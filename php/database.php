<?php
require_once('constants.php');
session_start();
//----------------------------------------------------------------------------
//--- dbConnect --------------------------------------------------------------
//----------------------------------------------------------------------------
// Create the connection to the database.
// \return False on error and the database otherwise.
function dbConnect()
{
    $dsn = 'pgsql:dbname=' . DB_NAME . ';host=' . DB_SERVER . ';port=' . DB_PORT;

    $user = DB_USER;
    $password = DB_PASSWORD;
    try {
        $db = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        echo 'Connexion échouée : ' . $e->getMessage();
    }
    return $db;
}

//-- affiche que la connexion a réussi
?>