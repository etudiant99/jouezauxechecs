<?php

    include_once ("../include/config.php");
    
    $id = $_GET['uid'];
    
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $requete = "delete from login where uid=$id";
    $db->exec($requete);
    $requete = "delete from users where uid=$id";
    $db->exec($requete);
    $requete = "delete from statistiques where uid=$id";
    $db->exec($requete);

    header('Location:  ../index.php?action=les joueurs');

?>