<?php

class ConnectionManager
{
  public function exists($pseudo)
  {
    try 
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $requete = "SELECT * FROM login where pseudo='".$pseudo."'";
        $q = $db->query($requete);
        if (!$q)
            die("Table login inexistante");

        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        $bidon = $donnees['bidon'];
        
        if (count($bidon) == 1)
            return $bidon;
        else
            return '0';
    }
    catch (PDOException $e)
    {
        die("Impossibilité d'accéder à la base de données<br/>");
    }
  }

  public function get($pseudo)
  {
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $requete = "SELECT * FROM login WHERE pseudo = '$pseudo'";
    $q = $db->query($requete);
    $donnees = $q->fetch(PDO::FETCH_ASSOC);

    return new Connection($donnees);
  }

  public function count($id)
  {
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $requete = "SELECT COUNT(*) FROM verif where id=$id";

    return $db->query($requete)->fetchColumn();
  }
  
  public function getList()
  {
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $connections = array();
        
    $requete = 'SELECT * FROM verif';
    $q = $db->query($requete);
    
    while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
    {
        $connections[] = new Connection($donnees);
    }
        
    return $connections;
  }
    
  public function add($uid)
  {
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $requete = "update login set connecte = true where uid = $uid";
    $q = $db->query($requete);
    $requete = "update users set date_connection = now() where uid = $uid ";
    $q = $db->query($requete);
    $requete = "insert into verif (uid) values ('$uid')";
    $q = $db->query($requete);
  }
    
  public function quitter($uidActif)
  {
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $requete = 'UPDATE login SET connecte=false where uid='.$uidActif;
    $q = $db->query($requete);
        
    $requete = 'UPDATE login SET connecte=false where uid='.$uidActif;
    $q = $db->query($requete);
        
    $requete = "DELETE from verif where uid=$uidActif";
    $q = $db->query($requete);
        
    header('Location: ../index.php');   
  }

}

?>